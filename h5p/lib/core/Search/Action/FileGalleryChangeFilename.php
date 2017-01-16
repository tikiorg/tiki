<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_FileGalleryChangeFilename implements Search_Action_Action
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
    );

    function getValues()
    {
        return array(
          'object_type' => true,
          'object_id' => true,
          'field' => true,
          'value' => true,
          'in_place' => false,
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
            throw new Search_Action_Exception(tr('Cannot apply filegal_change_filename action to an object type %0.', $object_type));
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
            throw new Search_Action_Exception(tr('filegal_change_filename action missing value parameter.'));
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
        $in_place = $data->in_place->text();

        if ('tracker_field_' === substr($field, 0, 14)) {
            $field = substr($field, 14);
        }

        if ($in_place == 'y') {
            $in_place = true;
        } else {
            $in_place = false;
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

        $newFileList = array();
        foreach (explode(',', $fileList) as $fileId) {
            $file = $fileGal->get_file($fileId);
            $galInfo = $fileGal->get_file_gallery_info($file['galleryId']);
            $newUser = $user ?: $file['user'];
            $newName = $this->generateString($value, $file, $galInfo, $info, $fieldDefinition);
            if ($in_place) {
                $fileGal->update_file($fileId, $newName, $file['description'], $newUser, null, $newName);
            } else {
                $newFileList[] = $fileGal->update_single_file(
                  $galInfo,
                  $newName,
                  $file['filesize'],
                  $file['filetype'],
                  $file['data'],
                  $fileId,
                  $newUser
                );
            }

        }

        if (!$in_place && ($prefs['fgal_keep_fileId'] != 'y')) {
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
     * @param $template The template (see $replaceKeys)
     * @param $fileData File Details
     * @param $galleryData Gallery Details
     * @param $itemData Item Details
     * @param $fieldData Field Details
     * @return string
     */
    protected function generateString($template, $fileData, $galleryData, $itemData, $fieldData)
    {
        $dataValues = array(
          'file' => $fileData,
          'gallery' => $galleryData,
          'item' => $itemData,
          'field' => $fieldData,
          'parts' => pathinfo($fileData['filename'])
        );

        $values = array();
        foreach ($this->replaceKeys as $search => $dataKey) {
            list($data, $key) = explode('.', $dataKey);
            $values[$search] = (isset($dataValues[$data])&&isset($dataValues[$data][$key])) ? $dataValues[$data][$key] : '';
        }

        return str_replace(array_keys($values), array_values($values), $template);
    }
}

