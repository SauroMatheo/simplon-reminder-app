<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\CategoryController;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class CategoryControllerTest extends TestCase
{
    public function testIndex(): void
    {

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->method('findAll')->willReturn([
            new Category(),
            new Category(),
        ]);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('category/index.html.twig', [
                'categories' => [new Category(), new Category()],
            ])
            ->willReturn('<html>Categories</html>');

        $controller = new CategoryController();
        $controller->setContainer($this->getContainerMock($twig));

        $response = $controller->index($categoryRepository);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Categories', $response->getContent());
    }

    public function testNew(): void    {

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');


        $form = $this->createMock(\Symfony\Component\Form\FormInterface::class);        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);

        $request = $this->createMock(Request::class);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('category/new.html.twig', $this->anything())
            ->willReturn('<html>New Category</html>');

        $controller = new CategoryController();
        $controller->setContainer($this->getContainerMock($twig));

        $response = $controller->new($request, $entityManager);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('New Category', $response->getContent());
    }

    public function testDelete(): void    {

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('remove');
        $entityManager->expects($this->once())->method('flush');

        $request = $this->createMock(Request::class);
        $request->method('get')->willReturn('valid_token');

        $controller = $this->getMockBuilder(CategoryController::class)
            ->onlyMethods(['isCsrfTokenValid'])
            ->getMock();
        $controller->method('isCsrfTokenValid')->willReturn(true);

        $category = new Category();
        $response = $controller->delete($request, $category, $entityManager);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(303, $response->getStatusCode());
    }

    private function getContainerMock($twig = null)
    {
        $container = $this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class);
        $container->method('get')->willReturnMap([
            ['twig', $twig],
        ]);

        return $container;
    }
}

