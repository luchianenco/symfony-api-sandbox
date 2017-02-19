<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;

class TopicController extends FOSRestController
{
    /**
     * Find Topic by Id
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function readAction($id)
    {
        try {
            // if no error - topic found
            $topic = $this->get('api.topic')->read($id);
            $statusCode = 200;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return nothing
            $topic = [];
            $statusCode = 404;
        }

        return $this->view($topic, $statusCode);
    }

    /**
     * List all Topics
     * @return \FOS\RestBundle\View\View
     */
    public function listAction()
    {
        try {
            // if no error - topic found
            $topic = $this->get('api.topic')->list();
            $statusCode = 200;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return nothing
            $topic = [];
            $statusCode = 204;
        }

        return $this->view($topic, $statusCode);
    }

    /**
     * Create Topic
     * @return \FOS\RestBundle\View\View
     */
    public function createAction()
    {
        try {
            // if no error - topic found
            $topic = $this->get('api.topic')->create();
            $statusCode = 201;
        } catch (\Exception $e) {
            // if an exception thrown - return error
            $topic = [];
            $statusCode = 500;
        }
        return $this->view($topic, $statusCode);
    }

    /**
     * @param integer $id
     * @return \FOS\RestBundle\View\View
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateAction($id)
    {
        try {
            // if no error - topic found
            $topic = $this->get('api.topic')->update($id);
            $statusCode = 200;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return error
            $topic = [];
            $statusCode = 204;
        }
        return $this->view($topic, $statusCode);
    }

    /**
     * @param $id
     * @return \FOS\RestBundle\View\View
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction($id)
    {
        try {
            // if no error - topic found
            $topic = $this->get('api.topic')->delete($id);
            $statusCode = 200;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return error
            $topic = [];
            $statusCode = 204;
        }
        return $this->view($topic, $statusCode);
    }
}
