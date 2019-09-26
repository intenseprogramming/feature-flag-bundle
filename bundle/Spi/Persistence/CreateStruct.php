<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 07:23 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Spi\Persistence;

/**
 * Class CreateStruct.
 *
 * @package   IntProg\FeatureFlagBundle\Spi\Persistence
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class CreateStruct
{
    /** @var string $identifier */
    public $identifier;

    /** @var string $scope */
    public $scope;

    /** @var bool $enabled */
    public $enabled;
}
