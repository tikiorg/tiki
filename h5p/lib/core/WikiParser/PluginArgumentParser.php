<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiParser_PluginArgumentParser
{
	function parse( $data )
	{
		$arguments = array();
		$data = TikiLib::lib("parser")->unprotectSpecialChars($data, true);	// need to get &quot; converted back to " etc

		// Handle parameters one by one
		while (is_string($data) && false !== $pos = strpos($data, '=') ) {
			$name = substr($data, 0, $pos);
			$name = ltrim($name, ', ');
			$name = trim($name);
			$value = '';

			if (strlen($data) == $pos + 1) {
				break;
			}

			// Consider =>
			if ( $data{$pos + 1} == '>' )
				$pos++;

			// Cut off the name part
			$data = substr($data, $pos + 1);
			$data = ltrim($data);

			if ( !empty($data) && $data{0} == '"' ) {
				$quote = 0;
				// Parameter between quotes, find closing quote not escaped by a \
				while ( false !== $quote = strpos($data, '"', $quote + 1) ) {
					if ( $data{$quote - 1} != "\\" )
						break;
				}

				// Closing quote found
				if ( $quote !== false ) {
					$value = substr($data, 1, $quote - 1);
					$arguments[$name] = str_replace('\"', '"', $value);

					$data = substr($data, $quote + 1);
					continue;
				}

				// Not found, fallback as if opening quote was part of the string
			}

			// If last parameter, consider next as end of string
			if (preg_match("/[\s,]\w+=/", $data, $parts)) {
				$end = strpos($data, $parts[0]);
				$value = substr($data, 0, $end);
				$data = substr($data, $end);
			} else {
				$value = $data;
				$data = '';
			}

			$value = rtrim($value, ', ');
			$arguments[$name] = $value;
		}

		return $arguments;
	}
}
