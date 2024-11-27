<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\ReminderRepository;

class HomepageController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(
        ReminderRepository $reminderRepo
        ): Response
    {
        // TODO: La limite est à 4 pour montrer qu'elle fonctionne, modifier plus tard
        $limitReminders = $reminderRepo->findBy([], limit: 4);

        return $this->render('homepage/index.html.twig', [
            'reminders' => $limitReminders
        ]);
    }
}
