<?php

class TikiFilter_PrepareInput
{
	private $delimiter;

	function __construct($delimiter)
	{
		$this->delimiter = $delimiter;
	}

	function prepare(array $input)
	{
		$output = array();

		foreach ($input as $key => $value) {
			if (strpos($key, $this->delimiter) === false ) {
				$output[$key] = $value;
			} else {
				list ($base, $remain) = explode($this->delimiter, $key, 2);

				if (! isset($output[$base]) || ! is_array($output[$base])) {
					$output[$base] = array();
				}

				$output[$base][$remain] = $value;
			}
		}

		foreach ($output as $key => & $value) {
			if (is_array($value)) {
				$value = $this->prepare($value);
			}
		}

		return $output;
	}
}

