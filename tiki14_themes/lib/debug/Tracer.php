<?php
  //
  // Simple class for printing traces to a file.
  //
  // To print a trace:
  //
  //   require_once('lib/debug/Tracer.php');
  //   $tracer->trace('some_trace_id', "Hello world");
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
class Tracer
{

	public $traces_are_on = false;
	public $trace_file_path = NULL;
	public $tiki_trace_active_ids = array();

	public function __construct($trace_file_path, $traces_are_on=false, $traces_active_ids = null)
	{
        if ($traces_active_ids == null)
        {
            $traces_active_ids = array();
        }
		$this->trace_file_path = $trace_file_path;
		$this->traces_are_on = $traces_are_on;
        if (isset($traces_active_ids))
        {
            $this->tiki_trace_active_ids = array_merge($traces_active_ids, array());
        }
        else
        {
            $this->tiki_trace_active_ids = array();
        }
		$this->tiki_trace_active_ids = array_merge($traces_active_ids, array());
		if ($trace_file_path != NULL) {
			file_put_contents($this->trace_file_path, '');
		}
	}

	public function trace($trace_id, $message)
	{
		if ($this->traces_are_on && $this->trace_file_path != NULL &&
			in_array($trace_id, $this->tiki_trace_active_ids)) {
			file_put_contents($this->trace_file_path, "-- $trace_id: $message\n", FILE_APPEND);
		}
	}

    public function clear_trace_file()
    {
        file_put_contents($this->trace_file_path, "");
    }

	//
	// Method for pretty printing a data structure as a "human readable"
	// JSON string
	//
	function pretty_print($in, $indent = 0, Closure $_escape = null)
	{
        //
        // Pretty printing of a large data structure can consume time if it is called often.
        // We wouldn't want that to happen in a production context where some traces were
        // left behind in the code.
        // To avoid this, we only do the pretty_print if traces are on.
        //
		if (!$this->traces_are_on) {
			return "WARNING: Pretty print not carried out because traces are not active.";
		}

		if (__CLASS__ && isset($this)) {
			$_myself = array($this, __FUNCTION__);
		} elseif (__CLASS__) {
			$_myself = array('self', __FUNCTION__);
		} else {
			$_myself = __FUNCTION__;
		}

		if (is_null($_escape)) {
			$_escape = function ($str) {
				return str_replace(
					array('\\', '"', "\n", "\r", "\b", "\f", "\t", '/', '\\\\u'),
					array('\\\\', '\\"', "\\n", "\\r", "\\b", "\\f", "\\t", '\\/', '\\u'),
					$str
				);
			};
		}

		$out = '';

		foreach ($in as $key => $value) {
			$out .= str_repeat("\t", $indent + 1);
			$out .= "\"" . $_escape((string)$key) . "\": ";

			if (is_object($value) || is_array($value)) {
				$out .= "\n";
				$out .= call_user_func($_myself, $value, $indent + 1, $_escape);
			} elseif (is_bool($value)) {
				$out .= $value ? 'true' : 'false';
			} elseif (is_null($value)) {
				$out .= 'null';
			} elseif (is_string($value)) {
				$out .= "\"" . $_escape($value) . "\"";
			} else {
				$out .= $value;
			}

			$out .= ",\n";
		}

		if (!empty($out)) {
			$out = substr($out, 0, -2);
		}

		$out = str_repeat("\t", $indent) . "{\n" . $out;
		$out .= "\n" . str_repeat("\t", $indent) . "}";

		return $out;
	}
}

if (file_exists('db/local.php')) {
  include 'db/local.php';
}
global $tiki_traces_fpath, $tiki_traces_are_on, $tiki_traces_active_ids;
$tracer = new Tracer($tiki_traces_fpath, $tiki_traces_are_on, $tiki_traces_active_ids);
