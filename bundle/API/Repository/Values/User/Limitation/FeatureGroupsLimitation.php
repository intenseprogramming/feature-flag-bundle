<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-06-02 09:17 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\API\Repository\Values\User\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;

/**
 * Class FeatureGroupsLimitation.
 *
 * @package   IntProg\FeatureFlagBundle\API\Repository\Values\User\Limitation
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2020 Intense Programming
 */
class FeatureGroupsLimitation extends Limitation
{
    public const FEATURE_GROUP = 'FeatureGroups';

    /**
     * Returns the limitation identifier (one of the defined constants) or a custom limitation.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return self::FEATURE_GROUP;
    }
}
