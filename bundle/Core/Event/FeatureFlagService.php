<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-01-03 11:09 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Core\Event;

use IntProg\FeatureFlagBundle\API\Repository\Events\FeatureFlag\CreatedStateEvent;
use IntProg\FeatureFlagBundle\API\Repository\Events\FeatureFlag\RemovedStateEvent;
use IntProg\FeatureFlagBundle\API\Repository\Events\FeatureFlag\UpdatedStateEvent;
use IntProg\FeatureFlagBundle\API\Repository\FeatureFlagService as FeatureFlagServiceInterface;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use IntProg\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use IntProg\FeatureFlagBundle\Spi\Persistence\UpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class FeatureFlagService.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Event
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2020 Intense Programming
 */
class FeatureFlagService implements FeatureFlagServiceInterface
{
    /** @var FeatureFlagServiceInterface $service */
    protected $service;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    /**
     * FeatureFlagService constructor.
     *
     * @param FeatureFlagServiceInterface $service
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(FeatureFlagServiceInterface $service, EventDispatcherInterface $eventDispatcher)
    {
        $this->service         = $service;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Loads the feature for the current siteaccess.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return FeatureFlag
     */
    public function load(string $identifier, string $scope): FeatureFlag
    {
        return $this->service->load($identifier, $scope);
    }

    /**
     * Loads a list of features for the current siteaccess.
     *
     * @param string $scope
     *
     * @return array
     */
    public function list(string $scope): array
    {
        return $this->service->list($scope);
    }

    /**
     * Generates a new feature flag create struct.
     *
     * @return CreateStruct
     */
    public function newFeatureFlagCreateStruct(): CreateStruct
    {
        return $this->service->newFeatureFlagCreateStruct();
    }

    /**
     * Creates the feature for the current siteaccess.
     *
     * @param CreateStruct $createStruct
     *
     * @return FeatureFlag
     */
    public function create(CreateStruct $createStruct): FeatureFlag
    {
        $featureFlag = $this->service->create($createStruct);

        $this->eventDispatcher->dispatch(new CreatedStateEvent($featureFlag->identifier, $featureFlag->scope));

        return $featureFlag;
    }

    /**
     * Generates a new feature flag update struct from $feature.
     *
     * @param FeatureFlag $feature
     *
     * @return UpdateStruct
     */
    public function newFeatureFlagUpdateStruct(FeatureFlag $feature): UpdateStruct
    {
        return $this->service->newFeatureFlagUpdateStruct($feature);
    }

    /**
     * Updates the feature for the current siteaccess.
     *
     * @param UpdateStruct $updateStruct
     *
     * @return FeatureFlag
     */
    public function update(UpdateStruct $updateStruct): FeatureFlag
    {
        $featureFlag = $this->service->update($updateStruct);

        $this->eventDispatcher->dispatch(new UpdatedStateEvent(
            $updateStruct->getFeature()->identifier, $updateStruct->getFeature()->scope
        ));

        return $featureFlag;
    }

    /**
     * Deletes the feature for the current siteaccess.
     *
     * @param FeatureFlag $feature
     *
     * @return void
     */
    public function delete(FeatureFlag $feature): void
    {
        $this->service->delete($feature);

        $this->eventDispatcher->dispatch(new RemovedStateEvent($feature->identifier, $feature->scope));
    }
}
