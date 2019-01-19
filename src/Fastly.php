<?php

namespace Fastly;

use Fastly\Adapter\AdapterInterface;

/**
 * @author Gusts Fridrihsons <gusts@overkontrol.com>
 */
class Fastly implements FastlyInterface
{
    const VERSION = '1.0.0';
    
    /**
     * Http adapter
     *
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Fastly API entry point
     *
     * @var string
     */
    private $entryPoint;
    
    private $service;
    private $customService;
    
    
    
    /**
     * @param AdapterInterface $adapter
     * @param string           $service
     * @param string           $entryPoint
     */
    public function __construct(AdapterInterface $adapter, $service = "", $entryPoint = 'https://api.fastly.com')
    {
        $this->adapter    = $adapter;
        $this->service    = $service;
        $this->entryPoint = $entryPoint;
    }
	
	
	
	/*
	|--------------------------------------------------------------------------
	| Raw
	|--------------------------------------------------------------------------
	|
	| This method allows to build a custom request to fastly api. It returns
	| an array which is the decodification of fastly's json output.
	|
	*/
	public function send ($method, $uri, array $options = [])
	{
		$uri = $this->buildEndpoint($uri);
		
		// reset custom service
		$this->customService = null;
		
		// Reset errors
		$this->error = null;
		
		
		$result = $this->adapter->send($method, $uri, $options);
		
		if ($result)
		{
			return $this->buildOutput($result);
			
		} else {
			
			return false;
		}
	}
	
	
	
	/*
	|--------------------------------------------------------------------------
	| Custom Service Selection
	|--------------------------------------------------------------------------
	|
	| There can be more than one service to manage. This class accepts a
	| default service name which can be overwriten by this method.
	|
	| Example usage:
	| $fastly->service("anotherservice")->send(...);
	|
	*/
    public function service ($name)
    {
    	$this->customService = $name;
    	
    	return $this;
    }
	
	
	
	/*
	|--------------------------------------------------------------------------
	| HARD Purge
	|--------------------------------------------------------------------------
	|
	| Methods for hard purging a content.
	|
	*/
	
	/**
	 * Purge a single url, but $url can be array and the purge will be
	 * done in async pool.
	 *
	 * @param mixed $url
	 * @return bool|array
	 */
	public function purge ($url)
    {
        return $this->send('PURGE', $url);
    }
	
	/**
	 * Purges all cache entries.
	 *
	 * @return bool|array
	 */
	public function purgeAll ()
    {
        $url = '/service/' . urlencode($this->getService()) . '/purge_all';

        return $this->send('POST', $url);
    }
	
	/**
	 * Purge cache by key. Keys can be an array with multiple tags.
	 *
	 * @param $keys
	 * @return array
	 */
	public function purgeKey ($keys)
    {
    	$output = [];
        
        if (!is_array($keys))
        {
        	$keys = [$keys];
        }
	
	    $url = '/service/' . urlencode($this->getService()) . '/purge';
        $keys = array_chunk($keys, 2);
	
	    foreach ($keys as $i => $chunk)
        {
        	$options = [
        		'headers' => [
        			'Surrogate-Key' => implode(" ", $chunk),
		        ]
	        ];

        	$result = $this->send('POST', $url, $options);
        	
	        $output += (is_array($result) and count($result)) ? $result : [];
        }

        return $output;
    }
	
	
	
	/*
	|--------------------------------------------------------------------------
	| SOFT Purge
	|--------------------------------------------------------------------------
	|
	| Methods for soft purging a content.
	|
	*/
    
    public function softPruge ($url)
    {
	    $options = [
		    'headers' => [
			    'Fastly-Soft-Purge' => '1',
		    ]
	    ];
	    
	    return $this->send('PURGE', $url, $options);
    
    }
	
	public function softPrugeKey ($keys)
	{
		$urls = [];
		
		if (!is_array($keys))
		{
			$keys = [$keys];
		}
		
		// Build Purge URLS
		foreach ($keys as $key)
		{
			$urls[] = '/service/' . urlencode($this->getService()) . '/purge/' . urlencode($key);
		}
		
		$options = [
			'headers' => [
				'Fastly-Soft-Purge' => '1',
			]
		];
		
		return $this->send('POST', $urls, $options);
	}
	
	
	
	/*
	|--------------------------------------------------------------------------
	| Errors
	|--------------------------------------------------------------------------
	|
	*/
	
	public function getError ()
	{
		return $this->adapter->getError();
	}
	
	
	
	/*
	|--------------------------------------------------------------------------
	| Internals
	|--------------------------------------------------------------------------
	|
	*/
	
	private function getService ()
	{
		return ($this->customService) ?? $this->service;
	}
	
	
	private function buildOutput ($responses)
	{
		$output = [];
		
		if (!is_array($responses))
		{
			$responses = [$responses];
		}
		
		foreach ($responses as $response)
		{
			$output += json_decode($response, true);
		}
		
		
		return $output;
	}
	
	
	private function buildEndpoint ($uri)
	{
		if (is_array($uri))
		{
			foreach ($uri as $key => $value)
			{
				$uri[$key] = $this->doBuildEndpoint($value);
			}
		} else {
			
			$uri = $this->doBuildEndpoint($uri);
		}
		
		return $uri;
	}
	
	
	private function doBuildEndpoint ($uri)
	{
		// Prepend entrypoint if $uri is not absolute
		if (0 !== strpos($uri, 'http')) {
			$uri = $this->entryPoint . $uri;
		}
		
		return $uri;
	}
}
