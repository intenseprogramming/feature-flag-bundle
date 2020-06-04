<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-06-02 11:40 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\Core\Repository;

use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException as ApiInvalidArgumentException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use IntProg\FeatureFlagBundle\Spi\Persistence\FeatureFlag as SpiFeatureFlag;
use IntProg\FeatureFlagBundle\Spi\Persistence\Handler;
use JsonException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureFlagServiceTest extends TestCase
{
    /**
     * @return void
     * @throws JsonException
     * @throws NotFoundException
     * @test
     */
    public function load_should_return_feature_value(): void
    {
        $handler = $this->createMock(Handler::class);
        $handler->expects($this->once())->method('load')->with('identifier_1', 'scope')->willReturn(new SpiFeatureFlag([
            'identifier' => 'identifier_1',
            'scope'      => 'scope',
            'enabled'    => false,
        ]));

        $service = new FeatureFlagService(
            $handler,
            $this->createMock(TranslatorInterface::class),
            $this->createMock(PermissionResolver::class),
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $service->load('identifier_1', 'scope');
    }

    /**
     * @return void
     * @test
     */
    public function load_should_throw_not_found_exception_on_not_stored_flag(): void
    {
        $service = new FeatureFlagService(
            $this->createMock(Handler::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(PermissionResolver::class),
            []
        );

        $this->expectException(NotFoundException::class);

        $service->load('identifier_1', 'scope');
    }

    /**
     * @return void
     * @test
     * @throws JsonException
     */
    public function list_should_return_all_flags_ignoring_unknown_features(): void
    {
        $handler = $this->createMock(Handler::class);
        $handler->method('list')->willReturn([
            new SpiFeatureFlag(['identifier' => 'identifier_2', 'scope' => 'scope', 'enabled' => false]),
            new SpiFeatureFlag(['identifier' => 'identifier_3', 'scope' => 'scope', 'enabled' => false]),
            new SpiFeatureFlag(['identifier' => 'identifier_4', 'scope' => 'scope', 'enabled' => false]),
        ]);

        $service = new FeatureFlagService(
            $handler,
            $this->createMock(TranslatorInterface::class),
            $this->createMock(PermissionResolver::class),
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $list = $service->list('scope');

        $this->assertCount(2, $list, 'returned features should match a count of 2.');
    }

    /**
     * @return void
     * @throws ApiInvalidArgumentException|BadStateException|InvalidArgumentException|JsonException|NotFoundException|UnauthorizedException
     * @test
     */
    public function create_should_send_create_struct_to_handler_and_return_feature_value(): void
    {
        $handler = $this->createMock(Handler::class);
        $handler->expects($this->at(0))->method('create')->willReturn(new SpiFeatureFlag([
            'identifier' => 'identifier_1',
            'scope'      => 'scope',
            'enabled'    => false,
        ]));

        $permissionResolver = $this->createMock(PermissionResolver::class);
        $permissionResolver->method('canUser')->willReturn(true);

        $service = new FeatureFlagService(
            $handler,
            $this->createMock(TranslatorInterface::class),
            $permissionResolver,
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $createStruct             = $service->newFeatureFlagCreateStruct();
        $createStruct->identifier = 'identifier_1';
        $createStruct->scope      = 'scope';
        $createStruct->enabled    = false;

        $feature = $service->create($createStruct);

        $this->assertNotNull($feature, 'Create should return a feature value.');
    }

    /**
     * @return void
     *
     * @throws ApiInvalidArgumentException|BadStateException|JsonException|NotFoundException|UnauthorizedException
     * @test
     */
    public function update_should_send_update_struct_to_handler_and_return_new_feature_value(): void
    {
        $handler = $this->createMock(Handler::class);
        $handler->expects($this->at(0))->method('update')->willReturn(new SpiFeatureFlag([
            'identifier' => 'identifier_1',
            'scope'      => 'scope',
            'enabled'    => false,
        ]));

        $permissionResolver = $this->createMock(PermissionResolver::class);
        $permissionResolver->method('canUser')->willReturn(true);

        $service = new FeatureFlagService(
            $handler,
            $this->createMock(TranslatorInterface::class),
            $permissionResolver,
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $featureFlag = new FeatureFlag([
            'enabled' => true,
        ]);

        $updateStruct          = $service->newFeatureFlagUpdateStruct($featureFlag);
        $updateStruct->enabled = false;

        $feature = $service->update($updateStruct);

        $this->assertNotEquals($featureFlag->enabled, $feature->enabled);
    }

    /**
     * @return void
     *
     * @throws ApiInvalidArgumentException|BadStateException|JsonException|UnauthorizedException
     * @test
     */
    public function delete_should_send_delete_to_handler(): void
    {
        $handler = $this->createMock(Handler::class);
        $handler->expects($this->at(0))->method('delete')->with($this->callback(static function (SpiFeatureFlag $featureFlag) {
            return 'identifier_1' === $featureFlag->identifier && 'scope' === $featureFlag->scope;
        }));

        $permissionResolver = $this->createMock(PermissionResolver::class);
        $permissionResolver->method('canUser')->willReturn(true);

        $service = new FeatureFlagService(
            $handler,
            $this->createMock(TranslatorInterface::class),
            $permissionResolver,
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $featureFlag = new FeatureFlag([
            'identifier' => 'identifier_1',
            'scope'      => 'scope',
            'enabled'    => true,
        ]);

        $service->delete($featureFlag);
    }

    /**
     * @return void
     * @throws ApiInvalidArgumentException|BadStateException|InvalidArgumentException|JsonException|NotFoundException|UnauthorizedException
     * @test
     */
    public function create_will_throw_when_unknown_feature_is_tried_to_be_defined(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $permissionResolver = $this->createMock(PermissionResolver::class);
        $permissionResolver->method('canUser')->willReturn(true);

        $service            = new FeatureFlagService(
            $this->createMock(Handler::class),
            $this->createMock(TranslatorInterface::class),
            $permissionResolver,
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $createStruct = $service->newFeatureFlagCreateStruct();
        $createStruct->identifier = 'unknown_feature';

        $service->create($createStruct);
    }

    /**
     * @return void
     * @throws ApiInvalidArgumentException|BadStateException|InvalidArgumentException|JsonException|NotFoundException|UnauthorizedException
     * @test
     */
    public function create_will_throw_when_user_is_not_allowed_to_change_state(): void
    {
        $this->expectException(UnauthorizedException::class);

        $permissionResolver = $this->createMock(PermissionResolver::class);
        $permissionResolver->method('canUser')->willReturn(false);

        $service            = new FeatureFlagService(
            $this->createMock(Handler::class),
            $this->createMock(TranslatorInterface::class),
            $permissionResolver,
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $createStruct = $service->newFeatureFlagCreateStruct();

        $service->create($createStruct);
    }

    /**
     * @return void
     * @throws ApiInvalidArgumentException
     * @throws BadStateException
     * @throws JsonException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @test
     */
    public function update_will_throw_when_user_is_not_allowed_to_change_state(): void
    {
        $this->expectException(UnauthorizedException::class);

        $permissionResolver = $this->createMock(PermissionResolver::class);
        $permissionResolver->method('canUser')->willReturn(false);

        $service            = new FeatureFlagService(
            $this->createMock(Handler::class),
            $this->createMock(TranslatorInterface::class),
            $permissionResolver,
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $updateStruct = $service->newFeatureFlagUpdateStruct(new FeatureFlag());

        $service->update($updateStruct);
    }

    /**
     * @return void
     * @throws ApiInvalidArgumentException
     * @throws BadStateException
     * @throws JsonException
     * @throws UnauthorizedException
     * @test
     */
    public function delete_will_throw_when_user_is_not_allowed_to_change_state(): void
    {
        $this->expectException(UnauthorizedException::class);

        $permissionResolver = $this->createMock(PermissionResolver::class);
        $permissionResolver->method('canUser')->willReturn(false);

        $service            = new FeatureFlagService(
            $this->createMock(Handler::class),
            $this->createMock(TranslatorInterface::class),
            $permissionResolver,
            json_decode(file_get_contents(__DIR__ . '/../../../fixture/feature_configuration.json'), true, 512, JSON_THROW_ON_ERROR)
        );

        $service->delete(new FeatureFlag());
    }
}
