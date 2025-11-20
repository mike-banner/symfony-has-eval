<?php 

namespace App\Tests\Entity;

use App\Entity\Establishment;

$entityManager = static::getContainer()->get('doctrine')->getManager();

$establishment = new Establishment();
$establishment->setName('Test Establishment');
$establishment->setCity('Paris');

$entityManager->persist($establishment);
$entityManager->flush();

$client = static::createClient();
$crawler = $client->request('GET', '/evaluate/'.$establishment->getId());
