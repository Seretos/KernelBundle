<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 19.06.2016
 * Time: 04:33
 */

namespace http\KernelBundle\factory;


use http\KernelBundle\core\BaseKernel;

class KernelBundleFactory {
    private static $kernel;

    /**
     * @param      $class
     * @param      $rootDir
     * @param bool $debug
     *
     * @return BaseKernel
     */
    public static function buildKernel ($class, $rootDir, $debug = false) {
        if (self::$kernel == null) {
            $factory = new KernelFactory();
            self::$kernel = new $class($factory, $rootDir.'/config/', $rootDir.'/var/cache/', $debug);
        }

        return self::$kernel;
    }
}