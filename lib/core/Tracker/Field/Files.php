<?php

class Tracker_Field_Files extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$galleryId = (int) $this->getOption(0);
		$count = (int) $this->getOption(2);

		$value = '';
		$ins_id = $this->getInsertId();
		if (isset($requestData[$ins_id])) {
			$value = $requestData[$ins_id];

			$fileIds = array_filter(explode(',', $value));
			if ($count) {
				$fileIds = array_slice($fileIds, 0, $count);
			}

			$fileInfo = $this->getFileInfo($fileIds);
			$fileInfo = array_filter($fileInfo, array($this, 'filterFile'));

			$value = implode(',', array_keys($fileInfo));
		} else {
			$value = $this->getValue();

			$fileIds = array_filter(explode(',', $value));
			$fileInfo = $this->getFileInfo($fileIds);
		}

		return array(
			'galleryId' => $galleryId,
			'limit' => $count,
			'files' => $fileInfo,
			'value' => $value,
		);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/files.tpl', $context);
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/files.tpl', $context);
	}

	function handleSave($value, $oldValue)
	{
		return array(
			'value' => $value,
		);
	}

	function watchCompare($old, $new)
	{
	}

	function filterFile($info)
	{
		$filter = $this->getOption(1);

		if (! $filter) {
			return true;
		}

		$parts = explode('*', $filter);
		$parts = array_map('preg_quote', $parts, array_fill(0, count($parts), '/'));

		$body = implode('[\w-]+', $parts);

		// Force begin, ignore end which may contain charsets or other attributes
		return preg_match("/^$body/", $info['filetype']);
	}

	function getFileInfo($ids)
	{
		$db = TikiDb::get();
		$table = $db->table('tiki_files');

		$data = $table->fetchAll(array('fileId', 'name', 'filetype'), array(
			'fileId' => $table->in($ids),
		));

		$out = array();
		foreach ($data as $info) {
			$out[$info['fileId']] = $info;
		}

		return $out;
	}
}

