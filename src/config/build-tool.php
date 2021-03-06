<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Build/Deployment Name
    |--------------------------------------------------------------------------
    |
    | This value is will control the name of the build fle
    */

    'name' => "",


    // Add BuildFile Output Directory as Config item

    // Add Environment File Source as Directory

    /*
    |--------------------------------------------------------------------------
    | Files included in Build
    |--------------------------------------------------------------------------
    |
    | This following array is a list of files to include in the build/deployment
    | package... Anything NOT in the following list will be ignored.
    */
    'include' => [
        'app/*',
        'bootstrap/*',
        'config/*',
        'database/*',
        'public/*',
        'resources/*',
        'routes/*',
        'storage/*',
        'vendor/*',
        '.ebextensions/*',
        '.platform/*',
        '.env',
        'composer.json',
        'artisan',
    ],


];
