<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit982cb149c2cfe588ca8ef602f9a6f3bd
{
    public static $prefixLengthsPsr4 = array (
        'U' => 
        array (
            'UWS\\LITE\\SMS\\' => 13,
        ),
        'T' => 
        array (
            'Twilio\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'UWS\\LITE\\SMS\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Twilio\\' => 
        array (
            0 => __DIR__ . '/..' . '/twilio/sdk/src/Twilio',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit982cb149c2cfe588ca8ef602f9a6f3bd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit982cb149c2cfe588ca8ef602f9a6f3bd::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit982cb149c2cfe588ca8ef602f9a6f3bd::$classMap;

        }, null, ClassLoader::class);
    }
}
