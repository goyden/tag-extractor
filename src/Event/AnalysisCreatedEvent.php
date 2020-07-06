<?php

namespace App\Event;

use App\Entity\Analysis;
use Symfony\Contracts\EventDispatcher\Event;

class AnalysisCreatedEvent extends Event
{
    public const NAME = 'analysis.created';

    /**
     * @var Analysis
     */
    private $analysis;

    public function __construct(Analysis $analysis)
    {
        $this->analysis = $analysis;
    }

    public function getAnalysis(): Analysis
    {
        return $this->analysis;
    }
}