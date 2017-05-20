<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Command\MonitorCommand;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class MonitorCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Command
 */
class MonitorCommandTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Test scheduler:execute without option
     */
    public function testExecuteWithError()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand(
            'scheduler:monitor',
            array(
                '--dump' => true
            )
        );

        $this->assertRegExp('/two:/', $output);
        $this->assertRegExp('/four:/', $output);
    }

    /**
     * Test scheduler:execute without option
     */
    public function testExecuteWithoutError()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $two = $this->em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find(2);
        $four = $this->em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find(4);
        $two->setLocked(false);
        $four->setLastReturnCode(0);
        $this->em->flush();

        // None command should be in error status here.

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand(
            'scheduler:monitor',
            array(
                '--dump' => true
            )
        );

        $this->assertStringStartsWith('No errors found.', $output);
    }

    /**
     * @group unit
     */
    public function testSendErrorMails()
    {
        $templatingMock = $this->getMockBuilder(Symfony\Bundle\TwigBundle\TwigEngine::class)->disableOriginalConstructor()->setMethods(['render'])->getMock();
        $templatingMock->expects($this->once())->method('render')->willReturn("The Email Body");

        $containerMock = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->setMethods(['get'])->getMock();
        $containerMock->expects($this->once())->method('get')->willReturn($templatingMock);

        $monitorCommandMock = $this->getMockBuilder(MonitorCommand::class)->disableOriginalConstructor()->setMethods(['getContainer'])->getMock();
        $monitorCommandMock->expects($this->once())->method('getContainer')->willReturn($containerMock);

        $swiftMailerMock = $this->getMockBuilder(\Swift_Mailer::class)->disableOriginalConstructor()->setMethods([])->getMock();
        $swiftMailerMock->expects($this->once())->method('send');

        $monitorCommandMock->setMailer($swiftMailerMock);
        $monitorCommandMock->sendErrorMails(array());
    }

    /**
     * @group unit
     */
    public function testSendNoErrorMails()
    {
        $templatingMock = $this->getMockBuilder(Symfony\Bundle\TwigBundle\TwigEngine::class)->disableOriginalConstructor()->setMethods(['render'])->getMock();
        $templatingMock->expects($this->once())->method('render')->willReturn("The Email Body");

        $containerMock = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->setMethods(['get'])->getMock();
        $containerMock->expects($this->once())->method('get')->willReturn($templatingMock);

        $monitorCommandMock = $this->getMockBuilder(MonitorCommand::class)->disableOriginalConstructor()->setMethods(['getContainer'])->getMock();
        $monitorCommandMock->expects($this->once())->method('getContainer')->willReturn($containerMock);

        $swiftMailerMock = $this->getMockBuilder(\Swift_Mailer::class)->disableOriginalConstructor()->setMethods([])->getMock();
        $swiftMailerMock->expects($this->once())->method('send');

        $monitorCommandMock->setMailer($swiftMailerMock);
        $monitorCommandMock->sendNoErrorMails();
    }

}
