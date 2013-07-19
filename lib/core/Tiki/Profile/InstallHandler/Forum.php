<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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

	function _install()
	{
		$comments = TikiLib::lib('comments');

		$data = $this->getData();
		$this->replaceReferences($data);

		$attConverter = new Tiki_Profile_ValueMapConverter(
			array(
				'none' => 'att_no',
				'everyone' => 'att_all',
				'allowed' => 'att_perm',
				'admin' => 'att_admin',
			)
		);

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
}
