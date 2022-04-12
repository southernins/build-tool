<?php

namespace Tests;


use Carbon\Carbon;
use SouthernIns\BuildTool\Commands\BuildDeployment;


class BuildDeploymentTraitTest extends TestCase
{

    protected  $testClass;


    public function setUp():void
    {
        parent::setUp();


        $this->testClass = new class {

        };


    }




    public function test_production_check_passes(){

        $testClass = new class {
            use BuildDeployment;

            public function testShouldBeTrue(){
                return $this->isProduction( 'production' );
            }

        };

        $this->assertTrue( $testClass->testShouldBeTrue() );

    }


    public function test_production_check_fails(){

        $testClass = new class {
            use BuildDeployment;

            public function testShouldBeFalse(){
                return $this->isProduction( 'NOT-production' );
            }

        };

        $this->assertFalse( $testClass->testShouldBeFalse() );

    }



    public function test_can_get_build_name_from_app(){

        $testClass = new class {
            use BuildDeployment;

            public function getBuildName(){
                return $this->buildName();
            }

        };

        config( [ 'app.name' =>  'App Name' ]);

        $this->assertSame( 'app_name', $testClass->getBuildName() );

    }


    public function test_can_get_custom_build_name(){

        $testClass = new class {
            use BuildDeployment;

            public function getBuildName(){
                return $this->buildName();
            }

        };

        config( [ 'build-tool.name' => 'Custom Name' ]);

        $this->assertSame( 'custom_name', $testClass->getBuildName() );

    }

    public function test_build_version_uses_config_timezone(){
        $testClass = new class {
            use BuildDeployment;

            public function getBuildVersion(){
                return $this->buildVersion( 'test' );
            }

        };

        config( [ 'build-tool.timezone' => 'America/Chicago' ]);

        $dateString = Carbon::now()->setTimezone( 'America/Chicago' )->format( 'Y.m.d.Hi' );

        $expected = $dateString . '_test';

        $this->assertSame( $expected, $testClass->getBuildVersion() );

    }

    public function test_build_version_with_null_timezone(){
        $testClass = new class {
            use BuildDeployment;

            public function getBuildVersion(){
                return $this->buildVersion( 'test' );
            }

        };

        config( [ 'build-tool.timezone' => null ]);

        $dateString = Carbon::now()->format( 'Y.m.d.Hi' );

        $expected = $dateString . '_test';

        $this->assertSame( $expected, $testClass->getBuildVersion() );

    }

    public function test_build_version_for_production(){
        $testClass = new class {
            use BuildDeployment;

            public function getBuildVersion(){
                return $this->buildVersion( 'production' );
            }

        };

        config( [ 'build-tool.timezone' => null ]);

        $expected = Carbon::now()->format( 'Y.m.d.Hi' );

        $this->assertSame( $expected, $testClass->getBuildVersion() );

    }


}
