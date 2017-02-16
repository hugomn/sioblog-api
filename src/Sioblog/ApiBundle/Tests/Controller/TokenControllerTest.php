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

/**
 * Test TokenController actions.
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
class TokenControllerTest extends BaseApiTest {

    /**
     * Test regular token.
     */
    public function testTokenAction() {
        $client = $this->getApiClient();
        $route = $this->getApiUrl('fos_oauth_server_token', array(
            'client_id' => $client->getId() . "_" . $client->getRandomId(),
            'client_secret' => $client->getSecret(),
            'grant_type' => 'password',
            'username' => 'hugomn',
            'password' => 'abc123'
        ), 'json', false);

        // Asserts a valid response is return
        $response = $this->requestGet($route);
        $this->assertJsonResponse($response, 200);

        // Asserts a valid token is return
        $array = $this->getArrayResponse($response);
        $this->assertTrue(array_key_exists('access_token', $array) && strlen($array['access_token']));
    }
}
