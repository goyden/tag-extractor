<?php

namespace App\Consumer;

use PhpAmqpLib\Message\AMQPMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use App\Storage\{AnalysisStorage, TagStorage};
use App\Service\WebpageFactory;
use App\TagsExtractor;

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

    public function __construct(
        AnalysisStorage $analysisStorage,
        TagStorage $tagStorage,
        WebpageFactory $webpageFactory
    )
    {
        $this->analysisStorage = $analysisStorage;
        $this->tagStorage = $tagStorage;
        $this->webpageFactory = $webpageFactory;
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

        $tags = (new TagsExtractor())->extract($webpage);

        $analysis = $this->analysisStorage->get($analysisId);

        foreach ($tags as $tag) {
            $tag->setAnalysis($analysis);
        }

        $this->tagStorage->createTags($tags);

        $this->analysisStorage->setFinished($analysisId);
    }
}