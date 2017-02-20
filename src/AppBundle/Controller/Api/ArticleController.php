<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;

class ArticleController extends FOSRestController
{
    /**
     * Find Article by Id
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function readAction($id)
    {
        try {
            // if no error - topic found
            $article = $this->get('api.article')->read($id);
            $statusCode = 200;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return nothing
            $article = [];
            $statusCode = 404;
        }

        return $this->view($article, $statusCode);
    }

    /**
     * List all Articles of a Topic
     * @return \FOS\RestBundle\View\View
     */
    public function listAction()
    {

    }

    /**
     * Create Article
     * @return \FOS\RestBundle\View\View
     */
    public function createAction()
    {
        try {
            // if no error - topic found
            $article = $this->get('api.article')->create();
            $statusCode = 201;
        } catch (\Exception $e) {
            // if an exception thrown - return error
            $article = [];
            $statusCode = 500;
        }
        return $this->view($article, $statusCode);
    }

    /**
     * Update Article
     * @param integer $id
     * @return \FOS\RestBundle\View\View
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateAction($id)
    {
        try {
            // if no error - topic found
            $article = $this->get('api.article')->update($id);
            $statusCode = 200;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return error
            $article = [];
            $statusCode = 204;
        }
        return $this->view($article, $statusCode);
    }

    /**
     * Delete Article
     * @param $id
     * @return \FOS\RestBundle\View\View
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction($id)
    {
        try {
            // if no error - topic found
            $article = $this->get('api.article')->delete($id);
            $statusCode = 200;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return error
            $article = [];
            $statusCode = 204;
        }
        return $this->view($article, $statusCode);
    }
}
