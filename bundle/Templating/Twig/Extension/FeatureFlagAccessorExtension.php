<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-27 07:24 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Templating\Twig\Extension;

use IntProg\FeatureFlagBundle\Services\FeatureFlagRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class FeatureFlagAccessor.
 *
 * @package   IntProg\IntProgFeatureFlagBundle\Templating\Twig
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class FeatureFlagAccessorExtension extends AbstractExtension
{
    /** @var FeatureFlagRepository $featureFlagRepository */
    protected $featureFlagRepository;

    /**
     * FeatureFlagAccessorExtension constructor.
     *
     * @param FeatureFlagRepository $featureFlagRepository
     */
    public function __construct(FeatureFlagRepository $featureFlagRepository)
    {
        $this->featureFlagRepository = $featureFlagRepository;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'is_feature_enabled',
                function (string $identifier, string $scope = null) {
                    return $this->featureFlagRepository->isEnabled($identifier, $scope);
                },
                ['needs_environment' => false]
            ),
            new TwigFunction(
                'is_feature_disabled',
                function (string $identifier, string $scope = null) {
                    return $this->featureFlagRepository->isDisabled($identifier, $scope);
                },
                ['needs_environment' => false]
            ),
            new TwigFunction(
                'get_feature',
                function (string $identifier, string $scope = null) {
                    return $this->featureFlagRepository->get($identifier, $scope);
                },
                ['needs_environment' => false]
            )
        ];
    }
}
