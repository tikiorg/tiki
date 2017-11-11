<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Tiki\Lib\Alchemy\AlchemyLib;

/**
 * Plugin definition for preview
 *
 * @return array
 */
function wikiplugin_preview_info()
{
	return [
		'name' => tr('Preview Files'),
		'documentation' => 'PluginPreviewFiles',
		'description' => tr('Enabled to generate preview of images or video files'),
		'prefs' => ['wikiplugin_preview'],
		'iconname' => 'file',
		'introduced' => 18,
		'tags' => ['experimental'],
		'packages_required' => ['media-alchemyst/media-alchemyst' => 'MediaAlchemyst\Alchemyst'],
		'format' => 'html',
		'params' => [
			'fileId' => [
				'required' => true,
				'name' => tr('fileId'),
				'description' => tr('Id of the file in the file gallery'),
				'since' => '18.0',
				'filter' => 'int',
			],
			'animation' => [
				'required' => false,
				'name' => tr('Animation'),
				'description' => tr('Output should be a static image (<code>0</code>) or an animation (<code>1</code>)'),
				'since' => '18.0',
				'filter' => 'int',
			],
			'width' => [
				'required' => false,
				'name' => tr('Width'),
				'description' => tr('Width of the result in pixels'),
				'since' => '18.0',
				'filter' => 'int',
			],
			'height' => [
				'required' => false,
				'name' => tr('Height'),
				'description' => tr('Height of the result in pixels'),
				'since' => '18.0',
				'filter' => 'int',
			],
		],
	];
}

/**
 * Plugin definition for Preview
 *
 * @param $data
 * @param $params
 * @return string|void
 */
function wikiplugin_preview($data, $params)
{
	global $user, $prefs;

	if (! AlchemyLib::isLibraryAvailable()) {
		return;
	}

	$fileId = isset($params['fileId']) ? intval($params['fileId']) : 0;
	$animation = isset($params['animation']) ? intval($params['animation']) : 0;
	$width = isset($params['width']) ? intval($params['width']) : null;
	$height = isset($params['height']) ? intval($params['height']) : null;

	$smartyLib = TikiLib::lib('smarty');

	$fileGalleryLib = TikiLib::lib('filegal');
	$userLib = TikiLib::lib('user');
	$info = $fileGalleryLib->get_file($fileId);
	if (! $userLib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_download_files')) {
		return;
	}

	$requestUniqueIdentifier = md5(serialize([$data, $params]));

	if (! isset($_REQUEST[$requestUniqueIdentifier])) {
		// generate the html output
		$urlParts = parse_url($_SERVER['REQUEST_URI']);
		$path = isset($urlParts['path']) ? $urlParts['path'] : '/';
		if (isset($urlParts['query'])) {
			parse_str($urlParts['query'], $pageParams);
		} else {
			$pageParams = [];
		}
		if (isset($_GET['page'])) {
			$pageParams['page'] = $_GET['page'];
		}
		$pageParams[$requestUniqueIdentifier] = '1';
		$pageParamStr = http_build_query($pageParams, null, '&');

		$fileLink = $path . '?' . $pageParamStr;

		$smartyLib->assign('param', $params);
		$smartyLib->assign('file', $fileLink);
		return $smartyLib->fetch('wiki-plugins/wikiplugin_preview.tpl');
	}

	$filePath = '';
	$fileMd5 = '';
	if (! empty($info['path'])) {
		if ($fileGalleryLib->isPodCastGallery($info['galleryId'])) {
			$filePath = $prefs['fgal_podcast_dir'] . $info['path'];
		} else {
			$filePath = $prefs['fgal_use_dir'] . $info['path'];
		}
		if (is_readable($filePath)) {
			$fileStats = stat($filePath);
			$lastModified = $fileStats['mtime'];
			$fileMd5 = empty($info['hash']) ?
				md5($fileStats['mtime'] . '=' . $fileStats['ino'] . '=' . $fileStats['size'])
				: md5($info['hash'] . $lastModified);
		} else {
			// File missing or not readable
			return;
		}
	} elseif (! empty($info['data'])) {
		$lastModified = $info['lastModif'];
		$fileMd5 = empty($info['hash']) ? md5($info['data']) : md5($info['hash'] . $lastModified);
	} else {
		// Empty content
		return;
	}

	/** @var Cachelib $cacheLib */
	$cacheLib = TikiLib::lib('cache');

	$cacheName = $fileMd5 . $requestUniqueIdentifier;
	$cacheType = 'wp_preview_' . $fileId . '_';

	$buildContent = true;
	$content = null;
	$contentType = null;

	$content_temp = $cacheLib->getCached($cacheName, $cacheType);
	if ($content_temp && $content_temp !== serialize(false) && $content_temp != "") {
		$buildContent = false;
		$pos = strpos($content_temp, ';');
		$contentType = substr($content_temp, 0, $pos);
		$content = substr($content_temp, $pos + 1);
	}
	unset($content_temp);

	if ($buildContent) {
		$sourceNeedsClean = false;
		if (empty($filePath)) {
			$filePath = 'temp/source_' . $cacheType . $cacheName;
			file_put_contents($filePath, $info['data']);
			$sourceNeedsClean = true;
		}
		$newFilePath = 'temp/target_' . $cacheType . $cacheName;

		$alchemy = new AlchemyLib();
		$contentType = $alchemy->convertToImage($filePath, $newFilePath, $width, $height, $animation);

		if (file_exists($newFilePath)) {
			$content = file_get_contents($newFilePath);
		}
		unlink($newFilePath);

		if ($sourceNeedsClean) {
			unlink($filePath);
		}

		if (empty($content)) {
			return;
		}

		$cacheLib->cacheItem($cacheName, $contentType . ';' . $content, $cacheType);
	}

	// Compression of the stream may corrupt files on windows
	ob_end_clean();
	ini_set('zlib.output_compression', 'Off');

	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header('Content-Length: ' . strlen($content));
	header('Content-Type: ' . $contentType);
	header('Content-Disposition: inline; filename="' . $fileMd5 . '";');
	header('Connection: close');
	header('Content-Transfer-Encoding: binary');
	echo $content;
	exit;
}
