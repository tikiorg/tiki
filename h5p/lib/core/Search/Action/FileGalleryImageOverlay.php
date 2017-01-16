<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_FileGalleryImageOverlay implements Search_Action_Action
{

    protected $replaceKeys = array(
      '%file_name%' => 'file.filename',
      '%file_id%' => 'file.fileId',
      '%parts_filename%' => 'parts.filename',
      '%parts_extension%' => 'parts.extension',
      '%gallery_name%' => 'gallery.name',
      '%gallery_id%' => 'gallery.galleryId',
      '%tracker_id%' => 'item.trackerId',
      '%item_id%' => 'item.itemId',
      '%field_id%' => 'field.fieldId',
      '%field_perm_name%' => 'field.permName',
      '%field_name%' => 'field.name',
      '%exif_date%' => 'exif.datetime',
      '%exif_gps%' => 'exif.gps',
      '%exif_gps_lat%' => 'exif.gps_lat',
      '%exif_gps_lon%' => 'exif.gps_lon',
      '%exif_gps_dms%' => 'exif.gps_dms',
      '%exif_gps_dms_lat%' => 'exif.gps_dms_lat',
      '%exif_gps_dms_lon%' => 'exif.gps_dms_lon',
    );

    function getValues()
    {
        return array(
          'object_type' => true,
          'object_id' => true,
          'field' => true,
          'value' => true,
          'error_if_missing' => false,
        );
    }

    function validate(JitFilter $data)
    {

        $object_type = $data->object_type->text();
        $object_id = $data->object_id->int();
        $field = $data->field->word();
        $value = $data->value->text();

        if ('tracker_field_' === substr($field, 0, 14)) {
            $field = substr($field, 14);
        }

        if ($object_type != 'trackeritem') {
            throw new Search_Action_Exception(tr('Cannot apply filegal_image_overlay action to an object type %0.', $object_type));
        }

        $trklib = TikiLib::lib('trk');
        $info = $trklib->get_item_info($object_id);

        if (!$info) {
            throw new Search_Action_Exception(tr('Tracker item %0 not found.', $object_id));
        }

        $definition = Tracker_Definition::get($info['trackerId']);

        $fieldDefinition = $definition->getFieldFromPermName($field);
        if (!$fieldDefinition) {
            throw new Search_Action_Exception(tr('Tracker field %0 not found for tracker %1.', $field, $info['trackerId']));
        }

        if ($fieldDefinition['type'] != 'FG') {
            throw new Search_Action_Exception(tr('Tracker field %0 is not a Files field type.', $field));
        }

        if (empty($value)) {
            throw new Search_Action_Exception(tr('filegal_image_overlay action missing value parameter.'));
        }

        //At the moment there is only support for ImageMagik, so check if is available
        if (!class_exists('Imagick') || !class_exists('ImagickDraw')) {
            throw new Search_Action_Exception(tr('filegal_image_overlay action requires Imagick, please review your server setup.'));
        }

        return true;
    }

    function execute(JitFilter $data)
    {

        global $user, $prefs;

        $object_type = $data->object_type->text();
        $object_id = $data->object_id->int();
        $field = $data->field->word();
        $value = $data->value->text();
        $error_if_missing = $data->error_if_missing->text();

        if ('tracker_field_' === substr($field, 0, 14)) {
            $field = substr($field, 14);
        }

        $trklib = TikiLib::lib('trk');
        $info = $trklib->get_tracker_item($object_id);

        /** @var Tracker_Definition $definition */
        $definition = Tracker_Definition::get($info['trackerId']);
        $fieldDefinition = $definition->getFieldFromPermName($field);

        /** @var FileGalLib $fileGal */
        $fileGal = TikiLib::lib('filegal');

        $fileList = $info[$fieldDefinition['fieldId']];

        if (empty($fileList)) {
            return true;
        }

        if ($error_if_missing == 'y') {
            $error_if_missing = true;
        } else {
            $error_if_missing = false;
        }

        $newFileList = array();
        foreach (explode(',', $fileList) as $fileId) {
            $file = $fileGal->get_file($fileId);
            if (substr($file['filetype'], 0, 6) != 'image/') {
                $newFileList[] = $fileId;
                continue;
            }
            $galInfo = $fileGal->get_file_gallery_info($file['galleryId']);
            $newUser = $user ?: $file['user'];
            $overlayString = $this->generateString(
              $value,
              $file,
              $galInfo,
              $info,
              $fieldDefinition,
              $error_if_missing,
              $missingKeys
            );
            if ($overlayString === false) {
                throw new Search_Action_Exception(tr('filegal_image_overlay: Problem processing image "%0", the following values form the template are empty: %1',
                    $file['filename'],
                    implode(', ', $missingKeys)));
            }
            $newImage = $this->addTextToImage($file['data'], $overlayString);
            if ($newImage) {
                $newFileList[] = $fileGal->update_single_file(
                  $galInfo,
                  $file['filename'],
                  $file['filesize'],
                  $file['filetype'],
                  $newImage,
                  $fileId,
                  $newUser
                );
            } else {
                $newFileList[] = $fileId;
            }
        }

        if ($prefs['fgal_keep_fileId'] == 'n') {
            // new IDs are generated to for the last version we updated
            $utilities = new Services_Tracker_Utilities;
            $utilities->updateItem(
              $definition,
              array(
                'itemId' => $object_id,
                'status' => $info['status'],
                'fields' => array(
                  $field => implode(',', $newFileList),
                ),
              )
            );
        }

        return true;
    }

    function requiresInput(JitFilter $data) {
        return false;
    }

    /**
     * Generate a string based on the template provided
     *
     * @param string $template The template (see $replaceKeys)
     * @param array $fileData File Details
     * @param array $galleryData Gallery Details
     * @param array $itemData Item Details
     * @param array $fieldData Field Details
     * @param boolean $checkMissing If enable function will return false if some element of the template has a empty value
     * @param array $missingTemplateKeys The keys missing
     *
     * @return string|false
     */
    protected function generateString(
      $template,
      $fileData,
      $galleryData,
      $itemData,
      $fieldData,
      $checkMissing = false,
      &$missingTemplateKeys = array()
    ) {
        $dataValues = array(
          'file' => $fileData,
          'gallery' => $galleryData,
          'item' => $itemData,
          'field' => $fieldData,
          'parts' => pathinfo($fileData['filename']),
          'exif' => $this->getExifArray($fileData),
        );

        $values = array();
        foreach ($this->replaceKeys as $search => $dataKey) {
            list($data, $key) = explode('.', $dataKey);
            $values[$search] = (isset($dataValues[$data]) && isset($dataValues[$data][$key])) ? $dataValues[$data][$key] : '';
        }

        if ($checkMissing) {
            foreach ($values as $key => $value) {
                if (strpos($template, $key) !== false) {
                    if (empty($value)) {
                        $missingTemplateKeys[] = $key;
                    }
                }
            }
            if (count($missingTemplateKeys)) {
                return false;
            }
        }

        return str_replace(array_keys($values), array_values($values), $template);
    }

    /**
     * Allow adding text as overlay to a image
     * @param $imageString
     * @param $text
     * @return string
     */
    protected function addTextToImage($imageString, $text)
    {
        $font = dirname(dirname(dirname(__DIR__))).'/captcha/DejaVuSansMono.ttf';

        $padLeft = 20;
        $padBottom = 20;

        $image = new Imagick();
        $image->readImageBlob($imageString);
        $height = $image->getimageheight();

        $draw = new ImagickDraw();
        $draw->setFillColor('#000000');
        $draw->setStrokeColor(new ImagickPixel('#000000'));
        $draw->setStrokeWidth(3);
        $draw->setFont($font);
        $draw->setFontSize(12);
        $image->annotateImage($draw, $padLeft, $height - $padBottom, 0, $text);

        $draw = new ImagickDraw();
        $draw->setFillColor('#ffff00');
        $draw->setFont($font);
        $draw->setFontSize(12);
        $image->annotateImage($draw, $padLeft, $height - $padBottom, 0, $text);

        return $image->getImageBlob();
    }

    /**
     * Get some selected Exif information from a image
     * @param $fileData
     * @return array
     */
    function getExifArray($fileData)
    {
        $exif = array();
        if ($fileData['filetype'] != 'image/jpeg' || !function_exists('exif_read_data')) {
            return $exif;
        }
        $exifData = exif_read_data('data://image/jpeg;base64,'.base64_encode($fileData['data']));

        $exif['datetime'] = isset($exifData['DateTimeOriginal']) ? $exifData['DateTimeOriginal'] : '';

        if (isset($exifData['GPSLongitude']) && isset($exifData['GPSLatitude'])) {
            $latitude = $this->gpsCoordinates($exifData["GPSLatitude"], $exifData['GPSLatitudeRef']);
            $longitude = $this->gpsCoordinates($exifData["GPSLongitude"], $exifData['GPSLongitudeRef']);
            $exif['gps'] = $latitude['dd'].', '.$longitude['dd'];
            $exif['gps_lat'] = $latitude['dd'];
            $exif['gps_lon'] = $longitude['dd'];
            $exif['gps_dms'] = $latitude['dms'].' '.$longitude['dms'];
            $exif['gps_dms_lat'] = $latitude['dms'];
            $exif['gps_dms_lon'] = $longitude['dms'];
        } else {
            $exif['gps'] = '';
            $exif['gps_lat'] = '';
            $exif['gps_lon'] = '';
            $exif['gps_dms'] = '';
            $exif['gps_dms_lat'] = '';
            $exif['gps_dms_lon'] = '';
        }

        return ($exif);
    }

    /**
     * Conver Exif coordinate information into DD and DMS GPS coordinates
     * @param $coordinate
     * @param $hemisphere
     * @return array
     */
    protected function gpsCoordinates($coordinate, $hemisphere)
    {
        for ($i = 0; $i < 3; $i++) {
            $part = explode('/', $coordinate[$i]);
            if (count($part) == 1) {
                $coordinate[$i] = $part[0];
            } else {
                if (count($part) == 2) {
                    $coordinate[$i] = floatval($part[0]) / floatval($part[1]);
                } else {
                    $coordinate[$i] = 0;
                }
            }
        }
        list($degrees, $minutes, $seconds) = $coordinate;

        $sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
        $coordinateDD = sprintf("%.4f", $sign * ($degrees + $minutes / 60 + $seconds / 3600));

        //normalize
        $minutes += 60 * ($degrees - floor($degrees));
        $degrees = floor($degrees);
        $seconds += 60 * ($minutes - floor($minutes));
        $minutes = floor($minutes);

        //extra normalization, probably not necessary unless you get weird data
        if ($seconds >= 60) {
            $minutes += floor($seconds / 60.0);
            $seconds -= 60 * floor($seconds / 60.0);
        }
        if ($minutes >= 60) {
            $degrees += floor($minutes / 60.0);
            $minutes -= 60 * floor($minutes / 60.0);
        }

        $coordinateDMS = sprintf("%d° %d' %.3f'' %s", $degrees, $minutes, $seconds, $hemisphere);

        return array(
          'dd' => $coordinateDD,
          'dms' => $coordinateDMS,
        );
    }
}
