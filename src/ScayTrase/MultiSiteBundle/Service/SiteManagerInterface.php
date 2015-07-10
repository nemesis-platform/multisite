<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.03.2015
 * Time: 13:58
 */

namespace ScayTrase\MultiSiteBundle\Service;

use ScayTrase\MultiSiteBundle\Entity\Site;

interface SiteManagerInterface
{
    /** @return Site */
    public function getSite();
}
