<?php
// tests/Controller/EvaluationControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Establishment;
use App\Entity\Criterion;
use Doctrine\ORM\EntityManagerInterface;

class EvaluationControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();

        // Nettoyer les entités pour repartir à zéro
        $this->em->createQuery('DELETE FROM App\Entity\Evaluation')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Criterion')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Establishment')->execute();

        // Créer un établissement test
        $establishment = new Establishment();
        $establishment->setName('Test Etablissement');
        $establishment->setCity('Paris');
        $this->em->persist($establishment);

        // Créer des critères test
        $criteriaLabels = [
            ['label' => 'Propreté', 'objective' => 'Vérifier la propreté du lieu'],
            ['label' => 'Service', 'objective' => 'Évaluer la qualité du service'],
        ];

        foreach ($criteriaLabels as $data) {
            $criterion = new Criterion();
            $criterion->setLabel($data['label']);
            $criterion->setObjective($data['objective']);
            $this->em->persist($criterion);
        }

        $this->em->flush();
    }

    public function testEvaluatePage(): void
    {
        // On récupère le premier établissement
        $establishment = $this->em->getRepository(Establishment::class)->findOneBy([]);
        $this->client->request('GET', '/evaluate/'.$establishment->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Évaluer : '.$establishment->getName());

        // Vérifier que les selects pour les scores existent
        $this->assertSelectorExists('select[name^="scores"]');
    }

    public function testReportPage(): void
    {
        $establishment = $this->em->getRepository(Establishment::class)->findOneBy([]);
        $this->client->request('GET', '/establishments/'.$establishment->getId().'/report');

        $this->assertResponseIsSuccessful();
        // Vérifie juste que le h1 contient "Rapport d'évaluation"
        $this->assertSelectorTextContains('h1', 'Rapport d\'évaluation');
    }

}
