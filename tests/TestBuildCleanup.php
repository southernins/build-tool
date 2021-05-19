<?php

namespace SouthernIns\BuildTool\Tests;


trait TestBuildCleanup {


    public function setupTestConfig(){
        Config::set( 'app.name', 'build-test-deployment' );

    }



    public function unlinkBuildFiles(){


    }

}