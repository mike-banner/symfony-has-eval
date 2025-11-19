<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Establishment;
use App\Entity\Criterion;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création des établissements
        $e1 = new Establishment();
        $e1->setName('EHPAD Les Lilas')
           ->setCity('Paris');
        $manager->persist($e1);

        $e2 = new Establishment();
        $e2->setName('IME Arc-en-Ciel')
           ->setCity('Lyon');
        $manager->persist($e2);

        $e3 = new Establishment();
        $e3->setName('Foyer Résidence Soleil')
           ->setCity('Marseille');
        $manager->persist($e3);

        // Création des critères
        $criteria = [
            ['Respect des droits', 'CH1 OBJ1'],
            ['Participation de la personne', 'CH1 OBJ2'],
            ['Sécurité sanitaire', 'CH2 OBJ6'],
            ['Projet individualisé', 'CH2 OBJ5'],
            ['Continuité de l’accompagnement', 'CH3 OBJ8'],
        ];

        foreach ($criteria as $c) {
            $criterion = new Criterion();
            $criterion->setLabel($c[0])
                      ->setObjective($c[1]);
            $manager->persist($criterion);
        }

        // Enregistrement en base
        $manager->flush();
    }
}
