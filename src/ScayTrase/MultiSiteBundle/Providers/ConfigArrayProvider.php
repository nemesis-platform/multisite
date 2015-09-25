<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 23.09.2015
 * Time: 12:22
 */

namespace ScayTrase\MultiSiteBundle\Providers;

use ScayTrase\MultiSiteBundle\Entity\Site;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ConfigArrayProvider extends AbstractArrayProvider
{
    /**
     * ArrayProvider constructor.
     * @param array $config
     *
     * @throws \LogicException
     * @throws AccessException
     * @throws InvalidArgumentException
     * @throws UnexpectedTypeException
     */
    public function __construct(array $config)
    {
        foreach ($config as $url => $row) {
            $row['url'] = $url;
            $this->addSite($this->createFromArray($row));
        }
    }

    private function addSite(Site $site)
    {
        $this->sites[$site->getUrl()] = $site;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $siteData)
    {
        $accessor = new PropertyAccessor();
        $site     = new Site();

        if (!array_key_exists('url', $siteData)) {
            throw new \LogicException('At least url property should be defined to construct site object');
        }

        foreach ($siteData as $property => $value) {
            $accessor->setValue($site, $property, $value);
        }

        return $site;
    }


}
