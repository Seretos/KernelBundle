<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.06.2016
 * Time: 00:50
 */

namespace http\KernelBundle\core;

use http\KernelBundle\factory\KernelFactory;
use http\KernelBundle\interfaces\BundleInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseKernel {
    private $debug;
    /**
     * @var ContainerInterface
     */
    private $container;
    private $cacheDir;
    private $confDir;
    private $factory;

    public function __construct (KernelFactory $factory, $confDir, $cacheDir, $debug = false) {
        $this->factory = $factory;
        $this->debug = $debug;
        $this->cacheDir = $cacheDir;
        $this->confDir = $confDir;
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles () {
        $bundles = [];

        return $bundles;
    }

    public function initialize () {
        $bundles = $this->registerBundles();
        $this->initializeContainer($bundles);
    }

    public function getContainer () {
        return $this->container;
    }

    /**
     * @param BundleInterface[] $bundles
     */
    protected function initializeContainer ($bundles = []) {
        $class = 'WebApp'.($this->debug ? 'Debug' : '').'Container';

        $cache = $this->factory->createConfigCache($this->cacheDir.$class.'.php', $this->debug);
        if (!$cache->isFresh()) {
            $this->buildContainer($class, $cache, $bundles);
        }

        $this->container = $this->factory->createContainer($cache, $class);
    }

    /**
     * @param                   $class
     * @param ConfigCache       $cache
     * @param BundleInterface[] $bundles
     */
    protected function buildContainer ($class, ConfigCache $cache, $bundles) {
        $container = $this->factory->createContainerBuilder();
        foreach ($bundles as $bundle) {
            if (file_exists($bundle->getPath().'/Resources/config/services.yml')) {
                $loader = $this->factory->createYamlFileLoader($container, $bundle->getPath().'/Resources/config/');
                $loader->load('services.yml');
            }
        }
        $loader = $this->factory->createYamlFileLoader($container, $this->confDir);
        $loader->load('services.yml');
        $container->compile();

        $dumper = $this->factory->createDumper($container);

        $content = $dumper->dump(['class' => $class,
                                  'base_class' => Container::class,
                                  'file' => $cache->getPath(),
                                  'debug' => $this->debug]);

        $cache->write($content, $container->getResources());
    }
}