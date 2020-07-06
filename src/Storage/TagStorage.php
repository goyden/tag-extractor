<?php

namespace App\Storage;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tag;

class TagStorage
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Tag[]
     */
    public function findByAnalysisId(int $analysisId): array
    {
        return $this->entityManager
            ->getRepository(Tag::class)
            ->findBy(['analysis' => $analysisId]);
    }

    /**
     * @param Tag[] $tags
     */
    public function createTags(array $tags): void
    {
        foreach ($tags as $tag) {
            $this->entityManager->persist($tag);
        }
        $this->entityManager->flush();
    }
}