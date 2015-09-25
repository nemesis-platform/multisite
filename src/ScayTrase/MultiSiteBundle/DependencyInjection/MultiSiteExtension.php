<?php

namespace ScayTrase\MultiSiteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MultiSiteExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     * @throws \InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('site.maintenance_urls', $config['maintenance_url']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        var_dump($container->getExtensions());
        foreach ($container->getExtensions() as $name => $extension) {

            switch ($name) {
                case 'doctrine': $loader->load('doctrine.yml'); break;
            }
        }

        foreach ($config['provider'] as $name => $parameters) {
            switch ($parameters['type']) {
                case 'service':
                    $this->addServiceProvider($container, $name, $parameters);
                    break;
                case 'array':
                    $this->addArrayProvider($container, $name, $parameters);
                    break;
                case 'class':
                    $this->addClassProvider($container, $name, $parameters);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unsupported provider type %s', $parameters['type']));
            }
        }

        $id = 'site.providers.' . $config['selected_provider'];

        while ($container->hasAlias($id)) {
            $id = (string)$container->getAlias($id);
        }

        if ($container->hasDefinition($id)) {
            $container->setAlias('site.provider', $id);
        }
    }

    private function addServiceProvider(ContainerBuilder $container, $name, array $parameters)
    {
        $container->setAlias('site.providers.' . $name, $parameters['id']);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        array(
                            'globals' => array(
                                'site_manager' => '@site.manager',
                            ),
                        )
                    );
                    break;
            }
        }
    }

    public function getAlias()
    {
        return 'site';
    }


}
