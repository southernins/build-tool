<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use SouthernIns\BuildTool\BuildRules\ProtectedEnvironmentRule;
use Facades\SouthernIns\BuildTool\Helpers\Git;
use Tests\TestCase;


class ProtectedEnvironmentRuleTest extends TestCase
{

    public function test_protected_envrionment_rule_passes_production()
    {

        $testEnvironment = 'production';

        $testConfig = [
            'production' => 'master'
        ];

        $rule = new ProtectedEnvironmentRule();

        Config::shouldReceive( 'get' )
            ->once()
            ->with( 'build-tool.protected' )
            ->andReturn( $testConfig );

        Git::shouldReceive( 'isBranch' )
            ->once()
            ->with( 'master' )
            ->andReturn( true );

        $this->assertTrue( $rule->passes('build', $testEnvironment ));

    } //- END test_protected_envrionment_rule_passes_production()

    public function test_protected_envrionment_rule_passes_non_production()
    {

        $testEnvironment = 'example';

        $testConfig = [
            'production' => 'master'
        ];

        $rule = new ProtectedEnvironmentRule();

        Config::shouldReceive( 'get' )
            ->once()
            ->with( 'build-tool.protected' )
            ->andReturn( $testConfig );

        $mock = Git::partialMock();
        $mock->shouldNotReceive( 'isBranch' );

        $this->assertTrue( $rule->passes('build', $testEnvironment ));

    } //- END test_protected_envrionment_rule_passes_non_production()


    /**
     * @group wtf1
     */
    public function test_protected_envrionment_rule_fails_production()
    {

        $testEnvironment = 'production';

        $testConfig = [
            'production' => 'master'
        ];

        $rule = new ProtectedEnvironmentRule();

        Config::shouldReceive( 'get' )
            ->once()
            ->with( 'build-tool.protected' )
            ->andReturn( $testConfig );

        Git::shouldReceive( 'isBranch' )
            ->once()
            ->with( $testConfig[ $testEnvironment ] )
            ->andReturn( false );

        $this->assertFalse( $rule->passes('build', $testEnvironment ) );

    } //- END test_protected_envrionment_rule_fails_production()


} //- END class ProtectedEnvironmentRuleTest{}
