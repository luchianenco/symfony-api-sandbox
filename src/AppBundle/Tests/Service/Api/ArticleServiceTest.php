<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 22/02/2017
 * Time: 13:46
 */

namespace AppBundle\Tests\Service\Api;

use AppBundle\Entity\Article;
use AppBundle\Entity\Topic;
use AppBundle\Repository\ArticleRepository;
use AppBundle\Repository\TopicRepository;
use AppBundle\Service\Api\ArticleService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class ArticleServiceTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $em;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $requestStack;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $formFactory;

    /**
     * @var array
     */
    private $query;

    /**
     * @var Prophet
     */
    private $prophet;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->query = [];
        $this->prophet = new Prophet();

        $this->requestStack = $this->mockRequestStack($this->query, []);
        $this->em = $this->prophet->prophesize(EntityManager::class);
        $this->formFactory = $this->prophet->prophesize(FormFactory::class);
    }

    /**
     * Article Read Test
     */
    public function testItCanRead()
    {
        $topic = new Topic();
        $topic->setTitle('Test');

        $article = new Article();
        $article
            ->setTitle('Test')
            ->setAuthor('John Doe')
            ->setText('Text')
            ->setTopic($topic)
        ;
        $articleRepository = $this->mockArticleRepository();
        $articleRepository->find(1)->willReturn($article);

        $this->em->getRepository('AppBundle:Article')->willReturn($articleRepository);

        $service = new ArticleService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());
        $result = $service->read(1);

        self::assertEquals($article, $result);
        self::assertEquals($article->getId(), $result->getId());
        self::assertEquals($article->getTitle(), $result->getTitle());
        self::assertEquals($article->getAuthor(), $result->getAuthor());
        self::assertEquals($article->getText(), $result->getText());
        self::assertEquals($article->getTopic(), $result->getTopic());
    }

    /**
     * Throws Exception On Invalid ID
     */
    public function testReadThrowsExceptionOnInvalidId()
    {
        $service = new ArticleService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->read('sdfds');
    }

    /**
     * Throws Exception When No Articles Found
     */
    public function testReadThrowsExceptionOnRepoNoResult()
    {
        $articleRepository = $this->mockArticleRepository();
        $articleRepository->find(1000)->willReturn(null);

        $this->em->getRepository('AppBundle:Article')->willReturn($articleRepository);

        $service = new ArticleService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->read(1000);
    }

    /**
     * Article Create Test
     */
    public function testItCanCreate()
    {
        $requestData = [
            'article' => [
                'title' => 'World Cup',
                'author' => 'John Dow',
                'text' => 'Text',
                'topic' => 1
            ]
        ];

        $topic = new Topic();
        $topic->setTitle('Sport');

        $article = new Article();
        $article
            ->setTitle('World Cup')
            ->setText('Text')
            ->setAuthor('John Dow')
            ->setTopic($topic)
        ;

        $requestStack = $this->mockRequestStack($this->query, $requestData);

        $form = $this->prophet->prophesize(Form::class);
        $form->getName()->willReturn('article');
        $form->submit(Argument::type('array'))->willReturn($form);
        $form->getData()->willReturn($article);

        $this->formFactory->create(Argument::type('string'), new Article())->willReturn($form);
        $this->em->persist($article)->shouldBeCalledTimes(1);
        $this->em->flush()->shouldBeCalledTimes(1);

        $service = new ArticleService($this->em->reveal(), $requestStack, $this->formFactory->reveal());
        $result = $service->create();

        self::assertEquals($article, $result);
        $this->prophet->checkPredictions();
    }

    /**
     * Throws Exception When No Articles Created
     */
    public function testCreateThrowsExceptionOnRepoNoResult()
    {
        $requestData = [
            'sport' => [
                'title' => 'World Cup',
                'author' => 'John Dow',
                'text' => 'Text',
                'topic' => 1
            ]
        ];

        $requestStack = $this->mockRequestStack($this->query, $requestData);

        $form = $this->prophet->prophesize(Form::class);
        $form->getName()->willReturn('sport');
        $form->submit(Argument::type('array'))->willReturn($form);
        $form->getData()->willReturn(null);

        $this->formFactory->create(Argument::type('string'), new Article())->willReturn($form);

        $service = new ArticleService($this->em->reveal(), $requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->create();
    }

    /**
     * Article Update Test
     */
    public function testItCanUpdate()
    {
        $topic = new Topic();
        $topic->setTitle('Sport');

        $requestData = [
            'article' => [
                'title' => 'World Cup',
                'author' => 'John Doe',
                'text' => 'Lorem ipsum',
                'topic' => 1
            ]
        ];


        $article = new Article();
        $article
            ->setTitle('World Cup')
            ->setText('Text')
            ->setAuthor('John Dow')
            ->setTopic($topic)
        ;

        $articleUpdated = new Article();
        $articleUpdated
            ->setTitle('World Cup')
            ->setText('Lorem ipsum')
            ->setAuthor('John Doe')
            ->setTopic($topic)
        ;

        $requestStack = $this->mockRequestStack($this->query, $requestData);

        $articleRepository = $this->mockArticleRepository();
        $articleRepository->find(1)->willReturn($article);

        $this->em->getRepository('AppBundle:Article')->willReturn($articleRepository);
        $this->em->persist($articleUpdated)->shouldBeCalledTimes(1);
        $this->em->flush()->shouldBeCalledTimes(1);

        $form = $this->prophet->prophesize(Form::class);
        $form->getName()->willReturn('article');
        $form->submit(Argument::type('array'))->willReturn($form);
        $form->getData()->willReturn($articleUpdated);

        $this->formFactory->create(Argument::type('string'), $article)->willReturn($form);

        $service = new ArticleService($this->em->reveal(), $requestStack, $this->formFactory->reveal());
        $result = $service->update(1);

        self::assertEquals($result, $articleUpdated);
        $this->prophet->checkPredictions();
    }

    /**
     * Throws Exception When On Invalid ID
     */
    public function testUpdateThrowsExceptionOnInvalidId()
    {
        $service = new ArticleService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->update('sdfds');
    }

    /**
     * Throws Exception When No Articles Found
     */
    public function testUpdateThrowsExceptionOnRepoNoResult()
    {
        $articleRepository = $this->mockArticleRepository();
        $articleRepository->find(1000)->willReturn(null);

        $this->em->getRepository('AppBundle:Article')->willReturn($articleRepository);

        $service = new ArticleService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->update(1000);
    }

    /**
     * Article Delete Test
     */
    public function testItCanDelete()
    {
        $topic = new Topic();
        $topic->setTitle('Sport');

        $article = new Article();
        $article
            ->setTitle('World Cup')
            ->setText('Text')
            ->setAuthor('John Dow')
            ->setTopic($topic)
        ;

        $articleRepository = $this->mockArticleRepository();
        $articleRepository->find(1)->willReturn($article);

        $this->em->getRepository('AppBundle:Article')->willReturn($articleRepository);
        $this->em->remove($article)->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        $service = new ArticleService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());
        $result = $service->delete(1);

        self::assertEquals($result, []);
    }

    /**
     * Throws Exception On Invalid ID
     */
    public function testDeleteThrowsExceptionOnInvalidId()
    {
        $service = new ArticleService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->delete('sdfds');
    }

    /**
     * Throws Exception When No Articles Found
     */
    public function testDeleteThrowsExceptionOnRepoNoResult()
    {
        $articleRepository = $this->mockArticleRepository();
        $articleRepository->find(1000)->willReturn(null);

        $this->em->getRepository('AppBundle:Article')->willReturn($articleRepository);

        $service = new ArticleService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->delete(1000);
    }

    /**
     * Mock Article Repository
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockArticleRepository()
    {
        $repo = $this->prophet->prophesize(ArticleRepository::class);
        $repo->willExtend(EntityRepository::class);

        return $repo;
    }

    /**
     * Mock RequestStack
     * @param $query
     * @param $request
     * @return RequestStack
     */
    private function mockRequestStack($query, $request)
    {
        $requestStack = new RequestStack();
        $session = $this->prophet->prophesize(Session::class);
        $req = new Request($query, $request);
        $req->setSession($session->reveal());
        $requestStack->push($req);

        return $requestStack;
    }
}
