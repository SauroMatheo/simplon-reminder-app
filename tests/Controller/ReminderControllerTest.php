<?php

namespace App\Tests\Controller;

use App\Entity\Reminder;
use PHPUnit\Framework\TestCase;
use App\Controller\ReminderController;
use App\Repository\ReminderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ReminderControllerTest extends TestCase
{
    private $reminderRepository;
    private $entityManager;
    private $controller;
    private $form;

    protected function setUp(): void
    {
        $this->reminderRepository = $this->createMock(ReminderRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->form = $this->createMock(FormInterface::class);

        $this->controller = $this->getMockBuilder(ReminderController::class)
            ->onlyMethods(['render', 'createForm', 'redirectToRoute', 'isCsrfTokenValid'])
            ->getMock();
    }

    public function testIndex(): void
    {
        $reminders = [new Reminder(), new Reminder()];

        $this->reminderRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($reminders);

        $response = $this->controller->index($this->reminderRepository);
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
            ->willReturn(new RedirectResponse('/reminders'));

        $response = $this->controller->new($request, $this->entityManager);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testNewWithInvalidForm(): void
{
    $request = new Request();
    $category = new Reminder();

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
    $reminder = new Reminder();
    $reminder->setTitle('Test Reminder');

    $this->controller->expects($this->once())
        ->method('render')
        ->with(
            'reminder/show.html.twig',
            ['reminder' => $reminder]
        )
        ->willReturn(new Response());

    $response = $this->controller->show($reminder);

    $this->assertInstanceOf(Response::class, $response);
}

public function testEditWithValidForm(): void
{
    $request = new Request();
    $reminder = new Reminder();

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

    $response = $this->controller->edit($request, $reminder, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}

public function testEditWithInvalidForm(): void
{
    $request = new Request();
    $reminder = new Reminder();

    $this->controller->expects($this->once())
        ->method('createForm')
        ->willReturn($this->form);

    $this->form->expects($this->once())
        ->method('handleRequest')
        ->with($request);

    $this->form->expects($this->once())
        ->method('isSubmitted')
        ->willReturn(false);

    $response = $this->controller->edit($request, $reminder, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}

public function testDeleteWithValidToken(): void
{
    $reminder = new Reminder();
    $reflection = new \ReflectionClass($reminder);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($reminder, 1);

    // Créer une requête simple avec un token
    $request = new Request([], ['_token' => 'valid_token']);

    $this->controller->expects($this->once())
        ->method('isCsrfTokenValid')
        ->with('delete1', 'valid_token')
        ->willReturn(true);

    $this->entityManager->expects($this->once())
        ->method('remove')
        ->with($reminder);

    $this->entityManager->expects($this->once())
        ->method('flush');

    $this->controller->expects($this->once())
        ->method('redirectToRoute')
        ->willReturn(new RedirectResponse('/categories'));

    $response = $this->controller->delete($request, $reminder, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}

public function testDeleteWithInvalidToken(): void
{
    $reminder = new Reminder();
    $reflection = new \ReflectionClass($reminder);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($reminder, 1);

    // Créer une requête simple avec un token invalide
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

    $response = $this->controller->delete($request, $reminder, $this->entityManager);
    $this->assertInstanceOf(Response::class, $response);
}
}