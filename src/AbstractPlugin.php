<?php

namespace APIFeaturedImage;

abstract class AbstractPlugin implements PluginInterface
{
    public static $plugin_dir_path;
    public static $plugin_dir_url;

    public static function init( string $plugin_dir_path = '', string $plugin_dir_url = '' ): PluginInterface
    {
        static $instance = [];

        $called_class = static::class;

        if ( ! isset( $instance[ $called_class ] ) ) {
            $instance[ $called_class ] = new $called_class();
        }

        self::$plugin_dir_path = $plugin_dir_path;
        self::$plugin_dir_url  = $plugin_dir_url;

        return $instance[ $called_class ];
    }
}
