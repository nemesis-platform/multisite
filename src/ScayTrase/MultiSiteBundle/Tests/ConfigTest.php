<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 23.09.2015
 * Time: 16:12
 */

namespace ScayTrase\MultiSiteBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use ScayTrase\MultiSiteBundle\MultiSiteBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $container = $this->buildContainer(
            array(
                new MultiSiteBundle(),
                new DoctrineBundle()
            ),
            array(
                'site' => array(
                    'provider' => 'site.provider.default'
                )
            )
        );
    }

    /**
     * @param array $configs
     *
     * @param BundleInterface[] $bundles
     * @return ContainerBuilder
     */
    protected function buildContainer(array $bundles = array(), array $configs = array())
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        foreach ($bundles as $bundle) {
            $bundle->build($container);
            $this->loadExtension($bundle, $container, $configs);
        }
        $container->compile();
        $dumper = new PhpDumper($container);
        $dumper->dump();
        return $container;
    }

    /**
     * @param BundleInterface $bundle
     * @param ContainerBuilder $container
     * @param array $configs
     */
    protected function loadExtension(BundleInterface $bundle, ContainerBuilder $container, array $configs)
    {
        $extension = $bundle->getContainerExtension();
        if (!$extension) {
            return;
        }
        $config = array_key_exists($extension->getAlias(), $configs) ? $configs[$extension->getAlias()] : array();
        $extension->load(array($config), $container);
    }
}
