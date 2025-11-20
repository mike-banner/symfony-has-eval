<?php

namespace App\Tests\Entity;

use App\Entity\Evaluation;
use App\Entity\Criterion;
use App\Entity\Establishment;
use PHPUnit\Framework\TestCase;

class EvaluationTest extends TestCase
{
    public function testEvaluationSettersAndGetters(): void
    {
        $evaluation = new Evaluation();

        $establishment = new Establishment();
        $criterion = new Criterion();

        $evaluation->setEstablishment($establishment)
                   ->setCriterion($criterion)
                   ->setScore(3)
                   ->setComment('Test comment')
                   ->setEvaluator('Admin');

        $this->assertSame($establishment, $evaluation->getEstablishment());
        $this->assertSame($criterion, $evaluation->getCriterion());
        $this->assertSame(3, $evaluation->getScore());
        $this->assertSame('Test comment', $evaluation->getComment());
        $this->assertSame('Admin', $evaluation->getEvaluator());
        $this->assertInstanceOf(\DateTime::class, $evaluation->getCreatedAt());
    }
}
