<?php
define( 'TOOLS', dirname(__FILE__) );
define( 'ROOT', realpath( TOOLS . '/../..' ) );
define( 'COPYRIGHTS', ROOT . '/copyright.txt' );
define( 'SF_TW_MEMBERS_URL', 'http://sourceforge.net/project/memberlist.php?group_id=64258' );

require_once TOOLS . '/svntools.php';

$proxyContext = '';
$argv = array();
foreach ( $_SERVER['argv'] as $arg ) {
	if ( strlen($arg) > 11 && substr($arg, 0, 11) == '--tcpproxy=' ) {
		$proxyContext = stream_context_create( array( 'http' => array(
			'proxy' => 'tcp://' . substr($arg, 11),
			'request_fulluri' => true
		) ) );
		continue;
	}
	$argv[] = $arg;
}
$_SERVER['argv'] = $argv;
unset($argv);

if( $_SERVER['argc'] <= 1 )
	die( "Usage: php doc/devtools/release_copyright.php [--tcpproxy=HOST_DOMAIN:PORT] <version-number>
Example:
	php doc/devtools/release_copyright.php 2.0
" );
$newVersion = $_SERVER['argv'][1];

if ( empty($newVersion) ) {
	error('No version specified.');
	die;
}
if ( ! is_readable(COPYRIGHTS) || ! is_writable(COPYRIGHTS) ) {
	error('The copyright file "' . COPYRIGHTS . '" is not readable or writable.');
	die;
}

important('Updating copyrights...');

$nbCommiters = 0;
$contributors = array();
$repositoryInfo = get_info(TIKISVN);

get_contributors_data(TIKISVN, $contributors, 1, (int)$repositoryInfo->entry->commit['revision']);
ksort($contributors);

$totalContributors = count($contributors);
$now = date('Y-m-d');

$copyrights = <<<EOS
Tiki Copyright
----------------

The following list attempts to gather the copyright holders for tikiwiki
as of version $newVersion.

Accounts listed below with commits have contributed source code to CVS or SVN. 
Please note that even more people contributed on various other aspects (documentation, 
bug reporting, testing, etc.)

This is how we implement the Tikiwiki Social Contract.
http://dev.tikiwiki.org/SocialContract

List of members of the Community
As of $now, the community has:
  * $totalContributors members on Sourceforge,
  * $nbCommiters of those people who made at least one code commit

This list is automatically generated and alphabetically sorted
from subversion repository by the following script:
  doc/devtools/release_copyright.php

Counting the commits is not as trivial as it may sound. If your number of commits
seems incorrect, it could be that the script is not detecting them all. This 
has been reported especially for commits early on in the project. Nonetheless, 
the list provides a general idea.

====================================================================

EOS;

foreach ( $contributors as $author => $infos ) {
	$copyrights .= "\nNickname: $author";
	if ( !empty($infos['realName']) && $infos['realName'] != $author ) $copyrights .= "\nName: ".$infos['realName'];
	if ( !empty($infos['first_commit']) ) $copyrights .= "\nFirst Commit: ".$infos['first_commit'];
	if ( !empty($infos['last_commit']) ) $copyrights .= "\nLast Commit: ".$infos['last_commit'];
	if ( !empty($infos['nb_commits']) ) $copyrights .= "\nNumber of Commits: ".$infos['nb_commits'];
	if ( !empty($infos['role']) ) $copyrights .= "\nSF Role: ".$infos['role'];
	$copyrights .= "\n";
}

file_put_contents(COPYRIGHTS, $copyrights);

function get_contributors_data($path, &$contributors, $minRevision, $maxRevision, $step = 5000) {
	global $nbCommiters;
	if ( empty($contributors) ) get_contributors_sf_data($contributors);

	$minByStep = max($maxRevision - $step, $minRevision);
	$lastLogRevision = $maxRevision;
	info("Retrieving logs from revision $minByStep to $maxRevision ...");
	$logs = get_logs( $path, $minByStep, $maxRevision);
	if ( preg_match_all('/^r(\d+) \|\s([^\|]+)\s\|\s(\d+-\d+-\d+)\s.*\n\n(.*)\-+\n/Ums', $logs, $matches, PREG_SET_ORDER) ) {
		foreach ( $matches as $logEntry ) {
			if ( $lastLogRevision > 0 && $logEntry[1] != $lastLogRevision - 1 && $lastLogRevision != $maxRevision ) {
				print "\nProblem with commit ".( $lastLogRevision - 1 )."\n (trying {$logEntry[1]} after $lastLogRevision)";
				die;
			}
			$lastLogRevision = $logEntry[1];
			$author = strtolower($logEntry[2]);
			if ( empty( $author ) || $author == '(no author)' ) continue;

			if ( !isset($contributors[$author]) ) $contributors[$author] = array();

			$contributors[$author]['author'] = $logEntry[2];
			$contributors[$author]['first_commit'] = $logEntry[3];

			if ( isset($contributors[$author]['nb_commits']) ) {
				$contributors[$author]['nb_commits']++;
			} else {
				$contributors[$author]['last_commit'] = $logEntry[3];
				$nbCommiters++;
				$contributors[$author]['nb_commits'] = 1;
			}
		}
	}
	if ( $lastLogRevision > $minRevision ) get_contributors_data($path, $contributors, $minRevision, $lastLogRevision - 1, $step);
	return $contributors;
}

function get_contributors_sf_data(&$contributors) {
	global $proxyContext;
	$members = '';
	$matches = array();
	$userParsedInfo = array();

	$html = empty($proxyContext) ? file_get_contents(SF_TW_MEMBERS_URL) : file_get_contents(SF_TW_MEMBERS_URL, 0, $proxyContext);

	if ( !empty($html) && preg_match('/(<table.*<\/\s*table>)/sim', $html, $matches) ) {
		$usersInfo = array();
		if ( preg_match_all('/<tr[^>]*>'.str_repeat('\s*<td[^>]*>(.*)<\/td>\s*',4).'<\/\s*tr>/Usim', $matches[0], $usersInfo, PREG_SET_ORDER) ) {
			foreach ( $usersInfo as $k => $userInfo ) {
				$userInfo = array_map('trim', array_map('strip_tags', $userInfo));
				$user = strtolower($userInfo['2']);
				if ( empty($user) ) continue;
				$contributors[$user] = array(
					'realName' => html_entity_decode(iconv("ISO-8859-15", "UTF-8", $userInfo['1']), ENT_COMPAT, 'UTF-8'),
					'role' => $userInfo['3']
				);
			}
		}
	} else {
		error('Impossible to get SF.net users information.');
		die;
	}
}
