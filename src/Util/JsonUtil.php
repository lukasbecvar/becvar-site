<?php

namespace App\Util;

use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class JsonUtil
 *
 * JsonUtil provides functions for retrieving JSON data from a file or URL
 *
 * @package App\Util
 */
class JsonUtil
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get JSON data from a file or URL
     *
     * @param string $target The file path or URL
     * @param string $method The HTTP method to use
     *
     * @return array<mixed>|null The decoded JSON data as an associative array or null on failure
     */
    public function getJson(string $target, string $method = 'GET'): ?array
    {
        // request context
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => [
                    'User-Agent: becvar-site'
                ],
                'timeout' => 5
            ]
        ]);

        try {
            // get data
            $data = file_get_contents($target, false, $context);

            // return null if data retrieval fails
            if ($data == null) {
                return null;
            }

            // decode & return json
            return json_decode($data, true);
        } catch (Exception $e) {
            $errorMsg = 'Error retrieving JSON data: ' . $e->getMessage();

            // secure api token
            $errorMsg = str_replace($_ENV['EXTERNAL_LOG_TOKEN'], '********', $errorMsg);

            // log error
            $this->logger->error($errorMsg);

            // return null
            return null;
        }
    }
}
