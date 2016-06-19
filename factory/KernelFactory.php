<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.06.2016
 * Time: 01:45
 */

namespace http\KernelBundle\factory;


use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class KernelFactory {
    public function createConfigCache ($file, $debug) {
        return new ConfigCache($file, $debug);
    }

    public function createContainerBuilder () {
        return new ContainerBuilder();
    }

    public function createYamlFileLoader (ContainerBuilder $container, $configDir) {
        return new YamlFileLoader($container, new FileLocator($configDir));
    }

    public function createDumper (ContainerBuilder $container) {
        return new PhpDumper($container);
    }

    public function createContainer (ConfigCache $cache, $class) {
        require_once $cache->getPath();

        return new $class();
    }
}