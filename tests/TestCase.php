<?php

namespace SouthernIns\BuildTool\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SouthernIns\BuildTool\BuildServiceProvider;


abstract class TestCase extends OrchestraTestCase
{

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
