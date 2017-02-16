<?php

/*
 * This file is part of the Sioblog/CoreBundle
 *
 * (c) Hugo Magalhães <hugomn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sioblog\CoreBundle\Tests\Repository;

/**
 * Test ArticleRepository actions.
 *
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
class ArticleRepositoryTest extends BaseRepositoryTest {

    public function testGetCount() {
        $article = $this->getArticleRepository()->findAll();
        $this->assertEquals(count($articles), 1);
    }

}
