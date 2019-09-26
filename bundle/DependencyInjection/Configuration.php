<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 08:54 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @package   IntProg\FeatureFlagBundle\DependencyInjection
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('int_prog_feature_flag');

        $rootNode->children()
            ->booleanNode('allow_cookie_manipulation')
                ->defaultValue(false)
            ->end();

        /** @var ArrayNodeDefinition $featuresNode */
        $featuresNode = $rootNode->children()
            ->arrayNode('features')
                ->prototype('array');

        $this->addTranslatableString($featuresNode, 'name');
        $this->addTranslatableString($featuresNode, 'description');

        $featuresNode->children()->scalarNode('identifier')->cannotBeEmpty()->isRequired();
        $featuresNode->children()->booleanNode('default')->defaultFalse();

        return $treeBuilder;
    }

    private function addTranslatableString(ArrayNodeDefinition $arrayNode, string $key): void
    {
        $arrayNode
            ->children()
                ->arrayNode($key)
                    ->children()
                        ->scalarNode('id')
                        ->end()
                        ->scalarNode('context')
                        ->end()
                    ->end()
                    ->beforeNormalization()
                        ->ifString()
                            ->then(static function (string $string) {
                                return [
                                    'id' => $string,
                                    'context' => null,
                                ];
                            });
    }
}
