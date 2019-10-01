<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-10-01 07:22 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AdminFeatureFlagTabListener.
 *
 * @package   IntProg\FeatureFlagBundle\EventListener
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class AdminFeatureFlagTabListener implements EventSubscriberInterface
{
    public const ITEM__MIGRATION = 'main__admin__feature_flag';

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var FactoryInterface */
    protected $factory;

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => 'onMenuConfigure',
        ];
    }

    /**
     * Sets the authorization checker.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     *
     * @return void
     */
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker): void
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Sets the translator interface.
     *
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * Sets the factory interface for the tab.
     *
     * @param FactoryInterface $factory
     *
     * @return void
     */
    public function setFactory(FactoryInterface $factory): void
    {
        $this->factory = $factory;
    }

    /**
     * Adds the bookmark button to the menu.
     *
     * @param ConfigureMenuEvent $event
     *
     * @return void
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        if ($this->authorizationChecker->isGranted(new Attribute('intprog_feature_flag', 'change'))) {
            $adminMenu = $event->getMenu()->getChild(MainMenuBuilder::ITEM_ADMIN);

            $adminMenu->addChild(
                $this->factory->createItem(
                    self::ITEM__MIGRATION,
                    [
                        'label'           => $this->translator->trans('menu.button.text', [], 'feature_flag'),
                        'route'           => 'intprog_featureFlag_dashboard',
                        'routeParameters' => [],
                    ]
                )
            );
        }
    }
}
