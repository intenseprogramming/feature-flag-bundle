<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 07:35 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Core\Repository;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use IntProg\FeatureFlagBundle\API\Repository\FeatureFlagService as ApiFeatureFlagService;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use IntProg\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use IntProg\FeatureFlagBundle\Spi\Persistence\FeatureFlag as SpiFeature;
use IntProg\FeatureFlagBundle\Spi\Persistence\Handler;
use IntProg\FeatureFlagBundle\Spi\Persistence\UpdateStruct;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FeatureFlagService.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Repository
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class FeatureFlagService implements ApiFeatureFlagService
{
    /** @var Handler $handler */
    protected $handler;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var AuthorizationCheckerInterface $authorizationChecker */
    protected $authorizationChecker;

    /** @var string $featureDefinitions */
    protected $featureDefinitions;

    /**
     * FeatureFlagService constructor.
     *
     * @param Handler                       $handler
     * @param TranslatorInterface           $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param array                         $featureDefinitions
     */
    public function __construct(
        Handler $handler,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker,
        array $featureDefinitions
    )
    {
        $this->handler              = $handler;
        $this->featureDefinitions   = $featureDefinitions;
        $this->authorizationChecker = $authorizationChecker;
        $this->translator           = $translator;
    }

    /**
     * Loads the feature for the current siteaccess.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return FeatureFlag
     *
     * @throws NotFoundException
     */
    public function load(string $identifier, string $scope): FeatureFlag
    {
        $spiFeature = $this->handler->load($identifier, $scope);

        if (!$spiFeature) {
            throw new NotFoundException('FeatureFlag', [
                'identifier' => $identifier,
                'scope'      => $scope,
            ]);
        }

        return $this->generateFeatureFromSpi($spiFeature);
    }

    /**
     * Loads a list of features for the current siteaccess.
     *
     * @param string $scope
     *
     * @return array
     */
    public function list(string $scope): array
    {
        $features = [];

        foreach ($this->handler->list($scope) as $spiFeature) {
            $features[$spiFeature->identifier] = $this->generateFeatureFromSpi($spiFeature);
        }

        return $features;
    }

    /**
     * Generates a new feature flag create struct.
     *
     * @return CreateStruct
     */
    public function newFeatureFlagCreateStruct(): CreateStruct
    {
        return new CreateStruct();
    }

    /**
     * Creates the feature for the current siteaccess.
     *
     * @param CreateStruct $createStruct
     *
     * @return FeatureFlag
     *
     * @throws InvalidArgumentException
     */
    public function create(CreateStruct $createStruct): FeatureFlag
    {
        // TODO: add permission check

        if (!isset($this->featureDefinitions[$createStruct->identifier])) {
            throw new InvalidArgumentException(
                '$createStruct->identifier',
                sprintf('expected one of "%s"', implode('", "', array_keys($this->featureDefinitions)))
            );
        }

        $spiFeature = $this->handler->create($createStruct);

        return $this->generateFeatureFromSpi($spiFeature);
    }

    /**
     * Generates a new feature flag update struct from $feature.
     *
     * @param FeatureFlag $feature
     *
     * @return UpdateStruct
     */
    public function newFeatureFlagUpdateStruct(FeatureFlag $feature): UpdateStruct
    {
        return new UpdateStruct(new SpiFeature([
            'identifier' => $feature->identifier,
            'scope'      => $feature->scope,
            'enabled'    => $feature->enabled,
        ]));
    }

    /**
     * Updates the feature for the current siteaccess.
     *
     * @param UpdateStruct $updateStruct
     *
     * @return FeatureFlag
     */
    public function update(UpdateStruct $updateStruct): FeatureFlag
    {
        // TODO: add permission check

        $spiFeature = $this->handler->update($updateStruct);

        return $this->generateFeatureFromSpi($spiFeature);
    }

    /**
     * Deletes the feature for the current siteaccess.
     *
     * @param FeatureFlag $feature
     *
     * @return mixed
     */
    public function delete(FeatureFlag $feature): void
    {
        // TODO: add permission check

        $this->handler->delete($feature);
    }

    /**
     * Generating a feature from spi-feature applying translations from configuration.
     *
     * @param SpiFeature $feature The feature return from SpiHandler.
     *
     * @return FeatureFlag
     */
    private function generateFeatureFromSpi(SpiFeature $feature): FeatureFlag
    {
        $featureDefinition = $this->featureDefinitions[$feature->identifier];

        return new FeatureFlag([
            'identifier'  => $feature->identifier,
            'scope'       => $feature->scope,
            'name'        => $this->translate($featureDefinition['name']),
            'description' => $this->translate($featureDefinition['description']),
            'default'     => $featureDefinition['default'],
            'enabled'     => $feature->enabled,
        ]);
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
