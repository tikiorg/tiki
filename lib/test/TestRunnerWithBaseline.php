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

    private $logname_stem = 'phpunit-log';
    public $action = 'run'; // run|update_baseline
    public $phpunit_options = '';

    function run()
    {
        global $tracer;

        $this->config_from_cmdline_options();

        if ($this->help != null) {
            $this->usage();
        } else if ($this->action == 'run')
        {
            $this->run_tests_with_possibly_nonstandard_options();
        }
        else if ($this->action == 'update_baseline')
        {
            $this->run_all_tests_and_save_results_as_baseline();
        }
    }

    function run_tests_with_possibly_nonstandard_options()
    {
        $this->run_tests($this->phpunit_options);

        if (!file_exists($this->logname_baseline()))
        {
            $this->ask_if_want_to_create_baseline();
        }
        else
        {
            $this->print_diffs_with_baseline();
        }
    }

    function run_all_tests_and_save_results_as_baseline()
    {
        $this->run_tests();
        $this->save_current_log_as_baseline();
    }

    function run_tests($phpunit_options_and_args_string = "")
    {
        global $tracer;

        $cmd_line = "../../vendor/bin/phpunit $phpunit_options_and_args_string --log-json ".$this->logname_current()." .";
        system($cmd_line);

    }

    function print_diffs_with_baseline()
    {
        global $tracer;

        echo "\n\nChecking for differences with baseline test logs...\n\n";

        $baseline_issues = $this->read_log_file($this->logname_baseline());
        $current_issues = $this->read_log_file($this->logname_current());

        $diffs = $this->compare_two_test_runs($baseline_issues, $current_issues);

        $nb_failures_introduced = count($diffs['failures_introduced']);
        $nb_failures_fixed = count($diffs['failures_fixed']);
        $nb_errors_introduced = count($diffs['errors_introduced']);
        $nb_errors_fixed = count($diffs['errors_fixed']);

        $total_diffs =
            $nb_failures_introduced + $nb_errors_introduced +
                $nb_failures_fixed + $nb_errors_fixed;

        if ($total_diffs > 0)
        {
            echo "\n\nThere were $total_diffs differences with baseline.\n";
            if ($nb_failures_introduced > 0)
            {
                echo "\nNb of new FAILURES: $nb_failures_introduced:\n";
                foreach ($diffs['failures_introduced'] as $an_issue)
                {
                    echo "   $an_issue\n";
                }
            }

            if ($nb_errors_introduced > 0)
            {
                echo "\nNb of new ERRORS: $nb_errors_introduced:\n";
                foreach ($diffs['errors_introduced'] as $an_issue)
                {
                    echo "   $an_issue\n";
                }

            }

            if ($nb_failures_fixed > 0)
            {
                echo "\nNb of newly FIXED FAILURES: $nb_failures_fixed:\n";
                foreach ($diffs['failures_fixed'] as $an_issue)
                {
                    echo "   $an_issue\n";
                }

            }

            if ($nb_errors_fixed > 0)
            {
                echo "\nNb of newly FIXED ERRORS: $nb_errors_fixed:\n";
                foreach ($diffs['errors_fixed'] as $an_issue)
                {
                    echo "   $an_issue\n";
                }

            }
        }
        else
        {
            echo "\n\nNo differences with baseline run. All is \"normal\".\n\n";
        }

        echo "\n\n";

    }

    function logname_current()
    {
        return $this->logname_stem.".current.json";
    }

    function logname_baseline()
    {
        return $this->logname_stem.".baseline.json";
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
            if (! ((isset($log_entry['event']) && ($log_entry['event'] == 'test'))))
            {
                continue;
            }

            if (!isset($log_entry['test']))
            {
                continue;
            }
            $test = $log_entry['test'];

            if(!isset($log_entry['status']))
            {
                continue;
            }
            $status = $log_entry['status'];

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
        foreach ($baseline_failures as $a_baseline_failure)
        {
            if (in_array($a_baseline_failure, $current_pass))
            {
                array_push($diffs['failures_fixed'], $a_baseline_failure);
            }
        }

        foreach ($current_failures as $a_current_failure)
        {
            if (!in_array($a_current_failure, $baseline_failures))
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
            if (!in_array($a_current_error, $baseline_errors))
            {
                array_push($diffs['errors_introduced'], $a_current_error);
            }
        }

        return $diffs;
    }

    function save_current_log_as_baseline()
    {
        echo "\n\nSaving current phpunit log as the baseline.\n";
        copy($this->logname_current(), $this->logname_baseline());
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

        print "\$answer='$answer'\n'";
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

        $options = getopt('', array('action:', 'phpunit-options:', 'help'));
        $options = $this->validate_cmdline_options($options);

        if (isset($options['help']))
        {
            $this->help = 1;
        }

        if (isset($options['action']))
        {
            $this->action = $options['action'];
        }

        if (isset($options['phpunit-options']))
        {
            $this->phpunit_options = $options['phpunit-options'];
        }
    }

    function validate_cmdline_options($options)
    {
        global $tracer;

        if ($options['action'] == 'update_baseline' && isset($options['phpuni-options']))
        {
            $this->usage("Cannot specify --phpunit-options with --action=update_baseline.");
        }

        if (preg_match('/--log-json/', $options['phpunit-options']))
        {
            $this->usage("You cannot specify '--log-json' option in the '--phpunit-options' option.");
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
}