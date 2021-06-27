<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Build Validation Output
    |--------------------------------------------------------------------------
    |
    | The following messages output from the build tool validation rules.
    |
    */

    'protected_branch' => 'Unexpected branch for build environment',

    'npm_check' => 'node_modules Folder not found. Run npm install',

    'composer_check' => 'vendor Folder not found. Run composer install',

    'env_file_not_found' => 'Environment file  was not found for --env value',

];