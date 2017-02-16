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
 * Test ArticleController actions.
 *
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
class ArticleControllerTest extends BaseApiTest {

    public function testGetAllArticlesAction() {
        // Get articles loaded to the database
        $articles = $this->getArticles();

        // Asserts all events are returned
        $route = $this->getApiUrl('api_1_get_all_articles', array('page' => 1, 'limit' => count($articles)));
        $response = $this->requestGet($route);
        $array = $this->getArrayResponse($response);
        $this->assertCount(count($articles), $array);

        // Asserts query limit and default page
        $route = $this->getApiUrl('api_1_get_all_articles', array('limit' => 1));
        $response = $this->requestGet($route);
        $array = $this->getArrayResponse($response);
        $this->assertCount(1, $array);

        // Asserts query is returning a valid event
        $article = array_pop($array);
        $this->assertTrue(isset($article['title']));
    }

    public function testGetArticleAction() {
        // Get article loaded to the database
        $articles = $this->getArticles();
        $article = array_pop($articles);

        // Asserts a 404 is returned when an invalid id is passed
        $route = $this->getApiUrl('api_1_get_articles', array('id' => $article->getId() + 1));
        $response = $this->requestGet($route);
        $this->assertJsonResponse($response, 404);

        // Asserts a 200 is returned when a valid id is passed
        $route = $this->getApiUrl('api_1_get_articles', array('id' => $article->getId()));
        $response = $this->requestGet($route);
        $this->assertJsonResponse($response, 200);

        // Asserts returns a valid event
        $array = $this->getArrayResponse($response);
        $this->assertEquals($array['id'], $article->getId());
    }

    public function testDeleteArticlesAction() {
        // Get articles loaded to the database
        $articles = $this->getArticles();
        $article = array_pop($articles);

        // Asserts article is deleted
        $route = $this->getApiUrl('api_1_delete_articles', array('id' => $article->getId()));
        $response = $this->requestGet($route);
        $array = $this->getArrayResponse($response);
        $this->assertJsonResponse($response, 200);
    }
}
