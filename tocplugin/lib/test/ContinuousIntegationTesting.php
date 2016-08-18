<?php
/**
 * This script can be used to implement continuous integration testing.
 *
 * Just invoke it from a cron job.
 */

$this_file_dir = dirname(__FILE__);
$tiki_root_dir = $this_file_dir.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..";

set_include_path(get_include_path() . PATH_SEPARATOR . $tiki_root_dir);

$this_file_dir = dirname(__FILE__);
require_once("lib/test/TestRunnerWithBaseline.php");
require_once("lib/debug/Tracer.php");

if (realpath($argv[0]) == __FILE__)
{
    echo("Doing one integration test on tiki installation: $tiki_root_dir\n\n");

    $tester = new ContinuousIntegrationTesting($tiki_root_dir);
    $tester->run();
}

class ContinuousIntegrationTesting
{

    private $tiki_root_dir;
    private $testrunner;
    private $current_revision;
    private $revision_last_tested;

    public function __construct($tiki_root_dir)
    {
        $this->tiki_root_dir = $tiki_root_dir;
        $this->testrunner = new TestRunnerWithBaseline();
        $this->get_revision_last_tested();
    }

    public function run()
    {
        $current_revision = $this->svnup();
        if (!$this->needs_testing($current_revision))
        {
            echo "\n\nLatest revision was already tested. No need to retest.\n\n";
        }
        else
        {
            $this->run_tests();
        }
        $this->update_revision_last_tested();
    }

    public function svnup()
    {
        $svn_command = "svn up ".$this->tiki_root_dir;
        $svn_output_lines = array();
        $svn_return_status;
        exec ($svn_command, $svn_output_lines, $svn_return_status);
        $svn_output = implode("\n", $svn_output_lines);

        echo("

#################################################
# Output of '$svn_command'
#################################################

".$svn_output);

        $current_revision = $this->extract_current_revision_from_svnup_output($svn_output);

        $this->current_revision = $current_revision;

        return $current_revision;
    }


    private function extract_current_revision_from_svnup_output($svn_output)
    {
        $matches = array();
        $matched = preg_match ("/(^|\n)At revision ([\d]+)/", $svn_output, $matches);
        $revision = null;
        if ($matched)
        {
            $revision = $matches[2];
        }

        return $revision;
    }

    function needs_testing($current_revision)
    {
        $answer = true;
        if ($this->revision_last_tested == $current_revision)
        {
            $answer = false;
        }
        return $answer;
    }

    function run_tests()
    {
        echo("\n\nRunning the tests.\n\n");

        $baseline_log = $this->revision_log_fpath("baseline");
        $current_revision_log = $this->revision_log_fpath($this->current_revision);
        $output_fpath = $this->output_fpath();
        $this->testrunner = new TestRunnerWithBaseline($baseline_log, $current_revision_log, $output_fpath);

        $this->testrunner->run();

    }

    private function revision_log_fpath($revision)
    {
        $fname = "phpunit-log.".$revision.".json";
        $fpath = implode(DIRECTORY_SEPARATOR, array($this->tiki_root_dir, 'lib', 'test', $fname));

        return $fpath;
    }

    private function revision_last_tested_fpath()
    {
        $fpath = implode(DIRECTORY_SEPARATOR, array($this->tiki_root_dir, 'lib', 'test', 'revision_last_tested.txt'));
        return $fpath;
    }

    private function update_revision_last_tested()
    {
        file_put_contents ($this->revision_last_tested_fpath(), $this->current_revision);
    }

    private function get_revision_last_tested()
    {
        $this->revision_last_tested = file_get_contents ($this->revision_last_tested_fpath());

        return;
    }

    private function output_fpath()
    {
        $fpath = implode(DIRECTORY_SEPARATOR, array($this->tiki_root_dir, 'lib', 'test', 'phpunit-output.'.$this->current_revision.".txt"));

        return $fpath;
    }
}


