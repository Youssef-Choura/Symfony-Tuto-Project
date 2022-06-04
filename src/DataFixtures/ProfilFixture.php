<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProfilFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profil = new Profil();
        $profil->setRs('Facebook');
        $profil->setUrl('https://www.facebook.com/aymen.sellaouti');

        $profil = new Profil();
        $profil->setRs('twitter');
        $profil->setUrl('https://twitter.com/aymensellaouti');

        $profil1 = new Profil();
        $profil1->setRs('Facebook');
        $profil1->setUrl('https://www.facebook.com/aymen.sellaouti');

        $profil2 = new Profil();
        $profil2->setRs('LinkedIn');
        $profil2->setUrl('https://www.linkedin.com/in/aymen-sellaouti-b0427731/');

        $profil3 = new Profil();
        $profil3->setRs('Github');
        $profil3->setUrl('https://github.com/aymensellaouti');

        $manager->persist($profil);
        $manager->persist($profil2);
        $manager->persist($profil1);
        $manager->persist($profil3);
        $manager->flush();
    }
}
