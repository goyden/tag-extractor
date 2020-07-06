<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\{Entity\Tag, Webpage, TagsExtractor};

class TagsExtractorTest extends TestCase
{
    public function testExtraction(): void
    {
        $html = '<html lang="en">
            <body>
                <div>
                    <p>Baz</p>    
                </div>
                <p>Eggs</p>
            </body>
        </html>';

        $webpage = new Webpage('https://foo.bar', $html);
        $tags = (new TagsExtractor())->extract($webpage);

        $validResult = [
            (new Tag())->setType('html')->setAmount(1),
            (new Tag())->setType('body')->setAmount(1),
            (new Tag())->setType('div')->setAmount(1),
            (new Tag())->setType('p')->setAmount(2),
        ];

        $this->assertEquals($validResult, $tags);
    }
}