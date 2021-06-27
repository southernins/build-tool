<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 5/22/21
 * Time: 8:08 PM
 */

namespace SouthernIns\BuildTool;


use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use SouthernIns\BuildTool\BuildRules\ComposerInstalledRule;
use SouthernIns\BuildTool\BuildRules\NpmInstalledRule;
use SouthernIns\BuildTool\BuildRules\ProtectedEnvironmentRule;
use SouthernIns\BuildTool\BuildRules\EnvFileExistsRule;
use SouthernIns\BuildTool\Contracts\PackageBuilder;
use SouthernIns\BuildTool\Exceptions\BuildValidationException;
use SouthernIns\BuildTool\Helpers\CacheManager;
use SouthernIns\BuildTool\Helpers\StorageManager;


class AwsPackageBuilder implements PackageBuilder
{

    // TODO: This class now needs this trait.... AND the interface above.... pull to abstract class?????
    // does it? does it need this????  $could use an $output Object to render output to console.
//    use InteractsWithIO;


    protected $output;


    public function __construct()
    {
    } //- END function __construct(

    public function buildValidations( $force )
    {

        // Add ENV::CHeck ??

        // Add validations to run
        $validations = [
            new EnvFileExistsRule,
            new ComposerInstalledRule,
            new NpmInstalledRule
        ];

        // skipped if build runs  with --force flag
        if( $force === false ){
            $validations[] = new ProtectedEnvironmentRule;
        }

        $validator = Validator::make(
            [ 'build' => App::environment() ],
            [ 'build' => $validations ]
        );

        if( $validator->fails() ){
            throw new BuildValidationException( $validator->errors()->first( 'build' ) );
        }

    } //- END buildValidations()


    public function beforePackageBuild()
    {


        // Clear Caches

        $this->output->comment( "Clearing Local Caches" );
        CacheManager::clearAll();

        // Clear Laravel Logs before production deployment

        if( $this->isProduction() ) {
            StorageManager::clearLogs();
        }

        // Backup ENV File

        // backup Ebextensions

    } //- END function beforePackageBuild ()


    public function packageBuild()
    {
        // TODO: Implement packageBuild() method.

        // Generate Build Version

        // generate Build Name

        // Set Build Environment File

        // Set Build EB extension Files

        // Composer Install

        // Npm Run

        // Create Build Package

    } //- END function packageBuild ()


    public function afterPackageBuild()
    {
        // TODO: Implement afterPackageBuild() method.


        // Restore Composer Dependencies

        // Restore ENV File

        // Restore Ebextension Files


    } //- END function afterPackageBuild()


    /**
     * returns true if current environment is set to "production"
     *
     * @param $environment laravel environment to use during build
     *
     * @return bool
     */
    protected function isProduction( $environment )
    {

        return ( $environment == Confgi::get( 'build-tool:production-env' ));

    } //- END isProduction()


    public function setOutput( $output ){

        $this->output = $output;

    } //- END function setOutput ()


} //- END class AWSPackageBuilder {}