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
 * Test UserController actions.
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
class UserControllerTest extends BaseApiTest {

    /**
     * Test regular token.
     */
    public function testPostUsersAction() {
        $route = $this->getApiUrl('api_1_post_users');
        $response = $this->requestPost($route, array(
            'name' => 'John Doe',
            'email' => 'johndoe@sioblog.com',
            'password' => 'foobar'
        ));
        $array = $this->getArrayResponse($response);

        // Assert a valid json is returned
        $this->assertJsonResponse($response, 200);

        // Assert firstname is returned
        $this->assertEquals($array['firstname'], 'John');

        // Assert an int id is returned
        $this->assertInternalType('int', $array['id']);

        // Create new user to check duplicated e-mail
        $response = $this->requestPost($route, array(
            'name' => 'Doe John',
            'email' => 'johndoe@sioblog.com',
            'password' => 'foobar'
        ));
        $this->assertJsonResponse($response, 400);
    }
}
