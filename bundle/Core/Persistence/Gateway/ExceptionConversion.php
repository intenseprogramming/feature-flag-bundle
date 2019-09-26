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

namespace IntProg\FeatureFlagBundle\Core\Persistence\Gateway;

use Doctrine\DBAL\DBALException;
use IntProg\FeatureFlagBundle\Core\Persistence\Gateway;
use PDOException;
use RuntimeException;

/**
 * Class ExceptionConversion.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Persistence\Gateway
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class ExceptionConversion extends Gateway
{
    /** @var Gateway $innerGateway */
    public $innerGateway;

    /**
     * ExceptionConversion constructor.
     *
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->innerGateway = $gateway;
    }

    /**
     * Loads a feature flag by $identifier and $scope.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return array|null
     */
    public function load(string $identifier, string $scope): ?array
    {
        try {
            return $this->innerGateway->load($identifier, $scope);
        } catch (DBALException | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * Insert a feature flag.
     *
     * @param string $identifier
     * @param string $scope
     * @param bool   $enabled
     *
     * @return void
     */
    public function insert(string $identifier, string $scope, bool $enabled): void
    {
        try {
            $this->innerGateway->insert($identifier, $scope, $enabled);
        } catch (DBALException | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * Delete feature flag with the given $identifier and $scope.
     *
     * @param string $identifier
     * @param string $scope
     */
    public function delete(string $identifier, string $scope): void
    {
        try {
            $this->innerGateway->delete($identifier, $scope);
        } catch (DBALException | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * Updates the flag of a feature.
     *
     * @param string $identifier
     * @param string $scope
     * @param bool   $enabled
     *
     * @return void
     */
    public function update(string $identifier, string $scope, bool $enabled): void
    {
        try {
            $this->innerGateway->update($identifier, $scope, $enabled);
        } catch (DBALException | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * Returns all feature flags by scope.
     *
     * @param string $scope
     *
     * @return mixed
     */
    public function list(string $scope): array
    {
        try {
            return $this->innerGateway->list($scope);
        } catch (DBALException | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }
}
