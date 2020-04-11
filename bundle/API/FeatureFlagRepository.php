<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-11-12 07:49 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\API;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;

/**
 * Class FeatureFlagRepository.
 *
 * @package   IntProg\FeatureFlagBundle\API
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
interface FeatureFlagRepository
{
    /**
     * Sets the siteaccess for scope awareness.
     * Call is not defined in service but will trigger on scope change instead.
     *
     * @param SiteAccess|null $siteAccess
     *
     * @return void
     */
    public function setSiteAccess(SiteAccess $siteAccess = null): void;

    /**
     * Return true if the feature with $identifier is enabled.
     *
     * @param string      $identifier
     * @param string|null $scope
     *
     * @return bool
     */
    public function isEnabled(string $identifier, string $scope = null): bool;

    /**
     * Return true if the feature with $identifier is disabled.
     *
     * @param string      $identifier
     * @param string|null $scope
     *
     * @return bool
     */
    public function isDisabled(string $identifier, string $scope = null): bool;

    /**
     * Returns a feature by $identifier.
     *
     * @param string      $identifier
     * @param string|null $scope
     *
     * @return FeatureFlag
     *
     * @internal This method does not notify the cache and should not be used for cache sensitive resources.
     */
    public function get(string $identifier, string $scope = null): FeatureFlag;

    /**
     * Temporary enables a feature by $identifier. Will reset after calling FeatureFlagService::reset().
     *
     * @param string $identifier
     *
     * @return void
     */
    public function enableFeature(string $identifier): void;

    /**
     * Temporary disables a feature by $identifier. Will reset after calling FeatureFlagService::reset().
     *
     * @param string $identifier
     *
     * @return void
     */
    public function disableFeature(string $identifier): void;

    /**
     * Resets all temporary enabled/disabled features.
     *
     * @return void
     */
    public function reset(): void;

    /**
     * Rebuilds known features.
     *
     * @param bool $global
     *
     * @return void
     */
    public function rebuildFeature(bool $global = true): void;

    /**
     * Returns exposed features.
     *
     * @return array
     */
    public function getExposedFeatureStates(): array;

    /**
     * Returns all currently possible scopes.
     *
     * @return array
     */
    public function getAllScopes(): array;
}
