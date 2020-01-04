<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-01-03 11:17 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\API\Repository\Events\FeatureFlag;

use eZ\Publish\SPI\Repository\Event\AfterEvent;

/**
 * Class RemovedStateEvent.
 *
 * @package   IntProg\FeatureFlagBundle\API\Repository\Events\FeatureFlag
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2020 Intense Programming
 */
class RemovedStateEvent extends AfterEvent
{
    /** @var string $identifier */
    protected $identifier;

    /** @var string $scope */
    protected $scope;

    /**
     * UpdatedStateEvent constructor.
     *
     * @param string $identifier
     * @param string $scope
     */
    public function __construct(string $identifier, string $scope)
    {
        $this->identifier = $identifier;
        $this->scope      = $scope;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }
}
