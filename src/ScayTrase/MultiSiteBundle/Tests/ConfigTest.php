<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 23.09.2015
 * Time: 16:12
 */

namespace ScayTrase\MultiSiteBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Annotations\AnnotationReader;
use ScayTrase\MultiSiteBundle\Entity\Site;
use ScayTrase\MultiSiteBundle\MultiSiteBundle;
use ScayTrase\MultiSiteBundle\Providers\AbstractArrayProvider;
use ScayTrase\MultiSiteBundle\Providers\ConfigArrayProvider;
use ScayTrase\MultiSiteBundle\Providers\DoctrineProvider;
use ScayTrase\MultiSiteBundle\Providers\SiteProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private static $defaultDoctrineConfig = array(
        'dbal' => array(
            'connections' => array('default' => array()),
        ),
        'orm' => array(
            'entity_managers' => array('default' => array(),),
        ),
    );

    public function testEmptyConfig()
    {
        $container = $this->buildContainer(
            array(new MultiSiteBundle())
        );

        self::assertTrue($container->has('site.provider'));
        self::assertTrue($container->get('site.provider') instanceof SiteProviderInterface);
    }

    /**
     * @param array $configs
     *
     * @param BundleInterface[] $bundles
     * @return ContainerBuilder
     */
    protected function buildContainer(array $bundles = array(), array $configs = array())
    {
        $container = new ContainerBuilder(
            new ParameterBag(
                array(
                    'kernel.debug' => false,
                    'kernel.bundles' => $bundles,
                    'kernel.cache_dir' => sys_get_temp_dir(),
                    'kernel.environment' => 'test',
                    'kernel.root_dir' => __DIR__,
                )
            )
        );
        $container->set('annotation_reader', new AnnotationReader());

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
        $container->registerExtension($extension);
        if (!$extension) {
            return;
        }
        $config = array_key_exists($extension->getAlias(), $configs) ? $configs[$extension->getAlias()] : array();
        $extension->load(array($config), $container);
    }

    public function testLegacyConfiguration()
    {
        $container = $this->buildContainer(
            array(
                new DoctrineBundle(),
                new MultiSiteBundle(),
            ),
            array(
                'doctrine' => self::$defaultDoctrineConfig
            )
        );

        self::assertTrue($container->has('site.provider.site_entity'));
        self::assertTrue($container->get('site.provider.site_entity') instanceof DoctrineProvider);
        self::assertTrue($container->has('site.provider'));
        self::assertTrue($container->get('site.provider') instanceof SiteProviderInterface);
        self::assertTrue($container->get('site.provider') instanceof DoctrineProvider);
    }

    public function testConfigurableArrayProvider()
    {
        $container = $this->buildContainer(
            array(new MultiSiteBundle()),
            array(
                'site' => array(
                    'provider' => array(
                        'default' => array(
                            'type' => 'array',
                            'sites' => array(
                                'localhost' => array(
                                    'url' => 'test',
                                    'name' => 'Test site'
                                )
                            )
                        )
                    )
                )
            )
        );

        self::assertTrue($container->has('site.provider'));

        /** @var ConfigArrayProvider $provider */
        $provider = $container->get('site.provider');
        self::assertTrue($provider instanceof SiteProviderInterface);
        self::assertTrue($provider instanceof AbstractArrayProvider);

        self::assertNotNull($provider->getSite('localhost'));
        self::assertNull($provider->getSite('test'));

        self::assertTrue($provider->getSite('localhost') instanceof Site);
    }
}
