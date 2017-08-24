#!/usr/bin/php
<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\HelpCommand;
use Language;
use Language_FileType_Php;

if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}

$tikiBase = realpath(dirname(__FILE__). '/../..');
require_once $tikiBase . '/vendor_bundled/vendor/autoload.php';


/**
 * Add a singleton command "englishupdate" using the Symfony console component for this script
 *
 * Class EnglishUpdateCommand
 * @package Tiki\Command
 */

class EnglishUpdateCommand extends Command
{

	protected function configure()
	{
		$this
			->setName('englishupdate')
			->setDescription("Update translation files with updates made to English strings. Will compare working copy by default.")
			->addOption(
				'revision',
				'r',
				InputOption::VALUE_REQUIRED,
				'Revision numbers may be selected eg. 63000:63010, or simply 63000 to update strings from 63000 onward.'
			)
			->addOption(
				'lag',
				'l',
				InputOption::VALUE_REQUIRED,
				'Search through previous commits by X number of days, for updated translation strings. Working copy will be ignored.'
			)
			->addOption(
				'audit',
				'a',
				InputOption::VALUE_NONE,
				'Reports any translation strings that have been broken. Will not change repository. '
			)
			->addOption(
				'email',
				'e',
				InputOption::VALUE_REQUIRED,
				'Email address to send a message to if untranslated strings are found. Must be used in conjunction with "audit".'
			);
	}

	/**
	 * The total number of changed strings
	 * @var int
	 */
	private $stringCount = 0;


	/**
	 * The number of identical original & changed pairs found.
	 * @var int
	 */
	private $duplicates = 0;

	/**
	 * An array of all the language directories in Tiki
	 * @var array
	 */
	private $languages;
	/**
	 *
	 * Seperates svn diff output into changes made in PHP and TPL files
	 *
	 * @param $content string raw svn diff output
	 *
	 * @return array with [0] containing PHP and [1] containing TPL strings
	 */

	private function separatePhpTpl($content)
	{

		$content .= "\nIndex:  \n=";                            // used as a dummy to match the last entry

		// Separate php and tpl content
		preg_match_all('/^Index:\s.+(php|tpl)$\n={10}([\w\W]+?)(?=^Index:.+\n=)/m', $content, $phpTpl);

		$changes['php'] = '';
		$changes['tpl'] = '';
		$count = 0;
		while ($count < count($phpTpl[1])) {

			if ($phpTpl[1][$count] === 'php') {
				$changes['php'] .= $phpTpl[2][$count];
			} else if ($phpTpl[1][$count] === 'tpl') {
				$changes['tpl'] .= $phpTpl[2][$count];
			}
			$count++;
		}

		return $changes;
	}

	/**
	 * @param $content string diff content to split into pairs of removed and added content
	 *
	 * @return array equal pairs of added and removed diff content
	 */

	private function pairMatches($content)
	{

		/**
		 * @var $pairedMatches array any changes that took away and added lines.
		 */

		// strip some diff verbiage to prevent conflict in next match
		$content = preg_replace('/(?>---|\+\+\+)\s.*\)$/m', '', $content);
		// place in an array changes that have multiple lines changes
		preg_match_all('/(\n[-+].*){2,}/m', $content, $diffs);

		$content = $diffs[0];
		unset ($diffs);

		$pairs = array();
		foreach ($content as $diff) {

			//now trim it down so its a - then + pair
			if (preg_match('/^-[\s\S]*^\+.*/m', $diff, $pair)) {

				// now extract a equally paired sets
				$count = min(preg_match_all('/^-/m', $pair[0]), preg_match_all('/^\+/m', $pair[0]));
				if ($count) {
					preg_match('/(?>\n-.*){' . $count . '}(?>\n\+.*){' . $count . '}/', "\n" . $pair[0], $equilPair);
					$pairs[] = $equilPair[0];
				}
			}
		}

		unset ($content);
		$count = 0;
		$pairedMatches = array();

		foreach ($pairs as $pair) {
			if (preg_match_all('/^-(.*)/m', $pair, $negativeMatch)) {
				if (preg_match_all('/^\+(.*)/m', $pair, $positiveMatch)) {
					$pairedMatches[$count]['-'] = implode(' ', $negativeMatch[1]);
					$pairedMatches[$count]['+'] = implode(' ', $positiveMatch[1]);
					$count++;
				}
			}
		}

		return $pairedMatches;
	}

	/**
	 * Takes a semi-prepared list of commit changes (from a diff) and extracts pairs of original and changed translatoion strings
	 *
	 * @param $content array of equally paired diff content pairs of removed and added, previously precessed by pairMatches()
	 * @param $file string can be 'php' or 'tpl'. Will determine how strings are extracted.
	 *
	 * @return array extracted strings
	 */
	private function pairStrings($content, $file)
	{

		$count = 0;
		$pairedStrings = array();

		// set what regex to use depending on file type.
		if ($file === 'php') {
			$regex = '/\Wtra?\s*\(\s*([\'"])(.+?)\1\s*[\),]/';
			$php = new Language_FileType_Php;

		} else
			$regex = '/\{(t)r(?:\s+[^\}]*)?\}(.+?)\{\/tr\}/';

		foreach ($content as $pair) {
			if (preg_match_all($regex, $pair['-'], $negativeMatch)) {
				if (preg_match_all($regex, $pair['+'], $positiveMatch)) {
					// strip out any changes that have a dissimilar number of translation strings. No way to match them properly :(
					if (count($negativeMatch[1]) === count($positiveMatch[1])) {

						// content needs post processing based on single or double quote matches
						if (isset($negativeMatch[1][0])) {
							if ($negativeMatch[1][0] == "'") {
								$negativeMatch[2] = $php->singleQuoted($negativeMatch[2]);
							} else if ($negativeMatch[1][0] == '"'){
								$negativeMatch[2] = $php->doubleQuoted($negativeMatch[2]);
							}
							if ($positiveMatch[1][0] == "'") {
								$positiveMatch[2] = $php->singleQuoted($positiveMatch[2]);
							} else if ($positiveMatch[1][0] == '"')
								$positiveMatch[2] = $php->doubleQuoted($positiveMatch[2]);
						}
						$pairedStrings[$count]['-'] = $negativeMatch[2];
						$pairedStrings[$count]['+'] = $positiveMatch[2];
						$count++;
					}
				}
			}
		}

		return $pairedStrings;

	}

	/**
	 * Filters, formats & escapes paired translation strings to produce a final list of translation changes.
	 *
	 * @param $content array paired strings previously processed by pairStrings()
	 *
	 * @return array A final list of before and after translation strings to update.
	 */

	private function filterStrings($content)
	{

		$updateStrings = array();
		foreach ($content as $strings) {
			$count = 0;
			while (isset($strings['-'][$count])) {
				// strip any end punctuation from both strings to support tikis punctuations translation functionality.
				if (in_array(substr($strings['-'][$count], -1), Language::punctuations))
					$strings['-'][$count] = substr($strings['-'][$count], 0, -1);
				if (in_array(substr($strings['+'][$count], -1), Language::punctuations))
					$strings['+'][$count] = substr($strings['+'][$count], 0, -1);

				if ($strings['-'][$count] !== $strings['+'][$count]) {
					$updateStrings[$this->stringCount]['-'] = Language::addPhpSlashes($strings['-'][$count]);
					$updateStrings[$this->stringCount]['+'] = Language::addPhpSlashes($strings['+'][$count]);
					$this->stringCount++;
				}
				$count++;
			}
		}

		return $updateStrings;
	}

	/**
	 * Takes a paired list of original and replacement strings and checks if they are identical
	 *
	 * @param $content array paired string, that has previously been processed by filterStrings()
	 *
	 * @return array return an array of paired strings with duplicate entries omitted
	 */

	private function removeIdentical($content){

		$filtered = array();
		foreach ($content as $array) {
			if(!in_array($array, $filtered)){
				$filtered[] = $array;
			}
		}
		$this->duplicates = $this->stringCount - count($filtered);
		$this->stringCount -= $this->duplicates;

		return $filtered;

	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$tikiBase = realpath(dirname(__FILE__) . '/../..');

		$output->writeln('*******************************************************');
		$output->writeln('*                     <info>Limitations</info>                     *');
		$output->writeln('* Will not find strings if they span multiple lines.  *');
		$output->writeln('*                                                     *');
		$output->writeln('* Will not match strings if a translation string has  *');
		$output->writeln('* been added or removed on the line above or below.   *');
		$output->writeln('*******************************************************');
		$output->writeln('');

		$rev = '';
		// check that email is being used in audit mode
		if ($input->getOption('email') && !$input->getOption('audit')){
			$help = new HelpCommand();
			$help->setCommand($this);
			$help->run($input, $output);

			return $output->writeln(' --email, only available when running in --audit mode.');

		}
		// check that the --lag option is valid, and complain if its not.
		if ($input->getOption('lag')) {
			if ($input->getOption('lag') < 0 || !is_numeric($input->getOption('lag'))) {
				$help = new HelpCommand();
				$help->setCommand($this);
				$help->run($input, $output);

				return $output->writeln('Invalid option for --lag, must be a positive integer.');
			}
			// current time minus number of days specified through lag
			$rev = date('{"Y-m-d H:i"}', time() - $input->getOption('lag') * 60 * 60 * 24);
			$rev = '-r ' . $rev;
		} else if ($input->getOption('revision')) {
			$rev = '-r' . $input->getOption('revision');
		}

		$this->languages = glob($tikiBase . '/lang/*', GLOB_ONLYDIR);

		$progress = new ProgressBar($output, count($this->languages) + 7);
		if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE)
			$progress->setOverwrite(false);
		$progress->setFormatDefinition('custom', ' %current%/%max% [%bar%] -- %message%');
		$progress->setFormat('custom');

		$progress->setMessage('Checking System');
		$progress->start();


		// die gracefully if shell_exec is not enabled;
		if (!is_callable('shell_exec')) {
			$progress->setMessage('<error>Translation string update Failed. Could not execute shell_exec()</error>');
			$progress->finish();

			return false;
		}

		$progress->setMessage('Getting String Changes');
		$progress->advance();

		$raw = shell_exec("svn diff $rev 2>&1");

		$progress->setMessage('Finding Updated Strings');
		$progress->advance();

		// strip any empty translation strings now to avoid complexities later
		$raw = preg_replace('/tra?\(["\'](\s*?)[\'"]\)/m', '', $raw);

		$output->writeln($raw, OutputInterface::VERBOSITY_DEBUG);

		$diffs = $this->separatePhpTpl($raw);

		$output->writeln(var_export($diffs, true), OutputInterface::VERBOSITY_DEBUG);

		$diffs['php'] = $this->pairMatches($diffs['php']);
		$diffs['tpl'] = $this->pairMatches($diffs['tpl']);

		$progress->setMessage('Found ' . count($diffs['php']) . ' PHP and ' . count($diffs['tpl']) . ' TPL changes');
		$progress->advance();

		$output->writeln(var_export($diffs, true), OutputInterface::VERBOSITY_DEBUG);

		$diffs['php'] = $this->pairStrings($diffs['php'], 'php');
		$diffs['tpl'] = $this->pairStrings($diffs['tpl'], 'tpl');
		$diffs = array_merge($diffs['php'], $diffs['tpl']);

		$progress->setMessage('Found ' . count($diffs) . ' String pairs');
		$progress->advance();

		$output->writeln(var_export($diffs, true), OutputInterface::VERBOSITY_DEBUG);

		$diffs = $this->filterStrings($diffs);

		$progress->setMessage("Found $this->stringCount translation strings");
		$progress->advance();

		$diffs = $this->removeIdentical($diffs);

		$progress->setMessage('Found ' . $this->duplicates . ' duplicate translation strings');
		$progress->advance();

		if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
			$output->writeln("\n\n<info>Strings Being Updated</info>\n");
			foreach ($diffs as $diff) {
				$output->writeln('* ' . $diff['-']);
				$output->writeln('* ' . $diff['+'] . "\n");
			}
		}

		/**
		 * Tokens indicating that the replacement sting was found and replaced in the language file
		 * @ver array
		 */
		$string = array();

		/**
		 * Tokens indicating that the replacement string was already present in the language file, so was skipped
		 * @var array
		 */
		$skipped = array();

		/**
		 * Tokens indicating what language files have had changes made to them
		 * @var array
		 */
		$lang = array();

		// update the language files with the new strings

		if ($this->stringCount) {
			foreach ($this->languages as $directory) {
				$langNow = substr($directory, strrpos($directory, "/") + 1);
				if (is_writable($directory . '/language.php')) {
					$file = file_get_contents($directory . '/language.php');
					foreach ($diffs as $key => $entry) {
						// if the original string is in the language file
						if (preg_match('/.*?"' . preg_quote($entry['-'], '/') . '[' . implode('', Language::punctuations) . ']?".*/', $file, $match)) {
							// if the replacement string does not already exist
							if (!strpos($file, '"' . $entry['+'] . '"')) {
								// then replace the original string with an exact copy and a 'updated' copy on the next line
								$replace = preg_replace('/"' . preg_quote($entry['-'], '/') . '[' . implode('', Language::punctuations) . ']?"/', '"' . $entry['+'] . '"', $match[0], 2);
								$file = str_replace($match[0], $match[0] . "\n" . $replace, $file);

								// keep track of overall numbers
								$string[$key] = true;
								$lang[$langNow] = true;
							} else {
								$skipped[$key] = true;
							}
						}
					}
					if (isset($lang[$langNow])) {
						$progress->setMessage($langNow . "\tStrings to update");
						$progress->advance();
						if (!$input->getOption('audit'))
							file_put_contents($directory . '/language.php', $file);
					} else {
						$progress->setMessage($langNow . "\tNo changes to make");
						$progress->advance();
					}
				} else {
					$progress->setMessage($langNow . "\tSkipping <info>language.php not writable</info>");
					$progress->advance();
				}
			}
		}
		$skippedMessage = '';
		if ($this->duplicates)
			$skippedMessage = ' Skipped ' . $this->duplicates . ' duplicate strings.';

		if ($input->getOption('audit')) {
			$updateMessage = 'Out of Sync';
		}else{
			$updateMessage = 'Updated';
		}
		$progress->setMessage(count($string) . " of $this->stringCount strings $updateMessage in " . count($lang) . ' of ' . count($this->languages) . ' language files.' . $skippedMessage);
		$progress->finish();

		if ($input->getOption('audit')) {
			if (count($string)) {
				$syncMessage= "\n";
				$output->writeln("\n\n<info>Updated Strings not found in Language Files</info>");
				foreach ($diffs as $key => $entry) {
					if (isset($string[$key]))
						$syncMessage.='* ' . $entry['-']."\n";
				}
				$output->writeln($syncMessage);
				if ($input->getOption('email')) {
					mail($input->getOption('email'), 'Updated Strings not found in Language Files', wordwrap($tikiBase."\n".$syncMessage, 70, "\r\n"));
				}
			} else {
				$output->writeln("\n\n<info>English and Translations are in Sync</info>\n");
			}
			// if were not in audit mode
		} else {
			if (count($string) < $this->stringCount) {
				$output->writeln("\n\n<info>Original Strings not Found in Language Files</info>");
				foreach ($diffs as $key => $entry) {
					if (!isset($string[$key]) && !isset($skipped[$key]))
						$output->writeln('* ' . $entry['-']);
				}
			}
			$output->writeln("\n\nOptionally run php get_strings.php to remove any unused translation strings.");
			$output->writeln("Verify before committing.\n");
		}
		return true;
	}
}

// create the application and new console
$console = new Application;
$console->add(new EnglishUpdateCommand);
$console->setDefaultCommand('englishupdate');
$console->run();
