<?php

namespace App\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\{Request, Response};
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Storage\{AnalysisStorage, TagStorage};
use App\Event\AnalysisCreatedEvent;
use App\Form;

class AnalysisController extends AbstractFOSRestController
{
    public function create(
        EventDispatcherInterface $eventDispatcher,
        AnalysisStorage $analysisStorage,
        Request $request
    ): Response
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $createAnalysis = new Form\Validation\CreateAnalysis();
        $form = $this->createForm(Form\CreateAnalysisType::class, $createAnalysis);
        $form->submit($data);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $view = $this->view($form);
            return $this->handleView($view);
        }

        $analysis = $analysisStorage->create($createAnalysis->getUrl());

        $eventDispatcher->dispatch(new AnalysisCreatedEvent($analysis));

        $view = $this->view(['id' => $analysis->getId()], 201);
        return $this->handleView($view);
    }

    /**
     * @throws HttpException
     */
    public function read(AnalysisStorage $analysisStorage, TagStorage $tagStorage, Request $request): Response
    {
        $getAnalysis = new Form\Validation\GetAnalysis();
        $form = $this->createForm(Form\GetAnalysisType::class, $getAnalysis);
        $form->submit($request->query->all());

        if (!$form->isSubmitted() || !$form->isValid()) {
            $view = $this->view($form);
            return $this->handleView($view);
        }

        $analysis = $analysisStorage->get($getAnalysis->getId());
        if ($analysis === null) {
            throw new HttpException(404, 'Analysis with this ID was not found.');
        }

        if ($analysis->getIsFailed()) {
            throw new HttpException(400, 'Analysis has failed. There was some problems with requesting it\'s URL.');
        }

        if (!$analysis->getIsFinished()) {
            throw new HttpException(202, 'Analysis is in progress.');
        }

        $tags = $tagStorage->findByAnalysisId($analysis->getId());

        $result = [];
        foreach ($tags as $tag) {
            $result[$tag->getType()] = $tag->getAmount();
        }

        $view = $this->view($result, 200);
        return $this->handleView($view);
    }
}