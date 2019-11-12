<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-30 07:11 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\API\Repository\Values\User\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;

/**
 * Class ConfigurationScopeLimitation.
 *
 * @package   IntProg\FeatureFlagBundle\API\Repository\Values\User\Limitation
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class ConfigurationScopeLimitation extends Limitation
{
    public const ConfigurationScope = 'ConfigurationScope';

    /**
     * Returns the limitation identifier (one of the defined constants) or a custom limitation.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return self::ConfigurationScope;
    }
}
