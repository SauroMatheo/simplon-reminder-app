<?php

namespace App\Controller;

use App\Entity\Reminder;
use App\Form\ReminderType;
use App\Repository\ReminderRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reminder')]
final class ReminderController extends AbstractController
{
    public function __construct(
        private PaginationService $paginationService
    ) {}

    #[Route(name: 'app_reminder_index', methods: ['GET'])]
    public function index(ReminderRepository $repository, Request $request): Response
    {
        $queryBuilder = $repository->createQueryBuilder('r')
            ->orderBy('r.dueDate', 'ASC');
            
        $pagination = $this->paginationService->paginate($queryBuilder, $request, 12);

        return $this->render('reminder/index.html.twig', [
            'reminders' => $pagination['items'],
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_reminder_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reminder = new Reminder();
        $form = $this->createForm(ReminderType::class, $reminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reminder->setCreatedAt(new \DateTime('now'));
            $reminder->setDone(false);

            $entityManager->persist($reminder);
            $entityManager->flush();

            return $this->redirectToRoute('app_reminder_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reminder/new.html.twig', [
            'reminder' => $reminder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reminder_show', methods: ['GET'])]
    public function show(Reminder $reminder): Response
    {
        return $this->render('reminder/show.html.twig', [
            'reminder' => $reminder,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reminder_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reminder $reminder, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReminderType::class, $reminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reminder_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reminder/edit.html.twig', [
            'reminder' => $reminder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reminder_delete', methods: ['POST'])]
    public function delete(Request $request, Reminder $reminder, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reminder->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reminder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reminder_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/done', name: 'app_reminder_toggle', methods: ['POST'])]
    public function toggle(Request $request, Reminder $reminder, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reminder->getId(), $request->getPayload()->getString('_token'))) {
            if ($reminder->isDone()) {
                $reminder->setDone(false);
                $reminder->setCompletedAt(null);
            } else {
                $reminder->setDone(true);
                $reminder->setCompletedAt(new \DateTime('now'));
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reminder_index', [], Response::HTTP_SEE_OTHER);
    }
}
