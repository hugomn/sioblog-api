<?php

namespace Sioblog\ApiBundle\Controller;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sioblog\CoreBundle\Entity\Article;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleController extends BaseController {

    /**
     * @Delete("/articles/{id}")
     *
     * @ApiDoc(
     *   section = "Articles",
     *   description = "Deletes an article"
     * )
     */
    public function deleteArticlesAction($id) {
        if (!($article = $this->getArticleRepository()->find($id))) {
            $this->throwNotFound(sprintf('Error deleting entity \'%s\'.', $id));
        }
        $this->checkLoggedPermission($article->getUser()->getId());
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush($article);
        return array('success' => true);
    }

    /**
    * @Get("/articles/")
    * @ApiDoc(
    *   section = "Articles",
    *   description = "Returns all articles",
    *   output = {
    *   	"class" = "Sioblog\CoreBundle\Entity\Article",
    *    	"groups" = {"article_full"}
    *   },
    * )
    * @QueryParam(name="search", nullable=true, description="Terms to search")
    * @QueryParam(name="page", requirements="\d+", default="1", description="Page from which to start listing entities")
    * @QueryParam(name="limit", requirements="\d+", default="30", description="How many entities to return")
    */
    public function getAllArticlesAction($search, $page = 1, $limit = 30) {
        $qb = $this->getArticleRepository()->createQueryBuilder('v')->addOrderBy('v.id', 'DESC');
        if ($search) {
            $qb->andWhere($qb->expr()->like('v.title', ':search'))->setParameter(':search', "%" . $search . "%");
        }
        return $this->getPaginatedView($qb->getQuery(), $page, $limit, array(), array('article_full'));
    }

    /**
    * @Get("/articles/{id}")
    * @ApiDoc(
    *   section = "Articles",
    *   description = "Returns one single article for a given id",
    *   output = {
    *   	"class" = "Sioblog\CoreBundle\Entity\Article",
    *    	"groups" = {"article_full"}
    *   },
    *   requirements = {
    *       { "name"="id", "dataType"="integer", "requirement"="\d+", "description"="Entity id"}
    *   }
    * )
    */
    public function getArticlesAction($id) {
      if (!($article = $this->getArticleRepository()->find($id))) {
          $this->throwNotFound('Article not found.');
      }
      return $this->getView($article, array('article_full'));;
    }

    /**
     * @Patch("/articles/{id}")
     * @ApiDoc(
     *   section = "Articles",
     *   description = "Update a article",
     *   output = "Sioblog\CoreBundle\Entity\Article",
     * )
     *
     * @RequestParam(name="title", nullable=true, description="Article's name")
     * @RequestParam(name="content", nullable=true, description="Article's avatar base64 encoded string")
     */
    public function patchArticlesAction($id, $title, $content) {
        $em = $this->getDoctrine()->getManager();
        if (!($article = $this->getArticleRepository()->find($id))) {
            $this->throwNotFound(sprintf('The resource \'%s\' was not found.', $id));
        }
        $this->checkLoggedPermission($article->getUser()->getId());
        $dirty = false;
        if (isset($title)) {
            $dirty = true;
            $article->setTitle($title);
        }
        if (isset($content)) {
            $dirty = true;
            $article->setContent($content);
        }
        if ($dirty) {
            $em->persist($article);
            $em->flush($article);
        }
        return $this->getView($article, array('article_full'));
    }

    /**
     * @Post("/articles/")
     * @ApiDoc(
     *   section = "Articles",
     *   description = "Creates a article",
     *   output = "Sioblog\CoreBundle\Entity\Article",
     * )
     *
     * @RequestParam(name="title", nullable=false, description="Article's title")
     * @RequestParam(name="content", nullable=false, description="Article's content")
     */
    public function postArticlesAction($title, $content) {
        $article = new Article();
        $article->setUser($this->getUser());
        $article->setTitle($title);
        $article->setContent($content);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->getView($article, array('article_full'));
    }

}
