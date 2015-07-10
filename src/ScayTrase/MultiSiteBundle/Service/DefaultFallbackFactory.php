<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 13.04.2015
 * Time: 12:38
 */

namespace ScayTrase\MultiSiteBundle\Service;

use ScayTrase\MultiSiteBundle\Entity\Site;

class DefaultFallbackFactory implements SiteFactoryInterface
{

    /**
     * @param array $options
     *
     * @return Site
     */
    public function createSite(array $options = array())
    {
        $options = $this->getOptions($options);

        $fallbackSite = new Site();
        $fallbackSite->setActive($options['active']);
        $fallbackSite->setDescription($options['description']);
        $fallbackSite->setName($options['name']);
        $fallbackSite->setShortName($options['short_name']);
        $fallbackSite->setUrl($options['maintenance_url']);

        return $fallbackSite;
    }

    protected function getOptions(array $options = array())
    {
        $defaults = array(
            'active'          => false,
            'name'            => 'Режим обслуживания',
            'short_name'      => 'Обслуживание',
            'description'     => 'Режим обслуживания',
            'maintenance_url' => 'localhost',
        );

        return array_replace_recursive($defaults, $options);
    }
}
