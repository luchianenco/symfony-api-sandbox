<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 19/02/2017
 * Time: 17:43
 */

namespace AppBundle\Service\Api;

use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;

class ArticleService implements ApiCrudInterface
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
     * Create Article
     * @return Article
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function create()
    {
        $request = $this->requestStack->getMasterRequest();

        $form = $this->formFactory->create(ArticleType::class, new Article());
        $article= $form->submit($request->request->get($form->getName()))->getData();

        if (!$article instanceof Article) {
            throw new \InvalidArgumentException('Invalid data submitted');
        }

        $this->em->persist($article);
        $this->em->flush();

        return $article;
    }

    /**
     * Get an Article by ID
     * @param $id
     * @return Article
     * @throws \InvalidArgumentException
     */
    public function read($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('Invalid type, expected integer but got "' . gettype($id) . '"');
        }

        $result = $this->em->getRepository('AppBundle:Article')->find($id);

        if (!$result instanceof Article) {
            throw new \InvalidArgumentException('Article not found');
        }

        return $result;
    }

    /**
     * Update Article by ID
     * @param $id
     * @return Article
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \InvalidArgumentException
     */
    public function update($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('Invalid type, expected integer but got "' . gettype($id) . '"');
        }

        $result = $this->em->getRepository('AppBundle:Article')->find($id);

        if (!$result instanceof Article) {
            throw new \InvalidArgumentException('Article not found');
        }

        $request = $this->requestStack->getMasterRequest();
        $form = $this->formFactory->create(ArticleType::class, $result)->handleRequest($request);
        $article = $form->submit($request->request->get($form->getName()))->getData();

        $this->em->persist($article);
        $this->em->flush();

        return $article;
    }

    /**
     * Delete Article by ID
     * @param $id
     * @return array
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \InvalidArgumentException
     */
    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('Invalid type, expected integer but got "' . gettype($id) . '"');
        }

        $result = $this->em->getRepository('AppBundle:Article')->find($id);

        if (!$result instanceof Article) {
            throw new \InvalidArgumentException('Article not found');
        }

        $this->em->remove($result);
        $this->em->flush();

        return [];
    }
}
