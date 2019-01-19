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
     * @param mixed $uri
     * @param array  $options
     *
     * @return ResponseInterface
     */
    function send($method, $uri, array $options = []);

	function getError();
}