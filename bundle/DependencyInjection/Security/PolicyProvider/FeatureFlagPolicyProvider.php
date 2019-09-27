<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-27 07:11 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\DependencyInjection\Security\PolicyProvider;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

/**
 * Class FeatureFlagPolicyProvider.
 *
 * @package   IntProg\FeatureFlagBundle\DependencyInjection\Security\PolicyProvider
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class FeatureFlagPolicyProvider extends YamlPolicyProvider
{
    /**
     * Returns an array of files where the policy configuration lies.
     * Each file path MUST be absolute.
     *
     * @return array
     */
    public function getFiles(): array
    {
        return [
            __DIR__ . '/../../../Resources/config/policies.yml',
        ];
    }
}
