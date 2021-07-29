<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 6/2/21
 * Time: 8:09 PM
 */

namespace SouthernIns\BuildTool\Helpers;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheManager
{


    public function clearAll(){


        $this->clearConfigCache();

        $this->clearFileCache();

        $this->clearViewsCache();


        //      // Remove Route Cache file
        // Route Caching fails due to Closures in Routes


    }

    protected function clearConfigCache(){

        Artisan::call( "config:clear" );
    }

    protected function clearFileCache(){

        Cache::store( 'file' )->flush();
    }

    protected function clearViewsCache(){

        Artisan::call( "view:clear" );

    }



}