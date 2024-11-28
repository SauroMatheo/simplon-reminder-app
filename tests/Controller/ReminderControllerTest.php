<?php

namespace App\Tests\Controller;

use App\Entity\Reminder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReminderControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/reminder/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Reminder::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reminder index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'reminder[title]' => 'Testing',
            'reminder[description]' => 'Testing',
            'reminder[createdAt]' => 'Testing',
            'reminder[dueDate]' => 'Testing',
            'reminder[completedAt]' => 'Testing',
            'reminder[isDone]' => 'Testing',
            'reminder[category]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reminder();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setDueDate('My Title');
        $fixture->setCompletedAt('My Title');
        $fixture->setIsDone('My Title');
        $fixture->setCategory('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reminder');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reminder();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setDueDate('Value');
        $fixture->setCompletedAt('Value');
        $fixture->setIsDone('Value');
        $fixture->setCategory('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'reminder[title]' => 'Something New',
            'reminder[description]' => 'Something New',
            'reminder[createdAt]' => 'Something New',
            'reminder[dueDate]' => 'Something New',
            'reminder[completedAt]' => 'Something New',
            'reminder[isDone]' => 'Something New',
            'reminder[category]' => 'Something New',
        ]);

        self::assertResponseRedirects('/reminder/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getDueDate());
        self::assertSame('Something New', $fixture[0]->getCompletedAt());
        self::assertSame('Something New', $fixture[0]->getIsDone());
        self::assertSame('Something New', $fixture[0]->getCategory());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reminder();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setDueDate('Value');
        $fixture->setCompletedAt('Value');
        $fixture->setIsDone('Value');
        $fixture->setCategory('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/reminder/');
        self::assertSame(0, $this->repository->count([]));
    }
}
