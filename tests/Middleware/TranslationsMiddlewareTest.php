<?php

namespace App\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use App\Manager\VisitorManager;
use App\Middleware\TranslationsMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/**
 * Class TranslationsMiddleware
 *
 * Test the TranslationsMiddleware class.
 *
 * @package App\Tests\Middleware
 */
class TranslationsMiddlewareTest extends TestCase
{
    private MockObject|TranslationsMiddleware $middleware;
    private MockObject|VisitorManager $visitorManagerMock;
    private MockObject|LocaleAwareInterface $translatorMock;

    protected function setUp(): void
    {
        $this->visitorManagerMock = $this->createMock(VisitorManager::class);
        $this->translatorMock = $this->createMock(LocaleAwareInterface::class);

        $this->middleware = new TranslationsMiddleware(
            $this->visitorManagerMock,
            $this->translatorMock
        );
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testOnKernelRequestWithUnknownLanguage(): void
    {
        // expecting a call to getVisitorLanguage method
        $this->visitorManagerMock->expects($this->once())->method('getVisitorLanguage')->willReturn('unknown');

        // expecting a call to setLocale method
        $this->translatorMock->expects($this->once())->method('setLocale')->with('en');

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testOnKernelRequestWithHostLanguage(): void
    {
        // expecting a call to getVisitorLanguage method
        $this->visitorManagerMock->expects($this->once())->method('getVisitorLanguage')->willReturn('host');

        // expecting a call to setLocale method
        $this->translatorMock->expects($this->once())->method('setLocale')->with('en');

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testOnKernelRequestWithIdentifiedLanguage(): void
    {
        // expecting a call to getVisitorLanguage method
        $this->visitorManagerMock->expects($this->once())->method('getVisitorLanguage')->willReturn('fr');

        // expecting a call to setLocale method
        $this->translatorMock->expects($this->once())->method('setLocale')->with('fr');

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }
}
