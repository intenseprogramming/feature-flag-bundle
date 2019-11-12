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
        $treeBuilder = new TreeBuilder('int_prog_feature_flag');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->booleanNode('allow_cookie_manipulation')
                ->info('Allow users to set a cookie to enable features (check docs for varnish support!).')
                ->defaultValue(false)
            ->end();

        /** @var ArrayNodeDefinition $featuresNode */
        $featuresNode = $rootNode->children()
            ->arrayNode('features')
                ->prototype('array');

        $this->addTranslatableString($featuresNode, 'name');
        $this->addTranslatableString($featuresNode, 'description');

        $featuresNode->children()
            ->scalarNode('identifier')
                ->info('The identifier used for checking the state of the flag.')
                ->cannotBeEmpty()
                ->isRequired()
            ->end()
            ->booleanNode('default')
                ->info('The default state of the feature.')
                ->defaultFalse()
            ->end()
        //    ->arrayNode('tags')
        //        ->info('Tags can be used to limit access over control of features.')
        //        ->defaultValue([])
        //        ->prototype('scalar')
        //    ->end()
        ;

        return $treeBuilder;
    }

    private function addTranslatableString(ArrayNodeDefinition $arrayNode, string $key): void
    {
        $arrayNode
            ->children()
                ->arrayNode($key)
                    ->info('Can contain a static string or id and context to support multiple languages.')
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
