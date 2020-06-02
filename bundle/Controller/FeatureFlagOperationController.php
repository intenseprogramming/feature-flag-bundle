<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-11-07 08:16 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\Controller;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use IntProg\FeatureFlagBundle\API\FeatureFlagRepository;
use IntProg\FeatureFlagBundle\API\Repository\FeatureFlagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class FeatureFlagOperationController.
 *
 * @package   IntProg\FeatureFlagBundle\Controller
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class FeatureFlagOperationController extends AbstractController
{
    /** @var AuthorizationCheckerInterface $authorizationChecker */
    protected $authorizationChecker;

    /** @var FeatureFlagRepository $featureFlagRepository */
    protected $featureFlagRepository;

    /** @var FeatureFlagService $featureFlagService */
    protected $featureFlagService;

    /** @var array $featureDefinitions */
    protected $featureDefinitions;

    /**
     * FeatureFlagOperationController constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param FeatureFlagRepository         $featureFlagRepository
     * @param FeatureFlagService            $featureFlagService
     * @param array                         $featureDefinitions
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FeatureFlagRepository $featureFlagRepository,
        FeatureFlagService $featureFlagService,
        array $featureDefinitions
    )
    {
        $this->authorizationChecker  = $authorizationChecker;
        $this->featureFlagRepository = $featureFlagRepository;
        $this->featureFlagService    = $featureFlagService;
        $this->featureDefinitions    = $featureDefinitions;
    }

    /**
     * Returns a json response containing all available features and their information.
     *
     * @param string $scope
     *
     * @return JsonResponse
     */
    public function list(string $scope): JsonResponse
    {
        if (!$this->authorizationChecker->isGranted(new Attribute('intprog_feature_flag', 'dashboard'))) {
            throw new UnauthorizedException('intprog_feature_flag', 'dashboard');
        }

        $featureList = [];

        foreach ($this->featureDefinitions as $featureDefinition) {
            $identifier  = $featureDefinition['identifier'];
            $featureFlag = $this->featureFlagRepository->get($identifier, $scope);

            try {
                $enabled = $this->featureFlagService->load($identifier, $scope)->enabled;
            } catch (NotFoundException $exception) {
                $enabled = null;
            }

            $featureList[] = [
                'identifier'  => $identifier,
                'name'        => $featureFlag->name,
                'description' => $featureFlag->description,
                'default'     => $featureFlag->default,
                'enabled'     => $enabled,
                'scope'       => $scope,
                'fromEnabled' => $featureFlag->enabled,
                'fromScope'   => $featureFlag->scope,
            ];
        }

        return new JsonResponse($featureList);
    }

    public function change(Request $request): Response
    {
        $target = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        try {
            $featureFlag           = $this->featureFlagService->load($target['identifier'], $target['scope']);
            $updateStruct          = $this->featureFlagService->newFeatureFlagUpdateStruct($featureFlag);
            $updateStruct->enabled = $target['state'];

            var_dump($featureFlag);

            $this->featureFlagService->update($updateStruct);
        } catch (NotFoundException $exception) {
            $createStruct             = $this->featureFlagService->newFeatureFlagCreateStruct();
            $createStruct->scope      = $target['scope'];
            $createStruct->identifier = $target['identifier'];
            $createStruct->enabled    = $target['state'];

            $this->featureFlagService->create($createStruct);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    public function reset(Request $request): Response
    {
        $target = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $featureFlag = $this->featureFlagService->load($target['identifier'], $target['scope']);
        $this->featureFlagService->delete($featureFlag);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
