<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit43f23b62cb1b805dc57dbfcc744682a8
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RobRichards\\XMLSecLibs\\' => 23,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'N' => 
        array (
            'NFePHP\\Common\\' => 14,
        ),
        'L' => 
        array (
            'League\\Flysystem\\' => 17,
        ),
        'G' => 
        array (
            'Greenter\\XMLSecLibs\\' => 20,
        ),
        'C' => 
        array (
            'Click\\ClickNfse\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RobRichards\\XMLSecLibs\\' => 
        array (
            0 => __DIR__ . '/..' . '/robrichards/xmlseclibs/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'NFePHP\\Common\\' => 
        array (
            0 => __DIR__ . '/..' . '/nfephp-org/sped-common/src',
        ),
        'League\\Flysystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/flysystem/src',
        ),
        'Greenter\\XMLSecLibs\\' => 
        array (
            0 => __DIR__ . '/..' . '/greenter/xmldsig/src',
        ),
        'Click\\ClickNfse\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Nfse',
        ),
    );

    public static $prefixesPsr0 = array (
        'F' => 
        array (
            'ForceUTF8\\' => 
            array (
                0 => __DIR__ . '/..' . '/neitanod/forceutf8/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit43f23b62cb1b805dc57dbfcc744682a8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit43f23b62cb1b805dc57dbfcc744682a8::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit43f23b62cb1b805dc57dbfcc744682a8::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
