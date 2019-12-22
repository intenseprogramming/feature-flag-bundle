<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-11-12 08:14 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\EventListener;

use EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvent;
use EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BuildSchemaListener.
 *
 * @package   IntProg\FeatureFlagBundle\EventListener
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class BuildSchemaListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $schemaPath;

    /**
     * BuildSchemaListener constructor.
     *
     * @param string $schemaPath
     */
    public function __construct(string $schemaPath)
    {
        $this->schemaPath = $schemaPath;
    }

    /**
     * Registers the schema build to add the schema to the system.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SchemaBuilderEvents::BUILD_SCHEMA => 'onBuildSchema',
        ];
    }

    /**
     * Adds the schema to the builder.
     *
     * @param SchemaBuilderEvent $event
     *
     * @return void
     */
    public function onBuildSchema(SchemaBuilderEvent $event): void
    {
        $event
            ->getSchemaBuilder()
            ->importSchemaFromFile($this->schemaPath);
    }
}
