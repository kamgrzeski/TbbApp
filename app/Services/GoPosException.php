<?php

namespace App\Services;

class GoPosException
{

    /**
     * @param string $string
     * @param int $int
     * @param \Exception|ConnectionException $e
     */
    public function __construct(string $string, int $int, $e)
    {
    }
}