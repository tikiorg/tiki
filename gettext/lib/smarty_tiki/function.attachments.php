<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Handle special actions of the smarty_function_attachments smarty plugin
function s_f_attachments_actionshandler( $params ) {
	global $prefs, $user, $tikilib;
	if ( $prefs['feature_wiki_attachments'] != 'y' ) return false;

	/*** Works only for wiki attachments yet ***/
	if ( ! empty( $params['upload'] ) && empty( $params['fileId'] ) && empty( $params['page'] ) ) return false; ///FIXME

	if ( ! empty( $params['page'] ) ) {
		require_once ("lib/wiki/renderlib.php");
		$info =& $tikilib->get_page_info( $params['page'] );
		$pageRenderer = new WikiRenderer( $info, $user, $info['data']);
		$objectperms = $pageRenderer->applyPermissions();
	}

	global $filegallib; include_once('lib/filegals/filegallib.php');

	foreach ( $params as $k => $v ) {
		switch ( $k ) {

			case 'remove':
				/* FIXME
					check_ticket('index');
					$owner = $wikilib->get_attachment_owner($_REQUEST['removeattach']);
					if( ($user && ($owner == $user) ) || $objectperms->wiki_admin_attachments ) {
						$access->check_authenticity();
						$wikilib->remove_wiki_attachment($_REQUEST['removeattach']);
					}
					$pageRenderer->setShowAttachments( 'y' );
				*/
				$filegallib->actionHandler( 'removeFile', array( 'fileId' => $v ) );
				break;

			case 'upload':
				if ( isset( $objectperms ) && ( $objectperms->wiki_admin_attachments || $objectperms->wiki_attach_files ) ) {
					/* check_ticket('index'); */

					global $smarty;
					require_once $smarty->_get_plugin_filepath('function', 'query');

					$filegallib->actionHandler( 'uploadFile', array(
						'galleryId' => $filegallib->get_attachment_gallery( $params['page'], 'wiki page' ),
						'comment' => $params['comment'],
						'returnUrl' => smarty_function_query( array(
								'_type' => 'absolute_path',
								's_f_attachments-upload' => 'NULL',
								's_f_attachments-page' => 'NULL',
								's_f_attachments-comment' => 'NULL'
							), $smarty )
					) );
				}

				break;
		}
	}

	return true;
}

/*
 * smarty_function_attachments: Display the list of files attached to a wiki page
 *
 * params will be used as smarty params for browse_file_gallery.tpl, except special params starting with '_' :
 *   _id : id of the object (for a wiki page, use it's name)
 *   _type : type of the object ( e.g. "wiki page" - see objectTypes in lib/setup/sections.php )
 */
function smarty_function_attachments($params, &$smarty) {
	if ( ! is_array($params) || ! isset($params['_id']) || ! isset($params['_type']) ) return;

	global $prefs, $tikilib, $userlib;
	global $filegallib; include_once('lib/filegals/filegallib.php');

	/*** For the moment, only wiki attachments are handled through file galleries ***/
	if ( $prefs['feature_wiki_attachments'] != 'y' ) return;

	$galleryId = $filegallib->get_attachment_gallery( $params['_id'], $params['_type'] );

	/*** If anything in this function is changed, please change lib/wiki-plugins/wikiplugin_attach.php as well. ***/

	if ( empty( $galleryId ) || ! $gal_info = $filegallib->get_file_gallery( $galleryId ) ) {
		include_once('lib/smarty_tiki/block.remarksbox.php');
		return smarty_block_remarksbox( array( 'type' => 'errors', 'title' => tra('Wrong attachments gallery')),
			tra('You are attempting to display a gallery that is not a valid attachment gallery') . ' (ID=' . $galleryId . ')',
		$smarty)."\n";
	}

////	if( $this->showAttachments !== false )
////		$this->smartyassign('atts_show', $this->showAttachments);

	foreach ( $params as $k => $v ) {
		if ( $k[0] == '_' ) {
			unset( $params[ $k ] );
		}
	}

	// Get URL params specific to this smarty function that should be assigned in smarty
	$url_override_prefix = 's_f_attachments';
	$url_overrided_arguments = array( 'sort_mode', 'remove', 'galleryId', 'comment', 'upload', 'page' );
	$smarty->set_request_overriders($url_override_prefix, $url_overrided_arguments);

	$params['sort_mode'] = isset( $_REQUEST[ $url_override_prefix . '-sort_mode' ] ) ? $_REQUEST[ $url_override_prefix . '-sort_mode' ] : '';

	// Get listing display config
	include_once('fgal_listing_conf.php');

	// Force some gallery display parameters
	$gal_info['show_checked'] = 'n';

	// Get list of files in the gallery
	$files = $filegallib->get_files(0, -1, $params['sort_mode'], '', $galleryId);

	// Reajust perms using special wiki attachments perms
	global $tiki_p_wiki_admin_attachments, $tiki_p_wiki_attach_files, $tiki_p_wiki_view_attachments;

	foreach ( $files[ 'data' ] as $k => $v ) {
		$p =& $files[ 'data' ][ $k ][ 'perms' ];

		// First disable file galleries "assign perms" & "admin" perms that allows too much actions on the list of files or that are related to subgalleries
		//   (attachements display should be simple)
		$p[ 'tiki_p_admin_file_galleries' ] = 'n';
		$p[ 'tiki_p_assign_perm_file_gallery' ] = 'n';

		// Disabling permissions below should not be necessary because subgalleries in attachments galleries should not happen...
		// $p[ 'tiki_p_upload_files' ] = 'n';
		// $p[ 'tiki_p_create_file_galleries' ] = 'n';

		$p[ 'tiki_p_download_files' ] = ( $tiki_p_wiki_admin_attachments == 'y' || $tiki_p_wiki_view_attachments == 'y' ) ? 'y' : 'n';
		$p[ 'tiki_p_edit_gallery_file' ] = $tiki_p_wiki_admin_attachments;
	}

	$params['gal_info'] = $gal_info;
	$params['files'] = $files['data'];
	$params['cant'] = $files['cant'];

	$return = "\n" . $smarty->plugin_fetch('fgal_attachments.tpl', $params) . "\n";

	$smarty->remove_request_overriders($url_override_prefix, $url_overrided_arguments);
	return $return;
}
