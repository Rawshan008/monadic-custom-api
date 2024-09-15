<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitbe7ddd78c758eb6a33bec40040350027
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

        spl_autoload_register(array('ComposerAutoloaderInitbe7ddd78c758eb6a33bec40040350027', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitbe7ddd78c758eb6a33bec40040350027', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitbe7ddd78c758eb6a33bec40040350027::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
