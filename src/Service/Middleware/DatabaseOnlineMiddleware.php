<?php

namespace App\Service\Middleware;

use App\Service\Manager\ErrorManager;
use \Doctrine\DBAL\Connection as Connection;

/**
 * Class DatabaseOnlineMiddleware
 *
 * This middleware is used to check the availability of the database.
 * 
 * @package App\Service\Middleware
 */
class DatabaseOnlineMiddleware
{
    /**
     * @var ErrorManager
     * Instance of the ErrorManager for handling error-related functionality.
     */
    private ErrorManager $errorManager;

    /**
     * @var Connection
     * Instance of the Doctrine\DBAL\Connection for interacting with the database.
     */
    private Connection $doctrineConnection;

    /**
     * DatabaseOnlineMiddleware constructor.
     *
     * @param ErrorManager $errorManager
     * @param Connection   $doctrineConnection
     */
    public function __construct(ErrorManager $errorManager, Connection $doctrineConnection) 
    {
        $this->errorManager = $errorManager;
        $this->doctrineConnection = $doctrineConnection;
    }

    /**
     * Check the availability of the database on each kernel request.
     */
    public function onKernelRequest(): void
    {
        try {
            // select for connection try
            $this->doctrineConnection->executeQuery('SELECT 1');
        } catch (\Exception $e) {

            // handle error if database not connected
            $this->errorManager->handleError('database connection error: '.$e->getMessage(), 1337);
        }
    }
}
