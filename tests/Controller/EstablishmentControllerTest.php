<?php
// tests/Controller/EstablishmentControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Establishment;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EstablishmentControllerTest extends WebTestCase
{
    public function testIndexPageLoadsAndListsEstablishments()
    {
        $client = static::createClient();

        // Requête GET sur la page des établissements
        $crawler = $client->request('GET', '/establishments');

        // Vérifie que la page se charge correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des établissements');

        // Vérifie que le repository retourne des établissements
        $establishments = $client->getContainer()
            ->get('doctrine')
            ->getRepository(Establishment::class)
            ->findAll();

        $this->assertNotEmpty($establishments, 'Il doit y avoir au moins un établissement en base');

        // Vérifie que chaque établissement est affiché dans le HTML avec un lien vers son rapport
        foreach ($establishments as $establishment) {
            $this->assertStringContainsString(
                $establishment->getName(),
                $client->getResponse()->getContent(),
                "Le nom de l'établissement '{$establishment->getName()}' doit apparaître dans la page"
            );

            $this->assertStringContainsString(
                '/establishments/' . $establishment->getId() . '/report',
                $client->getResponse()->getContent(),
                "Le lien vers le rapport de '{$establishment->getName()}' doit être présent"
            );
        }
    }
}
