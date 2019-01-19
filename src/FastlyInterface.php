<?php

namespace Fastly;

/**
 * @author Gusts Fridrihsons <gusts@overkontrol.com>
 */
interface FastlyInterface {
 
	function send($method, $uri, array $options = []);
	function service ($name);
 
	function purge($url);
	function purgeAll();
    function purgeKey($keys);
	
	function softPruge ($url);
	function softPrugeKey ($keys);
	
	function getError ();
}