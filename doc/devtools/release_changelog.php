<?php
define( 'TOOLS', dirname(__FILE__) );
define( 'ROOT', realpath( TOOLS . '/../..' ) );
define( 'CHANGELOG', ROOT . '/changelog.txt' );

require_once TOOLS . '/svntools.php';

if( $_SERVER['argc'] <= 1 )
	die( "Usage: php doc/devtools/release_changelog.php <version-number>
Example:
	php doc/devtools/release_changelog.php 2.0
" );
$newVersion = $_SERVER['argv'][1];

if ( empty($newVersion) ) {
	error('No version specified.');
	die;
}
if ( ! is_readable(CHANGELOG) || ! is_writable(CHANGELOG) ) {
	error('The changelog file "' . CHANGELOG . '" is not readable or writable.');
	die;
}

important('Updating changelog for version '.$newVersion.'...');

$isNewMajorVersion = substr($newVersion, -1) == 0;
$releaseNotesURL = '<http://tikiwiki.org/ReleaseNotes'.str_replace('.', '', $newVersion).'>';
$parseLogs = false;
$handle = @fopen(CHANGELOG, "r");
$minRevision = $currentParsedRevision = $lastReleaseMajorNumber = 0;
$lastReleaseLogs = array();
$versionMatches = array();
$newChangelog = '';
$newChangelogEnd = '';

if ($handle) {
	while (!feof($handle)) {
		$buffer = fgets($handle);
		if ( empty($buffer) ) continue;

		if ( preg_match('/^Version (\d+)\.(\d+)/', $buffer, $versionMatches) ) {
			if ( $versionMatches[1].'.'.$versionMatches[2] == $newVersion ) {
				error('The changelog file already contains log for the version '.$newVersion.'.');
				die;
			}
			if ( $lastReleaseMajorNumber == 0 || $parseLogs ) {
				$parseLogs = (
					$lastReleaseMajorNumber == 0
					|| ( ! $isNewMajorVersion && $lastReleaseMajorNumber == 0 )
					|| ( $isNewMajorVersion && $versionMatches[1] == $lastReleaseMajorNumber )
				);
				$lastReleaseMajorNumber = $versionMatches[1];
				if ( $parseLogs ) $minRevision = 0;
			}
		} elseif ( $parseLogs ) {
			$matches = array();
			if ( preg_match('/^r(\d+) \|/', $buffer, $matches) ) {
				if ( $minRevision == 0 ) {
					$minRevision = (int)$matches[1];
				}
				$currentParsedRevision = (int)$matches[1];
			} elseif ( $currentParsedRevision > 0 && $buffer[0] != '-' ) {
				$lastReleaseLogs[$currentParsedRevision] .= $buffer;
			}
		}
		if ( $lastReleaseMajorNumber == 0 ) {
			$newChangelog .= $buffer;
		} else {
			$newChangelogEnd .= $buffer;
		}
	}
	fclose($handle);
}

$newChangelog .= <<<EOS
Version $newVersion
$releaseNotesURL
------------------

----------------------------------------------

EOS;

$matches = array();
if ( $minRevision > 0 ) {
	if ( preg_match_all('/^r(\d+) \|.*\n\n(.*)\-{46}/Ums', get_logs('.', $minRevision), $matches, PREG_SET_ORDER) ) {
		foreach ( $matches as $logEntry ) {

			// Do not keep merges logs
			if ( substr(trim($logEntry[2]), 0, 5) == '[MRG]' ) continue;

			// Add log entries only if they were not already listed (same revision number or same log message) in the previous version
			if ( !isset($lastReleaseLogs[$logEntry[1]]) && !in_array("\n".$logEntry[2], $lastReleaseLogs) ) {
				$newChangelog .= $logEntry[0]."\n";
			}
		}
	}
}

file_put_contents(CHANGELOG, $newChangelog . $newChangelogEnd);
