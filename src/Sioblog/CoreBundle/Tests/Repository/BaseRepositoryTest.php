<?php

namespace Sioblog\CoreBundle\Tests\Repository;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;

abstract class BaseRepositoryTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        $this->bootKernel();
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        // Load fixtures
        $fixtures = array(
            'Sioblog\CoreBundle\Tests\Fixtures\Entity\LoadUserData',
            'Sioblog\CoreBundle\Tests\Fixtures\Entity\LoadArticleData');
        $this->loadFixtures($fixtures);
    }

    /**
     * Log message to system log.
     * @param  string $message Message to be logged.
     * @param  string $level   Log level.
     */
    protected function log($message, $level = 'info') {
        if (is_array($message)) $message = print_r($message, true);
        $this->client->getContainer()->get("logger")->log($level, '[ApiTest] ' . $message);
    }

    /**
     * Adds support for magic finders for repositories.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return object The found repository.
     * @throws \BadMethodCallException If the method called is an invalid find* method
     *                                 or no find* method at all and therefore an invalid
     *                                 method call.
     */
    public function __call($method, $arguments) {
      if (preg_match('/^get(\w+)Repository$/', $method, $matches)) {
        return $this->em->getRepository('SioblogCoreBundle:' . $matches[1]);
      } else {
        throw new \BadMethodCallException(
            "Undefined method '$method'. Provide a valid repository name!");
      }
    }
}
