<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Entity\Criterion;
use App\Entity\Evaluation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class EvaluationController extends AbstractController
{
    #[Route('/evaluate/{id}', name: 'evaluate_establishment', requirements: ['id' => '\d+'])]
    public function evaluate(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $establishment = $em->getRepository(Establishment::class)->find($id);
        $criteria = $em->getRepository(Criterion::class)->findAll();

        if (!$establishment) {
            throw $this->createNotFoundException('Établissement introuvable');
        }

        // Récupération des évaluations existantes
        $existingEvaluations = $em->getRepository(Evaluation::class)->findBy([
            'establishment' => $id
        ]);

        $evalData = [];
        foreach ($existingEvaluations as $eval) {
            $evalData[$eval->getCriterion()->getId()] = $eval;
        }

        // GESTION DU POST
        if ($request->isMethod('POST')) {

            $post = $request->request->all();
            $scores = $post['scores'] ?? [];
            $comments = $post['comments'] ?? [];

            foreach ($criteria as $criterion) {

                $criterionId = $criterion->getId();
                $score = (int)($scores[$criterionId] ?? 0);
                $comment = $comments[$criterionId] ?? null;

                if (isset($evalData[$criterionId])) {
                    // Mise à jour
                    $evaluation = $evalData[$criterionId];
                    $evaluation->setScore($score)
                               ->setComment($comment);

                } else {
                    // Création
                    $evaluation = new Evaluation();
                    $evaluation->setEstablishment($establishment)
                               ->setCriterion($criterion)
                               ->setScore($score)
                               ->setComment($comment)
                               ->setEvaluator('Admin'); // TODO: remplacer par l'utilisateur connecté

                    $em->persist($evaluation);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Évaluations enregistrées avec succès !');
            return $this->redirectToRoute('establishments_index');
        }

        return $this->render('evaluation/form.html.twig', [
            'establishment' => $establishment,
            'criteria' => $criteria,
            'existingEvaluations' => $evalData,
        ]);
    }

    // PAGE RAPPORT HTML
    #[Route('/establishments/{id}/report', name: 'establishment_report', requirements: ['id' => '\d+'])]
    public function report(int $id, EntityManagerInterface $em): Response
    {
        $establishment = $em->getRepository(Establishment::class)->find($id);
        $evaluations = $em->getRepository(Evaluation::class)->findBy([
            'establishment' => $id
        ]);

        if (!$establishment) {
            throw $this->createNotFoundException('Établissement introuvable');
        }

        // Calcul score
        $total = array_sum(array_map(fn($e) => $e->getScore(), $evaluations));
        $count = count($evaluations);
        $average = $count ? round($total / $count, 2) : 0;

        return $this->render('evaluation/report.html.twig', [
            'establishment' => $establishment,
            'evaluations' => $evaluations,
            'average' => $average
        ]);
    }

    // PDF DU RAPPORT
    #[Route('/establishments/{id}/report/pdf', name: 'establishment_report_pdf', requirements: ['id' => '\d+'])]
    public function reportPdf(int $id, EntityManagerInterface $em): Response
    {
        $establishment = $em->getRepository(Establishment::class)->find($id);
        $evaluations = $em->getRepository(Evaluation::class)->findBy(['establishment' => $id]);

        if (!$establishment) {
            throw $this->createNotFoundException('Établissement introuvable');
        }

        $total = array_sum(array_map(fn($e) => $e->getScore(), $evaluations));
        $count = count($evaluations);
        $average = $count ? round($total / $count, 2) : 0;

        $html = $this->renderView('evaluation/report_pdf.html.twig', [
            'establishment' => $establishment,
            'evaluations' => $evaluations,
            'average' => $average
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->stream("rapport_{$establishment->getName()}.pdf", ["Attachment" => false]),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }
}
