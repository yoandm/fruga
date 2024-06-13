<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0d422e9b9eb57abfa3f6136830ebb3db
{
    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit0d422e9b9eb57abfa3f6136830ebb3db::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit0d422e9b9eb57abfa3f6136830ebb3db::$classMap;

        }, null, ClassLoader::class);
    }
}
