<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 06:51 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Spi\Persistence;

use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag as FeatureValue;

/**
 * Class Handler.
 *
 * @package   IntProg\FeatureFlagBundle\Spi\Persistence\FeatureFlag
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
interface Handler
{
    /**
     * Creates the scoped entry for the feature to override default.
     *
     * @param CreateStruct $struct
     *
     * @return FeatureFlag
     */
    public function create(CreateStruct $struct): FeatureFlag;

    /**
     * Updates the flag for the feature.
     *
     * @param UpdateStruct $struct
     *
     * @return FeatureFlag
     */
    public function update(UpdateStruct $struct): FeatureFlag;

    /**
     * Gets the feature by identifier and scope.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return FeatureFlag
     */
    public function load(string $identifier, string $scope): ?FeatureFlag;

    /**
     * Removes the persisted flag for the feature.
     *
     * @param FeatureValue $feature
     *
     * @return void
     */
    public function delete(FeatureValue $feature): void;

    /**
     * Lists stored features.
     *
     * @param string $scope
     *
     * @return array|FeatureFlag[]
     */
    public function list(string $scope): array;
}
