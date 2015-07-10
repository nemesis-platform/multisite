<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 13.04.2015
 * Time: 12:37
 */

namespace ScayTrase\MultiSiteBundle\Service;

use ScayTrase\MultiSiteBundle\Entity\Site;

interface SiteFactoryInterface
{
    /**
     * @param array $options
     *
     * @return Site
     */
    public function createSite(array $options = array());
}
