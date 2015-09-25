<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2015
 * Time: 16:16
 */

namespace ScayTrase\MultiSiteBundle\Entity;

/**
 * Interface SiteBoundInterface
 * @package ScayTrase\MultiSiteBundle\Entity
 */
interface SiteBoundInterface
{
    /**
     * Checks that object is bound to given site.
     * @param Site $site
     *
     * @return bool True if objects belongs to site, false otherwise
     */
    public function checkSite(Site $site);
}
