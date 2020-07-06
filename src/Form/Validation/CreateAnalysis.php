<?php

namespace App\Form\Validation;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAnalysis
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min="1", max="250")
     *
     * @var string
     */
    private $url;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}