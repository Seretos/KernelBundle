<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.06.2016
 * Time: 00:50
 */

namespace http\KernelBundle\core;


use http\KernelBundle\factory\KernelFactory;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
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

    public function initialize () {
        $this->initializeContainer();
    }

    public function getContainer () {
        return $this->container;
    }

    protected function initializeContainer () {
        $class = 'WebApp'.($this->debug ? 'Debug' : '').'Container';

        $cache = $this->factory->createConfigCache($this->cacheDir.$class.'.php', $this->debug);
        if (!$cache->isFresh()) {
            $this->buildContainer($class, $cache);
        }

        $this->container = $this->factory->createContainer($cache, $class);
    }

    protected function buildContainer ($class, ConfigCache $cache) {
        $container = $this->factory->createContainerBuilder();
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