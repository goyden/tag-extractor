<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use App\{Entity\Tag, Webpage};

class TagsExtractor
{
    /**
     * @return Tag[]
     */
    public function extract(Webpage $webpage): array
    {
        $crawler = new Crawler($webpage->getHtml());

        $root = $crawler->getNode(0);
        $tags = [
            $root->nodeName => (new Tag())->setType($root->nodeName)->setAmount(1)
        ];

        return $this->getChildrenNodes($crawler, $tags);
    }

    /**
     * @param Tag[] $tags
     *
     * @return Tag[]
     */
    private function getChildrenNodes(Crawler $crawler, array $tags): array
    {
        try {
            $nodes = $crawler->children();
            // InvalidArgumentException will be thrown, when node has no children.
        } catch (\InvalidArgumentException $exception) {
            // array_values transforms map into a list.
            return array_values($tags);
        }

        foreach ($nodes as $node) {
            $tagType = $node->nodeName;

            $tag = $tags[$tagType] ?? null;
            if ($tag === null) {
                $tag = (new Tag())->setType($tagType);
                $tags[$tagType] = $tag;
            }

            $tag->incrementAmount();
        }

        return $this->getChildrenNodes($nodes, $tags);
    }
}