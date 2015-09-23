<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 23.09.2015
 * Time: 12:12
 */

namespace ScayTrase\MultiSiteBundle\Providers;

use Doctrine\ORM\EntityManagerInterface;
use ScayTrase\MultiSiteBundle\Entity\Site;

class DoctrineProvider implements SiteProviderInterface
{
    /** @var string  */
    private $class;
    /** @var string  */
    private $hostPropertyName;
    /** @var array  */
    private $filter;
    /** @var EntityManagerInterface  */
    private $manager;

    /**
     * DoctrineProvider constructor.
     * @param EntityManagerInterface $manager
     * @param string $class
     * @param string $hostPropertyName
     * @param array $filter
     */
    public function __construct(EntityManagerInterface $manager, $class, $hostPropertyName, array $filter = array())
    {
        $this->manager          = $manager;
        $this->class            = $class;
        $this->hostPropertyName = $hostPropertyName;
        $this->filter           = $filter;
    }

    /**
     * @param string $hostname
     * @return Site|null
     */
    public function getSite($hostname)
    {
        $repository = $this->manager->getRepository($this->class);
        $site       = $repository->findOneBy(
            array_replace_recursive(
                $this->filter,
                array($this->hostPropertyName => $hostname)
            )
        );

        return $site;
    }
}
