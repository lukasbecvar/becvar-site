<?php

namespace App\Util;

/*
    Json util provides function for get json file or url
*/

class JsonUtil
{
    public function getJson($target): ?array {

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

        // decode data
        $data = json_decode(utf8_encode($data), true);
        return $data;
    }
}
