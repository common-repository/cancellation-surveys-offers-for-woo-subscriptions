<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit20c8c13f3cb4f9e598442bf0af08dbb9
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MeowCrew\\CancellationOffers\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MeowCrew\\CancellationOffers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit20c8c13f3cb4f9e598442bf0af08dbb9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit20c8c13f3cb4f9e598442bf0af08dbb9::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit20c8c13f3cb4f9e598442bf0af08dbb9::$classMap;

        }, null, ClassLoader::class);
    }
}
