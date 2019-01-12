<?php
/**
 * Created by PhpStorm.
 * User: gusts
 * Date: 2019-01-12
 * Time: 06:11
 */

use \Fastly\Adapter\Guzzle\GuzzleAdapter;
use \Fastly\Fastly;

require __DIR__ . '/../vendor/autoload.php';

$adapter = new GuzzleAdapter('api_key');
$fastly = new Fastly($adapter);


echo "ok";
