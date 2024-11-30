<?php

namespace App\Controller;

use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ReminderRepository;

class HomepageController extends AbstractController
{
    public function __construct(
        private PaginationService $paginationService
    ) {}

    #[Route('/', name: 'homepage')]
    public function index(ReminderRepository $repository, Request $request): Response
    {
        $queryBuilder = $repository->createQueryBuilder('r')
            ->where('r.isDone = :isDone')
            ->setParameter('isDone', false)
            ->orderBy('r.dueDate', 'ASC');

        $pagination = $this->paginationService->paginate($queryBuilder, $request);

        return $this->render('homepage/index.html.twig', [
            'reminders' => $pagination['items'],
            'pagination' => $pagination,
        ]);
    }
}



