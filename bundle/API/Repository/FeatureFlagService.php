<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 05:53 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\API\Repository;

use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use IntProg\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use IntProg\FeatureFlagBundle\Spi\Persistence\UpdateStruct;

/**
 * Class FeatureFlagService.
 *
 * @package   IntProg\FeatureFlagBundle\API\Repository
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
interface FeatureFlagService
{
    /**
     * Loads the feature for the current siteaccess.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return FeatureFlag
     */
    public function load(string $identifier, string $scope): FeatureFlag;

    /**
     * Loads a list of features for the current siteaccess.
     *
     * @param string $scope
     *
     * @return array
     */
    public function list(string $scope): array;

    /**
     * Generates a new feature flag create struct.
     *
     * @return CreateStruct
     */
    public function newFeatureFlagCreateStruct(): CreateStruct;

    /**
     * Creates the feature for the current siteaccess.
     *
     * @param CreateStruct $createStruct
     *
     * @return FeatureFlag
     */
    public function create(CreateStruct $createStruct): FeatureFlag;

    /**
     * Generates a new feature flag update struct from $feature.
     *
     * @param FeatureFlag $feature
     *
     * @return UpdateStruct
     */
    public function newFeatureFlagUpdateStruct(FeatureFlag $feature): UpdateStruct;

    /**
     * Updates the feature for the current siteaccess.
     *
     * @param UpdateStruct $updateStruct
     *
     * @return FeatureFlag
     */
    public function update(UpdateStruct $updateStruct): FeatureFlag;

    /**
     * Deletes the feature for the current siteaccess.
     *
     * @param FeatureFlag $feature
     *
     * @return mixed
     */
    public function delete(FeatureFlag $feature);
}
