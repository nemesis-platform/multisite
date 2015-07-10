<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-12-20
 * Time: 14:05
 */

namespace ScayTrase\MultiSiteBundle\ParamConverter;

use Doctrine\Common\Persistence\ManagerRegistry;
use ScayTrase\MultiSiteBundle\Entity\SiteBoundInterface;
use ScayTrase\MultiSiteBundle\Service\SiteManagerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteParamConverter extends DoctrineParamConverter
{

    /** @var  SiteManagerService */
    private $siteManager;

    public function __construct(ManagerRegistry $registry, SiteManagerService $siteManager)
    {
        parent::__construct($registry);
        $this->siteManager = $siteManager;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool    True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {

        $name  = $configuration->getName();
        $class = $configuration->getClass();


        $site = $this->siteManager->getSite();

        if (!$site) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        if ($request->attributes->get($name, false) === null) {
            return false;
        }

        if ($name !== 'site') {
            $request->attributes->set('site', $site->getId());
        }

        if (parent::apply($request, $configuration) === false) {
            return false;
        }

        $object = $request->attributes->get($name);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        if (!$object instanceof SiteBoundInterface) {
            return false;
        }

        if ($object instanceof SiteBoundInterface && !$object->checkSite($site)) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool    True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return in_array(
            'ScayTrase\MultiSiteBundle\Entity\SiteBoundInterface',
            class_implements($configuration->getClass()),
            true
        );
    }
}
