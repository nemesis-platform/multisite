<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.03.2015
 * Time: 12:38
 */

namespace ScayTrase\MultiSiteBundle;

use ScayTrase\MultiSiteBundle\DependencyInjection\MultiSiteExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MultiSiteBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MultiSiteExtension();
    }

}
