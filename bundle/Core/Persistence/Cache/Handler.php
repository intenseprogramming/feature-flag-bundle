<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 07:19 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Core\Persistence\Cache;

use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag as FeatureValue;
use IntProg\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use IntProg\FeatureFlagBundle\Spi\Persistence\FeatureFlag;
use IntProg\FeatureFlagBundle\Spi\Persistence\Handler as SpiHandler;
use IntProg\FeatureFlagBundle\Spi\Persistence\UpdateStruct;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

/**
 * Class Handler.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Persistence\Cache
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class Handler implements SpiHandler
{
    /** @var SpiHandler $handler */
    protected $handler;

    /** @var TagAwareAdapterInterface $cache */
    protected $cache;

    /**
     * Handler constructor.
     *
     * @param SpiHandler               $handler
     * @param TagAwareAdapterInterface $cache
     */
    public function __construct(SpiHandler $handler, TagAwareAdapterInterface $cache)
    {
        $this->handler = $handler;
        $this->cache   = $cache;
    }

    /**
     * Creates the scoped entry for the feature flag to override default.
     *
     * @param CreateStruct $struct
     *
     * @return FeatureFlag
     *
     * @throws InvalidArgumentException
     */
    public function create(CreateStruct $struct): FeatureFlag
    {
        $feature = $this->handler->create($struct);

        $cacheItem = $this->cache->getItem($this->getCacheKey($struct->scope, $struct->identifier));
        $cacheItem->set($feature);

        $this->cache->save($cacheItem);
        $this->cache->deleteItem($this->getCacheKey($feature->scope));

        return $feature;
    }

    /**
     * Updates the flag for the feature.
     *
     * @param UpdateStruct $struct
     *
     * @return FeatureFlag
     *
     * @throws InvalidArgumentException
     */
    public function update(UpdateStruct $struct): FeatureFlag
    {
        $feature  = $this->handler->update($struct);
        $cacheKey = $this->getCacheKey($struct->getFeature()->scope, $struct->getFeature()->identifier);

        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheItem->set($feature);

        $this->cache->save($cacheItem);
        $this->cache->deleteItem($this->getCacheKey($feature->scope));

        return $feature;
    }

    /**
     * Gets the feature by identifier and scope.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return FeatureFlag
     *
     * @throws InvalidArgumentException
     */
    public function load(string $identifier, string $scope): ?FeatureFlag
    {
        $cacheItem = $this->cache->getItem($this->getCacheKey($scope, $identifier));

        if ($cacheItem->isHit() && $cacheItem->get() instanceof FeatureFlag) {
            return $cacheItem->get();
        }

        $feature = $this->handler->load($identifier, $scope);

        if ($feature instanceof FeatureFlag) {
            $cacheItem->set($feature);
            $this->cache->save($cacheItem);
        }

        return $feature;
    }

    /**
     * Removes the persisted flag for the feature.
     *
     * @param FeatureValue $feature
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function delete(FeatureValue $feature): void
    {
        $this->cache->deleteItems([
            $this->getCacheKey($feature->scope),
            $this->getCacheKey($feature->scope, $feature->identifier),
        ]);

        $this->handler->delete($feature);
    }

    /**
     * Lists stored features.
     *
     * @param string $scope
     *
     * @return array|FeatureFlag[]
     *
     * @throws InvalidArgumentException
     */
    public function list(string $scope): array
    {
        $cacheItem = $this->cache->getItem($this->getCacheKey($scope));

        if ($cacheItem->isHit() && $cacheItem->get() instanceof FeatureFlag) {
            return $cacheItem->get();
        }

        $featureList = $this->handler->list($scope);

        $cacheItem->set($featureList);
        $this->cache->save($cacheItem);

        return $featureList;
    }

    protected function getCacheKey(string $scope, string $identifier = null)
    {
        if (!$identifier) {
            $identifier = '--list';
        }

        return sprintf('intprog-feature-flag-%s-%s', $scope, $identifier);
    }
}
