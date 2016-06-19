<?php
use http\KernelBundle\factory\KernelFactory;
use http\KernelBundle\tests\factory\helper\Example;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.06.2016
 * Time: 02:11
 */
class KernelFactoryTest extends PHPUnit_Framework_TestCase {
    /**
     * @var KernelFactory
     */
    private $factory;

    protected function setUp () {
        $this->factory = new KernelFactory();
    }

    /**
     * @test
     */
    public function createConfigCache () {
        $cache = $this->factory->createConfigCache('path', false);
        $this->assertInstanceOf(ConfigCache::class, $cache);
    }

    /**
     * @test
     */
    public function createContainerBuilder () {
        $container = $this->factory->createContainerBuilder();
        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }

    /**
     * @test
     */
    public function createYamlFileLoader () {
        /* @var $mockContainer ContainerBuilder|PHPUnit_Framework_MockObject_MockObject */
        $mockContainer = $this->getMockBuilder(ContainerBuilder::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $loader = $this->factory->createYamlFileLoader($mockContainer, 'test');
        $this->assertInstanceOf(YamlFileLoader::class, $loader);
    }

    /**
     * @test
     */
    public function createDumper () {
        /* @var $mockContainer ContainerBuilder|PHPUnit_Framework_MockObject_MockObject */
        $mockContainer = $this->getMockBuilder(ContainerBuilder::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $dumper = $this->factory->createDumper($mockContainer);
        $this->assertInstanceOf(PhpDumper::class, $dumper);
    }

    /**
     * @test
     */
    public function createContainer () {
        /* @var $mockCache ConfigCache|PHPUnit_Framework_MockObject_MockObject */
        $mockCache = $this->getMockBuilder(ConfigCache::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $mockCache->expects($this->once())
                  ->method('getPath')
                  ->will($this->returnValue(__DIR__.'/helper/Example.php'));

        $this->factory->createContainer($mockCache, Example::class);
    }
}