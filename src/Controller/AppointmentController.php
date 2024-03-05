<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AppointmentController extends AbstractController
{
    #[Route('/schedule', name: 'appointment_schedule')]
    public function schedule(Request $request, EntityManagerInterface $entityManager): Response {
        $appointment = new Appointment();
    
        $dateParam = $request->query->get('date');
        if ($dateParam) {
            try {
                $appointment->setDate(new \DateTime($dateParam));
            } catch (\Exception $e) {
                $this->addFlash('error', 'Invalid date format.');
                return new RedirectResponse($this->generateUrl('appointment_calendar'));
            }
        }
    
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($appointment);
            $entityManager->flush();
            $this->addFlash('success', 'Your appointment has been scheduled successfully.');
            return $this->redirectToRoute('appointment_confirmation');
        }
    
        return $this->render('appointment/schedule.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/appointment/confirmation', name: 'appointment_confirmation')]
    public function confirmation(): Response
    {
        return $this->render('appointment/confirmation.html.twig');
    }
    #[Route('/appointments', name: 'appointment_list')]
    public function list(AppointmentRepository $appointmentRepository): Response
    {
        $appointments = $appointmentRepository->findAll();
        
        return $this->render('appointment/list.html.twig', [
            'appointments' => $appointments,
        ]);
    }

    #[Route('/appointment/cancel/{id}', name: 'appointment_cancel')]
    public function cancel(EntityManagerInterface $entityManager, AppointmentRepository $appointmentRepository, $id): Response
    {
        $appointment = $appointmentRepository->find($id);

        if ($appointment) {
            $entityManager->remove($appointment);
            $entityManager->flush();
            
            $this->addFlash('success', 'The appointment has been successfully canceled.');
        } else {
            $this->addFlash('error', 'Appointment not found.');
        }

        return $this->redirectToRoute('appointment_list');
    }

    #[Route('/appointment/reschedule/{id}', name: 'appointment_reschedule')]
    public function reschedule(Request $request, EntityManagerInterface $entityManager, AppointmentRepository $appointmentRepository, $id): Response
    {
        $appointment = $appointmentRepository->find($id);

        if (!$appointment) {
            $this->addFlash('error', 'Appointment not found.');
            return $this->redirectToRoute('appointment_list');
        }

        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The appointment has been successfully rescheduled.');

            return $this->redirectToRoute('appointment_list');
        }

        return $this->render('appointment/reschedule.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
