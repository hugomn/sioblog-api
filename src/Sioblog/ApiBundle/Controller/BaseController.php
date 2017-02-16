<?php

namespace Sioblog\ApiBundle\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends FOSRestController {

    /**
     * Indicates wheter a variable is true or false
     * @param  Mixed $boolean Variable to check
     * @return Boolean
     */
    public function boolean($boolean) {
        return filter_var($boolean, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Check if user is logged
     * @return boolean Boolean indicating whether user is logged or not.
     */
    public function isLogged() {
        return $this->isGranted('IS_AUTHENTICATED_FULLY');
    }

    /**
     * Check logged user or admin
     * @param  int $id User id
     * @throws AccessDeniedException In case user is not authorized
     */
    public function checkLoggedPermission($id) {
        if ($this->getUser()->getId() != $id
            && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Unauthorized access.');
        }
    }

    /**
     * Grant that logged user is admin.
     * @throws AccessDeniedException In case user is not authorized
     */
    public function denyUnlessAdmin() {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Unauthorized access.');
        }
    }

    /**
     * Returns a rest view with pagination headers.
     * @param  $query Query to bepaginated
     * @param  $page Page number
     * @param  $limit Max of entries per page
     * @param  $groups Serialization groups to use
     * @return
     */
    protected function getPaginatedView($query, $page = 1, $limit = 10, $params = array(), $groups = array()) {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $limit, array('wrap-queries' => true));
        $paginationData = $pagination->getPaginationData();
        $items = $pagination->getItems();
        $view = $this->view($items, 200);
        $route = $this->container->get('request')->get('_route');
        $links = array();
        $scheme = $this->get('request_stack')->getCurrentRequest()->getScheme();
        if (array_key_exists('next', $paginationData)) {
            $nextUrl = $this->generateUrl($route, array_merge($params, array('page' => $paginationData['next'], 'limit' => $paginationData['numItemsPerPage'])), UrlGeneratorInterface::ABSOLUTE_URL);
            if ($this->container->get('kernel')->getEnvironment() == 'prod') {
                $nextUrl = str_replace('http:', 'https:', $nextUrl);
            }
            array_push($links, '<' . $nextUrl . '>; rel="next"');
        }
        if (array_key_exists('last', $paginationData)) {
            $lastUrl = $this->generateUrl($route, array_merge($params, array('page' => $paginationData['last'], 'limit' => $paginationData['numItemsPerPage'])), UrlGeneratorInterface::ABSOLUTE_URL);
            if ($this->container->get('kernel')->getEnvironment() == 'prod') {
                $lastUrl = str_replace('http:', 'https:', $lastUrl);
            }
            array_push($links, '<' . $lastUrl . '>; rel="last"');
        }
        $view->setHeaders(array(
            'Link' => join(',', $links),
            'X-Total-Count' => $paginationData['totalCount']
        ));
        $context = new Context();
        $context->setGroups($groups);
        $context->setSerializeNull(true);
        $view->setContext($context);
        return $view;
    }

        /**
     * Returns a rest view
     * @param  $result Objects to be serialized
     * @param  array  $groups Serialization groups
     */
    protected function getView($result, $groups = array()) {
        $view = $this->view($result, 200);
        $context = new Context();
        $context->setGroups($groups);
        $context->setSerializeNull(true);
        $view->setContext($context);
        return $view;
    }

    /**
     * Generates Api system log.
     * @param string $message Mensagem a ser incluida no log.
     * @param string $level Level do log. Default: error.
     */
    protected function log($message, $level = "error") {
        if (is_array($message)) $message = print_r($message, true);
        $this->get('logger')->log($level, '[SioblogApi] ' . $message);
    }

    /**
     * Safe access array parameter.
     * @param  Array $ar Array to be checked
     * @return Value if found and null if not
     */
    protected function safe_array_access($ar) {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $aritterator = $ar;
        for ($i = 1; $i < $numargs; $i++) {
            if (isset($aritterator[$arg_list[$i]]) || array_key_exists($arg_list[$i], $aritterator)) {
                $aritterator = $aritterator[$arg_list[$i]];
            } else {
                return null;
            }
        }
        return($aritterator);
    }

    /**
     * Throws a NotFoundHttpException
     * @param  String $message Message
     * @throws NotFoundHttpException
     */
    protected function throwNotFound($message) {
        throw new NotFoundHttpException($message);
    }

    /**
     * Throws a BadRequestHttpException
     * @param  String $message Message
     * @throws BadRequestHttpException
     */
    protected function throwBadRequest($message) {
        throw new BadRequestHttpException($message);
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
          return $this->getDoctrine()->getRepository('SioblogCoreBundle:' . $matches[1]);
        } else if (preg_match('/^get(\w+)Reference$/', $method, $matches))  {
          return $this->getDoctrine()->getManager()->getReference('SioblogCoreBundle:' . $matches[1], $arguments[0]);
        } else {
            throw new \BadMethodCallException("Undefined method '$method'. Provide a valid repository name!");
      }
    }
}
