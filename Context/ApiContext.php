<?php
/**
 * File containing the ApiContext class for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context;

use Behat\Behat\Context\Context;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareContext;

class ApiContext implements Context, KernelAwareContext
{
    use CommonContext;

    const DEFAULT_SITEACCESS_NAME = 'behat_site';

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel( KernelInterface $kernel )
    {
        $this->kernel = $kernel;
    }

    /**
     * Get kenel
     *
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    public function getKernel()
    {
        if ( empty( $this->kernel ) )
        {
            throw new \Exception( 'Kernel is not loaded yet.' );
        }

        return $this->kernel;
    }

    /**
     * Get repository
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository()
    {
        return $this->getKernel()->getContainer()->get( 'ezpublish.api.repository' );
    }

    /**
     * @BeforeScenario
     *
     * @param \Behat\Behat\Event\ScenarioEvent|\Behat\Behat\Event\OutlineExampleEvent $event
     */
    public function prepareFeature( $event )
    {
        // Inject a properly generated siteaccess if the kernel is booted, and thus container is available.
        $this->getKernel()->getContainer()->set( 'ezpublish.siteaccess', $this->generateSiteAccess() );
    }

    /**
     * Generates the siteaccess
     *
     * @return \eZ\Publish\Core\MVC\Symfony\SiteAccess
     */
    protected function generateSiteAccess()
    {
        $siteAccessName = getenv( 'EZPUBLISH_SITEACCESS' );
        if ( !$siteAccessName )
        {
            $siteAccessName = static::DEFAULT_SITEACCESS_NAME;
        }

        return new SiteAccess( $siteAccessName, 'cli' );
    }
}
