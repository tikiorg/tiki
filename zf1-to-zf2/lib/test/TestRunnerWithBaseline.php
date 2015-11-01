<?php
/**

 * Created by JetBrains PhpStorm.
 * User: alain_desilets
 * Date: 2013-10-02
 * Time: 2:12 PM
 * To change this template use File | Settings | File Templates.
 *
 * This class is used to run phpunit tests and compare the list of failures
 * and errors to those of a benchmark "normal" run.
 *
 * Use this class in situations where it's not practical for everyone to
 * keep all the tests "in the green" at all time, and to only commit code
 * that doesn't break any tests.
 *
 * With this class, you can tell if you have broken tests that were working
 * previously, or if you have fixed tests that were broken before.
 */

require_once('lib/debug/Tracer.php');

class TestRunnerWithBaseline {

    private $baseline_log_fpath;
    private $current_log_fpath;
    private $output_fpath;

    private $last_test_started = null;

    private $logname_stem = 'phpunit-log';
    public $action = 'run'; // run|update_baseline
    public $phpunit_options = '';
    public $help = false;
    public $filter = '';
    public $diffs;

    function __construct($baseline_log_fpath = null, $current_log_fpath = null, $output_fpath = null)
    {
        $this->baseline_log_fpath = $baseline_log_fpath;
        $this->current_log_fpath = $current_log_fpath;
        $this->output_fpath =$output_fpath;
    }

    function run()
    {
        global $tracer;

        $this->config_from_cmdline_options();

        if ($this->help) {
            $this->usage();
        }

        $this->run_tests();

        $this->print_diffs_with_baseline();

        if ($this->action == 'update_baseline')
        {
            $this->save_current_log_as_baseline();
        }

    }

    function run_tests()
    {
        global $tracer;

        $cmd_line = "../../vendor/bin/phpunit --verbose";

        if ($this->phpunit_options != '')
        {
            $cmd_line = "$cmd_line ".$this->phpunit_options;
        }

        if ($this->filter != '')
        {
            $cmd_line = "$cmd_line --filter ".$this->filter;
        }

        $cmd_line = $cmd_line." --log-json \"".$this->logpath_current()."\" .";

        $this->do_echo( "
********************************************************************
*
* Executing phpunit as:
*
*    $cmd_line
*
********************************************************************
");

        if ($this->output_fpath == null)
        {
            system($cmd_line);
        }
        else
        {
            $phpunit_output = array();
            exec($cmd_line, $phpunit_output);
            $this->do_echo(implode("\n", $phpunit_output));
        }


    }

    function print_diffs_with_baseline()
    {
        global $tracer;

        $this->do_echo("\n\nChecking for differences with baseline test logs...\n\n");

        $baseline_issues;
        if (file_exists($this->logpath_baseline()))
        {
            $baseline_issues = $this->read_log_file($this->logpath_baseline());
        }
        else
        {
            $this->do_echo("=== WARNING: No baseline file exists. Assuming empty baseline.\n\n");
            $baseline_issues = $this->make_empty_issues_list();
        }

        $current_issues = $this->read_log_file($this->logpath_current());

        $this->diffs = $this->compare_two_test_runs($baseline_issues, $current_issues);

        $nb_failures_introduced = count($this->diffs['failures_introduced']);
        $nb_failures_fixed = count($this->diffs['failures_fixed']);
        $nb_errors_introduced = count($this->diffs['errors_introduced']);
        $nb_errors_fixed = count($this->diffs['errors_fixed']);

        $total_diffs =
            $nb_failures_introduced + $nb_errors_introduced +
                $nb_failures_fixed + $nb_errors_fixed;

        if ($total_diffs > 0)
        {
            $this->do_echo("\n\nThere were $total_diffs differences with baseline.\n

Below is a list of tests that differ from the baseline.
See above details about each error or failure.
");
            if ($nb_failures_introduced > 0)
            {
                $this->do_echo("\nNb of new FAILURES: $nb_failures_introduced:\n");
                foreach ($this->diffs['failures_introduced'] as $an_issue)
                {
                    $this->do_echo("   $an_issue\n");
                }
            }

            if ($nb_errors_introduced > 0)
            {
                $this->do_echo("\nNb of new ERRORS: $nb_errors_introduced:\n");
                foreach ($this->diffs['errors_introduced'] as $an_issue)
                {
                    $this->do_echo("   $an_issue\n");
                }

            }

            if ($nb_failures_fixed > 0)
            {
                $this->do_echo("\nNb of newly FIXED FAILURES: $nb_failures_fixed:\n");
                foreach ($this->diffs['failures_fixed'] as $an_issue)
                {
                    $this->do_echo("   $an_issue\n");
                }

            }

            if ($nb_errors_fixed > 0)
            {
                $this->do_echo("\nNb of newly FIXED ERRORS: $nb_errors_fixed:\n");
                foreach ($this->diffs['errors_fixed'] as $an_issue)
                {
                    $this->do_echo("   $an_issue\n");
                }

            }
        }
        else
        {
            $this->do_echo("\n\nNo differences with baseline run. All is \"normal\".\n\n");
        }

        $this->do_echo("\n\n");

    }

    function logpath_current()
    {

        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->logname_stem.".current.json";
        if ($this->current_log_fpath != null)
        {
          $path = $this->current_log_fpath;
        }
        return $path;
    }

    function logpath_baseline()
    {

        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->logname_stem.".baseline.json";
        if ($this->baseline_log_fpath != null)
        {
            $path = $this->baseline_log_fpath;
        }
        return $path;
    }

    function ask_if_want_to_create_baseline()
    {
        $answer = $this->prompt_for(
            "There is no baseline log. Would you like to log current failures and errors as the baseline?",
            array('y', 'n'));
        if ($answer == 'y')
        {
            $this->save_current_log_as_baseline();
        }
    }

    function process_phpunit_log_data($log_data)
    {
        global $tracer;

        $issues =
            array(
                'errors' => array(),
                'failures' => array(),
                'pass' => array()
            );

        foreach ($log_data as $log_entry)
        {
            $event = '';
            if (isset($log_entry['event']))
            {
                $event = $log_entry['event'];
            }

            if ($event != 'testStart' && $event != 'test')
            {
                continue;
            }

            $test = '';
            if (isset($log_entry['test']))
            {
                $test = $log_entry['test'];
            }

            if ($event == 'testStart')
            {
                $this->last_test_started = $test;
                continue;
            }
            else
            {
                $this->last_test_started = null;
            }

            /* For some reason, sometimes an event=test entry does not have
               a 'status' field.
               Whenever that happens, it seems to be a sign of an error.
            */
            $status = 'fail';
            if(isset($log_entry['status']))
            {
                $status = $log_entry['status'];
            }

            if ($status == 'fail')
            {
                array_push($issues['failures'], $test);
            }
            else if ($status == 'error')
            {
                array_push($issues['errors'], $test);
            }
            else if ($status == 'pass')
            {
                array_push($issues['pass'], $test);
            }
        }

        /* If a test was started by never ended, flag it as a failure */
        if ($this->last_test_started != null)
        {
            if (!in_array($this->last_test_started, $issues['failures'])) {
                array_push($issues['failures'], $this->last_test_started);
            }
        }

        return $issues;

    }

    function compare_two_test_runs($baseline_issues, $current_issues)
    {
        global $tracer;

        $diffs = array('failures_introduced' => array(), 'failures_fixed' => array(),
            'errors_introduced' => array(), 'errors_fixed' => array());

        $current_failures = $current_issues['failures'];
        $current_pass = $current_issues['pass'];
        $baseline_failures = $baseline_issues['failures'];
        $baseline_errors =  $baseline_issues['errors'];
        foreach ($baseline_failures as $a_baseline_failure)
        {
            if (in_array($a_baseline_failure, $current_pass))
            {
                array_push($diffs['failures_fixed'], $a_baseline_failure);
            }
        }

        foreach ($current_failures as $a_current_failure)
        {
            if (!in_array($a_current_failure, $baseline_failures) && !in_array($a_current_failure, $baseline_errors))
            {
                array_push($diffs['failures_introduced'], $a_current_failure);
            }
        }

        $baseline_errors = $baseline_issues['errors'];
        $current_errors = $current_issues['errors'];
        foreach ($baseline_errors as $a_baseline_error)
        {
            if (in_array($a_baseline_error, $current_pass))
            {
                array_push($diffs['errors_fixed'], $a_baseline_error);
            }

        }

        foreach ($current_errors as $a_current_error)
        {
            if (!in_array($a_current_error, $baseline_errors) && !in_array($a_current_error, $baseline_failures))
            {
                array_push($diffs['errors_introduced'], $a_current_error);
            }
        }

        return $diffs;
    }

    function save_current_log_as_baseline()
    {
        if ($this->total_new_issues_found() > 0)
        {
            $answer = $this->prompt_for(
                                "Some new failures and/or errors were introduced (see above for details).\n\nAre you SURE you want to save the current run as a baseline?\n",
                                array('y', 'n'));
            if ($answer == 'n')
            {
                $this->do_echo("\nThe current run was NOT saved as the new baseline.\n");
                return;
            }
        }

        $this->do_echo("\n\nSaving current phpunit log as the baseline.\n");
        copy($this->logpath_current(), $this->logpath_baseline());
    }

    function prompt_for($prompt, $eligible_answers)
    {
        $prompt = "\n\n$prompt (".implode('|', $eligible_answers).")\n> ";
        $answer = null;
        while ($answer ==null)
        {
            echo $prompt;
            $tentative_answer = rtrim(fgets(STDIN));
            if (in_array($tentative_answer, $eligible_answers))
            {
                $answer = $tentative_answer;
            }
            else
            {
                $prompt = "\n\nSorry, '$tentative_answer' is not a valid answer.$prompt";
            }
        }

        $this->do_echo("\$answer='$answer'\n'");
        return $answer;
    }

    function read_log_file($log_file_path)
    {
        global $tracer;

        $json_string = file_get_contents($log_file_path);

        // The json string is actually a sequence of json arrays, but the
        // sequence itself is not wrapped inside an array.
        //
        // Wrap all the json arrays into one before parsing the json.
        //
        $json_string = preg_replace('/}\s*{/', "},\n   {", $json_string);
        $json_string = "[\n   $json_string\n]";

        $json_decoded = json_decode($json_string,true);

        $issues = $this->process_phpunit_log_data($json_decoded);

        return $issues;
    }

    function config_from_cmdline_options()
    {
        global $argv, $tracer;

        $options = getopt('', array('action:', 'phpunit-options:', 'filter:', 'help'));
        $options = $this->validate_cmdline_options($options);

        if (isset($options['help']))
        {
            $this->help = true;
        }

        if (isset($options['action']))
        {
            $this->action = $options['action'];
        }

        if (isset($options['phpunit-options']))
        {
            $this->phpunit_options = $options['phpunit-options'];
        }

        if (isset($options['filter']))
        {
            $this->filter = $options['filter'];
        }
    }

    function validate_cmdline_options($options)
    {
        global $tracer;

        $action = '';
        if (isset($options['action'])) {
            $action = $options['action'];
        }
        if ($action == 'update_baseline' && isset($options['phpunit-options']))
        {
            $this->usage("Cannot specify --phpunit-options with --action=update_baseline.");
        }

        $phpunit_options = '';
        if (isset($options['phpunit-options']))
        {
            $phpunit_options = $options['phpunit-options'];
        }
        if (preg_match('/--log-json/', $phpunit_options))
        {
            $this->usage("You cannot specify '--log-json' option in the '--phpunit-options' option.");
        }
        if (preg_match('/--filter/', $phpunit_options))
        {
            $this->usage("You cannot specify '--filter' option in the '--phpunit-options' option. Instead, the --filter option of $argv[0] directely (i.e., '$argv[0] --filter pattern')");
        }



        return $options;
    }

    function usage($error_message = null)
    {
        global $argv;

        $script_name = $argv[0];

        $help = "php $script_name options

Run phpunit tests, and compare the list of errors and failures against
a baseline. Only report tests that have either started or stopped
failing.

Options

   --action run|update_baseline (Default: run)
        run:
           Run the tests and report diffs from baseline.

        update_baseline
           Run ALL the tests, and save the list of generated failures
           and errors as the new baseline.

   --filter pattern
         Only run the test methods whose names match the pattern.

   --phpunit-options options (Default: '')
        Command line options to be passed to phpunit.

        Those are ignored when --action=update_baseline.

        Also, you cannot specify a --log-json option in those, as that would
        interfere with the script's ability to log test results for comparison
        against the baseline.

";

        if ($error_message != null)
        {
            $help = "ERROR: $error_message\n\n$help";
        }

        exit("\n$help");
    }

    function make_empty_issues_list()
    {
        $issues =
            array('pass' => array(), 'failures' => array(), 'errors' => array());

        return $issues;
    }

    function total_new_issues_found()
    {
        global $tracer;
        $total = count($this->diffs['errors_introduced']) + count($this->diffs['failures_introduced']);

        $tracer->trace('total_new_issues_found', "** Returning \$total=$total");

        return $total;
    }

    private function do_echo($message)
    {
        if ($this->output_fpath == null)
        {
            echo($message);
        }
        else
        {
            $fh_output = fopen($this->output_fpath, 'a');
            fwrite($fh_output, $message);
            fclose($fh_output);
        }
    }
}