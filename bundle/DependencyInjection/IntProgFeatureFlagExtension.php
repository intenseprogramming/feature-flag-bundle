<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-24 08:26 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * Class IntProgFeatureFlagExtension.
 *
 * @package   IntProg\FeatureFlagBundle\DependencyInjection
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class IntProgFeatureFlagExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'intprog.feature.flag.allow_cookie_manipulation',
            $config['allow_cookie_manipulation'] ?? false
        );

        $features = $config['features'] ?? [];
        foreach ($features as $identifier => $feature) {
            $features[$identifier]['identifier'] = $identifier;
        }
        $container->setParameter('intprog.feature.flag.feature_list', $features);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('controller.yml');
        $loader->load('persistence.yml');
        $loader->load('event_listeners.yml');
        $loader->load('services.yml');
        $loader->load('templating.yml');
        $loader->load('role.yml');
        $loader->load('view_cache.yml');
    }

    /**
     * Adds system configuration to the config load chain.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function prepend(ContainerBuilder $container): void
    {
        $configsDir = __DIR__ . '/../Resources/config/';
        $configs    = [
            'ezdesign.yml'   => 'ezdesign',
            'ezplatform.yml' => 'ezpublish',
        ];

        foreach ($configs as $file => $namespace) {
            $config = Yaml::parse(file_get_contents($configsDir . $file));
            $container->prependExtensionConfig($namespace, $config);
            $container->addResource(new FileResource($configsDir . $file));
        }

        $container->prependExtensionConfig(
            'bazinga_js_translation',
            [
                'active_domains' => [
                    'feature_flag',
                ]
            ]
        );
    }
}
