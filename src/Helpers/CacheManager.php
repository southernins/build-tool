<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 6/2/21
 * Time: 8:09 PM
 */

namespace SouthernIns\BuildTool\Helpers;


use Illuminate\Support\Facades\Artisan;

class CacheManager
{


    static function clearAll(){


        // Flush Application Cache for local environment
        // before creating $environment build
        //        $this->call( "cache:clear", [
        //            '--env' => "local"
        //        ]);


        Cache::store( 'file' )->flush();

        // TODO:: Clear All of Storage from local environment before deploying

        // Remove Cached Views
        Artisan::call( "view:clear" );

        // Remove Config Cache File
        Artisan::call( "config:clear" );

        // Calling Build with --env="" will overwrite the
        // cache with the config of the environment being deployed..
        // CANNOT CACHE Config in local env before deployment
        //        $this->call( "config:cache" );

//        $this->info( "Route Caching - Disabled" );
        //      // Remove Route Cache file
        // Route Caching fails due to Closures in Routes
        // PHP Cannot serialize routes with closures.
        //      $this->call( "route:clear", [
        //          '--env' => $environment
        //      ]);

    }

}