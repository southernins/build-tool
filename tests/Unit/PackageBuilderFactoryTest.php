<?php

namespace Tests\Unit;

use SouthernIns\BuildTool\Contracts\PackageBuilder;
use SouthernIns\BuildTool\PackageBuilderFactory;
use Tests\TestCase;


class PackageBuilderFactoryTest extends TestCase
{

    public function test_it_makes_makes_builder_class()
    {
        $factory = new PackageBuilderFactory();

        $builderInstance = $factory::makeBuilder();

        $this->assertInstanceOf( PackageBuilder::class, $builderInstance );

    } //- END test_it_checks_npm_install()

}
