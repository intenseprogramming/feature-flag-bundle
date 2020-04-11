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

use IntProg\FeatureFlagBundle\API\FeatureFlagRepository;
use Twig\Environment;
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

    /** @var Environment $environment */
    protected $environment;

    /**
     * FeatureFlagAccessorExtension constructor.
     *
     * @param FeatureFlagRepository $featureFlagRepository
     * @param Environment           $environment
     */
    public function __construct(FeatureFlagRepository $featureFlagRepository, Environment $environment)
    {
        $this->featureFlagRepository = $featureFlagRepository;
        $this->environment           = $environment;
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
            ),
            new TwigFunction(
                'expose_features_json',
                function () {
                    return json_encode(
                        $this->featureFlagRepository->getExposedFeatureStates(),
                        JSON_THROW_ON_ERROR,
                        2
                    );
                },
                ['needs_environment' => false, 'is_safe' => ['html']]
            ),
            new TwigFunction(
                'expose_features_data_attributes',
                function () {
                    $features = [];
                    foreach ($this->featureFlagRepository->getExposedFeatureStates() as $identifier => $feature) {
                        $features[] = sprintf(
                            'data-%s="%s"',
                            str_replace('_', '-', $identifier),
                            $feature ? 'true' : 'false'
                        );
                    }

                    return implode(' ', $features);
                },
                ['needs_environment' => false, 'is_safe' => ['html']]
            ),
            new TwigFunction(
                'expose_features_javascript',
                function (string $variable = 'ipFeatureFlags') {
                    return $this->environment->render(
                        '@ezdesign/feature_flag/expose/javascript.html.twig',
                        [
                            'variable' => $variable,
                        ]
                    );
                },
                ['needs_environment' => false, 'is_safe' => ['html']]
            ),
        ];
    }
}
