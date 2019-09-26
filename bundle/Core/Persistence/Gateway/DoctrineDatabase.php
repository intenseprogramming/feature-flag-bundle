<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-26 05:12 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Core\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use IntProg\FeatureFlagBundle\Core\Persistence\Gateway;
use PDO;

/**
 * Class DoctrineDatabase.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Persistence\Gateway
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class DoctrineDatabase extends Gateway
{
    /** @var Connection $connection */
    protected $connection;

    /**
     * Handler constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
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
        $query             = $this->connection->createQueryBuilder();
        $expressionBuilder = $query->expr();

        $query
            ->select(...$this->getColumns())
            ->from(self::TABLE_FEATURE_FLAG)
            ->where($expressionBuilder->andX(
                $expressionBuilder->eq(self::COLUMN_IDENTIFIER, ':identifier'),
                $expressionBuilder->eq(self::COLUMN_SCOPE, ':scope')
            ))
            ->setParameter(':identifier', $identifier, PDO::PARAM_STR)
            ->setParameter(':scope', $scope, PDO::PARAM_STR);

        return $query->execute()->fetch(PDO::FETCH_ASSOC) ?: null;
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
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert(self::TABLE_FEATURE_FLAG)
            ->setValue(self::COLUMN_IDENTIFIER, ':identifier')
            ->setValue(self::COLUMN_SCOPE, ':scope')
            ->setValue(self::COLUMN_ENABLED, ':enabled')
            ->setParameter(':identifier', $identifier, PDO::PARAM_STR)
            ->setParameter(':scope', $scope, PDO::PARAM_STR)
            ->setParameter(':enabled', $enabled, PDO::PARAM_BOOL);

        $query->execute();
    }

    /**
     * Delete feature flag with the given $identifier and $scope.
     *
     * @param string $identifier
     * @param string $scope
     */
    public function delete(string $identifier, string $scope): void
    {
        $query             = $this->connection->createQueryBuilder();
        $expressionBuilder = $query->expr();

        $query
            ->delete(self::TABLE_FEATURE_FLAG)
            ->where($expressionBuilder->andX(
                $expressionBuilder->eq(self::COLUMN_IDENTIFIER, ':identifier'),
                $expressionBuilder->eq(self::COLUMN_SCOPE, ':scope')
            ))
            ->setParameter(':identifier', $identifier, PDO::PARAM_STR)
            ->setParameter(':scope', $scope, PDO::PARAM_STR);

        $query->execute();
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
        $query             = $this->connection->createQueryBuilder();
        $expressionBuilder = $query->expr();

        $query
            ->update(self::TABLE_FEATURE_FLAG)
            ->where($expressionBuilder->andX(
                $expressionBuilder->eq(self::COLUMN_IDENTIFIER, ':identifier'),
                $expressionBuilder->eq(self::COLUMN_SCOPE, ':scope')
            ))
            ->set(self::COLUMN_ENABLED, ':enabled')
            ->setParameter(':identifier', $identifier, PDO::PARAM_STR)
            ->setParameter(':scope', $scope, PDO::PARAM_STR)
            ->setParameter(':enabled', $enabled, PDO::PARAM_BOOL);

        $query->execute();
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
        $query = $this->connection->createQueryBuilder();

        $query
            ->select(...$this->getColumns())
            ->from(self::TABLE_FEATURE_FLAG)
            ->where($query->expr()->eq(self::COLUMN_SCOPE, ':scope'))
            ->setParameter(':scope', $scope, PDO::PARAM_STR);

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all columns of the feature flag table.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            self::COLUMN_IDENTIFIER,
            self::COLUMN_SCOPE,
            self::COLUMN_ENABLED,
        ];
    }
}
