<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-25 09:12 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Spi\Persistence;

use eZ\Publish\SPI\Persistence\ValueObject;

/**
 * Class FeatureFlag.
 *
 * @package   IntProg\FeatureFlagBundle\Spi\Persistence
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class FeatureFlag extends ValueObject
{
    /** @var string $identifier */
    public $identifier;

    /** @var string $scope */
    public $scope;

    /** @var bool $enabled */
    public $enabled;
}
