<?php

namespace App\Tests\Controller;

use App\Controller\HomepageController;
use App\Repository\ReminderRepository;
use App\Service\PaginationService;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomepageControllerTest extends KernelTestCase
{
    private $reminderRepository;
    private $paginationService;
    private $controller;
    private $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reminderRepository = $this->createMock(ReminderRepository::class);
        $this->paginationService = $this->createMock(PaginationService::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);

        $this->controller = new HomepageController($this->paginationService);
    }

    public function testIndex()    {

        $this->queryBuilder
            ->expects($this->once())
            ->method('where')
            ->with('r.isDone = :isDone')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('isDone', false)
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->with('r.dueDate', 'ASC')
            ->willReturn($this->queryBuilder);

        $this->reminderRepository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('r')
            ->willReturn($this->queryBuilder);

        $paginationData = [
            'items' => [
                ['id' => 1, 'title' => 'Reminder 1'],
                ['id' => 2, 'title' => 'Reminder 2']
            ],
            'currentPage' => 1,
            'totalPages' => 3,
            'totalItems' => 25,
            'itemsPerPage' => 10
        ];

        $request = new Request();

        $this->paginationService
            ->expects($this->once())
            ->method('paginate')
            ->with($this->queryBuilder, $request)
            ->willReturn($paginationData);

        $templateEngine = $this->createMock(\Twig\Environment::class);
        $templateEngine
            ->expects($this->once())
            ->method('render')
            ->with(
                'homepage/index.html.twig',
                [
                    'reminders' => $paginationData['items'],
                    'pagination' => $paginationData
                ]
            )
            ->willReturn('rendered content');

        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->any())
            ->method('has')
            ->willReturn(true);
        $container->expects($this->any())
            ->method('get')
            ->with('twig')
            ->willReturn($templateEngine);

        $this->controller->setContainer($container);

        $response = $this->controller->index($this->reminderRepository, $request);

        $this->assertInstanceOf(Response::class, $response);
    }
}

