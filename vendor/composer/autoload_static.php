<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit14b905912678bc989b5fb9c70210c402
{
    public static $prefixesPsr0 = array (
        'I' => 
        array (
            'Imgix\\' => 
            array (
                0 => __DIR__ . '/..' . '/imgix/imgix-php/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WP_GitHub_Updater' => __DIR__ . '/..' . '/radishconcepts/wordpress-github-plugin-updater/updater.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit14b905912678bc989b5fb9c70210c402::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit14b905912678bc989b5fb9c70210c402::$classMap;

        }, null, ClassLoader::class);
    }
}
