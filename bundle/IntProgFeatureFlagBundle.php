<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-23 07:23 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use IntProg\FeatureFlagBundle\DependencyInjection\Security\PolicyProvider\FeatureFlagPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class IntProgFeatureFlagBundle.
 *
 * @package   IntProg\FeatureFlagBundle
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class IntProgFeatureFlagBundle extends Bundle
{
    /**
     * Builds the kernel and adds the compiler passes for collections.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $eZExtension = $container->getExtension('ezpublish');

        if ($eZExtension instanceof EzPublishCoreExtension) {
            $eZExtension->addPolicyProvider(new FeatureFlagPolicyProvider());
        }
    }
}
