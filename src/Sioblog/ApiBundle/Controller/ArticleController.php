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
     *   description = "Deletes a article"
     * )
     */
    public function deleteArticleAction($id) {
        if (!($article = $this->getArticleRepository()->find($id))) {
            $this->throwNotFound(sprintf('Error deleting entity \'%s\'.', $id));
        }
        $this->checkLoggedPermission($article->getUser()->getId());
        if (count($article->getEvents()) > 0) {
            $this->throwBadRequest('O local está sendo utilizado por algum evento.');
        }
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
        $qb = $this->getArticleRepository()->createQueryBuilder('v')->addOrderBy('v.name', 'ASC');
        if ($search) {
            $qb->andWhere($qb->expr()->like('v.name', ':search'))->setParameter(':search', "%" . $search . "%");
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
    public function getArticleAction($id) {
      if (!($article = $this->getArticleRepository()->find($id))) {
          throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
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
     * @RequestParam(name="name", nullable=true, description="Article's name")
     * @RequestParam(name="avatar", nullable=true, description="Article's avatar base64 encoded string")
     * @RequestParam(name="articletype", nullable=true, description="ArticleType id")
     * @RequestParam(name="addressline1", nullable=true, description="Article's address line 1")
     * @RequestParam(name="addressline2", nullable=true, description="Article's address line 2")
     * @RequestParam(name="city", nullable=true, description="Article's city")
     * @RequestParam(name="zipcode", nullable=true, description="Article's zipcode")
     */
    public function patchArticlesAction($id, $name, $avatar, $articletype, $addressline1, $addressline2, $city, $zipcode) {
        $em = $this->getDoctrine()->getManager();
        if (!($article = $this->getArticleRepository()->find($id))) {
            $this->throwNotFound(sprintf('The resource \'%s\' was not found.', $id));
        }
        $this->checkLoggedPermission($article->getCreator()->getId());
        $dirty = false;
        if (isset($avatar)) {
            $dirty = true;
            $folder = $this->getPathManager()->getAvatarFolder('article');
            $filename = $this->getUploadManager()->uploadBase64($avatar, $folder);
            $article->setAvatar($filename);
        }
        if (isset($name)) {
            $dirty = true;
            $article->setName($name);
        }
        if (isset($articletype)) {
            $dirty = true;
            $article->setArticleType($this->getArticleTypeReference($articletype));
        }
        if (isset($addressline1)) {
            $dirty = true;
            $article->setAddressline1($addressline1);
        }
        if (isset($addressline2)) {
            $dirty = true;
            $article->setAddressline2($addressline2);
        }
        if (isset($city)) {
            $dirty = true;
            $article->setCity($this->getCityReference($city));
        }
        if (isset($zipcode)) {
            $dirty = true;
            $article->setZipcode($zipcode);
        }
        if ($dirty) {
            $latLng = $this->getGeolocationManager()->getLatLng($article->getFullAddress());
            $article->setLatitude($latLng['lat']);
            $article->setLongitude($latLng['lng']);
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
     * @RequestParam(name="name", nullable=false, description="Article's name")
     * @RequestParam(name="avatar", nullable=true, description="Article's avatar base64 encoded string")
     * @RequestParam(name="articletype", strict=true, nullable=true, description="ArticleType id")
     * @RequestParam(name="addressline1", description="Article's address line 1")
     * @RequestParam(name="addressline2", nullable=true, description="Article's address line 2")
     * @RequestParam(name="city", description="Article's city")
     * @RequestParam(name="zipcode", nullable=true, description="Article's zipcode")
     */
    public function postArticlesAction($name, $avatar, $articletype, $addressline1, $addressline2, $city, $zipcode) {
        $article = new Article();
        $article->setCreator($this->getUser());
        if (!empty($avatar)) {
            $folder = $this->getPathManager()->getAvatarFolder('article');
            $filename = $this->getUploadManager()->uploadBase64($avatar, $folder);
            $article->setAvatar($filename);
        }
        $article->setName($name);
        $article->setAddressline1($addressline1);
        if (!empty($addressline2)) {
            $article->setAddressline2($addressline2);
        }
        $article->setCity($this->getCityReference($city));
        if (!empty($zipcode)) {
            $article->setZipcode($zipcode);
        }
        if (!empty($articletype)) {
            $article->setArticleType($this->getArticleTypeReference($articletype));
        }
        $article->setSlug($this->getUrlManager()->getUniqueSlug($name));
        $latLng = $this->getGeolocationManager()->getLatLng($article->getFullAddress());
        $article->setLatitude($latLng['lat']);
        $article->setLongitude($latLng['lng']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->getView($article, array('article_full'));
    }

}
