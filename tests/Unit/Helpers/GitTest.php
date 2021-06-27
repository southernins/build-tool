<?php

namespace Tests\Unit;

use Tests\TestCase;
use SouthernIns\BuildTool\Helpers\Git;
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
        $branch = Git::branchName();
        $this->assertNotEmpty( $branch );
        $this->assertIsString( $branch );
    }


} //- END class GitTest
