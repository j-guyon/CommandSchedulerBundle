<?php

namespace JMose\CommandSchedulerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JMoseCommandSchedulerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $BCBreakParams = array(
            'jmose_command_scheduler.log_path' => 'log_path',
            'jmose_command_scheduler.command_choice_list.excluded_namespaces' => 'excluded_command_namespaces',
            'jmose_command_scheduler.doctrine_manager' => 'doctrine_manager',
        );

        foreach ($BCBreakParams as $keyToReplace => $newKey) {
            if ($container->hasParameter($keyToReplace)) {
                trigger_error(sprintf(
                    'The "%s" container parameter is deprecated. You should move this parameter in your config.yml file under the "%s" config, with the new key "%s".',
                    $keyToReplace, 'jmose_command_scheduler', $newKey
                ), E_USER_DEPRECATED);

                if (!isset($configs['jmose_command_scheduler'][$newKey])) {
                    $configs['jmose_command_scheduler'][$newKey] = $container->getParameter($keyToReplace);
                }
            }
        }

        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config as $key => $value) {
            $container->setParameter('jmose_command_scheduler.'.$key, $value);
        }
    }

    public function getAlias()
    {
        return 'jmose_command_scheduler';
    }
}
