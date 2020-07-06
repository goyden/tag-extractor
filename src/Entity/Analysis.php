<?php

namespace App\Entity;

use App\Repository\AnalysisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnalysisRepository::class)
 */
class Analysis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $url;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $is_finished = false;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $is_failed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getIsFinished(): ?bool
    {
        return $this->is_finished;
    }

    public function setIsFinished(bool $is_finished): self
    {
        $this->is_finished = $is_finished;

        return $this;
    }

    public function getIsFailed(): ?bool
    {
        return $this->is_failed;
    }

    public function setIsFailed(bool $is_failed): self
    {
        $this->is_failed = $is_failed;

        return $this;
    }
}
