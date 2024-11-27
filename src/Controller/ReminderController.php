<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\ReminderRepository;

#[Route('/reminders')]
class ReminderController extends AbstractController
{
    #[Route('/todo', name: 'reminders_todo')]
    public function todo(
        ReminderRepository $reminderRepo
        ): Response
    {
        $limitReminders = $reminderRepo->findBy(
            [
                "isDone" => 0
            ],
            limit: 4);

        return $this->render('homepage/index.html.twig', [
            'reminders' => $limitReminders
        ]);
    }

    #[Route('/done', name: 'reminders_done')]
    public function done(
        ReminderRepository $reminderRepo
        ): Response
    {
        $limitReminders = $reminderRepo->findBy(
            [
                "isDone" => 1
            ],
            limit: 4);

        return $this->render('homepage/index.html.twig', [
            'reminders' => $limitReminders
        ]);
    }
}
