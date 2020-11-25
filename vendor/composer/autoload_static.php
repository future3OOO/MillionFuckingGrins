<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit948617e27c08e9185de9ac107254b78e
{
    public static $files = array (
        '2cffec82183ee1cea088009cef9a6fc3' => __DIR__ . '/..' . '/ezyang/htmlpurifier/library/HTMLPurifier.composer.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'I' => 
        array (
            'Imagine\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Imagine\\' => 
        array (
            0 => __DIR__ . '/..' . '/imagine/imagine/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'H' => 
        array (
            'HTMLPurifier' => 
            array (
                0 => __DIR__ . '/..' . '/ezyang/htmlpurifier/library',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit948617e27c08e9185de9ac107254b78e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit948617e27c08e9185de9ac107254b78e::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit948617e27c08e9185de9ac107254b78e::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit948617e27c08e9185de9ac107254b78e::$classMap;

        }, null, ClassLoader::class);
    }
}
