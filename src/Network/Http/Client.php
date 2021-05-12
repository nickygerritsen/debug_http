<?php
namespace DebugHttp\Network\Http;

use Cake\Core\Configure;
use Cake\Http\Client\Response;
use DebugHttp\Panel\ClientCallPanel;
use DebugKit\DebugTimer;

/**
 * Class Client
 *
 * Client automatically registers all requests and responses with the panel
 *
 * @package DebugHttp\Network\Http
 */
class Client extends \Cake\Http\Client
{

    /**
     * @inheritDoc
     */
    protected function _doRequest(string $method, string $url, $data, $options): Response
    {
        $request = $this->_createRequest($method, $url, $data, $options);

        $time = microtime();
        $timerKey = 'debug_http.call.' . $url . '.' . $time;
        if (Configure::read('debug')) {
            DebugTimer::start($timerKey, $method . ' ' . $url);
        }

        $response = $this->send($request, $options);

        if (Configure::read('debug')) {
            DebugTimer::stop($timerKey);
            ClientCallPanel::addCall($request, $response, DebugTimer::elapsedTime($timerKey));
        }

        return $response;
    }
}
