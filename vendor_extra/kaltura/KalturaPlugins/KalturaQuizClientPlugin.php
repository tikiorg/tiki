<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2016  Kaltura Inc.
//
// This file has been included in the Tiki distribution with special permission 
// from Kaltura Inc. for the convenience of Tiki users. It is not LGPL licensed. 
// Please obtain your own copy from http://kaltura.org if you need it for any other purpose.
//
// @ignore
// ===================================================================================================

/**
 * @package Kaltura
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");
require_once(dirname(__FILE__) . "/KalturaCuePointClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnswerCuePointOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const START_TIME_ASC = "+startTime";
	const TRIGGERED_AT_ASC = "+triggeredAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const START_TIME_DESC = "-startTime";
	const TRIGGERED_AT_DESC = "-triggeredAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuestionCuePointOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const START_TIME_ASC = "+startTime";
	const TRIGGERED_AT_ASC = "+triggeredAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const START_TIME_DESC = "-startTime";
	const TRIGGERED_AT_DESC = "-triggeredAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaOptionalAnswer extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $key = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $text = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $weight = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isCorrect = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuiz extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $version = null;

	/**
	 * Array of key value ui related objects
	 * 	 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $uiAttributes;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $showResultOnAnswer = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $showCorrectKeyOnAnswer = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $allowAnswerUpdate = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $showCorrectAfterSubmission = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $allowDownload = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $showGradeAfterSubmission = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnswerCuePoint extends KalturaCuePoint
{
	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $parentId = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $quizUserEntryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $answerKey = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $isCorrect = null;

	/**
	 * Array of string
	 * 	 
	 *
	 * @var array of KalturaString
	 * @readonly
	 */
	public $correctAnswerKeys;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $explanation = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuestionCuePoint extends KalturaCuePoint
{
	/**
	 * Array of key value answerKey->optionAnswer objects
	 * 	 
	 *
	 * @var map
	 */
	public $optionalAnswers;

	/**
	 * 
	 *
	 * @var string
	 */
	public $hint = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $question = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $explanation = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuizAdvancedFilter extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isQuiz = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuizListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaQuiz
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuizFilter extends KalturaRelatedFilter
{
	/**
	 * This filter should be in use for retrieving only a specific quiz entry (identified by its entryId).
	 * 	 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * This filter should be in use for retrieving few specific quiz entries (string should include comma separated list of entryId strings).
	 * 	 
	 *
	 * @var string
	 */
	public $entryIdIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAnswerCuePointBaseFilter extends KalturaCuePointFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $parentIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $quizUserEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $quizUserEntryIdIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaQuestionCuePointBaseFilter extends KalturaCuePointFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $questionLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $questionMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $questionMultiLikeAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnswerCuePointFilter extends KalturaAnswerCuePointBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuestionCuePointFilter extends KalturaQuestionCuePointBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuizService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Allows to add a quiz to an entry
	 * 
	 * @param string $entryId 
	 * @param KalturaQuiz $quiz 
	 * @return KalturaQuiz
	 */
	function add($entryId, KalturaQuiz $quiz)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "quiz", $quiz->toParams());
		$this->client->queueServiceActionCall("quiz_quiz", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaQuiz");
		return $resultObject;
	}

	/**
	 * Allows to update a quiz
	 * 
	 * @param string $entryId 
	 * @param KalturaQuiz $quiz 
	 * @return KalturaQuiz
	 */
	function update($entryId, KalturaQuiz $quiz)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "quiz", $quiz->toParams());
		$this->client->queueServiceActionCall("quiz_quiz", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaQuiz");
		return $resultObject;
	}

	/**
	 * Allows to get a quiz
	 * 
	 * @param string $entryId 
	 * @return KalturaQuiz
	 */
	function get($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("quiz_quiz", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaQuiz");
		return $resultObject;
	}

	/**
	 * List quiz objects by filter and pager
	 * 
	 * @param KalturaQuizFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaQuizListResponse
	 */
	function listAction(KalturaQuizFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("quiz_quiz", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaQuizListResponse");
		return $resultObject;
	}

	/**
	 * Creates a pdf from quiz object
	 The Output type defines the file format in which the quiz will be generated
	 Currently only PDF files are supported
	 * 
	 * @param string $entryId 
	 * @param int $quizOutputType 
	 * @return file
	 */
	function serve($entryId, $quizOutputType)
	{
		if ($this->client->isMultiRequest())
			throw new KalturaClientException("Action is not supported as part of multi-request.", KalturaClientException::ERROR_ACTION_IN_MULTIREQUEST);
		
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "quizOutputType", $quizOutputType);
		$this->client->queueServiceActionCall("quiz_quiz", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Sends a with an api request for pdf from quiz object
	 * 
	 * @param string $entryId 
	 * @param int $quizOutputType 
	 * @return string
	 */
	function getUrl($entryId, $quizOutputType)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "quizOutputType", $quizOutputType);
		$this->client->queueServiceActionCall("quiz_quiz", "getUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuizClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaQuizService
	 */
	public $quiz = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->quiz = new KalturaQuizService($client);
	}

	/**
	 * @return KalturaQuizClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaQuizClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'quiz' => $this->quiz,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'quiz';
	}
}

