<?php
use http\KernelBundle\core\BaseKernel;
use http\KernelBundle\factory\KernelFactory;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.06.2016
 * Time: 02:16
 */
class BaseKernelTest extends PHPUnit_Framework_TestCase {
    /**
     * @var BaseKernel
     */
    private $kernel;

    /**
     * @var KernelFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFactory;

    /**
     * @var ReflectionClass
     */
    private $reflection;

    protected function setUp () {
        $this->mockFactory = $this->getMockBuilder(KernelFactory::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $this->kernel = new BaseKernel($this->mockFactory, 'config', 'cache', true);

        $this->reflection = new ReflectionClass(BaseKernel::class);
    }

    /**
     * @test
     */
    public function initialize () {
        $mockCache = $this->getMockBuilder(ConfigCache::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createConfigCache')
                          ->with('cacheWebAppDebugContainer.php', true)
                          ->will($this->returnValue($mockCache));

        $mockCache->expects($this->once())
                  ->method('isFresh')
                  ->will($this->returnValue(false));

        $mockCache->expects($this->any())
                  ->method('getPath')
                  ->will($this->returnValue('path'));

        $this->buildContainerMock($mockCache, 'WebAppDebugContainer');

        $this->mockFactory->expects($this->once())
                          ->method('createContainer')
                          ->with($mockCache, 'WebAppDebugContainer')
                          ->will($this->returnValue('container'));

        $this->kernel->initialize();
        $this->assertSame('container', $this->kernel->getContainer());
    }

    /**
     * @test
     */
    public function buildContainer () {
        $mockCache = $this->getMockBuilder(ConfigCache::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $mockCache->expects($this->any())
                  ->method('getPath')
                  ->will($this->returnValue('path'));

        $this->buildContainerMock($mockCache);

        $method = $this->reflection->getMethod('buildContainer');
        $method->setAccessible(true);

        $method->invokeArgs($this->kernel, ['example', $mockCache]);
    }

    /**
     * @test
     */
    public function initializeContainer () {
        $mockCache = $this->getMockBuilder(ConfigCache::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createConfigCache')
                          ->with('cacheWebAppDebugContainer.php', true)
                          ->will($this->returnValue($mockCache));

        $mockCache->expects($this->once())
                  ->method('isFresh')
                  ->will($this->returnValue(false));

        $mockCache->expects($this->any())
                  ->method('getPath')
                  ->will($this->returnValue('path'));

        $this->buildContainerMock($mockCache, 'WebAppDebugContainer');

        $this->mockFactory->expects($this->once())
                          ->method('createContainer')
                          ->with($mockCache, 'WebAppDebugContainer')
                          ->will($this->returnValue('container'));

        $method = $this->reflection->getMethod('initializeContainer');
        $method->setAccessible(true);

        $method->invokeArgs($this->kernel, []);
        $this->assertSame('container', $this->kernel->getContainer());
    }

    /**
     * @test
     */
    public function reinitializeContainer () {
        $mockCache = $this->getMockBuilder(ConfigCache::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createConfigCache')
                          ->with('cacheWebAppDebugContainer.php', true)
                          ->will($this->returnValue($mockCache));

        $mockCache->expects($this->once())
                  ->method('isFresh')
                  ->will($this->returnValue(true));

        $this->mockFactory->expects($this->once())
                          ->method('createContainer')
                          ->with($mockCache, 'WebAppDebugContainer')
                          ->will($this->returnValue('container'));

        $method = $this->reflection->getMethod('initializeContainer');
        $method->setAccessible(true);

        $method->invokeArgs($this->kernel, []);
        $this->assertSame('container', $this->kernel->getContainer());
    }

    private function buildContainerMock (PHPUnit_Framework_MockObject_MockObject $mockCache, $class = 'example') {
        $mockContainer = $this->getMockBuilder(ContainerBuilder::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createContainerBuilder')
                          ->will($this->returnValue($mockContainer));

        $mockLoader = $this->getMockBuilder(YamlFileLoader::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createYamlFileLoader')
                          ->with($mockContainer, 'config')
                          ->will($this->returnValue($mockLoader));

        $mockLoader->expects($this->once())
                   ->method('load')
                   ->with('services.yml');

        $mockContainer->expects($this->once())
                      ->method('compile');

        $mockDumper = $this->getMockBuilder(PhpDumper::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createDumper')
                          ->with($mockContainer)
                          ->will($this->returnValue($mockDumper));

        $mockDumper->expects($this->once())
                   ->method('dump')
                   ->with(['class' => $class, 'base_class' => Container::class, 'file' => 'path', 'debug' => true])
                   ->will($this->returnValue('test'));

        $mockContainer->expects($this->once())
                      ->method('getResources')
                      ->will($this->returnValue(['resources']));

        $mockCache->expects($this->once())
                  ->method('write')
                  ->with('test', ['resources']);
    }
}