<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-25 10:44 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Core\Persistence;

/**
 * Class Gateway.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Persistence
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
abstract class Gateway
{
    public const TABLE_FEATURE_FLAG = 'intprog_feature_flag';
    public const COLUMN_IDENTIFIER = 'identifier';
    public const COLUMN_SCOPE      = 'scope';
    public const COLUMN_ENABLED    = 'enabled';

    /**
     * Loads a feature flag by $identifier and $scope.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return array|null
     */
    abstract public function load(string $identifier, string $scope): ?array;

    /**
     * Insert a feature flag.
     *
     * @param string $identifier
     * @param string $scope
     * @param bool   $enabled
     *
     * @return void
     */
    abstract public function insert(string $identifier, string $scope, bool $enabled): void;

    /**
     * Delete feature flag with the given $identifier and $scope.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return void
     */
    abstract public function delete(string $identifier, string $scope): void;

    /**
     * Updates the flag of a feature.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @param bool   $enabled
     *
     * @return void
     */
    abstract public function update(string $identifier, string $scope, bool $enabled): void;

    /**
     * Returns all feature flags by scope.
     *
     * @param string $scope
     *
     * @return array
     */
    abstract public function list(string $scope): array;
}
