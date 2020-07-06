<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use App\Producer\WebpageAnalysisProducer;
use App\Entity\{Analysis, Tag};
use App\Form;

class AnalysisController extends AbstractFOSRestController
{
    public function create(
        EntityManagerInterface $entityManager,
        WebpageAnalysisProducer $analysisProducer,
        Request $request
    )
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $createAnalysis = new Form\Validation\CreateAnalysis();
        $form = $this->createForm(Form\CreateAnalysisType::class, $createAnalysis);
        $form->submit($data);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $view = $this->view($form);
            return $this->handleView($view);
        }

        $analysis = (new Analysis())->setUrl($createAnalysis->getUrl());

        $entityManager->persist($analysis);
        $entityManager->flush();

        $message = ['id' => $analysis->getId(), 'url' => $analysis->getUrl()];
        $analysisProducer->publish(serialize($message));

        $view = $this->view(['id' => $analysis->getId()], 200);

        return $this->handleView($view);
    }

    public function read(EntityManagerInterface $entityManager, Request $request)
    {
        $analysis = new Form\Validation\GetAnalysis();
        $form = $this->createForm(Form\GetAnalysisType::class, $analysis);
        $form->submit($request->query->all());

        if (!$form->isSubmitted() || !$form->isValid()) {
            $view = $this->view($form);
            return $this->handleView($view);
        }

        $tags = $entityManager
            ->getRepository(Tag::class)
            ->findBy(['analysis' => $analysis->getId()]);

        $result = [];
        foreach ($tags as $analysis) {
            $result[$analysis->getType()] = $analysis->getAmount();
        }

        $view = $this->view($result, 200);
        return $this->handleView($view);
    }
}