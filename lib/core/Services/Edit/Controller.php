<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class Services_Edit_Controller
 *
 * Controller for various editing based services, wiki/html conversion, preview, inline editing etc
 *
 */
class Services_Edit_Controller
{

	function setUp()
	{
		Services_Exception_Disabled::check('feature_wiki');
	}


	function action_towiki($input)
	{
		$res = TikiLib::lib('edit')->parseToWiki($input->data->none());

		return array(
			'data' => $res,
		);
	}

	function action_tohtml($input)
	{
		$res = TikiLib::lib('edit')->parseToWysiwyg($input->data->none(), false, $input->allowhtml->int() ? true : false);

		return array(
			'data' => $res,
		);
	}

	function action_inlinesave($input)
	{
		global $user;

		$pageName = $input->page->text();
		$info = TikiLib::lib('tiki')->get_page_info($pageName);
		$data = $input->data->none();

		// Check if HTML format is allowed
		if ($info['is_html']) {
			// Save as HTML
			$edit_data = TikiLib::lib('edit')->partialParseWysiwygToWiki($data);
			$is_html= '1';
		} else {
			// Convert HTML to wiki and save as wiki
			$edit_data = TikiLib::lib('edit')->parseToWiki($data);
			$is_html= null;
		}

		$edit_comment = tra('Inline editor update');
		$res = TikiLib::lib('tiki')->update_page($pageName, $edit_data, $edit_comment, $user, $_SERVER['REMOTE_ADDR']);

		return array(
			'data' => $res,
		);
	}

	function action_preview($input)
	{

		Services_Exception_Disabled::check('feature_warn_on_edit');

		global $user, $prefs, $page;
		$tikilib = TikiLib::lib('tiki');

		$autoSaveIdParts = explode(':', $input->autoSaveId->text());	// user, section, object id
		foreach ($autoSaveIdParts as & $part) {
			$part = urldecode($part);
		}

		$page = $autoSaveIdParts[2];	// plugins use global $page for approval

		if (!Perms::get('wiki page', $page)->edit || $user != TikiLib::lib('service')->internal('semaphore', 'get_user', ['object_id' => $page, 'check' => 1])) {
			return '';
		}

		$info = $tikilib->get_page_info($page, false);
		if (empty($info)) {
			$info = array(		// new page
				'data' => '',
			);
		}

		$info['is_html'] = $input->allowHtml->int();

		if (!isset($info['wysiwyg']) && isset($_SESSION['wysiwyg'])) {
			$info['wysiwyg'] = $_SESSION['wysiwyg'];
		}
		$options = array(
			'is_html' => $info['is_html'],
			'preview_mode' => true,
			'process_wiki_paragraphs' => ($prefs['wysiwyg_htmltowiki'] === 'y' || $info['wysiwyg'] == 'n'),
			'page' => $page,
		);

		if (count($autoSaveIdParts) === 3 && !empty($user) && $user === $autoSaveIdParts[0] && $autoSaveIdParts[1] === 'wiki_page') {

			$editlib = TikiLib::lib('edit');
			$smarty = TikiLib::lib('smarty');
			$wikilib = TikiLib::lib('wiki');

			$smarty->assign('inPage', $input->inPage->int() ? true : false);

			$parserlib = TikiLib::lib('parser');
			if ($input->inPage->int()) {
				$diffstyle = $input->diff_style->text();
				if (!$diffstyle) {	// use previously set diff_style
					$diffstyle = getCookie('preview_diff_style', 'preview', '');
				}
				$data = $editlib->partialParseWysiwygToWiki(
					TikiLib::lib('autosave')->get_autosave($input->editor_id->text(), $input->autoSaveId->text())
				);
				TikiLib::lib('smarty')->assign('diff_style', $diffstyle);
				if ($diffstyle) {
					if (!empty($info['created'])) {
						$info = $tikilib->get_page_info($page); // get page with data this time
					}
					if ($input->hdr->int()) {		// TODO refactor with code in editpage
						if ($input->hdr->int() === 0) {
							list($real_start, $real_len) = $tikilib->get_wiki_section($info['data'], 1);
							$real_len = $real_start;
							$real_start = 0;
						} else {
							list($real_start, $real_len) = $tikilib->get_wiki_section($info['data'], $input->hdr->int());
						}
						$info['data'] = substr($info['data'], $real_start, $real_len);
					}
					require_once('lib/diff/difflib.php');
					if ($info['is_html'] == 1) {
						$diffold = $tikilib->htmldecode($info['data']);
					} else {
						$diffold = $info['data'];
					}
					if ($info['is_html']) {
						$diffnew = $tikilib->htmldecode($data);
					} else {
						$diffnew = $data;
					}
					if ($diffstyle === 'htmldiff') {
						$diffnew = $parserlib->parse_data($diffnew, $options);
						$diffold = $parserlib->parse_data($diffold, $options);
					}
					$data = diff2($diffold, $diffnew, $diffstyle);
					$smarty->assign_by_ref('diffdata', $data);

					$smarty->assign('translation_mode', 'y');	// disables the headings etc
					$smarty->assign('show_version_info', 'n');	// disables the headings etc
					$data = $smarty->fetch('pagehistory.tpl');
				} else {
					$data = $parserlib->parse_data($data, $options);
				}
				$parsed = $data;

			} else {					// popup window
				TikiLib::lib('header')->add_js(
					'
function get_new_preview() {
	$("body").css("opacity", 0.6);
	location.reload(true);
}
$(window).on("load", function(){
	if (typeof opener != "undefined") {
		opener.ajaxPreviewWindow = this;
	}
}).on("unload", function(){
	if (typeof opener.ajaxPreviewWindow != "undefined") {
		opener.ajaxPreviewWindow = null;
	}
});
'
				);
				$smarty->assign('headtitle', tra('Preview'));
				$data = '<div class="container"><div class="row row-middle"><div class="col-sm-12"><div class="wikitext">';
				if (TikiLib::lib('autosave')->has_autosave($input->editor_id->text(), $input->autoSaveId->text())) {
					$data .= $parserlib->parse_data(
						$editlib->partialParseWysiwygToWiki(
							TikiLib::lib('autosave')->get_autosave($input->editor_id->text(), $input->autoSaveId->text())
						), $options
					);
				} else {
					if ($autoSaveIdParts[1] == 'wiki_page') {
						$canBeRefreshed = false;
						$data .= $wikilib->get_parse($autoSaveIdParts[2], $canBeRefreshed);
					}
				}
				$data .= '</div></div></div></div>';
				$smarty->assign_by_ref('mid_data', $data);
				$smarty->assign('mid', '');
				$parsed = $smarty->fetch("tiki_full.tpl");

				$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';	// to fool Services_Broker into putputting full page
			}

			if ($prefs['feature_wiki_footnotes'] === 'y') {

				$footnote = $input->footnote->text();
				if ($footnote) {
					$footnote = $parserlib->parse_data($footnote);
				} else {
					$footnote = $wikilib->get_footnote($user, $page);
				}
			}

			return array('parsed' => $parsed, 'parsed_footnote' => $footnote);
		}
	}

    public static function page_editable($autoSaveId = null, &$page = null)
    {
        global $user;
        $tikilib = TikiLib::lib('tiki');

        if ($autoSaveId !== null) {
            $autoSaveIdParts = explode(':', $autoSaveId);	// user, section, object id
            foreach ($autoSaveIdParts as & $part) {
                $part = urldecode($part);
            }

            $page = $autoSaveIdParts[2];	// plugins use global $page for approval
        }

        if (!Perms::get('wiki page', $page)->edit || $user != TikiLib::lib('service')->internal('semaphore', 'get_user', ['object_id' => $page, 'check' => 1])
		) {
            return false;
        }

        return true;
    }

	public function action_help($input)
	{
		$smarty = TikiLib::lib('smarty');

		$help_sections = [];

		if ($input->wiki->int()) {
			$help_sections[] = [
				'id' => 'wiki-help',
				'title' => tr('Syntax Help'),
				'content' => $smarty->fetch('tiki-edit_help.tpl'),
			];
		}

		if ($input->wysiwyg->int()) {
			$help_sections[] = [
				'id' => 'wysiwyg-help',
				'title' => tr('WYSIWYG Help'),
				'content' => $smarty->fetch('tiki-edit_help_wysiwyg.tpl'),
			];
		}

		if ($input->plugins->int()) {
			$areaId = $input->areaId->word();
			$wikilib = TikiLib::lib('wiki');
			$plugins = $wikilib->list_plugins(true, $areaId);

			$smarty->assign('plugins', $plugins);
			$help_sections[] = [
				'id' => 'plugin-help',
				'title' => tr('Plugin Help'),
				'content' => $smarty->fetch('tiki-edit_help_plugins.tpl'),
			];
		}

		if ($input->sheet->int()) {
			$help_sections[] = [
				'id' => 'sheet-help',
				'title' => tr('Spreadsheet Help'),
				'content' => $smarty->fetch('tiki-edit_help_sheet.tpl'),
			];
		}

		return array(
			'title' => tr('Edit Help'),
			'help_sections' => $help_sections,
		);
	}

	function action_inline_dialog($input)
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_service_inline');

		$display = [];
		foreach ($input->fields as $field) {
			$html = smarty_function_service_inline($field->fetch->text(), $smarty);
			$display[] = [
				'label' => $field->label->text(),
				'field' => new Tiki_Render_Editable($html, [
					'layout' => 'dialog',
					'object_store_url' => $field->store->text(),
				]),
			];
		}

		return [
			'title' => tr('Edit'),
			'fields' => $display,
		];
	}
}
