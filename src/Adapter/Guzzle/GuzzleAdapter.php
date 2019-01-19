<?php

namespace Fastly\Adapter\Guzzle;

use Fastly\Fastly as Fastly;
use Fastly\Adapter\AdapterInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\RequestException;

/**
 * @author Gusts Fridrihsons <gusts@overkontrol.com>
 */
class GuzzleAdapter implements AdapterInterface
{
    private $error = [];
    private $options = [];
    
    private $output = [];
    

    
    /**
     * @param string $fastlyKey
     * @param array  $defaultHeaders
     */
    public function __construct ($fastlyKey = null, $defaultHeaders = [])
    {
        $this->options = array_merge(
            ['headers' => [
                'Fastly-Key' => $fastlyKey,
                'Accept'     => 'application/json',
                'User-Agent' => 'fastly-php-v' . Fastly::VERSION
            ]],
            $defaultHeaders
        );
    }
    
    
    /*
     * {@inheritdoc}
     */
    public function send($method, $uri, array $options = [])
    {
    	$this->error = [];
    	$this->output = [];
    	
    	if (!is_array($uri))
	    {
	    	$uri = [$uri];
	    }
	
	    // POOLING
	    $client = new Client();
	
	    $requests = function ($urls) use ($method, $options) {
		
			foreach ($urls as $url)
			{
				yield new Request($method, $url, array_merge_recursive($options, $this->options)['headers']);
			}
	    };
	
	    $pool = new Pool($client, $requests($uri), [
		
		    'concurrency' => 100,
		
		    'fulfilled' => function (ResponseInterface $response) {
			
			    $this->output[] = $this->getBody($response);
		    },
		
		    'rejected' => function (RequestException $e) {
	    	
	    	    $this->error[] = $e->getMessage();
		    },
	    ]);
	
	
	    // Initiate the transfers and create a promise
	    $promise = $pool->promise();
	
	    // Force the pool of requests to complete.
	    $promise->wait();
	    
	    return $this->output;
    }
    
    
    public function getError ()
    {
    	return implode("\r\n", $this->error);
    }
    
    
    private function getBody (ResponseInterface $response)
    {
    	return (string)$response->getBody();
    }
    
}
