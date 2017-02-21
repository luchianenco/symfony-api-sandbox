<?php

namespace AppBundle\Controller\Api;

use AppBundle\Service\Api\StatusCode;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

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
            $statusCode = StatusCode::OK;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return nothing
            $topic = [];
            $statusCode = StatusCode::NOT_FOUND;
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
            $statusCode = StatusCode::OK;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return nothing
            $topic = [];
            $statusCode = StatusCode::NO_CONTENT;
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
            $statusCode = Response::HTTP_CREATED;
        } catch (\Exception $e) {
            // if an exception thrown - return error
            $topic = [];
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
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
            $statusCode = Response::HTTP_OK;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return error
            $topic = [];
            $statusCode = Response::HTTP_NO_CONTENT;
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
            $statusCode = Response::HTTP_OK;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return error
            $topic = [];
            $statusCode = Response::HTTP_NO_CONTENT;
        }
        return $this->view($topic, $statusCode);
    }

    public function articlesAction($id)
    {
        try {
            // if no error - topic found
            $topic = $this->get('api.topic')->articles($id);
            $statusCode = Response::HTTP_OK;
        } catch (\InvalidArgumentException $e) {
            // if an exception thrown - return error
            $topic = [];
            $statusCode = Response::HTTP_NO_CONTENT;
        }
        return $this->view($topic, $statusCode);
    }
}
