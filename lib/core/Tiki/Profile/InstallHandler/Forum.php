<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Forum extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$defaults = array(
			'description' => '',
			'flood_interval' => 120,
			'moderator' => 'admin',
			'per_page' => 10,
			'prune_max_age' => 3*24*3600,
			'prune_unreplied_max_age' => 30*24*3600,
			'topic_order' => 'lastPost_desc',
			'thread_order' => '',
			'section' => '',
			'inbound_pop_server' => '',
			'inbound_pop_port' => 110,
			'inbound_pop_user' => '',
			'inbound_pop_password' => '',
			'outbound_address' => '',
			'outbound_from' => '',
			'approval_type' => 'all_posted',
			'moderator_group' => '',
			'forum_password' => '',
			'attachments' => 'none',
			'attachments_store' => 'db',
			'attachments_store_dir' => '',
			'attachments_max_size' => 10000000,
			'forum_last_n' => 0,
			'comments_per_page' => '',
			'thread_style' => '',
			'is_flat' => 'n',

			'list_topic_reads' => 'n',
			'list_topic_replies' => 'n',
			'list_topic_points' => 'n',
			'list_topic_last_post' => 'n',
			'list_topic_last_post_title' => 'n',
			'list_topic_last_post_avatar' => 'n',
			'list_topic_author' => 'n',
			'list_topic_author_avatar' => 'n',

			'show_description' => 'n',

			'enable_flood_control' => 'n',
			'enable_inbound_mail' => 'n',
			'enable_prune_unreplied' => 'n',
			'enable_prune_old' => 'n',
			'enable_vote_threads' => 'n',
			'enable_outbound_for_inbound' => 'n',
			'enable_outbound_reply_link' => 'n',
			'enable_topic_smiley' => 'n',
			'enable_topic_summary' => 'n',
			'enable_ui_avatar' => 'n',
			'enable_ui_rating_choice_topic' => 'n',
			'enable_ui_flag' => 'n',
			'enable_ui_posts' => 'n',
			'enable_ui_level' => 'n',
			'enable_ui_email' => 'n',
			'enable_ui_online' => 'n',
			'enable_password_protection' => 'n',
			'forum_language' => '',
		);

		$data = Tiki_Profile::convertLists($data, array('enable' => 'y', 'list' => 'y',	'show' => 'y'), true);

		$data = array_merge($defaults, $data);

		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['name'] ) )
			return false;

		return true;
	}

	private static function getAttConverter()
	{
		return new Tiki_Profile_ValueMapConverter(
			array(
				'none' => 'att_no',
				'everyone' => 'att_all',
				'allowed' => 'att_perm',
				'admin' => 'att_admin',
			)
		);
	}

	function _install()
	{
		$comments = TikiLib::lib('comments');

		$data = $this->getData();
		$this->replaceReferences($data);

		$attConverter = self::getAttConverter();

		$id = $comments->replace_forum(
			0,
			$data['name'],
			$data['description'],
			$data['enable_flood_control'],
			$data['flood_interval'],
			$data['moderator'],
			$data['mail'],
			$data['enable_inbound_mail'],
			$data['enable_prune_unreplied'],
			$data['prune_unreplied_max_age'],
			$data['enable_prune_old'],
			$data['prune_max_age'],
			$data['per_page'],
			$data['topic_order'],
			$data['thread_order'],
			$data['section'],
			$data['list_topic_reads'],
			$data['list_topic_replies'],
			$data['list_topic_points'],
			$data['list_topic_last_post'],
			$data['list_topic_author'],
			$data['enable_vote_threads'],
			$data['show_description'],
			$data['inbound_pop_server'],
			$data['inbound_pop_port'],
			$data['inbound_pop_user'],
			$data['inbound_pop_password'],
			$data['outbound_address'],
			$data['enable_outbound_for_inbound'],
			$data['enable_outbound_reply_link'],
			$data['outbound_from'],
			$data['enable_topic_smiley'],
			$data['enable_topic_summary'],
			$data['enable_ui_avatar'],
			$data['enable_ui_rating_choice_topic'],
			$data['enable_ui_flag'],
			$data['enable_ui_posts'],
			$data['enable_ui_level'],
			$data['enable_ui_email'],
			$data['enable_ui_online'],
			$data['approval_type'],
			$data['moderator_group'],
			$data['forum_password'],
			$data['enable_password_protection'],
			$attConverter->convert($data['attachments']),
			$data['attachments_store'],
			$data['attachments_store_dir'],
			$data['attachments_max_size'],
			$data['forum_last_n'],
			$data['comments_per_page'],
			$data['thread_style'],
			$data['is_flat'],
			$data['list_att_nb'],
			$data['list_topic_last_post_title'],
			$data['list_topic_last_post_avatar'],
			$data['list_topic_author_avatar'],
			$data['forum_language']
		);

		return $id;
	}

	public static function export(Tiki_Profile_Writer $writer, $forumId)
	{
		$forumlib = TikiLib::lib('comments');
		if (! $info = $forumlib->get_forum($forumId)) {
			return false;
		}

		$writer->addObject(
			'forum',
			$forumId,
			array(
				'name' => $info['name'],
				'description' => $info['description'],
				'enable_flood_control' => $info['controlFlood'],
				'flood_interval' => $info['floodInterval'],
				'moderator' => $info['moderator'],
				'mail' => $info['mail'],
				'enable_inbound_mail' => $info['useMail'],
				'section' => $info['section'],
				'enable_prune_unreplied' => $info['usePruneUnreplied'],
				'prune_unreplied_max_age' => $info['pruneUnrepliedAge'],
				'enable_prune_old' => $info['usePruneOld'],
				'prune_max_age' => $info['pruneMaxAge'],
				'per_page' => $info['topicsPerPage'],
				'topic_order' => $info['topicOrdering'],
				'thread_order' => $info['threadOrdering'],
				'attachments' => self::getAttConverter()->reverse($info['att']),
				'attachments_store' => $info['att_store'],
				'attachments_store_dir' => $info['att_store_dir'],
				'attachments_max_size' => $info['att_max_size'],
				'list_att_nb' => $info['att_list_nb'],
				'enable_ui_level' => $info['ui_level'],
				'enable_password_protection' => $info['forum_use_password'],
				'forum_password' => $info['forum_password'],
				'moderator_group' => $info['moderator_group'],
				'approval_type' => $info['approval_type'],
				'outbound_address' => $info['outbound_address'],
				'enable_outbound_for_inbound' => $info['outbound_mails_for_inbound_mails'],
				'enable_outbound_mail_reply_link' => $info['outbound_mails_reply_link'],
				'outbound_from' => $info['outbound_from'],
				'inbound_pop_server' => $info['inbound_pop_server'],
				'inbound_pop_port' => $info['inbound_pop_port'],
				'inbound_pop_user' => $info['inbound_pop_user'],
				'inbound_pop_password' => $info['inbound_pop_password'],
				'enable_topic_smiley' => $info['topic_smileys'],
				'enable_ui_avatar' => $info['ui_avatar'],
				'enable_ui_rating_choice_topic' => $info['ui_rating_choice_topic'],
				'enable_ui_flag' => $info['ui_flag'],
				'enable_ui_posts' => $info['ui_posts'],
				'enable_ui_email' => $info['ui_email'],
				'enable_ui_online' => $info['ui_online'],
				'enable_topic_summary' => $info['topic_summary'],
				'show_description' => $info['show_description'],
				'list_topic_replies' => $info['topics_list_replies'],
				'list_topic_reads' => $info['topics_list_reads'],
				'list_topic_points' => $info['topics_list_pts'],
				'list_topic_last_post' => $info['topics_list_lastpost'],
				'list_topic_last_post_title' => $info['topics_list_lastpost_title'],
				'list_topic_last_post_avatar' => $info['topics_list_lastpost_avatar'],
				'list_topic_author_avatar' => $info['topics_list_author_avatar'],
				'list_topic_author' => $info['topics_list_author'],
				'enable_vote_threads' => $info['vote_threads'],
				'forum_last_n' => $info['forum_last_n'],
				'thread_style' => $info['threadStyle'],
				'comments_per_page' => $info['commentsPerPage'],
				'is_flat' => $info['is_flat'],
			)
		);

		return true;
	}
}
