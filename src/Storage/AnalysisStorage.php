<?php

namespace App\Storage;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Analysis;

class AnalysisStorage
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(string $url): Analysis
    {
        $analysis = (new Analysis())->setUrl($url);
        $this->entityManager->persist($analysis);
        $this->entityManager->flush();
        return $analysis;
    }

    public function get(int $id): ?Analysis
    {
        return $this->entityManager->find(Analysis::class, $id);
    }

    public function setFailed(int $id): bool
    {
        $analysis = $this->entityManager->find(Analysis::class, $id);
        if ($analysis === null) {
            return false;
        }

        $analysis->setIsFailed(true);
        $this->entityManager->flush();
        return true;
    }

    public function setFinished(int $id): bool
    {
        $analysis = $this->entityManager->find(Analysis::class, $id);
        if ($analysis === null) {
            return false;
        }

        $analysis->setIsFinished(true);
        $this->entityManager->flush();
        return true;
    }
}