<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_ArticleType extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();
		$data = Tiki_Profile::convertLists($data, array('show' => 'y', 'allow' => 'y'), true);

		$defaults = array(
			'show_pre_publication' => 'n',
			'show_post_expire' => 'n',
			'show_heading_only' => 'n',
			'show_image' => 'n',
			'show_avatar' => 'n',
			'show_author' => 'n',
			'show_publication_date' => 'n',
			'show_expiration_date' => 'n',
			'show_reads' => 'n',
			'show_size' => 'n',
			'show_topline' => 'n',
			'show_subtitle' => 'n',
			'show_link_to' => 'n',
			'show_image_caption' => 'n',

			'allow_ratings' => 'n',
			'allow_comments' => 'n',
			'allow_comments_rating_article' => 'n',
			'allow_creator_edit' => 'n',
		);

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
		global $artlib;
		$data = $this->getData();

		$this->replaceReferences($data);

		require_once 'lib/articles/artlib.php';

		$converter = new Tiki_Profile_ValueMapConverter(array( 'y' => 'on' ));

		if ( ! $artlib->get_type($data['name']) ) {
			$artlib->add_type($data['name']);
		}
		
		$artlib->edit_type(
						$data['name'],
						$converter->convert($data['allow_ratings']),
						$converter->convert($data['show_pre_publication']),
						$converter->convert($data['show_post_expire']),
						$converter->convert($data['show_heading_only']),
						$converter->convert($data['allow_comments']),
						$converter->convert($data['allow_comments_rating_article']),
						$converter->convert($data['show_image']),
						$converter->convert($data['show_avatar']),
						$converter->convert($data['show_author']),
						$converter->convert($data['show_publication_date']),
						$converter->convert($data['show_expiration_date']),
						$converter->convert($data['show_reads']),
						$converter->convert($data['show_size']),
						$converter->convert($data['show_topline']),
						$converter->convert($data['show_subtitle']),
						$converter->convert($data['show_link_to']),
						$converter->convert($data['show_image_caption']),
						$converter->convert($data['allow_creator_edit'])
		);

		return $data['name'];
	}
}
