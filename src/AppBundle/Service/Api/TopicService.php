<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 19/02/2017
 * Time: 11:00
 */

namespace AppBundle\Service\Api;

use AppBundle\Entity\Topic;
use AppBundle\Form\Type\TopicType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;

class TopicService implements ApiCrudInterface, ApiExtraInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * TopicService constructor
     * @param EntityManager $em
     * @param RequestStack $requestStack
     * @param FormFactory $formFactory
     */
    public function __construct(EntityManager $em, RequestStack $requestStack, FormFactory $formFactory)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
    }

    /**
     * Creates new Topic Entity
     * @return Topic
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function create()
    {
        $request = $this->requestStack->getMasterRequest();

        $form = $this->formFactory->create(TopicType::class, new Topic())->handleRequest($request);
        $topic = $form->submit($request->request->get($form->getName()))->getData();

        if (!$topic instanceof Topic) {
            throw new \InvalidArgumentException('Invalid data submitted');
        }

        $this->em->persist($topic);
        $this->em->flush();

        return $topic;
    }

    /**
     * Get Topic by ID
     * @param integer $id
     * @return Topic
     * @throws \InvalidArgumentException
     */
    public function read($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('Invalid type, expected integer but got "' . gettype($id) . '"');
        }

        $result = $this->em->getRepository('AppBundle:Topic')->find($id);

        if (!$result instanceof Topic) {
            throw new \InvalidArgumentException('Topic not found');
        }

        return $result;
    }

    /**
     * Get All Topics
     * @return Topic[]
     * @throws \InvalidArgumentException
     */
    public function list()
    {
        $result = $this->em->getRepository('AppBundle:Topic')->findAll();

        if (empty($result)) {
            throw new \InvalidArgumentException('Topics not found');
        }

        return $result;
    }

    /**
     * Update Topic By ID
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function update($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('Invalid type, expected integer but got "' . gettype($id) . '"');
        }

        $result = $this->em->getRepository('AppBundle:Topic')->find($id);

        if (!$result instanceof Topic) {
            throw new \InvalidArgumentException('Topic not found');
        }

        $request = $this->requestStack->getMasterRequest();

        $form = $this->formFactory->create(TopicType::class, $result);
        $topic = $form->submit($request->request->get($form->getName()))->getData();

        $this->em->persist($topic);
        $this->em->flush();

        return $topic;
    }

    /**
     * Delete Topic By ID
     * @param $id
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('Invalid type, expected integer but got "' . gettype($id) . '"');
        }

        $result = $this->em->getRepository('AppBundle:Topic')->find($id);

        if (!$result instanceof Topic) {
            throw new \InvalidArgumentException('Topic not found');
        }

        $this->em->remove($result);
        $this->em->flush();

        return [];
    }
}
