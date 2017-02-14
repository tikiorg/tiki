<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 59950 2016-10-11 09:18:39Z kroky6 $

/**
 * Class Services_Annotator_Controller
 *
 * Linking annotatorjs to inline comments
 */

const RANGE_ATTRIBUTE = 'tiki.comment.ranges';

class Services_Comment_AnnotationController
{
	/** @var  Comments */
	private $commentslib;
	/** @var  Services_Comment_Controller */
	private $commentController;


	function setUp()
	{
		Services_Exception_Disabled::check('feature_inline_comments');

		$this->commentslib = TikiLib::lib('comments');
		$this->commentController = new Services_Comment_Controller();
	}

	function action_create($input)
	{
		global $user;

		// annotatejs sends the params in the request payload by default, so we use option emulateJSON
		// but then need to decode the json string here
		$params = new jitFilter(json_decode($input->json->none(), true));

		$text = $params->text->text();
		$quote = $params->quote->text();
		$ranges = $params->ranges->asArray();
		$identifier = urldecode($params->uri->url());	// not really the uri but object-type:object-id identifier
		list($objectType, $objectId) = explode(':', $identifier, 2);

		if (! $this->commentController->canPost($objectType, $objectId)) {
			throw new Services_Exception_Denied;
		}

		$comment = ';note:' . $quote . "\n\n" . $text;
		$title = 'Untitled ' . TikiLib::lib('tiki')->get_long_datetime(TikiLib::lib('tikidate')->getTime());
		$messageId = '';

		// create the comment
		$threadId = $this->commentslib->post_new_comment(
			$identifier,
			0,
			$user,
			$title,
			$comment,
			$messageId
		);

		TikiLib::lib('attribute')->set_attribute(
			'comment',
			$threadId,
			RANGE_ATTRIBUTE,
			json_encode($ranges)
		);

		return [
			'id' => $threadId,
		];
	}

	function action_update($input)
	{

	}

	function action_destroy($input)
	{

	}

	function action_search($input)
	{
		$tikilib = TikiLib::lib('tiki');

		$limit = $input->limit->int();	// unused so far
		$offset = $input->offset->int();	// TODO pagination

		$identifier = urldecode($input->uri->url());
		$object = explode(':', $identifier);

		$list = $this->commentController->action_list(new jitFilter([
			'type' => $object[0],
			'objectId' => $object[1],
		]));

		$comments = [];

		foreach($list['comments'] as $comment) {
			if (strpos($comment['data'], ';note:') === 0) {			// only the "inline" ones starting ;note: so far
				$data = explode("\n", $comment['data'], 2);
				$quote = trim(substr($data[0], 6));
				$text = trim($data[1]);

				$ranges = json_decode(
					TikiLib::lib('attribute')->get_attribute(
						'comment',
						$comment['threadId'],
						RANGE_ATTRIBUTE
					),
					true
				);

				$comments[] = [
					'id' => $comment['threadId'],
					'text' => $text,
					'quote' => $quote,
					'created' => $tikilib->get_iso8601_datetime($comment['commentDate']),
					'updated' => $tikilib->get_iso8601_datetime($comment['commentDate']),	// we don't have a commentUpdated column?
					'ranges' => $ranges,

				];
			}
		};

		return [
			'total' => count($comments),	// TODO pagination
			'rows' => $comments,
		];
	}

}