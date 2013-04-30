<?php
//
// Simple class for printing traces to a file.
//
// To print a trace:
//
//   require_once('lib/Trace.php');
//   trace('some_trace_id', "Hello world");
//
// This will print the following to the trace file
//
//   -- some_trace_id: Hello world
//
// But only if tracing is active for id 'some_trace_id' is active.
// By default, all traces are inactive.
// To activate them, you must add the following to db/local.php
//
//    $tiki_traces_are_on = true;
//    $tiki_traces_fpath = '/some/path/that/is/writeable/by/apache.txt';
//    $tiki_traces_active_ids =
//       array(
//           'some_trace_id_that_you_want_to_be_active',
//           'some_other_trace_id_that_you_want_to_be_active',
//           etc...
//       );
//
// To deactivate an individual trace, simply remove it from the $tiki_traces_active_ids
// in db/local.php.
//
// To deactivate all traces in one go, set $tiki_traces_are_on = false in db/local.php.
//
class Tracer {

    public $traces_are_on = false;
    public $trace_file_path = NULL;
    public $tiki_trace_active_ids = NULL;

    public function __construct($trace_file_path, $traces_are_on=false, $traces_active_ids) {
        $this->trace_file_path = $trace_file_path;
        $this->traces_are_on = $traces_are_on;
        $this->tiki_trace_active_ids = array_merge($traces_active_ids, array());
        if ($trace_file_path != NULL) {
            file_put_contents ($this->trace_file_path, '');
        }
    }

    public function trace($trace_id, $message) {
        if ($this->traces_are_on && $this->trace_file_path != NULL &&
            in_array($trace_id, $this->tiki_trace_active_ids)) {
            file_put_contents($this->trace_file_path, "-- $trace_id: $message\n", FILE_APPEND);
        }
    }
}

if (file_exists('db/local.php')) {
    include 'db/local.php';
}
global $tiki_traces_fpath, $tiki_traces_are_on, $tiki_traces_active_ids;
$tracer = new Tracer($tiki_traces_fpath, $tiki_traces_are_on, $tiki_traces_active_ids);
