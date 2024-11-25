<?php

namespace App\Tests\Manager;

use Twig\Environment;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ErrorManagerTest
 *
 * Test cases for error manager component
 *
 * @package App\Tests\Manager
 */
class ErrorManagerTest extends TestCase
{
    /**
     * Test handle error exception
     *
     * @return void
     */
    public function testHandleError(): void
    {
        // create the twig mock
        /** @var Environment $twigMock */
        $twigMock = $this->createMock(Environment::class);

        // create the error manager
        $errorManager = new ErrorManager($twigMock);

        // expect exception
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Page not found');
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);

        // call tested method
        $errorManager->handleError('Page not found', Response::HTTP_NOT_FOUND);
    }
}
