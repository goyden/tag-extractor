<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class WebpageAnalysisConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $message)
    {
        // TODO Visit URL, get html.
        // data in $message->body
        // return false rejects.
    }
}