<?php
/*
This file is part of the Kaltura Collaborative Media Suite which allows users
to do with audio, video, and animation what Wiki platfroms allow them to do with
text.

Copyright (C) 2006-2008 Kaltura Inc.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
Generated at 24/12 13:22:14
**/

require_once("kaltura_client_base.php");

class KalturaEntry
{
	public $name;
	public $tags;
	public $type;
	public $mediaType;
	public $source;
	public $sourceId;
	public $sourceLink;
	public $licenseType;
	public $credit;
	public $groupId;
	public $partnerData;
	public $conversionQuality;
	public $permissions;
	public $dataContent;
	public $desiredVersion;
	public $url;
	public $thumbUrl;
	public $filename;
	public $realFilename;
	public $indexedCustomData1;
	public $thumbOffset;
	public $mediaId;
	public $screenName;
	public $siteUrl;
	public $description;
	public $mediaDate;
	public $adminTags;
}

class KalturaBatchJob
{
	public $data;
	public $status;
	public $abort;
	public $checkAgainTimeout;
	public $progress;
	public $message;
	public $description;
	public $updatesCount;
	public $processorExpiration;
}

class KalturaKShow
{
	public $name;
	public $description;
	public $tags;
	public $indexedCustomData3;
	public $groupId;
	public $permissions;
	public $partnerData;
	public $allowQuickEdit;
}

class KalturaModeration
{
	public $comments;
	public $objectType;
	public $objectId;
	public $reportCode;
	public $status;
}

class KalturaUiConf
{
	public $name;
	public $objType;
	public $width;
	public $height;
	public $htmlParams;
	public $swfUrl;
	public $swfUrlVersion;
	public $confFile;
	public $confVars;
	public $useCdn;
	public $tags;
}

class KalturaUser
{
	public $screenName;
	public $fullName;
	public $email;
	public $dateOfBirth;
	public $aboutMe;
	public $tags;
	public $gender;
	public $country;
	public $state;
	public $city;
	public $zip;
	public $urlList;
	public $networkHighschool;
	public $networkCollege;
	public $partnerData;
}

class KalturaWidget
{
	public $kshowId;
	public $entryId;
	public $sourceWidgetId;
	public $uiConfId;
	public $customData;
	public $partnerData;
	public $securityType;
}

class KalturaPuserKuser
{
}

class KalturaConvesionProfileFilter
{
	const ORDER_BY_CREATED_AT_ASC = "+created_at";
	const ORDER_BY_CREATED_AT_DESC = "-created_at";
	const ORDER_BY_PROFILE_TYPE_ASC = "+profile_type";
	const ORDER_BY_PROFILE_TYPE_DESC = "-profile_type";
	const ORDER_BY_ID_ASC = "+id";
	const ORDER_BY_ID_DESC = "-id";

	public $equalId;
	public $greaterThanOrEqualId;
	public $equalStatus;
	public $likeName;
	public $inProfileType;
	public $equalEnabled;
	public $equalType;
	public $equalUseWithBulk;
	public $orderBy;
	public $limit;
}

class KalturaConversionProfile
{
	public $name;
	public $profileType;
	public $width;
	public $height;
	public $aspectRatio;
	public $bypassFlv;
	public $commercialTranscoder;
	public $useWithBulk;
}

class KalturaBatchJobFilter
{
	const ORDER_BY_ID_ASC = "+id";
	const ORDER_BY_ID_DESC = "-id";

	public $equalId;
	public $greaterThanOrEqualId;
	public $equalStatus;
	public $equalJobType;
	public $inJobType;
	public $orderBy;
	public $limit;
}

class KalturaEntryFilter
{
	const ORDER_BY_CREATED_AT_ASC = "+created_at";
	const ORDER_BY_CREATED_AT_DESC = "-created_at";
	const ORDER_BY_VIEWS_ASC = "+views";
	const ORDER_BY_VIEWS_DESC = "-views";
	const ORDER_BY_NAME_ASC = "+name";
	const ORDER_BY_NAME_DESC = "-name";
	const ORDER_BY_MEDIA_DATE_ASC = "+media_date";
	const ORDER_BY_MEDIA_DATE_DESC = "-media_date";
	const ORDER_BY_TYPE_ASC = "+type";
	const ORDER_BY_TYPE_DESC = "-type";
	const ORDER_BY_MEDIA_TYPE_ASC = "+media_type";
	const ORDER_BY_MEDIA_TYPE_DESC = "-media_type";
	const ORDER_BY_PLAYS_ASC = "+plays";
	const ORDER_BY_PLAYS_DESC = "-plays";
	const ORDER_BY_RANK_ASC = "+rank";
	const ORDER_BY_RANK_DESC = "-rank";
	const ORDER_BY_MODERATION_COUNT_ASC = "+moderation_count";
	const ORDER_BY_MODERATION_COUNT_DESC = "-moderation_count";
	const ORDER_BY_MODERATION_STATUS_ASC = "+moderation_status";
	const ORDER_BY_MODERATION_STATUS_DESC = "-moderation_status";
	const ORDER_BY_MODIFIED_AT_ASC = "+modified_at";
	const ORDER_BY_MODIFIED_AT_DESC = "-modified_at";
	const ORDER_BY_ID_ASC = "+id";
	const ORDER_BY_ID_DESC = "-id";

	public $equalUserId;
	public $equalKshowId;
	public $equalStatus;
	public $inStatus;
	public $equalType;
	public $inType;
	public $equalMediaType;
	public $inMediaType;
	public $equalIndexedCustomData1;
	public $inIndexedCustomData1;
	public $likeName;
	public $equalName;
	public $equalTags;
	public $likeTags;
	public $multiLikeOrTags;
	public $multiLikeAndTags;
	public $multiLikeOrAdminTags;
	public $multiLikeAndAdminTags;
	public $likeAdminTags;
	public $multiLikeOrName;
	public $multiLikeAndName;
	public $multiLikeOrSearchText;
	public $multiLikeAndSearchText;
	public $equalGroupId;
	public $greaterThanOrEqualViews;
	public $greaterThanOrEqualCreatedAt;
	public $lessThanOrEqualCreatedAt;
	public $greaterThanOrEqualUpdatedAt;
	public $lessThanOrEqualUpdatedAt;
	public $greaterThanOrEqualModifiedAt;
	public $lessThanOrEqualModifiedAt;
	public $inPartnerId;
	public $equalPartnerId;
	public $equalSourceLink;
	public $greaterThanOrEqualMediaDate;
	public $lessThanOrEqualMediaDate;
	public $equalModerationStatus;
	public $inModerationStatus;
	public $inDisplayInSearch;
	public $multiLikeOrTagsOrName;
	public $multiLikeOrTagsOrAdminTags;
	public $multiLikeOrTagsOrAdminTagsOrName;
	public $orderBy;
	public $limit;
}

class KalturaKShowFilter
{
	const ORDER_BY_CREATED_AT_ASC = "+created_at";
	const ORDER_BY_CREATED_AT_DESC = "-created_at";
	const ORDER_BY_VIEWS_ASC = "+views";
	const ORDER_BY_VIEWS_DESC = "-views";
	const ORDER_BY_RANK_ASC = "+rank";
	const ORDER_BY_RANK_DESC = "-rank";
	const ORDER_BY_ID_ASC = "+id";
	const ORDER_BY_ID_DESC = "-id";

	public $likeName;
	public $likeTags;
	public $multiLikeOrTags;
	public $multiLikeAndTags;
	public $greaterThanOrEqualViews;
	public $equalType;
	public $equalProducerId;
	public $greaterThanOrEqualCreatedAt;
	public $lessThanOrEqualCreatedAt;
	public $bitAndStatus;
	public $equalIndexedCustomData3;
	public $orderBy;
	public $limit;
}

class KalturaModerationFilter
{
	const ORDER_BY_ID_ASC = "+id";
	const ORDER_BY_ID_DESC = "-id";

	public $equalId;
	public $equalPuserId;
	public $equalStatus;
	public $inStatus;
	public $likeComments;
	public $equalObjectId;
	public $equalObjectType;
	public $equalGroupId;
	public $orderBy;
	public $limit;
}

class KalturaNotificationFilter
{
	const ORDER_BY_ID_ASC = "+id";
	const ORDER_BY_ID_DESC = "-id";

	public $equalId;
	public $greaterThanOrEqualId;
	public $equalStatus;
	public $equalType;
	public $orderBy;
	public $limit;
}

class KalturaNotification
{
	public $id;
	public $status;
	public $notificationResult;
}

class KalturaUiConfFilter
{
	const ORDER_BY_ID_ASC = "+id";
	const ORDER_BY_ID_DESC = "-id";

	public $equalId;
	public $greaterThanOrEqualId;
	public $equalStatus;
	public $equalObjType;
	public $likeName;
	public $multiLikeOrTags;
	public $orderBy;
	public $limit;
}

class KalturaBatchjobType
{
	const CONVERT = "0";
	const IMPORT = "1";
	const DELETE = "2";
	const FLATTEN = "3";
	const BULKUPLOAD = "4";
	const DVDCREATOR = "5";
	const DOWNLOAD = "6";

}

class KalturaPartner
{
	public $name;
	public $url1;
	public $url2;
	public $appearInSearch;
	public $adminName;
	public $adminEmail;
	public $description;
	public $commercialUse;
	public $landingPage;
	public $userLandingPage;
	public $notificationsConfig;
	public $notify;
	public $allowMultiNotification;
	public $contentCategories;
	public $type;
}

class KalturaEntryMediaType
{
	const ANY = "0";
	const VIDEO = "1";
	const IMAGE = "2";
	const TEXT = "3";
	const HTML = "4";
	const AUDIO = "5";
	const SHOW = "6";
	const SHOW_XML = "7";
	const BUBBLES = "9";
	const XML = "10";
	const GENERIC_1 = "101";
	const GENERIC_2 = "102";
	const GENERIC_3 = "103";
	const GENERIC_4 = "104";

}

class KalturaEntryMediaSource
{
	const FILE = "1";
	const WEBCAM = "2";
	const FLICKR = "3";
	const YOUTUBE = "4";
	const URL = "5";
	const TEXT = "6";
	const MYSPACE = "7";
	const PHOTOBUCKET = "8";
	const JAMENDO = "9";
	const CCMIXTER = "10";
	const NYPL = "11";
	const CURRENT = "12";
	const MEDIA_COMMONS = "13";
	const KALTURA = "20";
	const KALTURA_USER_CLIPS = "21";
	const ARCHIVE_ORG = "22";
	const KALTURA_PARTNER = "23";
	const METACAFE = "24";
	const KALTURA_QA = "25";
	const KALTURA_KSHOW = "26";
	const KALTURA_PARTNER_KSHOW = "27";
	const SEARCH_PROXY = "28";

}

class KalturaEntryType
{
	const BACKGROUND = "0";
	const MEDIACLIP = "1";
	const SHOW = "2";
	const BUBBLES = "4";
	const PLAYLIST = "5";
	const DVD = "300";

}

class KalturaClient extends KalturaClientBase
{
	public function __constructor()
	{
		parent::__constructor();
	}

	public function addDownload(KalturaSessionUser $kalturaSessionUser, $entryId, $fileFormat, $entryVersion = null)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$params["file_format"] = $fileFormat;
		$this->addOptionalParam($params, "entry_version", $entryVersion);

		$result = $this->hit("adddownload", $kalturaSessionUser, $params);
		return $result;
	}

	public function addDvdEntry(KalturaSessionUser $kalturaSessionUser, KalturaEntry $dvdEntry)
	{
		$params = array();
		$this->addOptionalParam($params, "dvdEntry_name", $dvdEntry->name);
		$this->addOptionalParam($params, "dvdEntry_tags", $dvdEntry->tags);
		$this->addOptionalParam($params, "dvdEntry_type", $dvdEntry->type);
		$this->addOptionalParam($params, "dvdEntry_mediaType", $dvdEntry->mediaType);
		$this->addOptionalParam($params, "dvdEntry_source", $dvdEntry->source);
		$this->addOptionalParam($params, "dvdEntry_sourceId", $dvdEntry->sourceId);
		$this->addOptionalParam($params, "dvdEntry_sourceLink", $dvdEntry->sourceLink);
		$this->addOptionalParam($params, "dvdEntry_licenseType", $dvdEntry->licenseType);
		$this->addOptionalParam($params, "dvdEntry_credit", $dvdEntry->credit);
		$this->addOptionalParam($params, "dvdEntry_groupId", $dvdEntry->groupId);
		$this->addOptionalParam($params, "dvdEntry_partnerData", $dvdEntry->partnerData);
		$this->addOptionalParam($params, "dvdEntry_conversionQuality", $dvdEntry->conversionQuality);
		$this->addOptionalParam($params, "dvdEntry_permissions", $dvdEntry->permissions);
		$this->addOptionalParam($params, "dvdEntry_dataContent", $dvdEntry->dataContent);
		$this->addOptionalParam($params, "dvdEntry_desiredVersion", $dvdEntry->desiredVersion);
		$this->addOptionalParam($params, "dvdEntry_url", $dvdEntry->url);
		$this->addOptionalParam($params, "dvdEntry_thumbUrl", $dvdEntry->thumbUrl);
		$this->addOptionalParam($params, "dvdEntry_filename", $dvdEntry->filename);
		$this->addOptionalParam($params, "dvdEntry_realFilename", $dvdEntry->realFilename);
		$this->addOptionalParam($params, "dvdEntry_indexedCustomData1", $dvdEntry->indexedCustomData1);
		$this->addOptionalParam($params, "dvdEntry_thumbOffset", $dvdEntry->thumbOffset);
		$this->addOptionalParam($params, "dvdEntry_mediaId", $dvdEntry->mediaId);
		$this->addOptionalParam($params, "dvdEntry_screenName", $dvdEntry->screenName);
		$this->addOptionalParam($params, "dvdEntry_siteUrl", $dvdEntry->siteUrl);
		$this->addOptionalParam($params, "dvdEntry_description", $dvdEntry->description);
		$this->addOptionalParam($params, "dvdEntry_mediaDate", $dvdEntry->mediaDate);
		$this->addOptionalParam($params, "dvdEntry_adminTags", $dvdEntry->adminTags);

		$result = $this->hit("adddvdentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function addDvdJob(KalturaSessionUser $kalturaSessionUser, $entryId)
	{
		$params = array();
		$params["entry_id"] = $entryId;

		$result = $this->hit("adddvdjob", $kalturaSessionUser, $params);
		return $result;
	}

	public function addEntry(KalturaSessionUser $kalturaSessionUser, $kshowId, KalturaEntry $entry, $uid = null)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "entry_name", $entry->name);
		$this->addOptionalParam($params, "entry_tags", $entry->tags);
		$this->addOptionalParam($params, "entry_type", $entry->type);
		$this->addOptionalParam($params, "entry_mediaType", $entry->mediaType);
		$this->addOptionalParam($params, "entry_source", $entry->source);
		$this->addOptionalParam($params, "entry_sourceId", $entry->sourceId);
		$this->addOptionalParam($params, "entry_sourceLink", $entry->sourceLink);
		$this->addOptionalParam($params, "entry_licenseType", $entry->licenseType);
		$this->addOptionalParam($params, "entry_credit", $entry->credit);
		$this->addOptionalParam($params, "entry_groupId", $entry->groupId);
		$this->addOptionalParam($params, "entry_partnerData", $entry->partnerData);
		$this->addOptionalParam($params, "entry_conversionQuality", $entry->conversionQuality);
		$this->addOptionalParam($params, "entry_permissions", $entry->permissions);
		$this->addOptionalParam($params, "entry_dataContent", $entry->dataContent);
		$this->addOptionalParam($params, "entry_desiredVersion", $entry->desiredVersion);
		$this->addOptionalParam($params, "entry_url", $entry->url);
		$this->addOptionalParam($params, "entry_thumbUrl", $entry->thumbUrl);
		$this->addOptionalParam($params, "entry_filename", $entry->filename);
		$this->addOptionalParam($params, "entry_realFilename", $entry->realFilename);
		$this->addOptionalParam($params, "entry_indexedCustomData1", $entry->indexedCustomData1);
		$this->addOptionalParam($params, "entry_thumbOffset", $entry->thumbOffset);
		$this->addOptionalParam($params, "entry_mediaId", $entry->mediaId);
		$this->addOptionalParam($params, "entry_screenName", $entry->screenName);
		$this->addOptionalParam($params, "entry_siteUrl", $entry->siteUrl);
		$this->addOptionalParam($params, "entry_description", $entry->description);
		$this->addOptionalParam($params, "entry_mediaDate", $entry->mediaDate);
		$this->addOptionalParam($params, "entry_adminTags", $entry->adminTags);
		$this->addOptionalParam($params, "uid", $uid);

		$result = $this->hit("addentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function addKShow(KalturaSessionUser $kalturaSessionUser, KalturaKShow $kshow, $detailed = null, $allowDuplicateNames = null)
	{
		$params = array();
		$this->addOptionalParam($params, "kshow_name", $kshow->name);
		$this->addOptionalParam($params, "kshow_description", $kshow->description);
		$this->addOptionalParam($params, "kshow_tags", $kshow->tags);
		$this->addOptionalParam($params, "kshow_indexedCustomData3", $kshow->indexedCustomData3);
		$this->addOptionalParam($params, "kshow_groupId", $kshow->groupId);
		$this->addOptionalParam($params, "kshow_permissions", $kshow->permissions);
		$this->addOptionalParam($params, "kshow_partnerData", $kshow->partnerData);
		$this->addOptionalParam($params, "kshow_allowQuickEdit", $kshow->allowQuickEdit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "allow_duplicate_names", $allowDuplicateNames);

		$result = $this->hit("addkshow", $kalturaSessionUser, $params);
		return $result;
	}

	public function addModeration(KalturaSessionUser $kalturaSessionUser, KalturaModeration $moderation)
	{
		$params = array();
		$this->addOptionalParam($params, "moderation_comments", $moderation->comments);
		$this->addOptionalParam($params, "moderation_objectType", $moderation->objectType);
		$this->addOptionalParam($params, "moderation_objectId", $moderation->objectId);
		$this->addOptionalParam($params, "moderation_reportCode", $moderation->reportCode);
		$this->addOptionalParam($params, "moderation_status", $moderation->status);

		$result = $this->hit("addmoderation", $kalturaSessionUser, $params);
		return $result;
	}

	public function addPartnerEntry(KalturaSessionUser $kalturaSessionUser, $kshowId, KalturaEntry $entry, $uid = null)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "entry_name", $entry->name);
		$this->addOptionalParam($params, "entry_tags", $entry->tags);
		$this->addOptionalParam($params, "entry_type", $entry->type);
		$this->addOptionalParam($params, "entry_mediaType", $entry->mediaType);
		$this->addOptionalParam($params, "entry_source", $entry->source);
		$this->addOptionalParam($params, "entry_sourceId", $entry->sourceId);
		$this->addOptionalParam($params, "entry_sourceLink", $entry->sourceLink);
		$this->addOptionalParam($params, "entry_licenseType", $entry->licenseType);
		$this->addOptionalParam($params, "entry_credit", $entry->credit);
		$this->addOptionalParam($params, "entry_groupId", $entry->groupId);
		$this->addOptionalParam($params, "entry_partnerData", $entry->partnerData);
		$this->addOptionalParam($params, "entry_conversionQuality", $entry->conversionQuality);
		$this->addOptionalParam($params, "entry_permissions", $entry->permissions);
		$this->addOptionalParam($params, "entry_dataContent", $entry->dataContent);
		$this->addOptionalParam($params, "entry_desiredVersion", $entry->desiredVersion);
		$this->addOptionalParam($params, "entry_url", $entry->url);
		$this->addOptionalParam($params, "entry_thumbUrl", $entry->thumbUrl);
		$this->addOptionalParam($params, "entry_filename", $entry->filename);
		$this->addOptionalParam($params, "entry_realFilename", $entry->realFilename);
		$this->addOptionalParam($params, "entry_indexedCustomData1", $entry->indexedCustomData1);
		$this->addOptionalParam($params, "entry_thumbOffset", $entry->thumbOffset);
		$this->addOptionalParam($params, "entry_mediaId", $entry->mediaId);
		$this->addOptionalParam($params, "entry_screenName", $entry->screenName);
		$this->addOptionalParam($params, "entry_siteUrl", $entry->siteUrl);
		$this->addOptionalParam($params, "entry_description", $entry->description);
		$this->addOptionalParam($params, "entry_mediaDate", $entry->mediaDate);
		$this->addOptionalParam($params, "entry_adminTags", $entry->adminTags);
		$this->addOptionalParam($params, "uid", $uid);

		$result = $this->hit("addpartnerentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function addPlaylist(KalturaSessionUser $kalturaSessionUser, KalturaEntry $playlist)
	{
		$params = array();
		$this->addOptionalParam($params, "playlist_name", $playlist->name);
		$this->addOptionalParam($params, "playlist_tags", $playlist->tags);
		$this->addOptionalParam($params, "playlist_type", $playlist->type);
		$this->addOptionalParam($params, "playlist_mediaType", $playlist->mediaType);
		$this->addOptionalParam($params, "playlist_source", $playlist->source);
		$this->addOptionalParam($params, "playlist_sourceId", $playlist->sourceId);
		$this->addOptionalParam($params, "playlist_sourceLink", $playlist->sourceLink);
		$this->addOptionalParam($params, "playlist_licenseType", $playlist->licenseType);
		$this->addOptionalParam($params, "playlist_credit", $playlist->credit);
		$this->addOptionalParam($params, "playlist_groupId", $playlist->groupId);
		$this->addOptionalParam($params, "playlist_partnerData", $playlist->partnerData);
		$this->addOptionalParam($params, "playlist_conversionQuality", $playlist->conversionQuality);
		$this->addOptionalParam($params, "playlist_permissions", $playlist->permissions);
		$this->addOptionalParam($params, "playlist_dataContent", $playlist->dataContent);
		$this->addOptionalParam($params, "playlist_desiredVersion", $playlist->desiredVersion);
		$this->addOptionalParam($params, "playlist_url", $playlist->url);
		$this->addOptionalParam($params, "playlist_thumbUrl", $playlist->thumbUrl);
		$this->addOptionalParam($params, "playlist_filename", $playlist->filename);
		$this->addOptionalParam($params, "playlist_realFilename", $playlist->realFilename);
		$this->addOptionalParam($params, "playlist_indexedCustomData1", $playlist->indexedCustomData1);
		$this->addOptionalParam($params, "playlist_thumbOffset", $playlist->thumbOffset);
		$this->addOptionalParam($params, "playlist_mediaId", $playlist->mediaId);
		$this->addOptionalParam($params, "playlist_screenName", $playlist->screenName);
		$this->addOptionalParam($params, "playlist_siteUrl", $playlist->siteUrl);
		$this->addOptionalParam($params, "playlist_description", $playlist->description);
		$this->addOptionalParam($params, "playlist_mediaDate", $playlist->mediaDate);
		$this->addOptionalParam($params, "playlist_adminTags", $playlist->adminTags);

		$result = $this->hit("addplaylist", $kalturaSessionUser, $params);
		return $result;
	}

	public function addRoughcutEntry(KalturaSessionUser $kalturaSessionUser, $kshowId, KalturaEntry $entry)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "entry_name", $entry->name);
		$this->addOptionalParam($params, "entry_tags", $entry->tags);
		$this->addOptionalParam($params, "entry_type", $entry->type);
		$this->addOptionalParam($params, "entry_mediaType", $entry->mediaType);
		$this->addOptionalParam($params, "entry_source", $entry->source);
		$this->addOptionalParam($params, "entry_sourceId", $entry->sourceId);
		$this->addOptionalParam($params, "entry_sourceLink", $entry->sourceLink);
		$this->addOptionalParam($params, "entry_licenseType", $entry->licenseType);
		$this->addOptionalParam($params, "entry_credit", $entry->credit);
		$this->addOptionalParam($params, "entry_groupId", $entry->groupId);
		$this->addOptionalParam($params, "entry_partnerData", $entry->partnerData);
		$this->addOptionalParam($params, "entry_conversionQuality", $entry->conversionQuality);
		$this->addOptionalParam($params, "entry_permissions", $entry->permissions);
		$this->addOptionalParam($params, "entry_dataContent", $entry->dataContent);
		$this->addOptionalParam($params, "entry_desiredVersion", $entry->desiredVersion);
		$this->addOptionalParam($params, "entry_url", $entry->url);
		$this->addOptionalParam($params, "entry_thumbUrl", $entry->thumbUrl);
		$this->addOptionalParam($params, "entry_filename", $entry->filename);
		$this->addOptionalParam($params, "entry_realFilename", $entry->realFilename);
		$this->addOptionalParam($params, "entry_indexedCustomData1", $entry->indexedCustomData1);
		$this->addOptionalParam($params, "entry_thumbOffset", $entry->thumbOffset);
		$this->addOptionalParam($params, "entry_mediaId", $entry->mediaId);
		$this->addOptionalParam($params, "entry_screenName", $entry->screenName);
		$this->addOptionalParam($params, "entry_siteUrl", $entry->siteUrl);
		$this->addOptionalParam($params, "entry_description", $entry->description);
		$this->addOptionalParam($params, "entry_mediaDate", $entry->mediaDate);
		$this->addOptionalParam($params, "entry_adminTags", $entry->adminTags);

		$result = $this->hit("addroughcutentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function addUiConf(KalturaSessionUser $kalturaSessionUser, KalturaUiConf $uiconf)
	{
		$params = array();
		$this->addOptionalParam($params, "uiconf_name", $uiconf->name);
		$this->addOptionalParam($params, "uiconf_objType", $uiconf->objType);
		$this->addOptionalParam($params, "uiconf_width", $uiconf->width);
		$this->addOptionalParam($params, "uiconf_height", $uiconf->height);
		$this->addOptionalParam($params, "uiconf_htmlParams", $uiconf->htmlParams);
		$this->addOptionalParam($params, "uiconf_swfUrl", $uiconf->swfUrl);
		$this->addOptionalParam($params, "uiconf_swfUrlVersion", $uiconf->swfUrlVersion);
		$this->addOptionalParam($params, "uiconf_confFile", $uiconf->confFile);
		$this->addOptionalParam($params, "uiconf_confVars", $uiconf->confVars);
		$this->addOptionalParam($params, "uiconf_useCdn", $uiconf->useCdn);
		$this->addOptionalParam($params, "uiconf_tags", $uiconf->tags);

		$result = $this->hit("adduiconf", $kalturaSessionUser, $params);
		return $result;
	}

	public function addUser(KalturaSessionUser $kalturaSessionUser, $userId, KalturaUser $user)
	{
		$params = array();
		$params["user_id"] = $userId;
		$this->addOptionalParam($params, "user_screenName", $user->screenName);
		$this->addOptionalParam($params, "user_fullName", $user->fullName);
		$this->addOptionalParam($params, "user_email", $user->email);
		$this->addOptionalParam($params, "user_dateOfBirth", $user->dateOfBirth);
		$this->addOptionalParam($params, "user_aboutMe", $user->aboutMe);
		$this->addOptionalParam($params, "user_tags", $user->tags);
		$this->addOptionalParam($params, "user_gender", $user->gender);
		$this->addOptionalParam($params, "user_country", $user->country);
		$this->addOptionalParam($params, "user_state", $user->state);
		$this->addOptionalParam($params, "user_city", $user->city);
		$this->addOptionalParam($params, "user_zip", $user->zip);
		$this->addOptionalParam($params, "user_urlList", $user->urlList);
		$this->addOptionalParam($params, "user_networkHighschool", $user->networkHighschool);
		$this->addOptionalParam($params, "user_networkCollege", $user->networkCollege);
		$this->addOptionalParam($params, "user_partnerData", $user->partnerData);

		$result = $this->hit("adduser", $kalturaSessionUser, $params);
		return $result;
	}

	public function addWidget(KalturaSessionUser $kalturaSessionUser, KalturaWidget $widget)
	{
		$params = array();
		$this->addOptionalParam($params, "widget_kshowId", $widget->kshowId);
		$this->addOptionalParam($params, "widget_entryId", $widget->entryId);
		$this->addOptionalParam($params, "widget_sourceWidgetId", $widget->sourceWidgetId);
		$this->addOptionalParam($params, "widget_uiConfId", $widget->uiConfId);
		$this->addOptionalParam($params, "widget_customData", $widget->customData);
		$this->addOptionalParam($params, "widget_partnerData", $widget->partnerData);
		$this->addOptionalParam($params, "widget_securityType", $widget->securityType);

		$result = $this->hit("addwidget", $kalturaSessionUser, $params);
		return $result;
	}

	public function adminLogin(KalturaSessionUser $kalturaSessionUser, $email, $password)
	{
		$params = array();
		$params["email"] = $email;
		$params["password"] = $password;

		$result = $this->hit("adminlogin", $kalturaSessionUser, $params);
		return $result;
	}

	public function appendEntryToRoughcut(KalturaSessionUser $kalturaSessionUser, $entryId, $kshowId, $showEntryId = null)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "show_entry_id", $showEntryId);

		$result = $this->hit("appendentrytoroughcut", $kalturaSessionUser, $params);
		return $result;
	}

	public function checkNotifications(KalturaSessionUser $kalturaSessionUser, $notificationIds, $separator = ",", $detailed = null)
	{
		$params = array();
		$params["notification_ids"] = $notificationIds;
		$this->addOptionalParam($params, "separator", $separator);
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("checknotifications", $kalturaSessionUser, $params);
		return $result;
	}

	public function cloneKShow(KalturaSessionUser $kalturaSessionUser, $kshowId, $detailed = null)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("clonekshow", $kalturaSessionUser, $params);
		return $result;
	}

	public function cloneRoughcut(KalturaSessionUser $kalturaSessionUser, $entryId, $detailed = null)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("cloneroughcut", $kalturaSessionUser, $params);
		return $result;
	}

	public function cloneUiConf(KalturaSessionUser $kalturaSessionUser, $uiconfId, $detailed = null)
	{
		$params = array();
		$params["uiconf_id"] = $uiconfId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("cloneuiconf", $kalturaSessionUser, $params);
		return $result;
	}

	public function deleteEntry(KalturaSessionUser $kalturaSessionUser, $entryId)
	{
		$params = array();
		$params["entry_id"] = $entryId;

		$result = $this->hit("deleteentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function deleteKShow(KalturaSessionUser $kalturaSessionUser, $kshowId)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;

		$result = $this->hit("deletekshow", $kalturaSessionUser, $params);
		return $result;
	}

	public function deletePlaylist(KalturaSessionUser $kalturaSessionUser, $entryId)
	{
		$params = array();
		$params["entry_id"] = $entryId;

		$result = $this->hit("deleteplaylist", $kalturaSessionUser, $params);
		return $result;
	}

	public function deleteUser(KalturaSessionUser $kalturaSessionUser, $userId)
	{
		$params = array();
		$params["user_id"] = $userId;

		$result = $this->hit("deleteuser", $kalturaSessionUser, $params);
		return $result;
	}

	public function executePlaylist(KalturaSessionUser $kalturaSessionUser, $playlistId, $fp = null, $filter1 = null, $filter2 = null, $filter3 = null, $filter4 = null, $detailed = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$params["playlist_id"] = $playlistId;
		$this->addOptionalParam($params, "fp", $fp);
		$this->addOptionalParam($params, "filter1", $filter1);
		$this->addOptionalParam($params, "filter2", $filter2);
		$this->addOptionalParam($params, "filter3", $filter3);
		$this->addOptionalParam($params, "filter4", $filter4);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("executeplaylist", $kalturaSessionUser, $params);
		return $result;
	}

	public function getAdminTags(KalturaSessionUser $kalturaSessionUser)
	{
		$params = array();

		$result = $this->hit("getadmintags", $kalturaSessionUser, $params);
		return $result;
	}

	public function getAllEntries(KalturaSessionUser $kalturaSessionUser, $entryId, $kshowId, $listType = null, $version = null, $disableRoughcutEntryData = null)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "list_type", $listType);
		$this->addOptionalParam($params, "version", $version);
		$this->addOptionalParam($params, "disable_roughcut_entry_data", $disableRoughcutEntryData);

		$result = $this->hit("getallentries", $kalturaSessionUser, $params);
		return $result;
	}

	public function getDefaultWidget(KalturaSessionUser $kalturaSessionUser, $uiConfId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "ui_conf_id", $uiConfId);

		$result = $this->hit("getdefaultwidget", $kalturaSessionUser, $params);
		return $result;
	}

	public function getDvdEntry(KalturaSessionUser $kalturaSessionUser, $dvdEntryId, $detailed = null)
	{
		$params = array();
		$params["dvdEntry_id"] = $dvdEntryId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("getdvdentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function getEntries(KalturaSessionUser $kalturaSessionUser, $entryIds, $separator = ",", $detailed = null)
	{
		$params = array();
		$params["entry_ids"] = $entryIds;
		$this->addOptionalParam($params, "separator", $separator);
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("getentries", $kalturaSessionUser, $params);
		return $result;
	}

	public function getEntry(KalturaSessionUser $kalturaSessionUser, $entryId, $detailed = null, $version = null)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "version", $version);

		$result = $this->hit("getentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function getEntryRoughcuts(KalturaSessionUser $kalturaSessionUser, $entryId)
	{
		$params = array();
		$params["entry_id"] = $entryId;

		$result = $this->hit("getentryroughcuts", $kalturaSessionUser, $params);
		return $result;
	}

	public function getKShow(KalturaSessionUser $kalturaSessionUser, $kshowId, $detailed = null)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("getkshow", $kalturaSessionUser, $params);
		return $result;
	}

	public function getLastVersionsInfo(KalturaSessionUser $kalturaSessionUser, $kshowId)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;

		$result = $this->hit("getlastversionsinfo", $kalturaSessionUser, $params);
		return $result;
	}

	public function getMetaDataAction(KalturaSessionUser $kalturaSessionUser, $entryId, $kshowId, $version)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$params["kshow_id"] = $kshowId;
		$params["version"] = $version;

		$result = $this->hit("getmetadata", $kalturaSessionUser, $params);
		return $result;
	}

	public function getPartner(KalturaSessionUser $kalturaSessionUser, $partnerAdminEmail, $cmsPassword, $partnerId)
	{
		$params = array();
		$params["partner_adminEmail"] = $partnerAdminEmail;
		$params["cms_password"] = $cmsPassword;
		$params["partner_id"] = $partnerId;

		$result = $this->hit("getpartner", $kalturaSessionUser, $params);
		return $result;
	}

	public function getPlaylist(KalturaSessionUser $kalturaSessionUser, $playlistId, $detailed = null)
	{
		$params = array();
		$params["playlist_id"] = $playlistId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("getplaylist", $kalturaSessionUser, $params);
		return $result;
	}

	public function getThumbnail(KalturaSessionUser $kalturaSessionUser, $filename)
	{
		$params = array();
		$params["filename"] = $filename;

		$result = $this->hit("getthumbnail", $kalturaSessionUser, $params);
		return $result;
	}

	public function getUIConf(KalturaSessionUser $kalturaSessionUser, $uiConfId, $detailed = null)
	{
		$params = array();
		$params["ui_conf_id"] = $uiConfId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("getuiconf", $kalturaSessionUser, $params);
		return $result;
	}

	public function getUser(KalturaSessionUser $kalturaSessionUser, $userId, $detailed = null)
	{
		$params = array();
		$params["user_id"] = $userId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("getuser", $kalturaSessionUser, $params);
		return $result;
	}

	public function getWidget(KalturaSessionUser $kalturaSessionUser, $widgetId, $detailed = null)
	{
		$params = array();
		$params["widget_id"] = $widgetId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("getwidget", $kalturaSessionUser, $params);
		return $result;
	}

	public function handleModeration(KalturaSessionUser $kalturaSessionUser, $moderationId, $moderationStatus)
	{
		$params = array();
		$params["moderation_id"] = $moderationId;
		$params["moderation_status"] = $moderationStatus;

		$result = $this->hit("handlemoderation", $kalturaSessionUser, $params);
		return $result;
	}

	public function listConversionProfile(KalturaSessionUser $kalturaSessionUser, KalturaConvesionProfileFilter $filter, $detailed = null, $pageSize = 10, $page = 1)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_id", $filter->equalId);
		$this->addOptionalParam($params, "filter__gte_id", $filter->greaterThanOrEqualId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__in_profile_type", $filter->inProfileType);
		$this->addOptionalParam($params, "filter__eq_enabled", $filter->equalEnabled);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__eq_use_with_bulk", $filter->equalUseWithBulk);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);

		$result = $this->hit("listconversionprofiles", $kalturaSessionUser, $params);
		return $result;
	}

	public function listDownloads(KalturaSessionUser $kalturaSessionUser, KalturaBatchJobFilter $filter, $detailed = null, $pageSize = 10, $page = 1)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_id", $filter->equalId);
		$this->addOptionalParam($params, "filter__gte_id", $filter->greaterThanOrEqualId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__eq_job_type", $filter->equalJobType);
		$this->addOptionalParam($params, "filter__in_job_type", $filter->inJobType);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);

		$result = $this->hit("listdownloads", $kalturaSessionUser, $params);
		return $result;
	}

	public function listDvdEntries(KalturaSessionUser $kalturaSessionUser, KalturaEntryFilter $filter, $detailed = null, $detailedFields = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_user_id", $filter->equalUserId);
		$this->addOptionalParam($params, "filter__eq_kshow_id", $filter->equalKshowId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__in_status", $filter->inStatus);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__in_type", $filter->inType);
		$this->addOptionalParam($params, "filter__eq_media_type", $filter->equalMediaType);
		$this->addOptionalParam($params, "filter__in_media_type", $filter->inMediaType);
		$this->addOptionalParam($params, "filter__eq_indexed_custom_data_1", $filter->equalIndexedCustomData1);
		$this->addOptionalParam($params, "filter__in_indexed_custom_data_1", $filter->inIndexedCustomData1);
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__eq_name", $filter->equalName);
		$this->addOptionalParam($params, "filter__eq_tags", $filter->equalTags);
		$this->addOptionalParam($params, "filter__like_tags", $filter->likeTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__mlikeand_tags", $filter->multiLikeAndTags);
		$this->addOptionalParam($params, "filter__mlikeor_admin_tags", $filter->multiLikeOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeand_admin_tags", $filter->multiLikeAndAdminTags);
		$this->addOptionalParam($params, "filter__like_admin_tags", $filter->likeAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_name", $filter->multiLikeOrName);
		$this->addOptionalParam($params, "filter__mlikeand_name", $filter->multiLikeAndName);
		$this->addOptionalParam($params, "filter__mlikeor_search_text", $filter->multiLikeOrSearchText);
		$this->addOptionalParam($params, "filter__mlikeand_search_text", $filter->multiLikeAndSearchText);
		$this->addOptionalParam($params, "filter__eq_group_id", $filter->equalGroupId);
		$this->addOptionalParam($params, "filter__gte_views", $filter->greaterThanOrEqualViews);
		$this->addOptionalParam($params, "filter__gte_created_at", $filter->greaterThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__lte_created_at", $filter->lessThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__gte_updated_at", $filter->greaterThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__lte_updated_at", $filter->lessThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__gte_modified_at", $filter->greaterThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__lte_modified_at", $filter->lessThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__in_partner_id", $filter->inPartnerId);
		$this->addOptionalParam($params, "filter__eq_partner_id", $filter->equalPartnerId);
		$this->addOptionalParam($params, "filter__eq_source_link", $filter->equalSourceLink);
		$this->addOptionalParam($params, "filter__gte_media_date", $filter->greaterThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__lte_media_date", $filter->lessThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__eq_moderation_status", $filter->equalModerationStatus);
		$this->addOptionalParam($params, "filter__in_moderation_status", $filter->inModerationStatus);
		$this->addOptionalParam($params, "filter__in_display_in_search", $filter->inDisplayInSearch);
		$this->addOptionalParam($params, "filter__mlikeor_tags-name", $filter->multiLikeOrTagsOrName);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags", $filter->multiLikeOrTagsOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags-name", $filter->multiLikeOrTagsOrAdminTagsOrName);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "detailed_fields", $detailedFields);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("listdvdentries", $kalturaSessionUser, $params);
		return $result;
	}

	public function listEntries(KalturaSessionUser $kalturaSessionUser, KalturaEntryFilter $filter, $detailed = null, $detailedFields = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_user_id", $filter->equalUserId);
		$this->addOptionalParam($params, "filter__eq_kshow_id", $filter->equalKshowId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__in_status", $filter->inStatus);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__in_type", $filter->inType);
		$this->addOptionalParam($params, "filter__eq_media_type", $filter->equalMediaType);
		$this->addOptionalParam($params, "filter__in_media_type", $filter->inMediaType);
		$this->addOptionalParam($params, "filter__eq_indexed_custom_data_1", $filter->equalIndexedCustomData1);
		$this->addOptionalParam($params, "filter__in_indexed_custom_data_1", $filter->inIndexedCustomData1);
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__eq_name", $filter->equalName);
		$this->addOptionalParam($params, "filter__eq_tags", $filter->equalTags);
		$this->addOptionalParam($params, "filter__like_tags", $filter->likeTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__mlikeand_tags", $filter->multiLikeAndTags);
		$this->addOptionalParam($params, "filter__mlikeor_admin_tags", $filter->multiLikeOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeand_admin_tags", $filter->multiLikeAndAdminTags);
		$this->addOptionalParam($params, "filter__like_admin_tags", $filter->likeAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_name", $filter->multiLikeOrName);
		$this->addOptionalParam($params, "filter__mlikeand_name", $filter->multiLikeAndName);
		$this->addOptionalParam($params, "filter__mlikeor_search_text", $filter->multiLikeOrSearchText);
		$this->addOptionalParam($params, "filter__mlikeand_search_text", $filter->multiLikeAndSearchText);
		$this->addOptionalParam($params, "filter__eq_group_id", $filter->equalGroupId);
		$this->addOptionalParam($params, "filter__gte_views", $filter->greaterThanOrEqualViews);
		$this->addOptionalParam($params, "filter__gte_created_at", $filter->greaterThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__lte_created_at", $filter->lessThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__gte_updated_at", $filter->greaterThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__lte_updated_at", $filter->lessThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__gte_modified_at", $filter->greaterThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__lte_modified_at", $filter->lessThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__in_partner_id", $filter->inPartnerId);
		$this->addOptionalParam($params, "filter__eq_partner_id", $filter->equalPartnerId);
		$this->addOptionalParam($params, "filter__eq_source_link", $filter->equalSourceLink);
		$this->addOptionalParam($params, "filter__gte_media_date", $filter->greaterThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__lte_media_date", $filter->lessThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__eq_moderation_status", $filter->equalModerationStatus);
		$this->addOptionalParam($params, "filter__in_moderation_status", $filter->inModerationStatus);
		$this->addOptionalParam($params, "filter__in_display_in_search", $filter->inDisplayInSearch);
		$this->addOptionalParam($params, "filter__mlikeor_tags-name", $filter->multiLikeOrTagsOrName);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags", $filter->multiLikeOrTagsOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags-name", $filter->multiLikeOrTagsOrAdminTagsOrName);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "detailed_fields", $detailedFields);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("listentries", $kalturaSessionUser, $params);
		return $result;
	}

	public function listKShows(KalturaSessionUser $kalturaSessionUser, KalturaKShowFilter $filter, $detailed = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__like_tags", $filter->likeTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__mlikeand_tags", $filter->multiLikeAndTags);
		$this->addOptionalParam($params, "filter__gte_views", $filter->greaterThanOrEqualViews);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__eq_producer_id", $filter->equalProducerId);
		$this->addOptionalParam($params, "filter__gte_created_at", $filter->greaterThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__lte_created_at", $filter->lessThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__bitand_status", $filter->bitAndStatus);
		$this->addOptionalParam($params, "filter__eq_indexed_custom_data_3", $filter->equalIndexedCustomData3);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("listkshows", $kalturaSessionUser, $params);
		return $result;
	}

	public function listModerations(KalturaSessionUser $kalturaSessionUser, KalturaModerationFilter $filter, $detailed = null, $pageSize = 10, $page = 1)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_id", $filter->equalId);
		$this->addOptionalParam($params, "filter__eq_puser_id", $filter->equalPuserId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__in_status", $filter->inStatus);
		$this->addOptionalParam($params, "filter__like_comments", $filter->likeComments);
		$this->addOptionalParam($params, "filter__eq_object_id", $filter->equalObjectId);
		$this->addOptionalParam($params, "filter__eq_object_type", $filter->equalObjectType);
		$this->addOptionalParam($params, "filter__eq_group_id", $filter->equalGroupId);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);

		$result = $this->hit("listmoderations", $kalturaSessionUser, $params);
		return $result;
	}

	public function listMyDvdEntries(KalturaSessionUser $kalturaSessionUser, KalturaEntryFilter $filter, $detailed = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_user_id", $filter->equalUserId);
		$this->addOptionalParam($params, "filter__eq_kshow_id", $filter->equalKshowId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__in_status", $filter->inStatus);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__in_type", $filter->inType);
		$this->addOptionalParam($params, "filter__eq_media_type", $filter->equalMediaType);
		$this->addOptionalParam($params, "filter__in_media_type", $filter->inMediaType);
		$this->addOptionalParam($params, "filter__eq_indexed_custom_data_1", $filter->equalIndexedCustomData1);
		$this->addOptionalParam($params, "filter__in_indexed_custom_data_1", $filter->inIndexedCustomData1);
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__eq_name", $filter->equalName);
		$this->addOptionalParam($params, "filter__eq_tags", $filter->equalTags);
		$this->addOptionalParam($params, "filter__like_tags", $filter->likeTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__mlikeand_tags", $filter->multiLikeAndTags);
		$this->addOptionalParam($params, "filter__mlikeor_admin_tags", $filter->multiLikeOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeand_admin_tags", $filter->multiLikeAndAdminTags);
		$this->addOptionalParam($params, "filter__like_admin_tags", $filter->likeAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_name", $filter->multiLikeOrName);
		$this->addOptionalParam($params, "filter__mlikeand_name", $filter->multiLikeAndName);
		$this->addOptionalParam($params, "filter__mlikeor_search_text", $filter->multiLikeOrSearchText);
		$this->addOptionalParam($params, "filter__mlikeand_search_text", $filter->multiLikeAndSearchText);
		$this->addOptionalParam($params, "filter__eq_group_id", $filter->equalGroupId);
		$this->addOptionalParam($params, "filter__gte_views", $filter->greaterThanOrEqualViews);
		$this->addOptionalParam($params, "filter__gte_created_at", $filter->greaterThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__lte_created_at", $filter->lessThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__gte_updated_at", $filter->greaterThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__lte_updated_at", $filter->lessThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__gte_modified_at", $filter->greaterThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__lte_modified_at", $filter->lessThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__in_partner_id", $filter->inPartnerId);
		$this->addOptionalParam($params, "filter__eq_partner_id", $filter->equalPartnerId);
		$this->addOptionalParam($params, "filter__eq_source_link", $filter->equalSourceLink);
		$this->addOptionalParam($params, "filter__gte_media_date", $filter->greaterThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__lte_media_date", $filter->lessThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__eq_moderation_status", $filter->equalModerationStatus);
		$this->addOptionalParam($params, "filter__in_moderation_status", $filter->inModerationStatus);
		$this->addOptionalParam($params, "filter__in_display_in_search", $filter->inDisplayInSearch);
		$this->addOptionalParam($params, "filter__mlikeor_tags-name", $filter->multiLikeOrTagsOrName);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags", $filter->multiLikeOrTagsOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags-name", $filter->multiLikeOrTagsOrAdminTagsOrName);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("listmydvdentries", $kalturaSessionUser, $params);
		return $result;
	}

	public function listMyEntries(KalturaSessionUser $kalturaSessionUser, KalturaEntryFilter $filter, $detailed = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_user_id", $filter->equalUserId);
		$this->addOptionalParam($params, "filter__eq_kshow_id", $filter->equalKshowId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__in_status", $filter->inStatus);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__in_type", $filter->inType);
		$this->addOptionalParam($params, "filter__eq_media_type", $filter->equalMediaType);
		$this->addOptionalParam($params, "filter__in_media_type", $filter->inMediaType);
		$this->addOptionalParam($params, "filter__eq_indexed_custom_data_1", $filter->equalIndexedCustomData1);
		$this->addOptionalParam($params, "filter__in_indexed_custom_data_1", $filter->inIndexedCustomData1);
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__eq_name", $filter->equalName);
		$this->addOptionalParam($params, "filter__eq_tags", $filter->equalTags);
		$this->addOptionalParam($params, "filter__like_tags", $filter->likeTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__mlikeand_tags", $filter->multiLikeAndTags);
		$this->addOptionalParam($params, "filter__mlikeor_admin_tags", $filter->multiLikeOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeand_admin_tags", $filter->multiLikeAndAdminTags);
		$this->addOptionalParam($params, "filter__like_admin_tags", $filter->likeAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_name", $filter->multiLikeOrName);
		$this->addOptionalParam($params, "filter__mlikeand_name", $filter->multiLikeAndName);
		$this->addOptionalParam($params, "filter__mlikeor_search_text", $filter->multiLikeOrSearchText);
		$this->addOptionalParam($params, "filter__mlikeand_search_text", $filter->multiLikeAndSearchText);
		$this->addOptionalParam($params, "filter__eq_group_id", $filter->equalGroupId);
		$this->addOptionalParam($params, "filter__gte_views", $filter->greaterThanOrEqualViews);
		$this->addOptionalParam($params, "filter__gte_created_at", $filter->greaterThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__lte_created_at", $filter->lessThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__gte_updated_at", $filter->greaterThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__lte_updated_at", $filter->lessThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__gte_modified_at", $filter->greaterThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__lte_modified_at", $filter->lessThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__in_partner_id", $filter->inPartnerId);
		$this->addOptionalParam($params, "filter__eq_partner_id", $filter->equalPartnerId);
		$this->addOptionalParam($params, "filter__eq_source_link", $filter->equalSourceLink);
		$this->addOptionalParam($params, "filter__gte_media_date", $filter->greaterThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__lte_media_date", $filter->lessThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__eq_moderation_status", $filter->equalModerationStatus);
		$this->addOptionalParam($params, "filter__in_moderation_status", $filter->inModerationStatus);
		$this->addOptionalParam($params, "filter__in_display_in_search", $filter->inDisplayInSearch);
		$this->addOptionalParam($params, "filter__mlikeor_tags-name", $filter->multiLikeOrTagsOrName);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags", $filter->multiLikeOrTagsOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags-name", $filter->multiLikeOrTagsOrAdminTagsOrName);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("listmyentries", $kalturaSessionUser, $params);
		return $result;
	}

	public function listMyKShows(KalturaSessionUser $kalturaSessionUser, KalturaKShowFilter $filter, $detailed = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__like_tags", $filter->likeTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__mlikeand_tags", $filter->multiLikeAndTags);
		$this->addOptionalParam($params, "filter__gte_views", $filter->greaterThanOrEqualViews);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__eq_producer_id", $filter->equalProducerId);
		$this->addOptionalParam($params, "filter__gte_created_at", $filter->greaterThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__lte_created_at", $filter->lessThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__bitand_status", $filter->bitAndStatus);
		$this->addOptionalParam($params, "filter__eq_indexed_custom_data_3", $filter->equalIndexedCustomData3);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("listmykshows", $kalturaSessionUser, $params);
		return $result;
	}

	public function listNotifications(KalturaSessionUser $kalturaSessionUser, KalturaNotificationFilter $filter, $pageSize = 10, $page = 1)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_id", $filter->equalId);
		$this->addOptionalParam($params, "filter__gte_id", $filter->greaterThanOrEqualId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);

		$result = $this->hit("listnotifications", $kalturaSessionUser, $params);
		return $result;
	}

	public function listPartnerEntries(KalturaSessionUser $kalturaSessionUser, KalturaEntryFilter $filter, $detailed = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_user_id", $filter->equalUserId);
		$this->addOptionalParam($params, "filter__eq_kshow_id", $filter->equalKshowId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__in_status", $filter->inStatus);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__in_type", $filter->inType);
		$this->addOptionalParam($params, "filter__eq_media_type", $filter->equalMediaType);
		$this->addOptionalParam($params, "filter__in_media_type", $filter->inMediaType);
		$this->addOptionalParam($params, "filter__eq_indexed_custom_data_1", $filter->equalIndexedCustomData1);
		$this->addOptionalParam($params, "filter__in_indexed_custom_data_1", $filter->inIndexedCustomData1);
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__eq_name", $filter->equalName);
		$this->addOptionalParam($params, "filter__eq_tags", $filter->equalTags);
		$this->addOptionalParam($params, "filter__like_tags", $filter->likeTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__mlikeand_tags", $filter->multiLikeAndTags);
		$this->addOptionalParam($params, "filter__mlikeor_admin_tags", $filter->multiLikeOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeand_admin_tags", $filter->multiLikeAndAdminTags);
		$this->addOptionalParam($params, "filter__like_admin_tags", $filter->likeAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_name", $filter->multiLikeOrName);
		$this->addOptionalParam($params, "filter__mlikeand_name", $filter->multiLikeAndName);
		$this->addOptionalParam($params, "filter__mlikeor_search_text", $filter->multiLikeOrSearchText);
		$this->addOptionalParam($params, "filter__mlikeand_search_text", $filter->multiLikeAndSearchText);
		$this->addOptionalParam($params, "filter__eq_group_id", $filter->equalGroupId);
		$this->addOptionalParam($params, "filter__gte_views", $filter->greaterThanOrEqualViews);
		$this->addOptionalParam($params, "filter__gte_created_at", $filter->greaterThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__lte_created_at", $filter->lessThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__gte_updated_at", $filter->greaterThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__lte_updated_at", $filter->lessThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__gte_modified_at", $filter->greaterThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__lte_modified_at", $filter->lessThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__in_partner_id", $filter->inPartnerId);
		$this->addOptionalParam($params, "filter__eq_partner_id", $filter->equalPartnerId);
		$this->addOptionalParam($params, "filter__eq_source_link", $filter->equalSourceLink);
		$this->addOptionalParam($params, "filter__gte_media_date", $filter->greaterThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__lte_media_date", $filter->lessThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__eq_moderation_status", $filter->equalModerationStatus);
		$this->addOptionalParam($params, "filter__in_moderation_status", $filter->inModerationStatus);
		$this->addOptionalParam($params, "filter__in_display_in_search", $filter->inDisplayInSearch);
		$this->addOptionalParam($params, "filter__mlikeor_tags-name", $filter->multiLikeOrTagsOrName);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags", $filter->multiLikeOrTagsOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags-name", $filter->multiLikeOrTagsOrAdminTagsOrName);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("listpartnerentries", $kalturaSessionUser, $params);
		return $result;
	}

	public function listPlaylists(KalturaSessionUser $kalturaSessionUser, KalturaEntryFilter $filter, $detailed = null, $detailedFields = null, $pageSize = 10, $page = 1, $useFilterPuserId = null)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_user_id", $filter->equalUserId);
		$this->addOptionalParam($params, "filter__eq_kshow_id", $filter->equalKshowId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__in_status", $filter->inStatus);
		$this->addOptionalParam($params, "filter__eq_type", $filter->equalType);
		$this->addOptionalParam($params, "filter__in_type", $filter->inType);
		$this->addOptionalParam($params, "filter__eq_media_type", $filter->equalMediaType);
		$this->addOptionalParam($params, "filter__in_media_type", $filter->inMediaType);
		$this->addOptionalParam($params, "filter__eq_indexed_custom_data_1", $filter->equalIndexedCustomData1);
		$this->addOptionalParam($params, "filter__in_indexed_custom_data_1", $filter->inIndexedCustomData1);
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__eq_name", $filter->equalName);
		$this->addOptionalParam($params, "filter__eq_tags", $filter->equalTags);
		$this->addOptionalParam($params, "filter__like_tags", $filter->likeTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__mlikeand_tags", $filter->multiLikeAndTags);
		$this->addOptionalParam($params, "filter__mlikeor_admin_tags", $filter->multiLikeOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeand_admin_tags", $filter->multiLikeAndAdminTags);
		$this->addOptionalParam($params, "filter__like_admin_tags", $filter->likeAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_name", $filter->multiLikeOrName);
		$this->addOptionalParam($params, "filter__mlikeand_name", $filter->multiLikeAndName);
		$this->addOptionalParam($params, "filter__mlikeor_search_text", $filter->multiLikeOrSearchText);
		$this->addOptionalParam($params, "filter__mlikeand_search_text", $filter->multiLikeAndSearchText);
		$this->addOptionalParam($params, "filter__eq_group_id", $filter->equalGroupId);
		$this->addOptionalParam($params, "filter__gte_views", $filter->greaterThanOrEqualViews);
		$this->addOptionalParam($params, "filter__gte_created_at", $filter->greaterThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__lte_created_at", $filter->lessThanOrEqualCreatedAt);
		$this->addOptionalParam($params, "filter__gte_updated_at", $filter->greaterThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__lte_updated_at", $filter->lessThanOrEqualUpdatedAt);
		$this->addOptionalParam($params, "filter__gte_modified_at", $filter->greaterThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__lte_modified_at", $filter->lessThanOrEqualModifiedAt);
		$this->addOptionalParam($params, "filter__in_partner_id", $filter->inPartnerId);
		$this->addOptionalParam($params, "filter__eq_partner_id", $filter->equalPartnerId);
		$this->addOptionalParam($params, "filter__eq_source_link", $filter->equalSourceLink);
		$this->addOptionalParam($params, "filter__gte_media_date", $filter->greaterThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__lte_media_date", $filter->lessThanOrEqualMediaDate);
		$this->addOptionalParam($params, "filter__eq_moderation_status", $filter->equalModerationStatus);
		$this->addOptionalParam($params, "filter__in_moderation_status", $filter->inModerationStatus);
		$this->addOptionalParam($params, "filter__in_display_in_search", $filter->inDisplayInSearch);
		$this->addOptionalParam($params, "filter__mlikeor_tags-name", $filter->multiLikeOrTagsOrName);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags", $filter->multiLikeOrTagsOrAdminTags);
		$this->addOptionalParam($params, "filter__mlikeor_tags-admin_tags-name", $filter->multiLikeOrTagsOrAdminTagsOrName);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "detailed_fields", $detailedFields);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "use_filter_puser_id", $useFilterPuserId);

		$result = $this->hit("listplaylists", $kalturaSessionUser, $params);
		return $result;
	}

	public function listUiconf(KalturaSessionUser $kalturaSessionUser, KalturaUiConfFilter $filter, $detailed = null, $detailedFields = null, $pageSize = 10, $page = 1)
	{
		$params = array();
		$this->addOptionalParam($params, "filter__eq_id", $filter->equalId);
		$this->addOptionalParam($params, "filter__gte_id", $filter->greaterThanOrEqualId);
		$this->addOptionalParam($params, "filter__eq_status", $filter->equalStatus);
		$this->addOptionalParam($params, "filter__eq_obj_type", $filter->equalObjType);
		$this->addOptionalParam($params, "filter__like_name", $filter->likeName);
		$this->addOptionalParam($params, "filter__mlikeor_tags", $filter->multiLikeOrTags);
		$this->addOptionalParam($params, "filter__order_by", $filter->orderBy);
		$this->addOptionalParam($params, "filter__limit", $filter->limit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "detailed_fields", $detailedFields);
		$this->addOptionalParam($params, "page_size", $pageSize);
		$this->addOptionalParam($params, "page", $page);

		$result = $this->hit("listuiconfs", $kalturaSessionUser, $params);
		return $result;
	}

	public function ping(KalturaSessionUser $kalturaSessionUser)
	{
		$params = array();

		$result = $this->hit("ping", $kalturaSessionUser, $params);
		return $result;
	}

	public function queuePendingBatchJob(KalturaSessionUser $kalturaSessionUser, $jobType, $processorName, $processorTimeout, $overQuotaPartners = null, $deferedPartners = null)
	{
		$params = array();
		$params["job_type"] = $jobType;
		$params["processor_name"] = $processorName;
		$params["processor_timeout"] = $processorTimeout;
		$this->addOptionalParam($params, "over_quota_partners", $overQuotaPartners);
		$this->addOptionalParam($params, "defered_partners", $deferedPartners);

		$result = $this->hit("queuependingbatchjob", $kalturaSessionUser, $params);
		return $result;
	}

	public function rankKShow(KalturaSessionUser $kalturaSessionUser, $kshowId, $rank)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$params["rank"] = $rank;

		$result = $this->hit("rankkshow", $kalturaSessionUser, $params);
		return $result;
	}

	public function registerPartner(KalturaSessionUser $kalturaSessionUser, KalturaPartner $partner, $cmsPassword = null)
	{
		$params = array();
		$this->addOptionalParam($params, "partner_name", $partner->name);
		$this->addOptionalParam($params, "partner_url1", $partner->url1);
		$this->addOptionalParam($params, "partner_url2", $partner->url2);
		$this->addOptionalParam($params, "partner_appearInSearch", $partner->appearInSearch);
		$this->addOptionalParam($params, "partner_adminName", $partner->adminName);
		$this->addOptionalParam($params, "partner_adminEmail", $partner->adminEmail);
		$this->addOptionalParam($params, "partner_description", $partner->description);
		$this->addOptionalParam($params, "partner_commercialUse", $partner->commercialUse);
		$this->addOptionalParam($params, "partner_landingPage", $partner->landingPage);
		$this->addOptionalParam($params, "partner_userLandingPage", $partner->userLandingPage);
		$this->addOptionalParam($params, "partner_notificationsConfig", $partner->notificationsConfig);
		$this->addOptionalParam($params, "partner_notify", $partner->notify);
		$this->addOptionalParam($params, "partner_allowMultiNotification", $partner->allowMultiNotification);
		$this->addOptionalParam($params, "partner_contentCategories", $partner->contentCategories);
		$this->addOptionalParam($params, "partner_type", $partner->type);
		$this->addOptionalParam($params, "cms_password", $cmsPassword);

		$result = $this->hit("registerpartner", $kalturaSessionUser, $params);
		return $result;
	}

	public function reportEntry(KalturaSessionUser $kalturaSessionUser, KalturaModeration $moderation)
	{
		$params = array();
		$this->addOptionalParam($params, "moderation_comments", $moderation->comments);
		$this->addOptionalParam($params, "moderation_objectType", $moderation->objectType);
		$this->addOptionalParam($params, "moderation_objectId", $moderation->objectId);
		$this->addOptionalParam($params, "moderation_reportCode", $moderation->reportCode);
		$this->addOptionalParam($params, "moderation_status", $moderation->status);

		$result = $this->hit("reportentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function reportError(KalturaSessionUser $kalturaSessionUser, $reportingObj = null, $errorCode = null, $errorDescription = null)
	{
		$params = array();
		$this->addOptionalParam($params, "reporting_obj", $reportingObj);
		$this->addOptionalParam($params, "error_code", $errorCode);
		$this->addOptionalParam($params, "error_description", $errorDescription);

		$result = $this->hit("reporterror", $kalturaSessionUser, $params);
		return $result;
	}

	public function reportKShow(KalturaSessionUser $kalturaSessionUser, KalturaModeration $moderation)
	{
		$params = array();
		$this->addOptionalParam($params, "moderation_comments", $moderation->comments);
		$this->addOptionalParam($params, "moderation_objectType", $moderation->objectType);
		$this->addOptionalParam($params, "moderation_objectId", $moderation->objectId);
		$this->addOptionalParam($params, "moderation_reportCode", $moderation->reportCode);
		$this->addOptionalParam($params, "moderation_status", $moderation->status);

		$result = $this->hit("reportkshow", $kalturaSessionUser, $params);
		return $result;
	}

	public function reportUser(KalturaSessionUser $kalturaSessionUser, KalturaModeration $moderation)
	{
		$params = array();
		$this->addOptionalParam($params, "moderation_comments", $moderation->comments);
		$this->addOptionalParam($params, "moderation_objectType", $moderation->objectType);
		$this->addOptionalParam($params, "moderation_objectId", $moderation->objectId);
		$this->addOptionalParam($params, "moderation_reportCode", $moderation->reportCode);
		$this->addOptionalParam($params, "moderation_status", $moderation->status);

		$result = $this->hit("reportuser", $kalturaSessionUser, $params);
		return $result;
	}

	public function rollbackKShow(KalturaSessionUser $kalturaSessionUser, $kshowId, $kshowVersion)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$params["kshow_version"] = $kshowVersion;

		$result = $this->hit("rollbackkshow", $kalturaSessionUser, $params);
		return $result;
	}

	public function search(KalturaSessionUser $kalturaSessionUser, $mediaType, $mediaSource, $search, $authData = null, $page = 1, $pageSize = 10)
	{
		$params = array();
		$params["media_type"] = $mediaType;
		$params["media_source"] = $mediaSource;
		$params["search"] = $search;
		$this->addOptionalParam($params, "auth_data", $authData);
		$this->addOptionalParam($params, "page", $page);
		$this->addOptionalParam($params, "page_size", $pageSize);

		$result = $this->hit("search", $kalturaSessionUser, $params);
		return $result;
	}

	public function searchAuthData(KalturaSessionUser $kalturaSessionUser, $mediaSource, $username, $password)
	{
		$params = array();
		$params["media_source"] = $mediaSource;
		$params["username"] = $username;
		$params["password"] = $password;

		$result = $this->hit("searchauthdata", $kalturaSessionUser, $params);
		return $result;
	}

	public function searchFromUrl(KalturaSessionUser $kalturaSessionUser, $url, $mediaType)
	{
		$params = array();
		$params["url"] = $url;
		$params["media_type"] = $mediaType;

		$result = $this->hit("searchfromurl", $kalturaSessionUser, $params);
		return $result;
	}

	public function searchMediaInfo(KalturaSessionUser $kalturaSessionUser, $mediaType, $mediaSource, $mediaId)
	{
		$params = array();
		$params["media_type"] = $mediaType;
		$params["media_source"] = $mediaSource;
		$params["media_id"] = $mediaId;

		$result = $this->hit("searchmediainfo", $kalturaSessionUser, $params);
		return $result;
	}

	public function searchmediaproviders(KalturaSessionUser $kalturaSessionUser)
	{
		$params = array();

		$result = $this->hit("searchmediaproviders", $kalturaSessionUser, $params);
		return $result;
	}

	public function setMetaData(KalturaSessionUser $kalturaSessionUser, $entryId, $kshowId, $hasRoughCut, $xml)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$params["kshow_id"] = $kshowId;
		$params["HasRoughCut"] = $hasRoughCut;
		$params["xml"] = $xml;

		$result = $this->hit("setmetadata", $kalturaSessionUser, $params);
		return $result;
	}

	public function startSession(KalturaSessionUser $kalturaSessionUser, $secret, $admin = null, $privileges = null, $expiry = 86400)
	{
		$params = array();
		$params["secret"] = $secret;
		$this->addOptionalParam($params, "admin", $admin);
		$this->addOptionalParam($params, "privileges", $privileges);
		$this->addOptionalParam($params, "expiry", $expiry);

		$result = $this->hit("startsession", $kalturaSessionUser, $params);
		return $result;
	}

	public function startWidgetSession(KalturaSessionUser $kalturaSessionUser, $widgetId, $expiry = 86400)
	{
		$params = array();
		$params["widget_id"] = $widgetId;
		$this->addOptionalParam($params, "expiry", $expiry);

		$result = $this->hit("startwidgetsession", $kalturaSessionUser, $params);
		return $result;
	}

	public function testNotification(KalturaSessionUser $kalturaSessionUser)
	{
		$params = array();

		$result = $this->hit("testnotification", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateBatchJob(KalturaSessionUser $kalturaSessionUser, $batchjobId, KalturaBatchJob $batchjob)
	{
		$params = array();
		$params["batchjob_id"] = $batchjobId;
		$this->addOptionalParam($params, "batchjob_data", $batchjob->data);
		$this->addOptionalParam($params, "batchjob_status", $batchjob->status);
		$this->addOptionalParam($params, "batchjob_abort", $batchjob->abort);
		$this->addOptionalParam($params, "batchjob_checkAgainTimeout", $batchjob->checkAgainTimeout);
		$this->addOptionalParam($params, "batchjob_progress", $batchjob->progress);
		$this->addOptionalParam($params, "batchjob_message", $batchjob->message);
		$this->addOptionalParam($params, "batchjob_description", $batchjob->description);
		$this->addOptionalParam($params, "batchjob_updatesCount", $batchjob->updatesCount);
		$this->addOptionalParam($params, "batchjob_processorExpiration", $batchjob->processorExpiration);

		$result = $this->hit("updatebatchjob", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateDvdEntry(KalturaSessionUser $kalturaSessionUser, $entryId, KalturaEntry $entry)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$this->addOptionalParam($params, "entry_name", $entry->name);
		$this->addOptionalParam($params, "entry_tags", $entry->tags);
		$this->addOptionalParam($params, "entry_type", $entry->type);
		$this->addOptionalParam($params, "entry_mediaType", $entry->mediaType);
		$this->addOptionalParam($params, "entry_source", $entry->source);
		$this->addOptionalParam($params, "entry_sourceId", $entry->sourceId);
		$this->addOptionalParam($params, "entry_sourceLink", $entry->sourceLink);
		$this->addOptionalParam($params, "entry_licenseType", $entry->licenseType);
		$this->addOptionalParam($params, "entry_credit", $entry->credit);
		$this->addOptionalParam($params, "entry_groupId", $entry->groupId);
		$this->addOptionalParam($params, "entry_partnerData", $entry->partnerData);
		$this->addOptionalParam($params, "entry_conversionQuality", $entry->conversionQuality);
		$this->addOptionalParam($params, "entry_permissions", $entry->permissions);
		$this->addOptionalParam($params, "entry_dataContent", $entry->dataContent);
		$this->addOptionalParam($params, "entry_desiredVersion", $entry->desiredVersion);
		$this->addOptionalParam($params, "entry_url", $entry->url);
		$this->addOptionalParam($params, "entry_thumbUrl", $entry->thumbUrl);
		$this->addOptionalParam($params, "entry_filename", $entry->filename);
		$this->addOptionalParam($params, "entry_realFilename", $entry->realFilename);
		$this->addOptionalParam($params, "entry_indexedCustomData1", $entry->indexedCustomData1);
		$this->addOptionalParam($params, "entry_thumbOffset", $entry->thumbOffset);
		$this->addOptionalParam($params, "entry_mediaId", $entry->mediaId);
		$this->addOptionalParam($params, "entry_screenName", $entry->screenName);
		$this->addOptionalParam($params, "entry_siteUrl", $entry->siteUrl);
		$this->addOptionalParam($params, "entry_description", $entry->description);
		$this->addOptionalParam($params, "entry_mediaDate", $entry->mediaDate);
		$this->addOptionalParam($params, "entry_adminTags", $entry->adminTags);

		$result = $this->hit("updatedvdentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateEntriesThumbnails(KalturaSessionUser $kalturaSessionUser, $entryIds, $timeOffset)
	{
		$params = array();
		$params["entry_ids"] = $entryIds;
		$params["time_offset"] = $timeOffset;

		$result = $this->hit("updateentriesthumbnails", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateEntry(KalturaSessionUser $kalturaSessionUser, $entryId, KalturaEntry $entry, $allowEmptyField = null)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$this->addOptionalParam($params, "entry_name", $entry->name);
		$this->addOptionalParam($params, "entry_tags", $entry->tags);
		$this->addOptionalParam($params, "entry_type", $entry->type);
		$this->addOptionalParam($params, "entry_mediaType", $entry->mediaType);
		$this->addOptionalParam($params, "entry_source", $entry->source);
		$this->addOptionalParam($params, "entry_sourceId", $entry->sourceId);
		$this->addOptionalParam($params, "entry_sourceLink", $entry->sourceLink);
		$this->addOptionalParam($params, "entry_licenseType", $entry->licenseType);
		$this->addOptionalParam($params, "entry_credit", $entry->credit);
		$this->addOptionalParam($params, "entry_groupId", $entry->groupId);
		$this->addOptionalParam($params, "entry_partnerData", $entry->partnerData);
		$this->addOptionalParam($params, "entry_conversionQuality", $entry->conversionQuality);
		$this->addOptionalParam($params, "entry_permissions", $entry->permissions);
		$this->addOptionalParam($params, "entry_dataContent", $entry->dataContent);
		$this->addOptionalParam($params, "entry_desiredVersion", $entry->desiredVersion);
		$this->addOptionalParam($params, "entry_url", $entry->url);
		$this->addOptionalParam($params, "entry_thumbUrl", $entry->thumbUrl);
		$this->addOptionalParam($params, "entry_filename", $entry->filename);
		$this->addOptionalParam($params, "entry_realFilename", $entry->realFilename);
		$this->addOptionalParam($params, "entry_indexedCustomData1", $entry->indexedCustomData1);
		$this->addOptionalParam($params, "entry_thumbOffset", $entry->thumbOffset);
		$this->addOptionalParam($params, "entry_mediaId", $entry->mediaId);
		$this->addOptionalParam($params, "entry_screenName", $entry->screenName);
		$this->addOptionalParam($params, "entry_siteUrl", $entry->siteUrl);
		$this->addOptionalParam($params, "entry_description", $entry->description);
		$this->addOptionalParam($params, "entry_mediaDate", $entry->mediaDate);
		$this->addOptionalParam($params, "entry_adminTags", $entry->adminTags);
		$this->addOptionalParam($params, "allow_empty_field", $allowEmptyField);

		$result = $this->hit("updateentry", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateEntryTModeration(KalturaSessionUser $kalturaSessionUser, $entryId, $moderationStatus)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$params["moderation_status"] = $moderationStatus;

		$result = $this->hit("updateentrymoderation", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateEntryThumbnail(KalturaSessionUser $kalturaSessionUser, $entryId, $sourceEntryId = null, $timeOffset = null)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$this->addOptionalParam($params, "source_entry_id", $sourceEntryId);
		$this->addOptionalParam($params, "time_offset", $timeOffset);

		$result = $this->hit("updateentrythumbnail", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateEntryThumbnailJpeg(KalturaSessionUser $kalturaSessionUser, $entryId)
	{
		$params = array();
		$params["entry_id"] = $entryId;

		$result = $this->hit("updateentrythumbnailjpeg", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateKShow(KalturaSessionUser $kalturaSessionUser, $kshowId, KalturaKShow $kshow, $detailed = null, $allowDuplicateNames = null)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "kshow_name", $kshow->name);
		$this->addOptionalParam($params, "kshow_description", $kshow->description);
		$this->addOptionalParam($params, "kshow_tags", $kshow->tags);
		$this->addOptionalParam($params, "kshow_indexedCustomData3", $kshow->indexedCustomData3);
		$this->addOptionalParam($params, "kshow_groupId", $kshow->groupId);
		$this->addOptionalParam($params, "kshow_permissions", $kshow->permissions);
		$this->addOptionalParam($params, "kshow_partnerData", $kshow->partnerData);
		$this->addOptionalParam($params, "kshow_allowQuickEdit", $kshow->allowQuickEdit);
		$this->addOptionalParam($params, "detailed", $detailed);
		$this->addOptionalParam($params, "allow_duplicate_names", $allowDuplicateNames);

		$result = $this->hit("updatekshow", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateKshowOwner(KalturaSessionUser $kalturaSessionUser, $kshowId, $detailed = null)
	{
		$params = array();
		$params["kshow_id"] = $kshowId;
		$this->addOptionalParam($params, "detailed", $detailed);

		$result = $this->hit("updatekshowowner", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateNotification(KalturaSessionUser $kalturaSessionUser, KalturaNotification $notification)
	{
		$params = array();
		$this->addOptionalParam($params, "notification_id", $notification->id);
		$this->addOptionalParam($params, "notification_status", $notification->status);
		$this->addOptionalParam($params, "notification_notificationResult", $notification->notificationResult);

		$result = $this->hit("updatenotification", $kalturaSessionUser, $params);
		return $result;
	}

	public function updatePartner(KalturaSessionUser $kalturaSessionUser, KalturaPartner $partner)
	{
		$params = array();
		$this->addOptionalParam($params, "partner_name", $partner->name);
		$this->addOptionalParam($params, "partner_url1", $partner->url1);
		$this->addOptionalParam($params, "partner_url2", $partner->url2);
		$this->addOptionalParam($params, "partner_appearInSearch", $partner->appearInSearch);
		$this->addOptionalParam($params, "partner_adminName", $partner->adminName);
		$this->addOptionalParam($params, "partner_adminEmail", $partner->adminEmail);
		$this->addOptionalParam($params, "partner_description", $partner->description);
		$this->addOptionalParam($params, "partner_commercialUse", $partner->commercialUse);
		$this->addOptionalParam($params, "partner_landingPage", $partner->landingPage);
		$this->addOptionalParam($params, "partner_userLandingPage", $partner->userLandingPage);
		$this->addOptionalParam($params, "partner_notificationsConfig", $partner->notificationsConfig);
		$this->addOptionalParam($params, "partner_notify", $partner->notify);
		$this->addOptionalParam($params, "partner_allowMultiNotification", $partner->allowMultiNotification);
		$this->addOptionalParam($params, "partner_contentCategories", $partner->contentCategories);
		$this->addOptionalParam($params, "partner_type", $partner->type);

		$result = $this->hit("updatepartner", $kalturaSessionUser, $params);
		return $result;
	}

	public function updatePlaylist(KalturaSessionUser $kalturaSessionUser, $entryId, KalturaEntry $entry)
	{
		$params = array();
		$params["entry_id"] = $entryId;
		$this->addOptionalParam($params, "entry_name", $entry->name);
		$this->addOptionalParam($params, "entry_tags", $entry->tags);
		$this->addOptionalParam($params, "entry_type", $entry->type);
		$this->addOptionalParam($params, "entry_mediaType", $entry->mediaType);
		$this->addOptionalParam($params, "entry_source", $entry->source);
		$this->addOptionalParam($params, "entry_sourceId", $entry->sourceId);
		$this->addOptionalParam($params, "entry_sourceLink", $entry->sourceLink);
		$this->addOptionalParam($params, "entry_licenseType", $entry->licenseType);
		$this->addOptionalParam($params, "entry_credit", $entry->credit);
		$this->addOptionalParam($params, "entry_groupId", $entry->groupId);
		$this->addOptionalParam($params, "entry_partnerData", $entry->partnerData);
		$this->addOptionalParam($params, "entry_conversionQuality", $entry->conversionQuality);
		$this->addOptionalParam($params, "entry_permissions", $entry->permissions);
		$this->addOptionalParam($params, "entry_dataContent", $entry->dataContent);
		$this->addOptionalParam($params, "entry_desiredVersion", $entry->desiredVersion);
		$this->addOptionalParam($params, "entry_url", $entry->url);
		$this->addOptionalParam($params, "entry_thumbUrl", $entry->thumbUrl);
		$this->addOptionalParam($params, "entry_filename", $entry->filename);
		$this->addOptionalParam($params, "entry_realFilename", $entry->realFilename);
		$this->addOptionalParam($params, "entry_indexedCustomData1", $entry->indexedCustomData1);
		$this->addOptionalParam($params, "entry_thumbOffset", $entry->thumbOffset);
		$this->addOptionalParam($params, "entry_mediaId", $entry->mediaId);
		$this->addOptionalParam($params, "entry_screenName", $entry->screenName);
		$this->addOptionalParam($params, "entry_siteUrl", $entry->siteUrl);
		$this->addOptionalParam($params, "entry_description", $entry->description);
		$this->addOptionalParam($params, "entry_mediaDate", $entry->mediaDate);
		$this->addOptionalParam($params, "entry_adminTags", $entry->adminTags);

		$result = $this->hit("updateplaylist", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateUiconf(KalturaSessionUser $kalturaSessionUser, $uiconfId, KalturaUiConf $uiconf)
	{
		$params = array();
		$params["uiconf_id"] = $uiconfId;
		$this->addOptionalParam($params, "uiconf_name", $uiconf->name);
		$this->addOptionalParam($params, "uiconf_objType", $uiconf->objType);
		$this->addOptionalParam($params, "uiconf_width", $uiconf->width);
		$this->addOptionalParam($params, "uiconf_height", $uiconf->height);
		$this->addOptionalParam($params, "uiconf_htmlParams", $uiconf->htmlParams);
		$this->addOptionalParam($params, "uiconf_swfUrl", $uiconf->swfUrl);
		$this->addOptionalParam($params, "uiconf_swfUrlVersion", $uiconf->swfUrlVersion);
		$this->addOptionalParam($params, "uiconf_confFile", $uiconf->confFile);
		$this->addOptionalParam($params, "uiconf_confVars", $uiconf->confVars);
		$this->addOptionalParam($params, "uiconf_useCdn", $uiconf->useCdn);
		$this->addOptionalParam($params, "uiconf_tags", $uiconf->tags);

		$result = $this->hit("updateuiconf", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateUser(KalturaSessionUser $kalturaSessionUser, $userId, KalturaUser $user)
	{
		$params = array();
		$params["user_id"] = $userId;
		$this->addOptionalParam($params, "user_screenName", $user->screenName);
		$this->addOptionalParam($params, "user_fullName", $user->fullName);
		$this->addOptionalParam($params, "user_email", $user->email);
		$this->addOptionalParam($params, "user_dateOfBirth", $user->dateOfBirth);
		$this->addOptionalParam($params, "user_aboutMe", $user->aboutMe);
		$this->addOptionalParam($params, "user_tags", $user->tags);
		$this->addOptionalParam($params, "user_gender", $user->gender);
		$this->addOptionalParam($params, "user_country", $user->country);
		$this->addOptionalParam($params, "user_state", $user->state);
		$this->addOptionalParam($params, "user_city", $user->city);
		$this->addOptionalParam($params, "user_zip", $user->zip);
		$this->addOptionalParam($params, "user_urlList", $user->urlList);
		$this->addOptionalParam($params, "user_networkHighschool", $user->networkHighschool);
		$this->addOptionalParam($params, "user_networkCollege", $user->networkCollege);
		$this->addOptionalParam($params, "user_partnerData", $user->partnerData);

		$result = $this->hit("updateuser", $kalturaSessionUser, $params);
		return $result;
	}

	public function updateUserId(KalturaSessionUser $kalturaSessionUser, $userId, $newUserId)
	{
		$params = array();
		$params["user_id"] = $userId;
		$params["new_user_id"] = $newUserId;

		$result = $this->hit("updateuserid", $kalturaSessionUser, $params);
		return $result;
	}

	public function upload(KalturaSessionUser $kalturaSessionUser, $filename)
	{
		$params = array();
		$params["filename"] = $filename;

		$result = $this->hit("upload", $kalturaSessionUser, $params);
		return $result;
	}

	public function uploadJpeg(KalturaSessionUser $kalturaSessionUser, $filename, $hash)
	{
		$params = array();
		$params["filename"] = $filename;
		$params["hash"] = $hash;

		$result = $this->hit("uploadjpeg", $kalturaSessionUser, $params);
		return $result;
	}

	public function viewWidget(KalturaSessionUser $kalturaSessionUser, $entryId = null, $kshowId = null, $widgetId = null, $host = null)
	{
		$params = array();
		$this->addOptionalParam($params, "entry_id", $entryId);
		$this->addOptionalParam($params, "kshow_id", $kshowId);
		$this->addOptionalParam($params, "widget_id", $widgetId);
		$this->addOptionalParam($params, "host", $host);

		$result = $this->hit("viewwidget", $kalturaSessionUser, $params);
		return $result;
	}

}
