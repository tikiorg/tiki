<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_archivebuilder_info()
{
	return array(
		'name' => tra('Archive Builder'),
		'documentation' => 'PluginArchiveBuilder',
		'description' => tra('Define an archive that can be downloaded'),
		'prefs' => array( 'wikiplugin_archivebuilder' ),
		'body' => tra('Description of the archive content. Multiple handlers are available for content types. One per line. Ex: page-as-pdf:some-folder/foo.pdf:HomePage , tracker-attachments:target-folder/:3435'),
		'iconname' => 'file-archive',
		'introduced' => 5,
		'params' => array(
			'name' => array(
				'name' => tra('Archive Name'),
				'description' => tra('Upon download, the name of the file that will be provided.'),
				'required' => true,
				'since' => '5.0',
				'default' => ''
			),
		),
	);
}

function wikiplugin_archivebuilder( $data, $params )
{
	if ( ! class_exists('ZipArchive') ) {
		return '^' . tra('Missing ".zip" file name extension.') . '^';
	}

	$archive = md5(serialize(array( $data, $params )));

	if ( isset( $_POST[$archive] ) ) {
		$files = array();

		$handlers = array(
			'tracker-attachments' => 'wikiplugin_archivebuilder_trackeratt',
			'page-as-pdf' => 'wikiplugin_archivebuilder_pagetopdf',
		);

		$archive = new ZipArchive;
		$archive->open($file = tempnam('temp/', 'archive') . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

		foreach ( array_filter(explode("\n", trim($data))) as $line) {
			$parts = explode(":", trim($line));
			$handler = array_shift($parts);

			if ( isset( $handlers[$handler] ) ) {
				$result = call_user_func_array($handlers[$handler], $parts);
				foreach ( $result as $name => $content ) {
					$archive->addFromString($name, $content);
					$files[] = $name;
				}
			} else {
				return tra('Incorrect parameter').' '.$handler;
			}
		}

		$archive->addFromString('manifest.txt', implode("\n", $files));
		$archive->close();

		// Compression of the stream may corrupt files on windows
		ob_end_clean();
		ini_set('zlib.output_compression', 'Off');

		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header('Content-Length: ' . filesize($file));
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . $params['name'] . '";');
		header('Connection: close');
		header('Content-Transfer-Encoding: binary'); 
		readfile($file);
		unlink($file);
		exit;
	} else {
		$label = tra('Download archive');
		return <<<FORM
<form method="post" action="">
	<input type="submit" class="btn btn-default btn-sm" name="$archive" value="$label" />
</form>
FORM;
	}
}

function wikiplugin_archivebuilder_trackeratt( $basepath, $trackerItem )
{
	$trklib = TikiLib::lib('trk');
	$data = $trklib->get_tracker_item($trackerItem);

	$item = Tracker_Item::fromInfo($data);

	if (! $item->canView()) {
		return array();
	}

	$basepath = rtrim($basepath, '/') . '/';

	$attachments = array();

	$files = $trklib->list_item_attachments($trackerItem, 0, -1, 'attId_asc');
	foreach ( $files['data'] as $file ) {
		$name = $basepath . $file['filename'];
		$complete = $trklib->get_item_attachment($file['attId']);

		$attachments[$name] = wikiplugin_archivebuilder_tracker_get_attbody($complete);
	}
	
	return $attachments;
}

function wikiplugin_archivebuilder_tracker_get_attbody( $info )
{
	global $prefs;

	if ($info["path"]) {
		if (file_exists($prefs['t_use_dir'].$info["path"])) {
			return file_get_contents($prefs['t_use_dir'] . $info["path"]);
		}
	} else {
		return $info['data'];
	}
}

function wikiplugin_archivebuilder_pagetopdf( $file, $pageName )
{
	if (! Perms::get('wiki page', $pageName)->view) {
		return array();
	}

	require_once 'lib/pdflib.php';
	$generator = new PdfGenerator;
	$params = array( 'page' => $pageName );

	$args = func_get_args();
	$args = array_slice($args, 2);

	foreach ( $args as $arg ) {
		list( $key, $value ) = explode('=', $arg, 2);
		$params[$key] = $value;
	}

	return array(
		$file => $generator->getPdf('tiki-print.php', $params),
	);
}

