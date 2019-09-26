<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 05:59 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Core\Repository\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Class FeatureFlag.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Repository\Values
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 *
 * @property-read string      $identifier
 * @property-read string      $scope
 * @property-read string|null $name
 * @property-read string|null $description
 * @property-read bool        $default
 * @property-read bool        $enabled
 */
class FeatureFlag extends ValueObject
{
    /** @var string $identifier */
    protected $identifier;

    /** @var string $scope */
    protected $scope;

    /** @var string|null $name */
    protected $name;

    /** @var string|null $description */
    protected $description;

    /** @var bool $default */
    protected $default;

    /** @var bool $enabled */
    protected $enabled;
}
