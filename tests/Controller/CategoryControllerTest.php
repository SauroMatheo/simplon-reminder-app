<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;
use App\Controller\CategoryController;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CategoryControllerTest extends TestCase
{
    private $categoryRepository;
    private $entityManager;
    private $controller;
    private $form;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->form = $this->createMock(FormInterface::class);

        $this->controller = $this->getMockBuilder(CategoryController::class)
            ->onlyMethods(['render', 'createForm', 'redirectToRoute', 'isCsrfTokenValid'])
            ->getMock();
    }

    public function testIndex(): void
    {
        $categories = [new Category(), new Category()];

        $this->categoryRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($categories);

        $response = $this->controller->index($this->categoryRepository);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testNew(): void
    {
        $request = new Request();

        $this->controller->expects($this->once())
            ->method('createForm')
            ->willReturn($this->form);

        $this->form->expects($this->once())
            ->method('handleRequest')
            ->with($request);

        $this->form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->controller->expects($this->once())
            ->method('redirectToRoute')
            ->willReturn(new RedirectResponse('/categories'));

        $response = $this->controller->new($request, $this->entityManager);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testNewWithInvalidForm(): void
{
    $request = new Request();
    $category = new Category();

    $this->controller->expects($this->once())
        ->method('createForm')
        ->willReturn($this->form);

    $this->form->expects($this->once())
        ->method('handleRequest')
        ->with($request);

    $this->form->expects($this->once())
        ->method('isSubmitted')
        ->willReturn(false);

    $response = $this->controller->new($request, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}
public function testShow(): void
{
    $category = new Category();
    $category->setName('Test Category');

        $this->controller->expects($this->once())
        ->method('render')
        ->with(
            'category/show.html.twig',
            ['category' => $category]
        )
        ->willReturn(new Response());

    $response = $this->controller->show($category);


    $this->assertInstanceOf(Response::class, $response);
}
public function testEditWithValidForm(): void
{
    $request = new Request();
    $category = new Category();

    $this->controller->expects($this->once())
        ->method('createForm')
        ->willReturn($this->form);

    $this->form->expects($this->once())
        ->method('handleRequest')
        ->with($request);

    $this->form->expects($this->once())
        ->method('isSubmitted')
        ->willReturn(true);

    $this->form->expects($this->once())
        ->method('isValid')
        ->willReturn(true);

    $this->entityManager->expects($this->once())
        ->method('flush');

    $this->controller->expects($this->once())
        ->method('redirectToRoute')
        ->with('app_category_index', [], Response::HTTP_SEE_OTHER)
        ->willReturn(new RedirectResponse('/categories'));

    $response = $this->controller->edit($request, $category, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}

public function testEditWithInvalidForm(): void
{
    $request = new Request();
    $category = new Category();

    $this->controller->expects($this->once())
        ->method('createForm')
        ->willReturn($this->form);

    $this->form->expects($this->once())
        ->method('handleRequest')
        ->with($request);

    $this->form->expects($this->once())
        ->method('isSubmitted')
        ->willReturn(false);

    $response = $this->controller->edit($request, $category, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}

public function testDeleteWithValidToken(): void
{
    $category = new Category();
    $reflection = new \ReflectionClass($category);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($category, 1);


    $request = new Request([], ['_token' => 'valid_token']);

    $this->controller->expects($this->once())
        ->method('isCsrfTokenValid')
        ->with('delete1', 'valid_token')
        ->willReturn(true);

    $this->entityManager->expects($this->once())
        ->method('remove')
        ->with($category);

    $this->entityManager->expects($this->once())
        ->method('flush');

    $this->controller->expects($this->once())
        ->method('redirectToRoute')
        ->willReturn(new RedirectResponse('/categories'));

    $response = $this->controller->delete($request, $category, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}

public function testDeleteWithInvalidToken(): void
{
    $category = new Category();
    $reflection = new \ReflectionClass($category);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($category, 1);

    $request = new Request([], ['_token' => 'invalid_token']);

    $this->controller->expects($this->once())
        ->method('isCsrfTokenValid')
        ->with('delete1', 'invalid_token')
        ->willReturn(false);

    $this->entityManager->expects($this->never())
        ->method('remove');

    $this->entityManager->expects($this->never())
        ->method('flush');

    $this->controller->expects($this->once())
        ->method('redirectToRoute')
        ->willReturn(new RedirectResponse('/categories'));

    $response = $this->controller->delete($request, $category, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}
}