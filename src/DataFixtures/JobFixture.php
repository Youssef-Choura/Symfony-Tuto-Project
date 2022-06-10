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
        $data = [
            "Data scientist",
            "Statisticien",
            "Analyste cyber-sécurité",
            "Médecin ORL",
            "Échographiste",
            "Mathématicien",
            "Ingénieur logiciel",
            "Analyste informatique",
            "Pathologiste du discours / langage",
            "Actuaire",
            "Ergothérapeute",
            "Directeur des Ressources Humaines",
            "Hygiéniste dentaire "
        ];
        foreach ($data as $iValue) {
            $job = new Job();
            $job->setDesignation($iValue);
            $manager->persist($job);
        }
        $manager->flush();
    }
}
