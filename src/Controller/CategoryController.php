<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\CategoryRepository;

use App\Form\CategoryType;

use App\Entity\Category;

#[Route('/categories')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'categories')]
    public function index(
        CategoryRepository $categoryRepo
        ): Response
    {
        // TODO: Limiting to 4, just to show off that the limit works. Change me !
        $categories = $categoryRepo->findBy([], limit: 4);

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/add', name: 'add_category')]
    public function add(
        CategoryRepository $categoryRepo,
        Request $request,
        EntityManagerInterface $entityManager
        ): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            

            $entityManager->persist($categoryRepo);
            $entityManager->flush();

            /* TODO: Should instead redirect to a detailed view of the category...
            But a details page with only the name displayed is a bit redundant
            */ 
            return $this->redirectToRoute('categories');
        }

        return $this->render('category/add_form.html.twig', [
            'form' => $form
        ]);
    }
}
