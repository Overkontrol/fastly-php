<?php

namespace Fastly\Adapter;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Gusts Fridrihsons <gusts@overkontrol.com>
 */
interface AdapterInterface
{
    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return ResponseInterface
     */
    public function send($method, $uri, array $options = []);
}