<?php

namespace Sioblog\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\FileParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sioblog\CoreBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends BaseController
{

    /**
     * @Get("/users")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @ApiDoc(
     *   section = "Users",
     *   description = "Returns all users",
     *   output = {
     *   	"class" = "Sioblog\CoreBundle\Entity\User",
     *    	"groups" = {"user_full"}
     *   },
     * )
     * @QueryParam(name="search", nullable=true, description="Terms to search")
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page from which to start listing entities")
     * @QueryParam(name="limit", requirements="\d+", default="30", description="How many entities to return")
     */
    public function getAllUsersAction($search, $page = 1, $limit = 30) {
        $qb = $this->getUserRepository()->createQueryBuilder('u')->orderBy('u.firstname, u.lastname', 'ASC');
        if ($search) {
            $qb->andWhere(
                    $qb->expr()->orX(
                            $qb->expr()->like($qb->expr()->concat('u.firstname', $qb->expr()->concat($qb->expr()->literal(' '), 'u.lastname')), ':search'),
                            $qb->expr()->like($qb->expr()->upper('u.username'), ':search'),
                            $qb->expr()->like($qb->expr()->upper('u.email'), ':search')
                    )
            )->setParameter(':search', "%" . $search . "%");
        }
        return $this->getPaginatedView($qb->getQuery(), $page, $limit, array(), array('user_full'));
    }

    /**
     * @Get("/users/me")
     * @ApiDoc(
     *   section = "Users",
     *   description = "Returns the logged user",
     *   output = "Sioblog\CoreBundle\Entity\User",
     * )
     */
    public function getUsersMeAction() {
        if (!($user = $this->getUser())) {
            $this->throwNotFound('Usuário não encontrado');
        }
        return $this->getView($user, array('user_details'));
    }

    /**
     * @Get("/users/{id}/articles")
     * @ApiDoc(
     *   section = "Users",
     *   description = "Returns the user's articles",
     *   output = {
     *   	"class" = "Sioblog\CoreBundle\Entity\Article",
     *    	"groups" = {"article_full"}
     *   },
     *   requirements = {
     *       { "name"="id", "dataType"="integer", "requirement"="\d+", "description"="User id"}
     *   }
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page from which to start listing entities")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="How many entities to return")
     */
    public function getUsersArticleAction($id, $page = 1, $limit = 10) {
        $this->checkLoggedPermission($id);
        $qb = $this->getArticleRepository()->createQueryBuilder('a');
        $qb = $this->getArticleRepository()->findAllByUser($this->getUser());
        return $this->getPaginatedView($qb->getQuery(), $page, $limit, array('id' => $id), array('article_full'));
    }

    /**
     * @Get("/users/{id}")
     * @ApiDoc(
     *   section = "Users",
     *   description = "Returns one single user for a given id",
     *   output = "Sioblog\CoreBundle\Entity\User",
     *   requirements = {
     *       { "name"="id", "dataType"="integer", "requirement"="\d+", "description"="User id"}
     *   }
     * )
     */
    public function getUsersAction($id) {
        if (!($user = $this->getUserRepository()->find($id))) {
            $this->throwNotFound('Usuário não encontrado');
        }
        $this->checkLoggedPermission($id);
        return $this->getView($user, array('user_full'));
    }

    /**
    * @ApiDoc(
    *   section = "Users",
    *   description = "Updates an user",
    *   output = {
    *   	"class" = "Sioblog\CoreBundle\Entity\User",
    *    	"groups" = {"user_full"}
    *   },
    *   statusCodes = {
    *     200 = "Returned when successful",
    *     404 = "Returned when an error ocurred"
    *   }
    * )
    * @RequestParam(name="email", strict=true, nullable=true, description="E-mail")
    * @RequestParam(name="locale", strict=true, nullable=true, description="Locale")
    * @RequestParam(name="password", strict=true, nullable=true, description="Password")
    * @RequestParam(name="username", strict=true, nullable=true, description="Username")
    * @RequestParam(name="firstname", strict=true, nullable=true, description="First name")
    * @RequestParam(name="lastname", strict=true, nullable=true, description="Last name")
    */
    public function patchUsersAction($id, $email, $locale, $password, $username, $firstname, $lastname) {
        $em = $this->getDoctrine()->getManager();
        if (!$user = $this->getUserRepository()->find($id)) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }
        $this->checkLoggedPermission($id);
        $dirty = false;
        if (isset($email)) {
            $dirty = true;
            $user->setEmail($email);
        }
        if (isset($locale)) {
            $dirty = true;
            $user->setLocale($locale);
        }
        if (isset($password)) {
            $dirty = true;
            $user->setPlainPassword($password);
            $this->getUserManager()->updatePassword($user);
        }
        if (isset($username)) {
            $dirty = true;
            if ($username != $user->getUsername() && !$this->getUrlManager()->checkUniqueSlug($username)) {
                throw new NotFoundHttpException(sprintf('Username \'%s\' not available.', $username));
            }
            $user->setUsername($username);
        }
        if (isset($firstname)) {
            $dirty = true;
            $user->setFirstname($firstname);
        }
        if (isset($lastname)) {
            $dirty = true;
            $user->setLastname($lastname);
        }
        if ($dirty) {
            $em->persist($user);
            $em->flush($user);
        }
        return $this->getView($user, array('user_full'));
    }

    /**
     * @Post("/users")
     * @ApiDoc(
     *   resource = true,
     *   section = "Users",
     *   description = "Creates a user",
     *   output = "Sioblog\CoreBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when an error occured"
     *   }
     * )
     *
     * @RequestParam(name="email", nullable=false, description="User's email")
     * @RequestParam(name="password", nullable=false, description="User's password")
     */
    public function postUsersAction($email, $password) {
        $user = $this->getUserManager()->createUser();
        if ($this->getUserRepository()->countDuplicates($email) > 0) {
            $this->throwBadRequest('User already exists.');
        }
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $this->getUserManager()->processUser($user);
        $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_USER'));
        return $this->getView($user, array('user_full'));
    }
}
