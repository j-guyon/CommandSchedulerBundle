<?php

namespace JMose\CommandSchedulerBundle;

use JMose\CommandSchedulerBundle\DependencyInjection\JMoseCommandSchedulerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JMoseCommandSchedulerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     * @return JMoseCommandSchedulerExtension
     */
    public function getContainerExtension() {
        $class = $this->getContainerExtensionClass();
        return new $class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensionClass()
    {
        return 'JMose\CommandSchedulerBundle\DependencyInjection\JMoseCommandSchedulerExtension';
    }
}
