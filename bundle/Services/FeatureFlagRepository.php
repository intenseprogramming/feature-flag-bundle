<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 07:26 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Services;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use IntProg\FeatureFlagBundle\API\Repository\FeatureFlagService;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FeatureFlagRepository.
 *
 * @package   IntProg\FeatureFlagBundle\Services
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class FeatureFlagRepository implements SiteAccessAware
{
    /** @var FeatureFlagService $featureFlagService */
    protected $featureFlagService;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var array $groupsBySiteaccess */
    protected $groupsBySiteaccess;

    /** @var array $featureDefinitions */
    protected $featureDefinitions;

    /** @var SiteAccess $siteaccess */
    protected $siteaccess;

    /** @var array $featureFlagsByScope */
    protected $featureFlagsByScope = ['_temp_' => []];

    /**
     * FeatureFlagRepository constructor.
     *
     * @param FeatureFlagService  $featureFlagService
     * @param TranslatorInterface $translator
     * @param array               $featureDefinitions
     * @param array               $groupsBySiteaccess
     */
    public function __construct(
        FeatureFlagService $featureFlagService,
        TranslatorInterface $translator,
        array $featureDefinitions,
        array $groupsBySiteaccess
    )
    {
        $this->featureFlagService = $featureFlagService;
        $this->translator         = $translator;
        $this->featureDefinitions = $featureDefinitions;
        $this->groupsBySiteaccess = $groupsBySiteaccess;
    }

    /**
     * Sets the siteaccess for scope awareness.
     * Call is not defined in service but will trigger on scope change instead.
     *
     * @param SiteAccess|null $siteAccess
     *
     * @return void
     */
    public function setSiteAccess(SiteAccess $siteAccess = null): void
    {
        if ($siteAccess !== null) {
            $this->siteaccess = $siteAccess;
        }
    }

    /**
     * Return true if the feature with $identifier is enabled.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function isEnabled(string $identifier): bool
    {
        return $this->get($identifier)->enabled;
    }

    /**
     * Return true if the feature with $identifier is disabled.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function isDisabled(string $identifier): bool
    {
        return !$this->isEnabled($identifier);
    }

    /**
     * Returns a feature by $identifier.
     *
     * @param string $identifier
     *
     * @return FeatureFlag
     *
     * @internal This method does not notify the cache and should not be used for cache sensitive resources.
     */
    public function get(string $identifier): FeatureFlag
    {
        foreach ($this->getWeightedActiveScopes() as $scope) {
            if (!isset($this->featureFlagsByScope[$scope])) {
                if ($scope === '_definition_') { // _definition_ is not a valid scope (_temp_ is always defined)
                    continue;
                }

                $this->featureFlagsByScope[$scope] = $this->featureFlagService->list($scope);
            }

            if (isset($this->featureFlagsByScope[$scope][$identifier])) {
                return $this->featureFlagsByScope[$scope][$identifier];
            }
        }

        if (!isset($this->featureFlagsByScope['_definition_'])) {
            foreach ($this->featureDefinitions as $definitionIdentifier => $featureDefinition) {
                $this->featureFlagsByScope['_definition_'][] = new FeatureFlag([
                    'identifier'  => $definitionIdentifier,
                    'scope'       => '_definition_',
                    'name'        => $this->translate($featureDefinition['name']),
                    'description' => $this->translate($featureDefinition['description']),
                    'default'     => $featureDefinition['default'],
                    'enabled'     => $featureDefinition['default'],
                ]);
            }
        }

        trigger_error(
            sprintf('Feature with identifier "%s" is in neither storage, cache or definition!', $identifier),
            E_USER_WARNING
        );

        return new FeatureFlag([
            'identifier' => $identifier,
            'scope'      => '_not_found_',
            'default'    => false,
            'enabled'    => false,
        ]);
    }

    /**
     * Temporary enables a feature by $identifier. Will reset after calling FeatureFlagService::reset().
     *
     * @param string $identifier
     *
     * @return void
     */
    public function enabledFeature(string $identifier): void
    {
        $this->featureFlagsByScope['_temp_'][$identifier] = new FeatureFlag([
            'identifier' => $identifier,
            'scope'      => '_temp_',
            'enabled'    => true,
        ]);
    }

    /**
     * Temporary disables a feature by $identifier. Will reset after calling FeatureFlagService::reset().
     *
     * @param string $identifier
     *
     * @return void
     */
    public function disabledFeature(string $identifier): void
    {
        $this->featureFlagsByScope['_temp_'][$identifier] = new FeatureFlag([
            'identifier' => $identifier,
            'scope'      => '_temp_',
            'enabled'    => false,
        ]);
    }

    /**
     * Resets all temporary enabled/disabled features.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->featureFlagsByScope['_temp_'] = [];
    }

    /**
     * Rebuilds known features.
     *
     * @param bool $global
     *
     * @return void
     */
    public function rebuildFeature(bool $global = true): void
    {
    }

    /**
     * Returns a list of scopes to check for the current siteaccess.
     *
     * @return array
     */
    private function getWeightedActiveScopes(): array
    {
        $scopes = [
            '_temp_',
            'global',
            $this->siteaccess->name,
        ];

        foreach ($this->groupsBySiteaccess[$this->siteaccess->name] ?? [] as $siteaccessGroup) {
            $scopes[] = $siteaccessGroup;
        }

        $scopes[] = 'default';
        $scopes[] = '_definition_';

        return $scopes;
    }

    /**
     * Checks if field of feature definition is translatable and returns the translated string.
     *
     * @param array $part The part containing "id" and "context" for translation.
     *
     * @return mixed|string Returns the translated string or $part['id'] if context is null.
     */
    private function translate(array $part)
    {
        return $part['context'] ? $this->translator->trans($part['id'], [], $part['context']) : $part['id'];
    }
}
