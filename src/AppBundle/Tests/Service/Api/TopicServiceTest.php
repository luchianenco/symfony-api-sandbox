<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 20/02/2017
 * Time: 22:37
 */

namespace AppBundle\Tests\Service\Api;

use AppBundle\Entity\Topic;
use AppBundle\Service\Api\TopicService;
use Prophecy\Argument;
use Prophecy\Prophet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class TopicServiceTest extends TestCase
{
    private $em;

    private $requestStack;

    private $formFactory;

    /**
     * @var Prophet
     */
    private $prophet;

    public function setUp()
    {
        $this->prophet = new Prophet();

        $this->requestStack = new RequestStack();
        $session = $this->prophet->prophesize('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $request = new Request();
        $request->setSession($session->reveal());

        $this->requestStack->push($request);

        $this->em = $this->prophet->prophesize('Doctrine\ORM\EntityManager');
        $this->formFactory = $this->prophet->prophesize('Symfony\Component\Form\FormFactory');
    }

    public function testItCanRead()
    {
        $topic = new Topic();
        $topic->setTitle('Test');
        $topicRepository = $this->prophet->prophesize('Doctrine\ORM\EntityRepository\TopicRepository');
        $topicRepository->willExtend('Doctrine\ORM\EntityRepository');
        $topicRepository->find('1')->willReturn($topic);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());
        $result = $service->read(1);

        $this->assertEquals($topic, $result);
    }

    public function testItCanList()
    {
        $topic = new Topic();
        $topic->setTitle('Test');
        $topics = [$topic];
        $topicRepository = $this->prophet->prophesize('Doctrine\ORM\EntityRepository\TopicRepository');
        $topicRepository->willExtend('Doctrine\ORM\EntityRepository');
        $topicRepository->findAll()->willReturn($topics);

        $this->em->getRepository('AppBundle:Topic')->willReturn($topicRepository);

        $service = new TopicService($this->em->reveal(), $this->requestStack, $this->formFactory->reveal());
        $result = $service->list();

        $this->assertEquals($topics, $result);
    }

    public function testItCanCreate()
    {
        $query = [];
        $requestData = [
            'topic' => [
                'title' => 'Test'
            ]
        ];

        $topic = new Topic();
        $topic->setTitle('Test');

        $session = $this->prophet->prophesize('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $request = new Request($query, $requestData);
        $request->setSession($session->reveal());

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $form = $this->prophet->prophesize('Symfony\Component\Form');
        $form->willImplement('Symfony\Component\Form\FormInterface');
        $form->getName()->willReturn('topic');
        $form->submit(Argument::type('array'))->willReturn($form);
        $form->getData()->willReturn($topic);

        $this->formFactory->create(Argument::type('string'), new Topic())->willReturn($form);

        $service = new TopicService($this->em->reveal(), $requestStack, $this->formFactory->reveal());
        $result = $service->create();

        $this->assertEquals($topic, $result);
        $this->em->flush()->shouldBeCalledTimes(1);

        $this->prophet->checkPredictions();
    }
}
