<?php

namespace Sioblog\ApiBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * Serialization listener for Article model.
 */
class ArticleSerializationListener implements EventSubscriberInterface {

    /**
     * @inheritdoc
     */
    static public function getSubscribedEvents() {
        return array(
            // array('event' => 'serializer.pre_serialize', 'class' => 'Sioblog\CoreBundle\Entity\Article', 'method' => 'onPreSerialize'),
        );
    }

    public function onPreSerialize(ObjectEvent $serializeEvent) {
    }
}
