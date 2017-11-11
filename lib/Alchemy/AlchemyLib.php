<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Lib\Alchemy;

use MediaAlchemyst\Alchemyst;
use MediaAlchemyst\DriversContainer;
use MediaAlchemyst\Specification\Animation;
use MediaAlchemyst\Specification\Image;
use MediaVorus\Media\MediaInterface;
use MediaVorus\MediaVorus;
use Neutron\TemporaryFilesystem\Manager as FsManager;

/**
 * Wrapper of the library Media Alchemy, for media processing
 */
class AlchemyLib
{
	const TYPE_IMAGE = 'image/jpeg';
	const TYPE_IMAGE_ANIMATION = 'image/gif';

	/** @var Alchemyst */
	protected $alchemyst;

	/** @var MediaVorus */
	protected $mediavorus;

	/**
	 * AlchemyLib constructor.
	 */
	public function __construct()
	{
		global $prefs;

		if (! static::isLibraryAvailable()) {
			return;
		}

		$drivers = new DriversContainer();
		$drivers['configuration'] = [
			'ffmpeg.threads' => 4,
			'ffmpeg.ffmpeg.timeout' => 3600,
			'ffmpeg.ffprobe.timeout' => 60,
			'ffmpeg.ffmpeg.binaries' => $prefs['alchemy_ffmpeg_path'],
			'ffmpeg.ffprobe.binaries' => $prefs['alchemy_ffprobe_path'],
			'imagine.driver' => $prefs['alchemy_imagine_driver'],
		];

		$this->alchemyst = new Alchemyst($drivers, FsManager::create());

		$this->mediavorus = $drivers['mediavorus'];
	}

	/**
	 * Check if Alchemy is available
	 *
	 * @return bool true if the base library is available
	 */
	public static function isLibraryAvailable()
	{
		return class_exists('MediaAlchemyst\Alchemyst');
	}

	/**
	 * Convert a source file into a image, static or animated gif
	 *
	 * @param string $sourcePath the patch to read the file
	 * @param string $destinationPath the path to store the result
	 * @param int|null $width image width, use null to keep the source width
	 * @param int|null $height image height, use null to keep the source height
	 * @param bool $animated true for animated gif
	 * @return null|string the media type of the file, null on error
	 */
	public function convertToImage($sourcePath, $destinationPath, $width, $height, $animated)
	{
		try {
			$guess = $this->mediavorus->guess($sourcePath);

			$guessedType = $guess->getType();

			if ($guessedType == MediaInterface::TYPE_VIDEO && $animated) {
				$targetType = new Animation();
				$targetImageType = self::TYPE_IMAGE_ANIMATION;
			} else {
				$targetType = new Image();
				$targetImageType = self::TYPE_IMAGE;
			}

			if ($width > 0 && $height > 0) {
				$targetType->setDimensions($width, $height);
			}

			$this->alchemyst->turnInto($sourcePath, $destinationPath, $targetType);

			return $targetImageType;
		} catch (\Exception $e) {
			// empty
		}

		return null;
	}
}
