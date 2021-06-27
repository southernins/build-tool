<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 5/24/21
 * Time: 10:57 PM
 */

namespace SouthernIns\BuildTool;


class BuildTool
{

    /**
     * Environment to deploy
     *
     * @var string
     */
    protected $environment;

    protected $builder;

    protected $output;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( PackageBuilderFactory $builder ){

        $this->builder =  $builder::makeBuilder();

    } //- END function __construct()


    public function createBuildPackage( $output, $force = false ){

        $this->builder->setOutput( $output );

        $this->builder->buildValidations( $force );

        $this->builder->beforePackageBuild();

        $this->builder->packageBuild();

        $this->builder->afterPackageBuild();

    } //- END function createBuildPackage()


} //- END class BuildTool{}