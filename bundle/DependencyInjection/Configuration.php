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
                ->validate()
                    ->ifTrue(static function ($array) {
                        $identifiers = array_keys($array);

                        foreach ($identifiers as $identifier) {
                            if (!preg_match('/^[a-z0-9_]+$/', $identifier)) {
                                return true;
                            }
                        }

                        return false;
                    })
                    ->thenInvalid('Feature keys may only consist of lowercase letters, numbers and underscores.')
                ->end()
                ->prototype('array');

        $this->addTranslatableString($featuresNode, 'name');
        $this->addTranslatableString($featuresNode, 'description');

        $children = $featuresNode->children();

        $children->arrayNode('groups')
            ->info('The groups of the feature used for policy limitations.')
            ->defaultValue([])
            ->prototype('scalar');
        $children->booleanNode('exposed')
            ->info('Sets the feature to be exposed to the frontend.')
            ->defaultFalse();
        $children->booleanNode('default')
            ->info('The default state of the feature.')
            ->defaultFalse();

        return $treeBuilder;
    }

    private function addTranslatableString(ArrayNodeDefinition $arrayNode, string $key): void
    {
        $subNodeDefinition = $arrayNode
            ->children()
            ->arrayNode($key);

        $nodeBuilder = $subNodeDefinition
            ->info('Can contain a static string or id and context to support multiple languages.')
            ->children();

        $nodeBuilder->scalarNode('id');
        $nodeBuilder->scalarNode('context');

        $subNodeDefinition
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
