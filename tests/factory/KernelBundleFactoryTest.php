<?php
use http\KernelBundle\factory\KernelBundleFactory;
use http\KernelBundle\tests\factory\helper\ExampleKernel;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.06.2016
 * Time: 06:02
 */
class KernelBundleFactoryTest extends PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function buildKernel_withoutConfig () {
        $factory1 = new KernelBundleFactory();
        $kernel1 = $factory1->buildKernel(ExampleKernel::class, __DIR__, true);
        $this->assertInstanceOf(ExampleKernel::class, $kernel1);
    }

    /**
     * @test
     */
    public function buildKernel () {
        $factory1 = new KernelBundleFactory();
        $factory2 = new KernelBundleFactory();

        $kernel1 = $factory1->buildKernel(ExampleKernel::class, __DIR__, true, __DIR__.'/helper/');
        $this->assertInstanceOf(ExampleKernel::class, $kernel1);
        $kernel2 = $factory2->buildKernel(ExampleKernel::class, 'test', true);
        $this->assertSame($kernel1, $kernel2);
    }
}