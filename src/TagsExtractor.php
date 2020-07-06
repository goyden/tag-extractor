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
        return $this->collectNodes($crawler, []);
    }

    /**
     * @param Tag[] $tags
     *
     * @return Tag[]
     */
    private function collectNodes(Crawler $crawler, array $tags): array
    {
        try {
            $nodes = $crawler->children();
            // InvalidArgumentException will be thrown, when node has no children.
        } catch (\InvalidArgumentException $exception) {
            return $tags;
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

        return $this->collectNodes($nodes, $tags);
    }
}