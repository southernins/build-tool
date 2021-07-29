<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 6/2/21
 * Time: 9:14 PM
 */

namespace SouthernIns\BuildTool\Helpers;


use Illuminate\Support\Facades\Storage;

class StorageManager
{


    public function clearLogs(){

        Storage::cleanDirectory( storage_path( 'logs' ) );

    }


}