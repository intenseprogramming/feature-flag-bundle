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
        $container->setParameter('intprog.feature.flag.feature_list', $config['features'] ?? []);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('controller.yml');
        $loader->load('persistence.yml');
        $loader->load('event_listeners.yml');
        $loader->load('services.yml');
        $loader->load('templating.yml');
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
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/ezdesign.yml'));
        $container->prependExtensionConfig('ezdesign', $config);
        $container->addResource(new FileResource(__DIR__ . '/../Resources/config/ezdesign.yml'));
    }
}
