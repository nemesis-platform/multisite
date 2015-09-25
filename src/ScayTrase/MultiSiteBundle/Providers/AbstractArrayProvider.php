<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-09-25
 * Time: 19:00
 */

namespace ScayTrase\MultiSiteBundle\Providers;

use ScayTrase\MultiSiteBundle\Entity\Site;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;

abstract class AbstractArrayProvider implements SiteProviderInterface
{
    /** @var  Site[] */
    protected $sites;

    /**
     * @param string $hostname
     * @return Site|null
     */
    public function getSite($hostname)
    {
        if (!array_key_exists($hostname, $this->sites)) {
            return null;
        }

        return $this->sites[$hostname];
    }

    /**
     * @param array $siteData
     * @return Site
     *
     * @throws \LogicException
     * @throws AccessException
     * @throws InvalidArgumentException
     * @throws UnexpectedTypeException
     */
    abstract public function createFromArray(array $siteData);
}