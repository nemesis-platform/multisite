<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2015
 * Time: 16:16
 */

namespace ScayTrase\MultiSiteBundle\Entity;

interface SiteBoundInterface
{
    /**
     * @param Site $site
     *
     * @return bool
     */
    public function checkSite(Site $site);
}
