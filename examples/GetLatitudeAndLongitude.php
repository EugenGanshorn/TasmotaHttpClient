<?php

use GuzzleHttp\Client;
use TasmotaHttpClient\Request;
use TasmotaHttpClient\Url;

require_once 'vendor/autoload.php';

$url = new Url();
$url->setIpAddress('10.0.10.107');

$client = new Client();

$request = new Request();
$request->setClient($client);
$request->setUrl($url);

/** @noinspection ForgottenDebugOutputInspection */
var_dump($request->Latitude());

/** @noinspection ForgottenDebugOutputInspection */
var_dump($request->Longitude());