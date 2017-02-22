<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 20/02/2017
 * Time: 22:37
 */

namespace AppBundle\Tests\Service\Api;

use AppBundle\Entity\Article;
use AppBundle\Entity\Topic;
use AppBundle\Repository\ArticleRepository;
use AppBundle\Repository\TopicRepository;
use AppBundle\Service\Api\TopicService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Prophecy\Argument;
use Prophecy\Prophet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class TopicServiceTest extends TestCase
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
     * Topic Read Test
     */
    public function testItCanRead()
    {
        $topic = new Topic();
        $topic->setTitle('Test');
        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1)->willReturn($topic);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());
        $result = $service->read(1);

        self::assertEquals($topic, $result);
        self::assertEquals($topic->getId(), $result->getId());
        self::assertEquals($topic->getTitle(), $result->getTitle());
    }

    /**
     * Throw Exception when Invalid ID
     */
    public function testReadThrowsExceptionOnInvalidId()
    {
        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->read('sdfds');
    }

    /**
     * Throws Error when No Topic Found
     */
    public function testReadThrowsExceptionOnRepoNoResult()
    {
        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1000)->willReturn(null);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->read(1000);
    }

    /**
     * Topic List Test
     */
    public function testItCanList()
    {
        $topic = new Topic();
        $topic->setTitle('Test');
        $topics = [$topic];
        $topicRepository = $this->mockTopicRepository();
        $topicRepository->findAll()->willReturn($topics);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());
        $result = $service->list();

        self::assertEquals($topics, $result);
    }

    /**
     * Throws Exception when no Topic Found
     */
    public function testListThrowsExceptionOnRepoNoResult()
    {
        $topicRepository = $this->mockTopicRepository();
        $topicRepository->findAll()->willReturn(null);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $result = $service->list();
    }

    /**
     * Topic Create Test
     */
    public function testItCanCreate()
    {

        $requestData = [
            'topic' => [
                'title' => 'Test'
            ]
        ];

        $topic = new Topic();
        $topic->setTitle('Test');

        $requestStack = $this->mockRequestStack($this->query, $requestData);

        $form = $this->prophet->prophesize(Form::class);
        $form->getName()->willReturn('topic');
        $form->submit(Argument::type('array'))->willReturn($form);
        $form->getData()->willReturn($topic);

        $this->formFactory->create(Argument::type('string'), new Topic())->willReturn($form);
        $this->em->persist($topic)->shouldBeCalledTimes(1);
        $this->em->flush()->shouldBeCalledTimes(1);

        $service = new TopicService($this->em->reveal(), $requestStack, $this->formFactory->reveal());
        $result = $service->create();

        self::assertEquals($topic, $result);

        $this->prophet->checkPredictions();
    }

    /**
     * Throws Exceptions When No Topic Created
     */
    public function testCreateThrowsExceptionOnRepoNoResult()
    {
        $requestData = [
            'sport' => [
                'title' => 'World Cup'
            ]
        ];

        $requestStack = $this->mockRequestStack($this->query, $requestData);

        $form = $this->prophet->prophesize(Form::class);
        $form->getName()->willReturn('sport');
        $form->submit(Argument::type('array'))->willReturn($form);
        $form->getData()->willReturn(null);

        $this->formFactory->create(Argument::type('string'), new Topic())->willReturn($form);

        $service = new TopicService($this->em->reveal(), $requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->create();
    }

    /**
     * Topic Update Test
     */
    public function testItCanUpdate()
    {
        $topic = new Topic();
        $topic->setTitle('Test');

        $topicUpdated = new Topic();
        $topicUpdated->setTitle('Sport');

        $requestData = [
            'topic' => [
                'title' => 'Sport'
            ]
        ];

        $requestStack = $this->mockRequestStack($this->query, $requestData);

        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1)->willReturn($topic);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);
        $this->em->persist($topicUpdated)->shouldBeCalledTimes(1);
        $this->em->flush()->shouldBeCalledTimes(1);

        $form = $this->prophet->prophesize(Form::class);
        $form->getName()->willReturn('topic');
        $form->submit(Argument::type('array'))->willReturn($form);
        $form->getData()->willReturn($topicUpdated);

        $this->formFactory->create(Argument::type('string'), $topic)->willReturn($form);

        $service = new TopicService($this->em->reveal(), $requestStack, $this->formFactory->reveal());
        $result = $service->update(1);

        self::assertEquals($result, $topicUpdated);
        $this->prophet->checkPredictions();
    }

    /**
     * Throws Exception on Invalid ID
     */
    public function testUpdateThrowsExceptionOnInvalidId()
    {
        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->update('sdfds');
    }

    /**
     * THrows Exception When No Topic Found
     */
    public function testUpdateThrowsExceptionOnRepoNoResult()
    {
        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1000)->willReturn(null);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->update(1000);
    }


    /**
     * Topic Delete Test
     */
    public function testItCanDelete()
    {
        $topic = new Topic();
        $topic->setTitle('Test');

        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1)->willReturn($topic);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);
        $this->em->remove($topic)->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());
        $result = $service->delete(1);

        self::assertEquals($result, []);
    }

    /**
     * Throws Exception On Invalid ID
     */
    public function testDeleteThrowsExceptionOnInvalidId()
    {
        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->delete('sdfds');
    }

    /**
     * Throws Exception When Topic Not Found
     */
    public function testDeleteThrowsExceptionOnRepoNoResult()
    {
        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1000)->willReturn(null);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->delete(1000);
    }

    /**
     * Get Topic Articles
     */
    public function testItCanGetTopicArticles()
    {

        $topic = new Topic();
        $topic->setTitle('Sport');

        $findBy = ['topic' => $topic];

        $article = new Article();
        $article
            ->setTitle('World Cup')
            ->setText('Text')
            ->setAuthor('John Dow')
            ->setTopic($topic)
        ;

        $articles = [$article, $article];

        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1)->willReturn($topic);

        $articleRepository = $this->mockArticleRepository();
        $articleRepository->findBy($findBy)->willReturn($articles);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);
        $this->em->getRepository('AppBundle:Article')->willReturn($articleRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());
        $result = $service->articles(1);

        self::assertEquals($articles, $result);
    }

    /**
     * Throws Exception On Invalid ID
     */
    public function testTopicArticlesThrowsExceptionOnInvalidId()
    {
        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->articles('sdfds');
    }

    /**
     * Throws Exception When No Topic Found
     */
    public function testTopicArticlesThrowsExceptionOnTopicNoResult()
    {
        $topic = new Topic();
        $topic->setTitle('Sport');

        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1000)->willReturn(null);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->articles(1000);
    }

    /**
     * Throws Exception When No Articles Found
     */
    public function testTopicArticlesThrowsExceptionOnRepoNoResult()
    {

        $topic = new Topic();
        $topic->setTitle('Sport');

        $findBy = ['topic' => $topic];

        $topicRepository = $this->mockTopicRepository();
        $topicRepository->find(1000)->willReturn($topic);

        $articleRepository = $this->mockArticleRepository();
        $articleRepository->findBy($findBy)->willReturn(null);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);
        $this->em->getRepository('AppBundle:Article')->willReturn($articleRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->articles(1000);
    }


    /**
     * Mock Topic Repository
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockTopicRepository()
    {
        $topicRepository = $this->prophet->prophesize(TopicRepository::class);
        $topicRepository->willExtend(EntityRepository::class);

        return $topicRepository;
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
