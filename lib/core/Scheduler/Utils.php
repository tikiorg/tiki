<?php

class Scheduler_Utils
{

	/**
	 * Checks if a cron should run at a time.
	 *
	 * @param $time int Timestamp of the time to run
	 * @param $cron string A cron time expression (ex.: 0 0 * * *)
	 * @return bool true if should run, false otherwise.
	 * @throws \Scheduler\Exception\CrontimeFormatException
	 */
	public static function is_time_cron($time, $cron)
	{
		if (! self::validate_cron_time_format($cron)) {
			throw new Scheduler\Exception\CrontimeFormatException(tra('Invalid cron time format'));
		}

		list($min, $hour, $day, $mon, $week) = explode(' ', $cron);

		$to_check = ['min' => 'i', 'hour' => 'G', 'day' => 'j', 'mon' => 'n', 'week' => 'w'];

		$ranges = [
			'min' => '0-59',
			'hour' => '0-23',
			'day' => '1-31',
			'mon' => '1-12',
			'week' => '0-6',
		];

		foreach ($to_check as $part => $c) {
			$val = $$part;
			$values = [];

			/*
				For patterns like 0-23/2
			*/
			if (strpos($val, '/') !== false) {
				//Get the range and step
				list($range, $steps) = explode('/', $val);

				//Now get the start and stop
				if ($range == '*') {
					$range = $ranges[$part];
				}
				list($start, $stop) = explode('-', $range);

				for ($i = $start; $i <= $stop; $i = $i + $steps) {
					$values[] = $i;
				}
			} /*
			For patterns like :
			2
			2,5,8
			2-23
			*/
			else {
				$k = explode(',', $val);

				foreach ($k as $v) {
					if (strpos($v, '-') !== false) {
						list($start, $stop) = explode('-', $v);

						for ($i = $start; $i <= $stop; $i++) {
							$values[] = $i;
						}
					} else {
						$values[] = $v;
					}
				}
			}

			if (! in_array(date($c, $time), $values) and (strval($val) != '*')) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate a cron time string
	 *
	 * @param $cron string A cron time expression (ex.: 0 0 * * *)
	 * @return bool true if valid, false otherwise
	 */
	public static function validate_cron_time_format($cron)
	{

		$regex = '/^(\\*|((\\*\\/)?[1-5]?[0-9])|[1-5]?[0-9]-[1-5]?[0-9]|[1-5]?[0-9](,[1-5]?[0-9])*) (\\*|((\\*\\/)?(1?[0-9]|2[0-3]))|(1?[0-9]|2[0-3])-(1?[0-9]|2[0-3])|(1?[0-9]|2[0-3])(,(1?[0-9]|2[0-3]))*) (\\*|((\\*\\/)?([1-9]|[12][0-9]|3[0-1]))|([1-9]|[12][0-9]|3[0-1])-([1-9]|[12][0-9]|3[0-1])|([1-9]|[12][0-9]|3[0-1])(,([1-9]|[12][0-9]|3[0-1]))*) (\\*|((\\*\\/)?([1-9]|1[0-2])|([1-9]|1[0-2])-([1-9]|1[0-2])|([1-9]|1[0-2])(,([1-9]|1[0-2]))*)) (\\*|((\\*\\/)?[0-6])|[0-6](,[0-6])*|[0-6]-[0-6])$/';
		preg_match($regex, $cron, $matches);

		return ! empty($matches);
	}
}
