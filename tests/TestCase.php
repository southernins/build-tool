<?php

namespace SouthernIns\BuildTool\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SouthernIns\BuildTool\BuildServiceProvider;


abstract class TestCase extends OrchestraTestCase
{


    public function setUp():void {



        parent::setUp();
    }


    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return SouthernIns\BuildTool\BuildServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [BuildServiceProvider::class];
    }



}
