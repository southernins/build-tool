<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Build/Deployment Name
    |--------------------------------------------------------------------------
    |
    | This value is will control the name of the build fle
    */

    'name' => '',


    /*
    |--------------------------------------------------------------------------
    | Production Environment Name
    |--------------------------------------------------------------------------
    |
    | This value is will control the name of the build fle
    */
    'production-env' => 'production',


    /*
    |--------------------------------------------------------------------------
    | Package Builder Class
    |--------------------------------------------------------------------------
    |
    | class returned from build factory, must implement PackageBuilder Contract
    */

    'build-class' => 'SouthernIns\BuildTool\AwsPackageBuilder',


    /*
    |--------------------------------------------------------------------------
    | protected build environment
    |--------------------------------------------------------------------------
    |   These environments have branch expectations for build
    |   'env_name' => 'branch_name'
    |
    | a build error will be thrown if building a protected envrionment on any
    | branch other than what is listed
    |
    */

    'protected' =>[
        'production' => 'master'
    ],


    /*
    |--------------------------------------------------------------------------
    | Build Destination
    |--------------------------------------------------------------------------
    |
    | BuildFile Output Directory
    */

    'destination' => '',


    /*
   |--------------------------------------------------------------------------
   | Envionrment Source
   |--------------------------------------------------------------------------
   |
   | Environment File Source Directory
   */

    'env-path' => '',


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
        '.env',
        'composer.json',
        'artisan',
    ],


];
