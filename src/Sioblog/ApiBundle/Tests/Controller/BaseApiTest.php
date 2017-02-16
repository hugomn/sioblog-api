<?php

/*
 * This file is part of the Sioblog package.
 *
 * (c) Hugo Magalhães <hugomn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sioblog\ApiBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;
use Sioblog\ApiBundle\Tests\Fixtures\Entity\LoadEventData;
use Sioblog\ApiBundle\Tests\Fixtures\Entity\LoadAuthData;

/**
 * Base class for all controller test classes.
 *
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
abstract class BaseApiTest extends WebTestCase {

    // Base access token
    private $accessToken;

    // Base client
    private $apiClient;

    // Base events
    private $events;

    /**
     * Setup test controller.
     */
    public function setUp() {
        $this->client = static::createClient();
        $fixtures = array(
            'Sioblog\ApiBundle\Tests\Fixtures\Entity\LoadAuthData',
            'Sioblog\ApiBundle\Tests\Fixtures\Entity\LoadArticleData');
        $this->loadFixtures($fixtures);
        $this->accessToken = LoadAuthData::$accessToken;
        $this->apiClient = LoadAuthData::$client;
        $this->articles = LoadArticleData::$articles;
    }

    /**
     * Returns api client.
     * @return Client
     */
    public function getApiClient() {
        return $this->apiClient;
    }

    /**
     * Asserts Json response is valid.
     * @param Respnse  $response        Json response.
     * @param integer $statusCode       Status code.
     * @param boolean  $checkValidJson  Wheter to check or not if json is valid.
     * @param string  $contentType      Content type.
     */
    protected function assertJsonResponse($response, $statusCode = 200, $checkValidJson =  true, $contentType = 'application/json') {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getContent());
        $this->assertTrue($response->headers->contains('Content-Type', $contentType), $response->headers);

        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            $this->assertTrue(($decode != null && $decode != false), 'is response valid json: [' . $response->getContent() . ']');
        }
    }

    /**
     * Return Api Url with all needed parameters.
     * @param string $route  Api route.
     * @param array  $params Params array.
     * @param string $format Response format.
     * @param boolean $includeToken Boolean indicating wheter to include the token or not.
     */
    protected function getApiUrl($route, $params = array(), $format = 'json', $includeToken = true) {
        if ($includeToken) {
            $params['access_token'] = $this->accessToken->getToken();
        }
        $params['_format'] = $format;
        return $this->getUrl($route, $params);
    }

    /**
     * Returns an associative array based on the response's json content
     * @param Response $response Response object.
     */
    protected function getArrayResponse($response) {
        $content = $response->getContent();
        return json_decode($content, true);
    }

    /**
     * Return created articles.
     */
    protected function getArticles() {
        return $this->articles;
    }

    /**
     * Log message to system log.
     * @param  stirng $message Message to be logged.
     * @param  string $level   Log level.
     */
    protected function log($message, $level = 'info') {
        if (is_array($message)) $message = print_r($message, true);
        $this->client->getContainer()->get("logger")->log($level, '[ApiTest] ' . $message);
    }

    /**
     * Make a GET request and return the response.
     * @param string $route Route to make the request.
     */
    protected function requestGet($route) {
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        return $this->client->getResponse();
    }

    /**
     * Make a POST request and return the response.
     * @param string $route Route to make the request.
     */
    protected function requestPost($route, $params) {
        $this->client->request('POST', $route, array_merge($params, array('ACCEPT' => 'application/json')));
        return $this->client->getResponse();
    }
}
