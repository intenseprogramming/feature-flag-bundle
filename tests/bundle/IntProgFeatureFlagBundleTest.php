<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-06-03 06:40 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use IntProg\FeatureFlagBundle\DependencyInjection\Security\PolicyProvider\FeatureFlagPolicyProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IntProgFeatureFlagBundleTest extends TestCase
{
    /**
     * @return void
     * @test
     */
    public function build_should_add_policy_provider_to_ez_core_extension(): void
    {
        $extension = $this->createMock(EzPublishCoreExtension::class);
        $extension->expects($this->once())->method('addPolicyProvider')->willReturnCallback(function ($provider) {
            $this->assertInstanceOf(FeatureFlagPolicyProvider::class, $provider);
        });

        $container = $this->createMock(ContainerBuilder::class);
        $container->method('getExtension')->with('ezpublish')->willReturn($extension);

        (new IntProgFeatureFlagBundle())->build($container);
    }
}
