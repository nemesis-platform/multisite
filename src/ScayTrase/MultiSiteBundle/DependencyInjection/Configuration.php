<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 23.09.2015
 * Time: 14:44
 */

namespace ScayTrase\MultiSiteBundle\DependencyInjection;

use ScayTrase\MultiSiteBundle\Providers\SiteProviderInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('site');

        $providers = new ArrayNodeDefinition('provider');
        $providers->isRequired()->requiresAtLeastOneElement();
        $providers
            ->beforeNormalization()
            ->ifString()
            ->then(function ($v) { return array('main' => array('type' => 'service', 'id' => $v)); })
            ->end();

        /** @var ArrayNodeDefinition $proto */
        $proto = $providers->prototype('array');
        $proto->performNoDeepMerging();
        $proto->validate()->always(function (array $arr) {
            switch ($arr['type']) {
                case 'service':
                    if (!array_key_exists('id', $arr)) {
                        throw new \InvalidArgumentException('id should be specified');
                    }
                    break;
                case 'array':
                    if (!array_key_exists('sites', $arr)) {
                        throw new \InvalidArgumentException('sites array should be specified');
                    }
                    break;
                case 'class':
                    if (!array_key_exists('classname', $arr)) {
                        throw new \InvalidArgumentException('classname should be specified');
                    }
                    if (!($arr['classname'] instanceof SiteProviderInterface)) {
                        throw new \InvalidArgumentException(
                            sprintf(
                                'Given class "%s" should implement SiteProviderInterface',
                                $arr['classname'])
                        );
                    }
                    break;
            }
        })->end();

        $provider_type = new ScalarNodeDefinition('type');
        $provider_type->isRequired();
        $provider_type
            ->validate()
            ->ifNotInArray(array('service', 'array', 'class'))
            ->thenInvalid('Invalid provider type "%s"')
            ->end();
        $proto->append($provider_type);

        $provider_id = new ScalarNodeDefinition('id');
        $provider_id->cannotBeEmpty();
        $proto->append($provider_id);

        $provider_classname = new ScalarNodeDefinition('classname');
        $provider_classname->cannotBeEmpty();
        $proto->append($provider_classname);

        $sites = new ArrayNodeDefinition('sites');
        $sites->requiresAtLeastOneElement();

        /** @var ArrayNodeDefinition $sites_proto */
        $sites_proto = $sites->prototype('array');
        $sites_proto
//            ->useAttributeAsKey('url')
            ->children()
            ->scalarNode('name')->defaultValue('')->end()
            ->scalarNode('short_name')->defaultValue('')->end()
            ->scalarNode('description')->defaultValue('')->end()
            ->scalarNode('url')->isRequired()->end()
            ->scalarNode('email')->defaultValue('')->end()
            ->scalarNode('active')->defaultTrue()->end()
            ->scalarNode('logo')->defaultNull()->end()
            ->end();
        $proto->append($sites);

        $rootNode->append($providers);

        $maintenanceUrl = new ArrayNodeDefinition('maintenance_url');
        $maintenanceUrl
            ->defaultValue(array('localhost'));
        $maintenanceUrl->prototype('scalar')->end();
        $maintenanceUrl
            ->beforeNormalization()
            ->ifString()
            ->then(function ($v) { return array($v); })
            ->end();


        $rootNode->append($maintenanceUrl);

        return $treeBuilder;
    }
}
