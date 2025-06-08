<?php

if (!function_exists('vite_assets')) {
    /**
     * Get the path to a versioned Vite file
     *
     * @param  string|array  $path
     * @return array
     */
    function vite_assets($path)
    {
        // Check if we're in development mode or Vite is not running
        if (!file_exists(public_path('build/manifest.json')) || config('app.use_vite_dev') === false) {
            // Return an array with CSS and JS paths
            return [
                'css' => asset('build/assets/app-PLACEHOLDER.css'),
                'js' => asset('build/assets/app-PLACEHOLDER.js')
            ];
        }

        return app('\\Illuminate\\Foundation\\Vite')($path);
    }
} 