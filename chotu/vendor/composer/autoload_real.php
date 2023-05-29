<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit36c891a353d4b8a1a2953dd2e1b728ca
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit36c891a353d4b8a1a2953dd2e1b728ca', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit36c891a353d4b8a1a2953dd2e1b728ca', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit36c891a353d4b8a1a2953dd2e1b728ca::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}