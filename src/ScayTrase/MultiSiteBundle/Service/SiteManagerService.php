<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.08.2014
 * Time: 18:30
 */

namespace ScayTrase\MultiSiteBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ScayTrase\MultiSiteBundle\Entity\Site;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteManagerService implements SiteManagerInterface
{
    /** @var null|Site */
    private $site;
    /** @var EntityManagerInterface */
    private $manager;
    /** @var  string */
    private $maintenanceUrl;
    /** @var  SiteFactoryInterface */
    private $fallbackFactory;

    /**
     * @param                        $maintenanceUrl
     * @param EntityManagerInterface $manager
     * @param SiteFactoryInterface   $fallbackFactory
     */
    public function __construct($maintenanceUrl, EntityManagerInterface $manager, SiteFactoryInterface $fallbackFactory)
    {
        $this->maintenanceUrl  = $maintenanceUrl;
        $this->manager         = $manager;
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
                    'name'        => '',
                    'description' => 'Ошибка',
                    'short_name'  => 'Ошибка',
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
     */
    protected function detectSite($host)
    {
        $manager = $this->manager;
        /** @var EntityRepository $siteRepo */
        $siteRepo = $manager->getRepository('MultiSiteBundle:Site');

        $site = $siteRepo->findOneBy(
            array('url' => $host, 'active' => true)
        );

        $urls = (array)$this->maintenanceUrl;

        if (!$site && (in_array($host, $urls, true))) {
            $this->site = $this->fallbackFactory->createSite();

            return;
        }

        if (!$site) {
            throw new NotFoundHttpException('Данный сайт не зарегистрирован');
        }

        $this->site = $site;
    }
}
