<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-26 07:52 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\ScopeChangeEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use IntProg\FeatureFlagBundle\API\FeatureFlagRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ConfigurationScopeListener.
 *
 * @package   IntProg\FeatureFlagBundle\EventListener
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class ConfigurationScopeListener implements EventSubscriberInterface
{
    /** @var FeatureFlagRepository $featureFlagRepository */
    protected $featureFlagRepository;

    /**
     * ConfigurationScopeListener constructor.
     *
     * @param FeatureFlagRepository $featureFlagRepository
     */
    public function __construct(FeatureFlagRepository $featureFlagRepository)
    {
        $this->featureFlagRepository = $featureFlagRepository;
    }

    /**
     * Returns the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::CONFIG_SCOPE_CHANGE  => ['onConfigurationScopeChange', 100],
            MVCEvents::CONFIG_SCOPE_RESTORE => ['onConfigurationScopeChange', 100],
        ];
    }

    /**
     * Sets the scope to the feature flag repository.
     *
     * @param ScopeChangeEvent $event
     *
     * @return void
     */
    public function onConfigurationScopeChange(ScopeChangeEvent $event): void
    {
        $this->featureFlagRepository->setSiteAccess($event->getSiteAccess());
    }
}
