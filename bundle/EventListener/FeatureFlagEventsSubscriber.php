<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-01-03 10:57 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\EventListener;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use IntProg\FeatureFlagBundle\API\Repository\Events\FeatureFlag\CreatedStateEvent;
use IntProg\FeatureFlagBundle\API\Repository\Events\FeatureFlag\RemovedStateEvent;
use IntProg\FeatureFlagBundle\API\Repository\Events\FeatureFlag\UpdatedStateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FeatureFlagEventsSubscriber.
 *
 * @package   IntProg\FeatureFlagBundle\EventListener
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2020 Intense Programming
 */
class FeatureFlagEventsSubscriber implements EventSubscriberInterface
{
    /** @var PurgeClientInterface */
    protected $purgeClient;

    public function __construct(PurgeClientInterface $purgeClient)
    {
        $this->purgeClient = $purgeClient;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CreatedStateEvent::class => 'onFeatureFlagStateCreated',
            RemovedStateEvent::class => 'onFeatureFlagStateRemoved',
            UpdatedStateEvent::class => 'onFeatureFlagStateUpdated',
        ];
    }

    /**
     * Handles the http-cache handling after a state has been defined.
     *
     * @param CreatedStateEvent $event
     *
     * @return void
     */
    public function onFeatureFlagStateCreated(CreatedStateEvent $event): void
    {
        $this->purgeClient->purge(['ipff-' . $event->getScope() . '-' . $event->getIdentifier()]);
    }

    /**
     * Handles the http-cache handling after a state has been reset.
     *
     * @param RemovedStateEvent $event
     *
     * @return void
     */
    public function onFeatureFlagStateRemoved(RemovedStateEvent $event): void
    {
        $this->purgeClient->purge(['ipff-' . $event->getScope() . '-' . $event->getIdentifier()]);
    }

    /**
     * Handles the http-cache handling after a state has been updated.
     *
     * @param UpdatedStateEvent $event
     *
     * @return void
     */
    public function onFeatureFlagStateUpdated(UpdatedStateEvent $event): void
    {
        $this->purgeClient->purge(['ipff-' . $event->getScope() . '-' . $event->getIdentifier()]);
    }
}
