<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8670018b775324be36d7c313e612247e
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SimFeaturedMediaSrc\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SimFeaturedMediaSrc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'SimFeaturedMediaSrc\\Admin\\FeaturedMediaSrcAdmin' => __DIR__ . '/../..' . '/src/Admin/FeaturedMediaSrcAdmin.php',
        'SimFeaturedMediaSrc\\WPAdminPage\\AdminPage' => __DIR__ . '/../..' . '/src/WPAdminPage/AdminPage.php',
        'SimFeaturedMediaSrc\\WPAdminPage\\FormHelper' => __DIR__ . '/../..' . '/src/WPAdminPage/FormHelper.php',
        'SimFeaturedMediaSrc\\addFeaturedImageSrc' => __DIR__ . '/../..' . '/src/addFeaturedImageSrc.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8670018b775324be36d7c313e612247e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8670018b775324be36d7c313e612247e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit8670018b775324be36d7c313e612247e::$classMap;

        }, null, ClassLoader::class);
    }
}
