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
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator\DispatcherTagger;
use IntProg\FeatureFlagBundle\API\FeatureFlagRepository as ApiFeatureFlagRepository;
use IntProg\FeatureFlagBundle\API\Repository\FeatureFlagService;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use Symfony\Contracts\Translation\TranslatorInterface;
use function in_array;

/**
 * Class FeatureFlagRepository.
 *
 * @package   IntProg\FeatureFlagBundle\Services
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class FeatureFlagRepository implements ApiFeatureFlagRepository, SiteAccessAware
{
    /** @var FeatureFlagService $featureFlagService */
    protected $featureFlagService;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var DispatcherTagger $tagger */
    protected $tagger;

    /** @var array $groupsBySiteaccess */
    protected $groupsBySiteaccess;

    /** @var array $featureDefinitions */
    protected $featureDefinitions;

    /** @var array $siteaccessList */
    protected $siteaccessList;

    /** @var SiteAccess $siteaccess */
    protected $siteaccess;

    /** @var array $featureFlagsByScope */
    protected $featureFlagsByScope = ['_temp_' => []];

    /** @var bool $debugEnabled */
    protected $debugEnabled;

    /**
     * FeatureFlagRepository constructor.
     *
     * @param FeatureFlagService  $featureFlagService
     * @param TranslatorInterface $translator
     * @param DispatcherTagger    $tagger
     * @param array               $featureDefinitions
     * @param array               $siteaccessList
     * @param array               $groupsBySiteaccess
     * @param bool                $debugEnabled
     */
    public function __construct(
        FeatureFlagService $featureFlagService,
        TranslatorInterface $translator,
        DispatcherTagger $tagger,
        array $featureDefinitions,
        array $siteaccessList,
        array $groupsBySiteaccess,
        bool $debugEnabled
    )
    {
        $this->featureFlagService = $featureFlagService;
        $this->translator         = $translator;
        $this->tagger             = $tagger;
        $this->featureDefinitions = $featureDefinitions;
        $this->siteaccessList     = $siteaccessList;
        $this->groupsBySiteaccess = $groupsBySiteaccess;
        $this->debugEnabled       = $debugEnabled;
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
     * @param string      $identifier
     * @param string|null $scope
     *
     * @return bool
     */
    public function isEnabled(string $identifier, string $scope = null): bool
    {
        return $this->get($identifier, $scope)->enabled;
    }

    /**
     * Return true if the feature with $identifier is disabled.
     *
     * @param string      $identifier
     * @param string|null $scope
     *
     * @return bool
     */
    public function isDisabled(string $identifier, string $scope = null): bool
    {
        return !$this->isEnabled($identifier, $scope);
    }

    /**
     * Returns a feature by $identifier.
     *
     * @param string      $identifier
     * @param string|null $scope
     *
     * @return FeatureFlag
     *
     * @internal This method does not notify the cache and should not be used for cache sensitive resources.
     */
    public function get(string $identifier, string $scope = null): FeatureFlag
    {
        $weightedScopes = $this->getWeightedActiveScopes($scope);

        foreach ($weightedScopes as $weightedActiveScope) {
            if (!isset($this->featureFlagsByScope[$weightedActiveScope])) {
                if ($weightedActiveScope === '_definition_') { // _definition_ is not a valid scope (_temp_ is always defined)
                    continue;
                }

                $this->featureFlagsByScope[$weightedActiveScope] = $this->featureFlagService->list($weightedActiveScope);
            }
        }

        if (!isset($this->featureFlagsByScope['_definition_'])) {
            $this->featureFlagsByScope['_definition_'] = [];

            foreach ($this->featureDefinitions as $featureDefinition) {
                $definitionIdentifier = $featureDefinition['identifier'];

                $this->featureFlagsByScope['_definition_'][$definitionIdentifier] = new FeatureFlag([
                    'identifier'  => $definitionIdentifier,
                    'scope'       => '_definition_',
                    'name'        => $this->translate($featureDefinition['name']),
                    'description' => $this->translate($featureDefinition['description']),
                    'default'     => $featureDefinition['default'],
                    'enabled'     => $featureDefinition['default'],
                ]);
            }
        }

        $checkedScopes = [];
        foreach ($weightedScopes as $weightedActiveScope) {
            if (!in_array($weightedActiveScope, ['_temp_', '_definition_'])) {
                $checkedScopes[] = $weightedActiveScope;
            }

            if (isset($this->featureFlagsByScope[$weightedActiveScope][$identifier])) {
                $result = $this->featureFlagsByScope[$weightedActiveScope][$identifier];

                break;
            }
        }

        if (!isset($result)) {
            if ($this->debugEnabled) {
                trigger_error(
                    sprintf('Feature with identifier "%s" is in neither storage, cache or definition!', $identifier),
                    E_USER_WARNING
                );
            }

            $result = new FeatureFlag([
                'identifier' => $identifier,
                'scope'      => '_not_found_',
                'default'    => false,
                'enabled'    => false,
            ]);
        }

        $result->checkedScopes = $checkedScopes;

        $this->tagger->tag($result);

        return $result;
    }

    /**
     * Temporary enables a feature by $identifier. Will reset after calling FeatureFlagService::reset().
     *
     * @param string $identifier
     *
     * @return void
     */
    public function enableFeature(string $identifier): void
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
    public function disableFeature(string $identifier): void
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
     * Returns exposed features.
     *
     * @return array
     */
    public function getExposedFeatureStates(): array
    {
        $features = [];

        foreach ($this->featureDefinitions as $identifier =>  $featureDefinition) {
            if ($featureDefinition['exposed'] ?? false) {
                $features[$identifier] = [
                    'enabled' => $this->isEnabled($identifier),
                ];
            }
        }

        return $features;
    }

    /**
     * Returns all currently possible scopes.
     *
     * @return array
     */
    public function getAllScopes(): array
    {
        return array_merge(
            ['global'],
            array_unique($this->siteaccessList),
            array_reduce(
                $this->groupsBySiteaccess,
                static function ($carry, $groupList) {
                    foreach ($groupList as $group) {
                        if (!in_array($group, $carry, true)) {
                            $carry[] = $group;
                        }
                    }

                    return $carry;
                },
                []
            ),
            ['default']
        );
    }

    /**
     * Returns a list of scopes to check for the current siteaccess.
     *
     * @param string|null $scope
     *
     * @return array
     */
    private function getWeightedActiveScopes(string $scope = null): array
    {
        if ($scope === null) {
            $scope = $this->siteaccess->name;
        }

        $scopes = [
            '_temp_',
            'global',
            $scope,
        ];

        foreach ($this->groupsBySiteaccess[$scope] ?? [] as $siteaccessGroup) {
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
