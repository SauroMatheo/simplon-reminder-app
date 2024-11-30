<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Reminder;
use PHPUnit\Framework\TestCase;


class ReminderTest extends TestCase
{
    public function testGetId()
    {
        $reminder = new Reminder();
        $this->assertNull($reminder->getId());
    }

    public function testGetSetTitle()
    {
        $reminder = new Reminder();
        $title = 'Test Title';
        $reminder->setTitle($title);
        $this->assertSame($title, $reminder->getTitle());
    }

    public function testGetSetDescription()
    {
        $reminder = new Reminder();
        $description = 'Test Description';
        $reminder->setDescription($description);
        $this->assertSame($description, $reminder->getDescription());
    }

    public function testGetSetCreatedAt()
    {
        $reminder = new Reminder();
        $createdAt = new \DateTime();
        $reminder->setCreatedAt($createdAt);
        $this->assertSame($createdAt, $reminder->getCreatedAt());
    }

    public function testGetSetDueDate()
    {
        $reminder = new Reminder();
        $dueDate = new \DateTime();
        $reminder->setDueDate($dueDate);
        $this->assertSame($dueDate, $reminder->getDueDate());
    }

    public function testGetSetCompletedAt()
    {
        $reminder = new Reminder();
        $completedAt = new \DateTime();
        $reminder->setCompletedAt($completedAt);
        $this->assertSame($completedAt, $reminder->getCompletedAt());
    }

    public function testIsDone()
    {
        $reminder = new Reminder();
        $reminder->setDone(true);
        $this->assertTrue($reminder->isDone());
    }

    public function testGetSetCategory()
    {
        $reminder = new Reminder();
        $category = $this->createMock(Category::class);
        $reminder->setCategory($category);
        $this->assertSame($category, $reminder->getCategory());
    }
}