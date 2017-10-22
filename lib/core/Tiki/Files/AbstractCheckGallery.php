<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Files;

use DirectoryIterator;

/**
 * Holds the methods shared between the different gallery check classes
 */
abstract class AbstractCheckGallery
{
	/**
	 * Entry point to analyse one type of gallery
	 *
	 * @return array The finding of the analyses
	 */
	abstract public function analyse();

	/**
	 * Return a list of files in a given directory
	 *
	 * @param string $path
	 * @return array
	 */
	protected function listFilesInDirectory($path)
	{
		if (! is_dir($path)) {
			return [];
		}

		$fileList = [];
		foreach (new DirectoryIterator($path) as $fileInfo) {
			if ($fileInfo->isDot()) {
				continue;
			}
			$fileList[] = [
				'name' => $fileInfo->getFilename(),
				'path' => $path,
				'size' => $fileInfo->getSize(),
			];
		}

		return $fileList;
	}

	/**
	 * Performs the match between 2 lists of files, including checking if the properties match
	 *
	 * @param array $expected
	 * @param array $actual
	 * @param array $suffixes
	 * @param null|function $callback
	 * @return array
	 */
	protected function matchFileList($expected, $actual, $suffixes = [], $callback = null)
	{
		$missing = [];
		$mismatch = [];

		$actualIndexed = [];
		foreach ($actual as $item) {
			$actualIndexed[$item['name']] = $item;
		}

		$expectedIndexed = [];
		foreach ($expected as $item) {
			$expectedIndexed[$item['name']] = $item;
			if (! array_key_exists($item['name'], $actualIndexed)) {
				$missing[] = $item;
			} elseif ($item['size'] != $actualIndexed[$item['name']]['size']) {
				$mismatch[] = $item;
			}
		}

		$unknown = $this->filterKnownFilesUsingSuffixes($actualIndexed, $expectedIndexed, $suffixes);

		if (is_callable($callback)) {
			$unknown = call_user_func($callback, $unknown, $actualIndexed, $expectedIndexed);
		}

		return [$missing, $mismatch, $unknown];
	}

	/**
	 * Returns the list of files that are not known, by filtering the known and eventual combinations with suffixes
	 *
	 * @param array $actualIndexed
	 * @param array $expectedIndexed
	 * @param array $suffixes
	 * @return array
	 */
	protected function filterKnownFilesUsingSuffixes($actualIndexed, $expectedIndexed, $suffixes)
	{
		$possibleValues = [];
		foreach (array_keys($expectedIndexed) as $key) {
			$possibleValues[$key] = $key;
			foreach ($suffixes as $suffix) {
				$newKey = $key . $suffix;
				$possibleValues[$newKey] = $newKey;
			}
		}
		return array_values(array_diff_key($actualIndexed, $possibleValues));
	}
}
