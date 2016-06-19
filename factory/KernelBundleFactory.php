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
    /**
     * @var BaseKernel
     */
    private static $kernel;

    /**
     * @param      $class
     * @param      $rootDir
     * @param bool $debug
     * @param      $configDir
     *
     * @return BaseKernel
     */
    public static function buildKernel ($class, $rootDir, $debug = false, $configDir = null) {
        if (self::$kernel === null) {
            if ($configDir === null) {
                $configDir = $rootDir.'config/';
            }
            $factory = new KernelFactory();
            self::$kernel = new $class($factory, $configDir, $rootDir.'var/cache/', $debug);
            self::$kernel->initialize();
        }

        return self::$kernel;
    }
}