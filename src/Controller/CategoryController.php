<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\CategoryRepository;

#[Route('/categories')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'categories')]
    public function index(
        CategoryRepository $categoryRepo
        ): Response
    {
        // TODO: La limite est à 4 pour montrer qu'elle fonctionne, modifier plus tard
        $categories = $categoryRepo->findBy([], limit: 4);

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
