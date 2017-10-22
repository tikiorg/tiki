<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Test\Files;

use ImageGalsLib;
use org\bovigo\vfs\vfsStream;
use Tiki\Files\CheckFileGallery;
use Tiki\Files\CheckImageGallery;
use TikiLib;

class CheckImageGalleryTest extends \PHPUnit_Framework_TestCase
{
	protected $fileRoot;
	protected $imagesDir;
	protected $filesDir;
	protected $podcastDir;

	/**
	 * @var CheckFileGallery
	 */
	protected $check;

	protected $testImage;

	protected $imageGalleryId;

	protected function setUp()
	{
		$this->testImage = TIKI_PATH . '/img/tiki/tikilogo.png';
		if (! file_exists($this->testImage)) {
			$this->markTestSkipped('Missing test image ' . $this->testImage);
		}

		global $prefs, $user;

		// impersonate admin
		$user = 'admin';
		$builder = new \Perms_Builder;
		\Perms::set($builder->build());

		// setup defaults for preferences
		$prefs['feature_galleries'] = 'y';
		$prefs['gal_use_db'] = 'y';
		$prefs['gal_use_dir'] = '';

		// setup virtual file system
		$this->fileRoot = vfsStream::setup(uniqid('', true), null);

		$this->imagesDir = $this->fileRoot->url() . '/images/';

		mkdir($this->imagesDir);

		// clean Image Gallery and generate the default categories
		/** @var ImageGalsLib $imageGalleryLib */
		$imageGalleryLib = TikiLib::lib('imagegal');
		$imageGalleryLib->table('tiki_images_data')->deleteMultiple([]);
		$imageGalleryLib->table('tiki_images')->deleteMultiple([]);
		$imageGalleryLib->table('tiki_galleries')->deleteMultiple([]);

		$this->imageGalleryId = $imageGalleryLib->replace_gallery(
			0,
			'test Gallery',
			'test Gallery',
			'',
			'admin',
			10,
			6,
			80,
			80,
			'n'
		);

		$this->check = new CheckImageGallery();
	}

	public function testImageGalleryEmptyReportsNoProblemOnDB()
	{
		$result = $this->check->analyse();

		$this->assertEquals(
			[
				'usesDatabase' => true,
				'path' => [''],
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

	public function testImageGalleryEmptyReportsNoProblemOnDisk()
	{
		$this->configToStoreFilesInDisk();

		$result = $this->check->analyse();

		$this->assertEquals(
			[
				'usesDatabase' => false,
				'path' => [$this->imagesDir],
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

	public function testImageGalleryWithOneImageOnDB()
	{
		$this->insertIntoImageGallery('testImageGalleryWithOneImageOnDB');

		$result = $this->check->analyse();

		$this->assertTrue($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count']);
		$this->assertEquals(0, $result['issueCount']);
	}

	public function testImageGalleryWithOneImageOnDisk()
	{
		$this->configToStoreFilesInDisk();

		$this->insertIntoImageGallery('testImageGalleryWithOneImageOnDisk');

		$result = $this->check->analyse();

		$this->assertFalse($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count']);
		$this->assertEquals(0, $result['issueCount']);
	}

	public function testImageGalleryMixedLocation()
	{
		$this->insertIntoImageGallery('testImageGalleryMixedLocationDb');

		$this->configToStoreFilesInDisk();

		$this->insertIntoImageGallery('testImageGalleryMixedLocationDisk');

		$result = $this->check->analyse();

		$this->assertFalse($result['usesDatabase']);
		$this->assertTrue($result['mixedLocation']);
		$this->assertEquals(2, $result['count']);
		$this->assertEquals(0, $result['issueCount']);
	}

	public function testImageGalleryMissingFile()
	{
		$this->configToStoreFilesInDisk();

		$imageId = $this->insertIntoImageGallery('testImageGalleryMissingFile');
		$imagePath = $this->getImagePath($imageId);

		$data = file_get_contents($this->testImage);

		unlink($this->imagesDir . $imagePath);

		$result = $this->check->analyse();

		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[
				[
					'id' => (string)$imageId,
					'name' => $imagePath,
					'path' => $this->imagesDir,
					'size' => (string)strlen($data),
				],
			],
			$result['missing']
		);
	}

	public function testImageGalleryMismatchFile()
	{
		$this->configToStoreFilesInDisk();

		$imageId = $this->insertIntoImageGallery('testImageGalleryMissingFile');
		$imagePath = $this->getImagePath($imageId);

		$data = file_get_contents($this->testImage);
		file_put_contents($this->imagesDir . $imagePath, $data . $data);

		$result = $this->check->analyse();

		$this->assertFalse($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[
				[
					'id' => (string)$imageId,
					'name' => $imagePath,
					'path' => $this->imagesDir,
					'size' => (string)strlen($data),
				],
			],
			$result['mismatch']
		);
	}

	public function testImageGalleryUnknownFile()
	{
		$this->configToStoreFilesInDisk();

		$imageId = $this->insertIntoImageGallery('testImageGalleryMissingFile');

		file_put_contents($this->imagesDir . 'unknownFile', 'content');

		$result = $this->check->analyse();

		$this->assertFalse($result['usesDatabase']);
		$this->assertFalse($result['mixedLocation']);
		$this->assertEquals(1, $result['count'], 'Count');
		$this->assertEquals(1, $result['issueCount'], 'Issue Count');
		$this->assertEquals(
			[['name' => 'unknownFile', 'path' => $this->imagesDir, 'size' => strlen('content')]],
			$result['unknown']
		);
	}

	protected function configToStoreFilesInDisk()
	{
		global $prefs;

		$prefs['gal_use_db'] = 'n';
		$prefs['gal_use_dir'] = $this->imagesDir;
	}

	protected function insertIntoImageGallery($baseName)
	{
		$imginfo = @getimagesize($this->testImage);
		$size_x = $imginfo[0];
		$size_y = $imginfo[1];
		$data = file_get_contents($this->testImage);
		$size = strlen($data);

		/** @var ImageGalsLib $imageGalleryLib */
		$imageGalleryLib = TikiLib::lib('imagegal');
		$imageId = $imageGalleryLib->insert_image(
			$this->imageGalleryId,
			$baseName,
			$baseName,
			$baseName . '.png',
			'image/png',
			$data,
			$size,
			$size_x,
			$size_y,
			'admin',
			$data,
			'image/png'
		);

		return $imageId;
	}

	protected function getImagePath($imageId)
	{
		/** @var ImageGalsLib $imageGalleryLib */
		$imageGalleryLib = TikiLib::lib('imagegal');
		$image = $imageGalleryLib->get_image($imageId);

		return $image['path'];
	}
}
