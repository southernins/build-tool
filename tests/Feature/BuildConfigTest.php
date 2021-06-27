<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BuildConfigTest extends TestCase
{


    public function test_config_is_loaded()
    {
        $this->assertTrue( Config::has( 'build-tool' ) );

        $this->assertIsArray( Config::get( 'build-tool.include' ) );

    }

    public function test_config_has_expected_keys(){

        $config = Config::get( 'build-tool' );

        $this->assertArrayHasKey( 'name', $config );
        $this->assertArrayHasKey( 'build-class', $config );
        $this->assertArrayHasKey( 'protected', $config );
        $this->assertArrayHasKey( 'destination', $config );
        $this->assertArrayHasKey( 'env-path', $config );
        $this->assertArrayHasKey( 'include', $config );

    }

} //- END class BuildConfigTest{}
