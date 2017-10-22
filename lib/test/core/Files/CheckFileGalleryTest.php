<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Test\Files;

use org\bovigo\vfs\vfsStream;
use Tiki\Files\CheckFileGallery;
use TikiLib;
use FileGalLib;

class CheckFileGalleryTest extends \PHPUnit_Framework_TestCase
{
	protected $fileRoot;

	protected $filesDir;
	protected $podcastDir;

	/** @var  CheckFileGallery */
	protected $check;

	protected $testImage;

	protected $galleryId;
	protected $gallery;
	protected $galleryPodcastId;
	protected $galleryPodcast;

	protected function setUp()
	{
		$this->testImage = TIKI_PATH . '/img/tiki/tikilogo.png';
		if (! file_exists($this->testImage)) {
			$this->markTestSkipped('Missing test file ' . $this->testImage);
		}

		global $prefs, $user;

		// impersonate admin
		$user = 'admin';

		// setup virtual file system
		$this->fileRoot = vfsStream::setup(uniqid('', true), null);

		$this->filesDir = $this->fileRoot->url() . '/files/';
		$this->podcastDir = $this->fileRoot->url() . '/podcast/';

		mkdir($this->filesDir);
		mkdir($this->podcastDir);

		// setup defaults for preferences
		$prefs['feature_file_galleries'] = 'y';
		$prefs['fgal_use_db'] = 'y';
		$prefs['fgal_use_dir'] = $this->filesDir;
		$prefs['fgal_podcast_dir'] = $this->podcastDir;

		// clean File Gallery and generate the default categories
		/** @var FileGalLib $fileGalleryLib */
		$fileGalleryLib = TikiLib::lib('filegal');
		$fileGalleryLib->table('tiki_files')->deleteMultiple([]);
		$fileGalleryLib->table('tiki_file_galleries')->deleteMultiple(
			['parentId' => $fileGalleryLib->table('tiki_files')->greaterThan(0)]
		);
		$fileGalleryLib->cleanGalleriesParentIdsCache();

		$this->galleryId = $fileGalleryLib->replace_file_gallery(
			[
				'name' => 'Test Gallery',
				'description' => 'Test Gallery',
			]
		);
		$this->gallery = $fileGalleryLib->get_file_gallery_info($this->galleryId);

		$this->galleryPodcastId = $fileGalleryLib->replace_file_gallery(
			[
				'name' => 'Test Gallery Podcast',
				'description' => 'Test Gallery Podcast',
				'type' => 'podcast',
			]
		);
		$this->galleryPodcast = $fileGalleryLib->get_file_gallery_info($this->galleryPodcastId);

		$this->check = new CheckFileGallery();
	}

	public function testFileGalleryEmptyReportsNoProblemOnDB()
	{
		$result = $this->check->analyse();

		$this->assertEquals(
			[
				'usesDatabase' => true,
				'path' => [$this->filesDir, $this->podcastDir],
				'mixedLocation' => false,
				'count' => 0,
				'countFilesDb' => 0,
				'countFilesDisk' => 0,
				'issueCount' => 0,
				'missing' => [],
				'mismatch' => [],
				'unknown' => [],
			],
			$result
		);
	}

	public function testFileGalleryEmptyReportsNoProblemOnDisk()
	{
		$this->configToStoreFilesInDisk();

		$result = $this->check->analyse();

		$this->assertEquals(
			[
				'usesDatabase' => false,
				'path' => [$this->filesDir, $this->podcastDir],
				'mixedLocation' => false,
				'count' => 0,
				'countFilesDb' => 0,
				'countFilesDisk' => 0,
				'issueCount' => 0,
				'missing' => [],
				'mismatch' => [],
				'unknown' => [],
			],
			$result
		);
	}

	public function testFileGalleryWithOneImageOnDB()
	{
		$this->insertFile('testFileGalleryWithOneImageOnDB');

		$result = $this->check->analyse();

		$this->assertTrue($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count']);
		$this->assertEquals(0, $result['issueCount']);
	}

	public function testFileGalleryWithOneImageOnDisk()
	{
		$this->configToStoreFilesInDisk();

		$this->insertFile('testFileGalleryWithOneImageOnDisk');

		$result = $this->check->analyse();

		$this->assertFalse($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count']);
		$this->assertEquals(0, $result['issueCount']);
	}

	public function testFileGalleryMixedLocation()
	{
		$this->insertFile('testFileGalleryMixedLocationDb');

		$this->configToStoreFilesInDisk();

		$this->insertFile('testFileGalleryMixedLocationDisk');

		$result = $this->check->analyse();

		$this->assertFalse($result['usesDatabase']);
		$this->assertTrue($result['mixedLocation']);
		$this->assertEquals(2, $result['count']);
		$this->assertEquals(0, $result['issueCount']);
	}

	public function testFileGalleryMissingFile()
	{
		$this->configToStoreFilesInDisk();

		$imageId = $this->insertFile('testFileGalleryMissingFile');
		$filePath = $this->getFilePath($imageId);

		$data = file_get_contents($this->testImage);

		unlink($this->filesDir . $filePath);

		$result = $this->check->analyse();

		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[
				[
					'id' => (string)$imageId,
					'name' => $filePath,
					'path' => $this->filesDir,
					'size' => (string)strlen($data),
				],
			],
			$result['missing']
		);
	}

	public function testFileGalleryMismatchFile()
	{
		$this->configToStoreFilesInDisk();

		$imageId = $this->insertFile('testFileGalleryMissingFile');
		$filePath = $this->getFilePath($imageId);

		$data = file_get_contents($this->testImage);
		file_put_contents($this->filesDir . $filePath, $data . $data);

		$result = $this->check->analyse();

		$this->assertFalse($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[
				[
					'id' => (string)$imageId,
					'name' => $filePath,
					'path' => $this->filesDir,
					'size' => (string)strlen($data),
				],
			],
			$result['mismatch']
		);
	}

	public function testFileGalleryUnknownFile()
	{
		$this->configToStoreFilesInDisk();

		$this->insertFile('testFileGalleryMissingFile');

		file_put_contents($this->filesDir . 'unknownFile', 'content');

		$result = $this->check->analyse();

		$this->assertFalse($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[['name' => 'unknownFile', 'path' => $this->filesDir, 'size' => strlen('content')]],
			$result['unknown']
		);
	}

	public function testFileGalleryWithOneImageAndPodcast()
	{
		$this->insertFile('testFileGalleryWithOneImageAndPodcastImg');
		$this->insertFile('testFileGalleryWithOneImageAndPodcastPod', $this->galleryPodcast);

		$result = $this->check->analyse();

		$this->assertTrue($result['usesDatabase'], 'Use Db');
		$this->assertFalse($result['mixedLocation'], 'Mixed Location');
		$this->assertEquals(2, $result['count']);
		$this->assertEquals(0, $result['issueCount']);
	}

	public function testFileGalleryMissingPodcastFile()
	{
		$imageId = $this->insertFile('testFileGalleryMissingPodcastFile', $this->galleryPodcast);
		$filePath = $this->getFilePath($imageId);

		$data = file_get_contents($this->testImage);

		unlink($this->podcastDir . $filePath);

		$result = $this->check->analyse();

		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[
				[
					'id' => (string)$imageId,
					'name' => $filePath,
					'path' => $this->podcastDir,
					'size' => (string)strlen($data),
				],
			],
			$result['missing']
		);
	}

	public function testFileGalleryMismatchPodcastFile()
	{
		$imageId = $this->insertFile('testFileGalleryMismatchPodcastFile', $this->galleryPodcast);
		$filePath = $this->getFilePath($imageId);

		$data = file_get_contents($this->testImage);
		file_put_contents($this->podcastDir . $filePath, $data . $data);

		$result = $this->check->analyse();

		$this->assertTrue($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[
				[
					'id' => (string)$imageId,
					'name' => $filePath,
					'path' => $this->podcastDir,
					'size' => (string)strlen($data),
				],
			],
			$result['mismatch']
		);
	}

	public function testFileGalleryUnknownPodcastFile()
	{
		$this->insertFile('testFileGalleryUnknownPodcastFile', $this->galleryPodcast);

		file_put_contents($this->podcastDir . 'unknownFile', 'content');

		$result = $this->check->analyse();

		$this->assertTrue($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[['name' => 'unknownFile', 'path' => $this->podcastDir, 'size' => strlen('content')]],
			$result['unknown']
		);
	}

	public function testFileGalleryFilesAndPodcastInTheSameDir()
	{
		global $prefs;
		$prefs['fgal_podcast_dir'] = $this->filesDir;

		$this->insertFile('testFileGalleryFilesAndPodcastInTheSameDirFile');
		$this->insertFile('testFileGalleryFilesAndPodcastInTheSameDirPod', $this->galleryPodcast);

		file_put_contents($this->filesDir . 'unknownFile', 'content');

		$result = $this->check->analyse();

		$this->assertTrue($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(2, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[['name' => 'unknownFile', 'path' => $this->filesDir, 'size' => strlen('content')]],
			$result['unknown']
		);
	}

	protected function configToStoreFilesInDisk()
	{
		global $prefs;

		$prefs['fgal_use_db'] = 'n';
	}

	protected function insertFile($baseName, $gallery = null)
	{
		if (is_null($gallery)) {
			$gallery = $this->gallery;
		}

		$data = file_get_contents($this->testImage);
		$size = strlen($data);

		/** @var FileGalLib $fileGalleryLib */
		$fileGalleryLib = TikiLib::lib('filegal');
		$fileId = $fileGalleryLib->upload_single_file($gallery, $baseName . '.png', $size, 'image/png', $data);

		return $fileId;
	}

	protected function getFilePath($id)
	{
		/** @var FileGalLib $fileGalleryLib */
		$fileGalleryLib = TikiLib::lib('filegal');
		$file = $fileGalleryLib->get_file($id);

		return $file['path'];
	}
}
