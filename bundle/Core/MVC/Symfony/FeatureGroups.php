<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-06-02 09:16 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\Core\MVC\Symfony;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Class FeatureGroups.
 *
 * @package   IntProg\FeatureFlagBundle\Core\MVC\Symfony
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2020 Intense Programming
 */
class FeatureGroups extends ValueObject
{
    /**
     * Name of the siteaccess.
     *
     * @var string
     */
    public $groups;

    /**
     * FeatureGroups constructor.
     *
     * @param array $groups
     */
    public function __construct(array $groups)
    {
        $this->groups = $groups;
    }
}
