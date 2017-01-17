<?php

/**
 * Because Lumen has no config_path function, we need to add this function
 * to make JWT Auth works.
 */
if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     *
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath().'/config'.($path ? '/'.$path : $path);
    }
}

/**
 * Because Lumen has no storage_path function, we need to add this function
 * to make caching configuration works.
 */
if (!function_exists('storage_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     *
     * @return string
     */
    function storage_path($path = '')
    {
        return app()->basePath().'/storage'.($path ? '/'.$path : $path);
    }
}
