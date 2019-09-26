<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 07:16 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Core\Persistence\Legacy;

use IntProg\FeatureFlagBundle\Core\Persistence\Gateway;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag as FeatureValue;
use IntProg\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use IntProg\FeatureFlagBundle\Spi\Persistence\FeatureFlag;
use IntProg\FeatureFlagBundle\Spi\Persistence\Handler as SpiHandler;
use IntProg\FeatureFlagBundle\Spi\Persistence\UpdateStruct;

/**
 * Class Handler.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Persistence\Doctrine
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class Handler implements SpiHandler
{
    /** @var Gateway $gateway */
    protected $gateway;

    /**
     * Handler constructor.
     *
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Creates the scoped entry for the feature to override default flag.
     *
     * @param CreateStruct $struct
     *
     * @return FeatureFlag
     */
    public function create(CreateStruct $struct): FeatureFlag
    {
        $this->gateway->insert($struct->identifier, $struct->scope, $struct->enabled);

        $row = $this->gateway->load($struct->identifier, $struct->scope);

        return $this->generateFeatureFromRow($row);
    }

    /**
     * Updates the flag for the feature.
     *
     * @param UpdateStruct $struct
     *
     * @return FeatureFlag
     */
    public function update(UpdateStruct $struct): FeatureFlag
    {
        $spiFeature = $struct->getFeature();
        $this->gateway->update($spiFeature->identifier, $spiFeature->scope, $struct->enabled);

        $row = $this->gateway->load($spiFeature->identifier, $spiFeature->scope);

        return $this->generateFeatureFromRow($row);
    }

    /**
     * Gets the feature by identifier and scope.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return FeatureFlag
     */
    public function load(string $identifier, string $scope): ?FeatureFlag
    {
        $row = $this->gateway->load($identifier, $scope);

        if ($row) {
            return $this->generateFeatureFromRow($row);
        }

        return null;
    }

    /**
     * Removes the persisted flag for the feature.
     *
     * @param FeatureValue $feature
     *
     * @return void
     */
    public function delete(FeatureValue $feature): void
    {
        $this->gateway->delete($feature->identifier, $feature->scope);
    }

    /**
     * Lists stored features.
     *
     * @param string $scope
     *
     * @return array|FeatureFlag[]
     */
    public function list(string $scope): array
    {
        $rows = $this->gateway->list($scope);
        $features = [];

        foreach ($rows as $row) {
            $features[] = $this->generateFeatureFromRow($row);
        }

        return $features;
    }

    protected function generateFeatureFromRow(array $row): FeatureFlag
    {
        return new FeatureFlag([
            'identifier' => $row[Gateway::COLUMN_IDENTIFIER],
            'scope'      => $row[Gateway::COLUMN_SCOPE],
            'enabled'    => (bool) $row[Gateway::COLUMN_ENABLED],
        ]);
    }
}
