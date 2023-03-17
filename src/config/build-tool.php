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


    /*
    |--------------------------------------------------------------------------
    | Build Destination
    |--------------------------------------------------------------------------
    |
    | BuildFile Output Directory
    */

    'destination' => base_path() . '/../' ,



    /*
    |--------------------------------------------------------------------------
    | Build Timezone
    |--------------------------------------------------------------------------
    |
    | Sets timezone used when build file version is generated
    | null | Timezone String ( e.g. 'America/Chicago' )
    */

    'timezone' => 'America/Chicago',


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
        'lang/*',
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
        'cron.yaml',
    ],


];
