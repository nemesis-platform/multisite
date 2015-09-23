<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 23.09.2015
 * Time: 11:54
 */

namespace ScayTrase\MultiSiteBundle\Providers;

use ScayTrase\MultiSiteBundle\Entity\Site;

/**
 * Interface SiteProviderInterface
 * @package ScayTrase\MultiSiteBundle\Service
 */
interface SiteProviderInterface
{
    /**
     * @param string $hostname
     * @return Site|null
     */
    public function getSite($hostname);
}
