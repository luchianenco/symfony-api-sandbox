<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 20/02/2017
 * Time: 22:37
 */

namespace AppBundle\Tests\Service\Api;

use AppBundle\Entity\Topic;
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

        $this->assertEquals($topic, $result);
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

        $this->assertEquals($topics, $result);
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

        $this->assertEquals($topic, $result);

        $this->prophet->checkPredictions();
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

        $topicRepository = $this->mockTopicRepository($topic);
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

        $this->assertEquals($result, $topicUpdated);
        $this->prophet->checkPredictions();
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

        $this->assertEquals($result, []);
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
