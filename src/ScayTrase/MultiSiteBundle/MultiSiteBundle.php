<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.03.2015
 * Time: 12:38
 */

namespace ScayTrase\MultiSiteBundle;

use ScayTrase\MultiSiteBundle\DependencyInjection\Compiler\DoctrineCompilerPass;
use ScayTrase\MultiSiteBundle\DependencyInjection\MultiSiteExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MultiSiteBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MultiSiteExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DoctrineCompilerPass());
    }
}
