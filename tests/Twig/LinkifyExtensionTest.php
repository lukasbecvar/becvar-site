<?php

namespace Tests\Twig;

use App\Twig\LinkifyExtension;
use PHPUnit\Framework\TestCase;

class LinkifyExtensionTest extends TestCase
{
    private LinkifyExtension $linkifyExtension;

    protected function setUp(): void
    {
        $this->linkifyExtension = new LinkifyExtension();
    }

    /**
     * Test the getFilters method
     *
     * @return void
     */
    public function testGetFilters(): void
    {
        $filters = $this->linkifyExtension->getFilters();

        $this->assertCount(1, $filters);
        $this->assertInstanceOf('Twig\TwigFilter', $filters[0]);
        $this->assertEquals('linkify', $filters[0]->getName());
    }

    /**
     * @dataProvider linkifyTextProvider
     */
    public function testLinkifyText(string $input, string $expectedOutput): void
    {
        $this->assertEquals($expectedOutput, $this->linkifyExtension->linkifyText($input));
    }

    /**
     * Data provider for the linkifyText method
     *
     * @return array<mixed>
     */
    public function linkifyTextProvider(): array
    {
        return [
            'plain text' => ['Hello world', 'Hello world'],
            'single URL' => ['Visit https://example.com', 'Visit <a href="https://example.com" target="_blank">https://example.com</a>'],
            'multiple URLs' => [
                'Check https://example.com and http://test.com',
                'Check <a href="https://example.com" target="_blank">https://example.com</a> and <a href="http://test.com" target="_blank">http://test.com</a>'
            ],
            'URL with text' => [
                'This is a link: https://example.com with some text.',
                'This is a link: <a href="https://example.com" target="_blank">https://example.com</a> with some text.'
            ]
        ];
    }
}
