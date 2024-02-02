<?php

namespace App\Util;

/**
 * Class JsonUtil
 * 
 * JsonUtil provides functions for retrieving JSON data from a file or URL.
 * 
 * @package App\Util
 */
class JsonUtil
{
    /**
     * Get JSON data from a file or URL.
     *
     * @param string $target The file path or URL.
     *
     * @return array<mixed>|null The decoded JSON data as an associative array or null on failure.
     */
    public function getJson($target): ?array 
    {
        // requst options
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ];

        // create request context
        $context = stream_context_create($opts);

        // try get contents data
        try {

            // get data
            $data = file_get_contents($target, false, $context);
        } catch (\Exception) {
            $data = null;
        }

        // decode & return json
        return json_decode(utf8_encode($data), true);
    }
}
