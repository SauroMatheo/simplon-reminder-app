<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Reminder;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        $this->category = new Category();
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->category->getId());
        $this->assertNull($this->category->getName());
        $this->assertCount(0, $this->category->getReminders());
    }

    public function testSetName(): void
    {
        $this->category->setName('Test Category');
        $this->assertEquals('Test Category', $this->category->getName());
    }

    public function testAddReminder(): void
    {
        $reminder = new Reminder();
        $this->category->addReminder($reminder);

        $this->assertCount(1, $this->category->getReminders());
        $this->assertTrue($this->category->getReminders()->contains($reminder));
        $this->assertEquals($this->category, $reminder->getCategory());
    }

    public function testRemoveReminder(): void
    {
        $reminder = new Reminder();
        $this->category->addReminder($reminder);
        $this->category->removeReminder($reminder);

        $this->assertCount(0, $this->category->getReminders());
        $this->assertFalse($this->category->getReminders()->contains($reminder));
        $this->assertNull($reminder->getCategory());
    }

    public function testAddExistingReminder(): void
    {
        $reminder = new Reminder();
        $this->category->addReminder($reminder);
        $this->category->addReminder($reminder);

        $this->assertCount(1, $this->category->getReminders());
    }
}

