<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-04-13 01:05 am
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\Services;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator\DispatcherTagger;
use IntProg\FeatureFlagBundle\API\Repository\FeatureFlagService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureFlagRepositoryTest extends TestCase
{
    /**
     * @param bool $debugEnabled
     *
     * @return FeatureFlagRepository
     */
    public function getFeatureRepository(bool $debugEnabled = false): FeatureFlagRepository
    {
        $featureFlagService = $this->createMock(FeatureFlagService::class);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static function ($id, $variables, $context) {
            if ($context === 'messages' && $id === 'trans.name') {
                return 'translated name';
            }
            if ($context === 'messages' && $id === 'trans.description') {
                return 'translated description';
            }

            return $id;
        });

        $tagger = $this->createMock(DispatcherTagger::class);

        $repository = new FeatureFlagRepository(
            $featureFlagService,
            $translator,
            $tagger,
            [
                'test_feature'            => [
                    'identifier'  => 'test_feature',
                    'name'        => ['id' => 'test', 'context' => null],
                    'description' => ['id' => 'test', 'context' => null],
                    'default'     => true,
                    'exposed'     => false,
                ],
                'test_feature_translated' => [
                    'identifier'  => 'test_feature_translated',
                    'name'        => ['id' => 'trans.name', 'context' => 'messages'],
                    'description' => ['id' => 'trans.description', 'context' => 'messages'],
                    'default'     => false,
                    'exposed'     => true,
                ],
            ],
            [
                'admin',
                'en',
                'en', // duplicate to prevent misconfiguration to influence performance.
            ],
            [
                'admin' => ['admin_group'],
                'en'    => ['frontend_group'],
            ],
            $debugEnabled
        );
        $repository->setSiteAccess(new SiteAccess('en'));

        return $repository;
    }

    /**
     * @return void
     * @test
     */
    public function regular_get_returns_feature_state_from_definition(): void
    {
        $this->assertEquals('_definition_', $this->getFeatureRepository()->get('test_feature')->scope);
    }

    /**
     * @return void
     * @test
     */
    public function requesting_undefined_feature_returns_inactive_not_found_feature_with_debug_disabled(): void
    {
        $featureFlag = $this->getFeatureRepository()->get('undefined_feature');

        $this->assertEquals('_not_found_', $featureFlag->scope);
        $this->assertEquals(false, $featureFlag->enabled);
    }

    /**
     * @return void
     * @test
     */
    public function requesting_undefined_feature_triggers_error_with_debug_enabled(): void
    {
        $this->expectWarning();

        $this->getFeatureRepository(true)->get('undefined_feature');
    }

    /**
     * @return void
     * @test
     */
    public function feature_name_and_description_are_translated_if_context_is_defined(): void
    {
        $featureFlag = $this->getFeatureRepository()->get('test_feature_translated');

        $this->assertEquals('translated name', $featureFlag->name);
        $this->assertEquals('translated description', $featureFlag->description);
    }

    /**
     * @return void
     * @test
     */
    public function temporary_activation_should_be_reversed_by_reset(): void
    {
        $repository = $this->getFeatureRepository();

        $defaultEnabled = $repository->get('test_feature');
        $defaultDisabled = $repository->get('test_feature_translated');

        $repository->disableFeature('test_feature');
        $repository->enableFeature('test_feature_translated');

        $temporaryEnabled = $repository->get('test_feature');
        $temporaryDisabled = $repository->get('test_feature_translated');

        $repository->reset();

        $resetEnabled = $repository->get('test_feature');
        $resetDisabled = $repository->get('test_feature_translated');

        $this->assertNotEquals($defaultEnabled->enabled, $temporaryEnabled->enabled);
        $this->assertEquals($defaultEnabled->enabled, $resetEnabled->enabled);
        $this->assertNotEquals($defaultDisabled->enabled, $temporaryDisabled->enabled);
        $this->assertEquals($defaultDisabled->enabled, $resetDisabled->enabled);
    }

    /**
     * @return void
     * @test
     */
    public function all_scopes_should_be_returned_when_requested(): void
    {
        $this->assertEquals(
            [
                'global',
                'admin',
                'en',
                'admin_group',
                'frontend_group',
                'default'
            ],
            $this->getFeatureRepository()->getAllScopes()
        );
    }

    /**
     * @return void
     * @test
     */
    public function only_expose_features_defined_to_be_exposed(): void
    {
        $this->assertEquals(
            [
                'test_feature_translated' => ['enabled' => false]
            ],
            $this->getFeatureRepository()->getExposedFeatureStates()
        );
    }

    /**
     * @return void
     * @test
     */
    public function check_for_disabled_should_invert_check_for_enabled(): void
    {
        $this->assertNotEquals(
            $this->getFeatureRepository()->isEnabled('test_feature'),
            $this->getFeatureRepository()->isDisabled('test_feature')
        );
    }
}
