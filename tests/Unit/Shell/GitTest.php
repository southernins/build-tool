<?php

namespace Tests\Unit;

use Tests\TestCase;
use SouthernIns\BuildTool\Shell\Git;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class GitTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGitBranch()
    {
//        SELF::setBranchMaster();

        $isBranchMaster = Git::branchName() == 'test';

//        dd( $isBranchMaster );
        $this->assertEquals( true , $isBranchMaster  );
    }

    static function setBranchMaster(){

        $gitBranch = new Process( "git checkout master" );
        $gitBranch->setTimeout(180);
        $gitBranch->run();

        if( !$gitBranch->isSuccessful() ){
            throw new ProcessFailedException( $gitBranch );
        }

        return $gitBranch->isSuccessful();

    }


}
