<?php

namespace App\DataFixtures;

use App\Entity\Job;
use App\Entity\Personne;
use Brunty\Faker\BuzzwordJobProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
           $job = new Job();
            $faker->addProvider(new BuzzwordJobProvider($faker));
            $job->setDesignation($faker->jobTitle);
           $manager->persist($job);
        }
        $manager->flush();
    }
}
