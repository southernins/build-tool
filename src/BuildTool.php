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


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( PackageBuilderFactory $builder ){


        $this->builder = $builder::makeBuilder();


    } //- END __construct()



    public function createBuildPackage(){


        $this->builder->beforePackageBuild();


        $this->builder->packageBuild();


        $this->builder->afterPackageBuild();

    }


}