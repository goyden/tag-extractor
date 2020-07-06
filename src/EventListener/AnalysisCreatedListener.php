<?php

namespace App\EventListener;

use App\Event\AnalysisCreatedEvent;
use App\Producer\AnalysisProducer;

class AnalysisCreatedListener
{
    /**
     * @var AnalysisProducer
     */
    private $analysisProducer;

    public function __construct(AnalysisProducer $analysisProducer)
    {
        $this->analysisProducer = $analysisProducer;
    }

    public function __invoke(AnalysisCreatedEvent $event): void
    {
        $analysis = $event->getAnalysis();

        $message = [
            'id' => $analysis->getId(),
            'url' => $analysis->getUrl()
        ];

        $this->analysisProducer->publish(serialize($message));
    }
}