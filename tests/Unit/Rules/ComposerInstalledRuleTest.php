<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\BuildRules\ComposerInstalledRule;
use Tests\TestCase;


class ComposerInstalledRuleTest extends TestCase
{

    public function test_composer_installed_rule_passes()
    {

        $testEnvironment = 'example';
        $rule = new ComposerInstalledRule();

        File::shouldReceive( 'exists' )->once()
            ->andReturn( true );


        $this->assertTrue( $rule->passes('build', $testEnvironment ));

    } //- END test_it_checks_npm_install()


    public function test_composer_installed_rule_fails()
    {

        $testEnvironment = 'example';
        $rule = new ComposerInstalledRule();

        File::shouldReceive( 'exists' )->once()
            ->andReturn( false );

        $this->assertFalse( $rule->passes('build', $testEnvironment ));


    } //- END test_it_checks_npm_install()


} //- END function ComposerInstalledRuleTest()
