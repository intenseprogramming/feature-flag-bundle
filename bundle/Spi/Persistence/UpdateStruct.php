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
 * Class UpdateStruct.
 *
 * @package   IntProg\FeatureFlagBundle\Spi\Persistence
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class UpdateStruct
{
    /** @var FeatureFlag $feature */
    protected $feature;

    /** @var bool $enabled */
    public $enabled;

    /**
     * UpdateStruct constructor.
     *
     * @param FeatureFlag $feature
     */
    public function __construct(FeatureFlag $feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return FeatureFlag
     */
    public function getFeature(): FeatureFlag
    {
        return $this->feature;
    }
}
