<?php

namespace Fastly;

use Fastly\Adapter\AdapterInterface;

/**
 * @author Gusts Fridrihsons <gusts@overkontrol.com>
 */
class Fastly implements FastlyInterface
{
    const VERSION = '0.4.0';
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

    /**
     * @param AdapterInterface $adapter
     * @param string           $entryPoint
     */
    public function __construct(
        AdapterInterface $adapter,
        $entryPoint = 'https://api.fastly.com'
    )
    {
        $this->adapter    = $adapter;
        $this->entryPoint = $entryPoint;
    }

    /*
     * {@inheritdoc}
     */
    public function send($method, $uri, array $options = [])
    {
        // Prepend entrypoint if $uri is not absolute
        if (0 !== strpos($uri, 'http')) {
            $uri = $this->entryPoint . $uri;
        }
        return $this->adapter->send($method, $uri, $options);
    }
    
    
    /*
     * TODO, vediamo cosa bisogna FARE.
     *
     * 1) Purge URL -> asyn calls
     * 2) Purge ALL
     * 3) PurgeKey
     * 4) PurgeMultipleKeys
     *
     * 5) SoftPurge Url -> async Calls
     * 6) SoftPurgeKey
     * 7) SoftPurgeMultipleKeys -> asyn calls
     *
     *
     * Bon, l'interfaccia è più o meno questa
     *
     *
     *
     */
    
    

    /*
     * {@inheritdoc}
     */
    public function purge($url, array $options = [])
    {
        return $this->send('PURGE', $url, $options);
    }

    /*
     * {@inheritdoc}
     */
    public function purgeAll($service, array $options = [])
    {
        $url = '/service/' . urlencode($service) . '/purge_all';

        return $this->send('POST', $url, $options);
    }

    /*
     * {@inheritdoc}
     */
    public function purgeKey($service, $key, array $options = [])
    {
        $url = '/service/' . urlencode($service) . '/purge/' . $key;

        return $this->send('POST', $url, $options);
    }
}
