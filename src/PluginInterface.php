<?php

namespace APIFeaturedImage;

interface PluginInterface
{
    /**
     * Initialization method.
     *
     * This static method serves as an entry point for initializing the plugin.
     * It allows the plugin to be initialized with optional parameters such as
     * the plugin directory path and URL.
     *
     * @param string $plugin_dir_path The directory path of the plugin. Default is an empty string.
     * @param string $plugin_dir_url  The URL of the plugin. Default is an empty string.
     *
     * @return PluginInterface An instance of the class implementing this interface.
     *
     * Implementing classes should implement this method to ensure that the
     * plugin can be properly initialized with the specified parameters.
     * The method should return an instance of the class, ensuring that only
     * one instance of the plugin is created.
     */
    public static function init( string $plugin_dir_path = '', string $plugin_dir_url = ''): self;
}
