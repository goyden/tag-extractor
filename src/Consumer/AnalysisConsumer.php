<?php

namespace App\Consumer;

use PhpAmqpLib\Message\AMQPMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use App\Service\{TagsExtractor, WebpageFactory};
use App\Storage\{AnalysisStorage, TagStorage};

class AnalysisConsumer implements ConsumerInterface
{
    /**
     * @var AnalysisStorage
     */
    private $analysisStorage;

    /**
     * @var TagStorage
     */
    private $tagStorage;

    /**
     * @var WebpageFactory
     */
    private $webpageFactory;

    /**
     * @var TagsExtractor
     */
    private $tagsExtractor;

    public function __construct(
        AnalysisStorage $analysisStorage,
        TagStorage $tagStorage,
        WebpageFactory $webpageFactory,
        TagsExtractor $tagsExtractor
    )
    {
        $this->analysisStorage = $analysisStorage;
        $this->tagStorage = $tagStorage;
        $this->webpageFactory = $webpageFactory;
        $this->tagsExtractor = $tagsExtractor;
    }

    public function execute(AMQPMessage $message): void
    {
        $data = unserialize($message->body);

        $analysisId = $data['id'];

        $webpage = $this->webpageFactory->createPage($data['url']);
        if ($webpage === null) {
            $this->analysisStorage->setFailed($analysisId);
            return;
        }

        $tags = $this->tagsExtractor->extract($webpage);

        $analysis = $this->analysisStorage->get($analysisId);

        foreach ($tags as $tag) {
            $tag->setAnalysis($analysis);
        }

        $this->tagStorage->createTags($tags);

        $this->analysisStorage->setFinished($analysisId);
    }
}