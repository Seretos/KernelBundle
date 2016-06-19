KernelBundle
============
this bundle handle the loading of resources. currently only the services config.

Installation
============
add the bundle in your composer.json as bellow:
```js
"require": {
    ...
    ,"LimetecBiotechnologies/http/KernelBundle" : "v0.1.*"
},
"repositories": [
    {
      "type": "git",
      "url": "https://github.com/Seretos/KernelBundle"
    }
]
```
and execute the composer update command

Usage
=====
build the singleton kernel with the factory:
```php
$kernel = KernelBundleFactory::buildKernel(BaseKernel::class,'/app/root/path/',$debug);
```

add the services.yml config in /app/root/path/config as below:
```php
services:
     my.example.service:
         class: my\example\Service
```

get the current service container:
```php
$container = $kernel->getContainer();
$service = $container->get('my.example.service');
```

you can create a custom kernel class to register bundles.
```php
class MyKernel extends BaseKernel {
    public function registerBundles () {
        $bundles = [
            new \database\DatabaseIntegrationBundle\DatabaseIntegrationBundle(),
        ];

        return $bundles;
    }
}
```
```php
$kernel = KernelBundleFactory::buildKernel(MyKernel::class,'/app/root/path/',$debug);
```