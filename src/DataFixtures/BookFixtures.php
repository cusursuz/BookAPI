<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100000; $i++) {
            $book = new Book();
            $book
                ->setTitle($faker->sentence)
                ->setAuthor($faker->name)
                ->setDescription($faker->paragraph)
                ->setPrice($faker->randomFloat(2, 1, 100));

            $manager->persist($book);
        }

        $manager->flush();
    }
}