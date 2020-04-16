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

use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use IntProg\FeatureFlagBundle\API\FeatureFlagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class DashboardController.
 *
 * @package   IntProg\FeatureFlagBundle\Controller
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class DashboardController extends AbstractController
{
    /** @var AuthorizationCheckerInterface $authorizationChecker */
    protected $authorizationChecker;

    /** @var FeatureFlagRepository $featureFlagRepository */
    protected $featureFlagRepository;

    /** @var array $groupsBySiteaccess */
    protected $groupsBySiteaccess;

    /** @var array $featureDefinitions */
    protected $featureDefinitions;

    /**
     * DashboardController constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param FeatureFlagRepository         $featureFlagRepository
     * @param array                         $groupsBySiteaccess
     * @param array                         $featureDefinitions
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FeatureFlagRepository $featureFlagRepository,
        array $groupsBySiteaccess,
        array $featureDefinitions
    )
    {
        $this->authorizationChecker  = $authorizationChecker;
        $this->featureFlagRepository = $featureFlagRepository;
        $this->groupsBySiteaccess    = $groupsBySiteaccess;
        $this->featureDefinitions    = $featureDefinitions;
    }

    /**
     * Renders the dashboard controlling feature flags.
     *
     * @return Response
     *
     * @throws UnauthorizedException
     */
    public function dashboard(): Response
    {
        if (!$this->authorizationChecker->isGranted(new Attribute('intprog_feature_flag', 'dashboard'))) {
            throw new UnauthorizedException('intprog_feature_flag', 'dashboard');
        }

        return $this->render(
            '@ezdesign/feature_flag/dashboard.html.twig',
            [
                'scopes'             => $this->featureFlagRepository->getAllScopes(),
                'featureDefinitions' => $this->featureDefinitions,
            ]
        );
    }
}
