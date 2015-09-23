<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.08.2014
 * Time: 18:30
 */

namespace ScayTrase\MultiSiteBundle\Service;

use ScayTrase\MultiSiteBundle\Entity\Site;
use ScayTrase\MultiSiteBundle\Providers\SiteProviderInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteManagerService implements SiteManagerInterface
{
    /** @var null|Site */
    private $site;
    /** @var  string */
    private $maintenanceUrl;
    /** @var  SiteFactoryInterface */
    private $fallbackFactory;
    /** @var  SiteProviderInterface */
    private $provider;

    /**
     * @param $maintenanceUrl
     * @param SiteProviderInterface $provider
     * @param SiteFactoryInterface $fallbackFactory
     */
    public function __construct($maintenanceUrl, SiteProviderInterface $provider, SiteFactoryInterface $fallbackFactory)
    {
        $this->maintenanceUrl  = $maintenanceUrl;
        $this->provider        = $provider;
        $this->fallbackFactory = $fallbackFactory;
    }

    /**
     * @return null|Site
     */
    public function getSite()
    {
        if (null === $this->site) {
            $this->site = $this->fallbackFactory->createSite(
                array(
                    'name' => '',
                    'description' => 'Ошибка',
                    'short_name' => 'Ошибка',
                )
            );
        }

        return $this->site;
    }

    /**
     * @param null $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @param GetResponseEvent $event
     * @throws NotFoundHttpException
     * @throws \UnexpectedValueException
     */
    public function onRequest(GetResponseEvent $event)
    {

        if ($this->site) {
            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        $this->detectSite($event->getRequest()->getHost());
    }

    /**
     * @param string $host
     * @throws NotFoundHttpException
     */
    protected function detectSite($host)
    {
        $site = $this->provider->getSite($host);

        $urls = (array)$this->maintenanceUrl;

        if (!$site && (in_array($host, $urls, true))) {
            $this->site = $this->fallbackFactory->createSite();

            return;
        }

        if (!$site) {
            throw new NotFoundHttpException('Site is not operated');
        }

        $this->site = $site;
    }
}
