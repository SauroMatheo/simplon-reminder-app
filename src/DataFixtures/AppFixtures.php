<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Category;
use App\Entity\Reminder;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $categories = [];
        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            $category->setName($faker->words(2, true));

            $manager->persist($category);

            $categories[] = $category;
        }

        for ($i = 0; $i < 100; $i++) {
            $reminder = new Reminder();
            $reminder->setTitle($faker->words(3, true));
            $reminder->setDescription($faker->paragraph());
            $reminder->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $reminder->setDueDate($faker->dateTimeBetween('-6 months', '+6 months'));
            $reminder->setCompletedAt($faker->boolean(50)? $faker->dateTimeBetween($reminder->getCreatedAt(), '+6 months') : null);
            $reminder->setDone($reminder->getCompletedAt() === null ? false : true);
            $reminder->setCategory($faker->randomElement($categories));

            $manager->persist($reminder);
        }

        $manager->flush();
    }
}

