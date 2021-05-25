<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\Contracts\PackageBuilder;
use SouthernIns\BuildTool\PackageBuilderFactory;
use SouthernIns\BuildTool\ShellCommand;
use Tests\TestCase;


class PackageBuilderFactoryTest extends TestCase
{


    /**
     * @group fix
     */

    public function test_it_makes_makes_builder_class()
    {
        $factory = new PackageBuilderFactory();

        $builderInstance = $factory::makeBuilder();


        $this->assertInstanceOf( PackageBuilder::class, $builderInstance );
//        $this->assertTrue( $builderInstance instanceof PackageBuilder );


    } //- END test_it_checks_npm_install()


}
