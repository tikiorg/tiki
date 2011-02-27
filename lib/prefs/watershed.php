<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_watershed_list()
{
	return array(
		'watershed_log_errors' => array(
			'name' => tra('Log errors to Tiki log'),
			'description' => tra('Errors will be logged to the Tiki log'),
			'type' => 'flag',
		),
		'watershed_channel_trackerId' => array(
			'name' => tra('Tracker ID of Channel tracker'),
			'description' => tra('There must be a tracker to store info of each channel you create'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_brand_fieldId' => array(
			'name' => tra('Field ID of BrandId'),
			'description' => tra('The Channel tracker must have a text field for the Watershed BrandId'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_channel_fieldId' => array(
			'name' => tra('Field ID of ChannelCode'),
			'description' => tra('The Channel tracker must have a text field for the Watershed ChannelCode'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_trackerId' => array(
			'name' => tra('Tracker ID of Archive tracker'),
			'description' => tra('There must be a tracker to store Archive info'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_fieldId' => array(
			'name' => tra('Field ID of videoId'),
			'description' => tra('The Archive tracker must have a text field for the videoId of each archive'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_brand_fieldId' => array(
			'name' => tra('Field ID of BrandId for Archive'),
			'description' => tra('The Archive tracker must have a text field for the BrandId of each archive'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_channel_fieldId' => array(
			'name' => tra('Field ID of ChannelCode for Archive'),
			'description' => tra('The Archive tracker must have a text field for the ChannelCode of each archive'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_rtmpurl_fieldId' => array(
			'name' => tra('Field ID of rtmpURL for Archive (url field type)'),
			'description' => tra('The Archive tracker must have a url field for storing the RTMP URL to access the archive'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_flvurl_fieldId' => array(
			'name' => tra('Field ID of flvURL for Archive (url field type)'),
			'description' => tra('The Archive tracker must have a url field for storing the FLV URL to access the archive'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_date_fieldId' => array(
			'name' => tra('Field ID of Date string for Archive'),
			'description' => tra('The Archive tracker can have a text field for storing the time the recording completed'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_duration_fieldId' => array(
			'name' => tra('Field ID of Duration for Archive'),
			'description' => tra('The Archive tracker can have a text field for storing the duration of the recording'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_filesize_fieldId' => array(
			'name' => tra('Field ID of Filesize for Archive'),
			'description' => tra('The Archive tracker can have a text field for storing the filesize of the recording'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_title_fieldId' => array(
			'name' => tra('Field ID of Title for Archive'),
			'description' => tra('The Archive tracker could have a text field for storing the title of the recording'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_desc_fieldId' => array(
			'name' => tra('Field ID of Description for Archive (textarea field type)'),
			'description' => tra('The Archive tracker could have a textarea field for storing the description of the recording'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_archive_tags_fieldId' => array(
			'name' => tra('Field ID of Tags for Archive (freetags field type)'),
			'description' => tra('The Archive tracker could have a freetags field for storing the tags of the recording'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'watershed_fme_key' => array(
			'name' => tra('Flash Media Encoder shared secret'),
			'description' => tra('Shared key for authenticating Flash Media Encoder'),
			'type' => 'text',
			'size' => '30',
			'filter' => 'text',
		),
	);	
}
