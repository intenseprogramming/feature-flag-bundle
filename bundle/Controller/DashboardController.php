<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-27 07:51 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class DashboardController.
 *
 * @package   IntProg\FeatureFlagBundle\Controller
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class DashboardController extends Controller
{
    /** @var AuthorizationCheckerInterface $authorizationChecker */
    protected $authorizationChecker;

    /** @var array $groupsBySiteaccess */
    protected $groupsBySiteaccess;

    /** @var array $featureDefinitions */
    protected $featureDefinitions;

    /**
     * DashboardController constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param array                         $groupsBySiteaccess
     * @param array                         $featureDefinitions
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        array $groupsBySiteaccess,
        array $featureDefinitions
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->groupsBySiteaccess   = $groupsBySiteaccess;
        $this->featureDefinitions   = $featureDefinitions;
    }

    /**
     * Renders the dashboard controlling feature flags.
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        $siteaccessList      = [];
        $siteaccessGroupList = [];

        foreach ($this->groupsBySiteaccess as $siteaccess => $groups) {
            $limitation = ['valueObject' => new SiteAccess($siteaccess)];
            $attribute  = new Attribute('intprog_feature_flag', 'change', $limitation);
            $canChange  = $this->authorizationChecker->isGranted($attribute);

            $siteaccessList[$siteaccess] = $canChange;

            foreach ($groups as $group) {
                if (!$canChange) {
                    $siteaccessGroupList[$group] = false;
                } elseif ($canChange && !isset($siteaccessGroupList[$group])) {
                    $siteaccessGroupList[$group] = true;
                }
            }
        }

        return $this->render(
            '@ezdesign/feature_flag/dashboard.html.twig',
            [
                'siteaccessList'      => $siteaccessList,
                'siteaccessGroupList' => $siteaccessGroupList,
                'featureDefinitions'  => $this->featureDefinitions,
            ]
        );
    }

    public function setFeatureFlag(): JsonResponse
    {
        // TODO: set feature flag by identifier and scope.
    }

    public function resetFeatureFlag(string $feature): JsonResponse
    {
        // TODO: remove entry of feature flag by identifier and scope.
    }
}
