<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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

		if (!Perms::get('wiki page', $page)->edit || $user != $tikilib->get_semaphore_user($page)) {
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
						$diffnew = $tikilib->parse_data($diffnew, $options);
						$diffold = $tikilib->parse_data($diffold, $options);
					}
					$data = diff2($diffold, $diffnew, $diffstyle);
					$smarty->assign_by_ref('diffdata', $data);

					$smarty->assign('translation_mode', 'y');
					$data = $smarty->fetch('pagehistory.tpl');
				} else {
					$data = $tikilib->parse_data($data, $options);
				}
				$parsed = $data;

			} else {					// popup window
				TikiLib::lib('header')->add_js(
					'
function get_new_preview() {
	$("body").css("opacity", 0.6);
	location.reload(true);
}
$(window).load(function(){
	if (typeof opener != "undefined") {
		opener.ajaxPreviewWindow = this;
	}
}).unload(function(){
	if (typeof opener.ajaxPreviewWindow != "undefined") {
		opener.ajaxPreviewWindow = null;
	}
});
'
				);
				$smarty->assign('headtitle', tra('Preview'));
				$data = '<div id="c1c2"><div id="wrapper"><div id="col1"><div id="tiki-center" class="wikitext">';
				if (TikiLib::lib('autosave')->has_autosave($input->editor_id->text(), $input->autoSaveId->text())) {
					$parserlib = TikiLib::lib('parser');
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

			if ($prefs['feature_wiki_footnotes']) {

				$footnote = $input->footnote->text();
				if ($footnote) {
					$footnote = $tikilib->parse_data($footnote);
				} else {
					$footnote = $wikilib->get_footnote($user, $page);
				}
			}

			return array('parsed' => $parsed, 'parsed_footnote' => $footnote);
		}
	}

    function action_wikiLingo(JitFilter $input)
    {
        global $user, $prefs, $page;
        $tikilib = TikiLib::lib('tiki');
        $globalPerms = Perms::get();

        $page = urldecode($input->page->none());

        if (!self::page_editable($input->autoSaveId->text(), $page)) {
            return array();
        }

        $scripts = new WikiLingo\Utilities\Scripts("vendor/wikilingo/wikilingo/");
        $wikiLingo = new WikiLingo\Parser($scripts);
	    require_once("lib/wikiLingo_tiki/WikiLingoEvents.php");
	    (new WikiLingoEvents($wikiLingo));

        if ($input->wysiwyg->int() == 1) {
            $toWikiLingo = new WYSIWYGWikiLingo\Parser();
            $data = $input->data->none();
            $source = $toWikiLingo->parse($data);
        } else {
            $source = $input->data->none();
        }

        $result = array();

        //save a wiki page
        if ($input->save->int() === 1) {
            $wysiwyg = $input->wysiwyg->int();
            $info = $tikilib->get_page_info($page, false);
            $exists = $tikilib->page_exists($page);

            $wiki_authors_style = '';
            if ( $prefs['wiki_authors_style_by_page'] === 'y' ) {
                $wiki_authors_style_updated = $input->wiki_authors_style->text();
                if ( $globalPerms->admin_wiki && !empty($wiki_authors_style_updated)) {
                    $wiki_authors_style = $wiki_authors_style_updated;
                } elseif ( isset($info['wiki_authors_style']) ) {
                    $wiki_authors_style = $info['wiki_authors_style'];
                }
            }

            $hash = array(
                'lock_it' => (!$input->lock_it->text() === 'on' ? 'y' : 'n'),
                'comments_enabled' => ($input->comments_enabled->text() === 'on' ? 'y' : 'n'),
                'contributions' => $input->contributions->text(),
                'contributors' => $input->contributors->text()
            );

            if ($exists) {
                $tikilib->update_page(
                    $page,
                    $source,
                    $input->comment->text() ?: $info['comment'],
                    $user,
                    $tikilib->get_ip_address(),
                    $input->description->text() ?: $info['description'],
                    ($input->isminor->text() === 'on' ? 1 : 0),
                    $input->lang->text() ?: $info['lang'],
                    false,
                    $hash,
                    null,
                    ($wysiwyg == 0 ? 'n' : 'y'),
                    $wiki_authors_style
                );
                $result['status'] = 'updated';
            } else {
                $tikilib->create_page(
                    $page,
                    0,
                    $source,
                    $tikilib->now,
                    $input->comment->text(),
                    $user,
                    $tikilib->get_ip_address(),
                    $input->description->text(),
                    $input->lang->text(),
                    false,
                    $hash,
                    ($wysiwyg == 0 ? 'n' : 'y'),
                    $wiki_authors_style
                );
                $result['status'] = 'created';
            }
        }

        $version = $result['version'] = $tikilib->getOne("SELECT version FROM `tiki_pages` WHERE pageName=? ORDER BY `version` DESC", array($page));
        $tikilib->query("INSERT INTO tiki_output (entityId, objectType, outputType, version) VALUES (?,?,?,?)", array($page, 'wikiPage', 'wikiLingo', $version));

        if ($input->preview->bool()) {
            $result['parsed'] = $wikiLingo->parse($source);
            $result['script'] = $scripts->renderScript();
            $result['css'] = $scripts->renderCss();
        }

        return $result;
    }

    function action_update_output_type(JitFilter $input)
    {
        $page = $input->page->text();
        $output_type = $input->output_type->text();

        if (self::page_editable(null, $page)) {
            $tikilib = TikiLib::lib('tiki');
            //delete colums from tiki_output if they're already created so new values can be inserted to use wikiLingo as the parser
            $version = $tikilib->getOne("SELECT version FROM `tiki_pages` WHERE pageName=? ORDER BY `version` DESC", array($page));
            $tikilib->query("DELETE FROM `tiki_output` WHERE `entityId` = ? AND `objectType` = ? AND `version` = ?", array($page, 'wikiPage', $version));
            if (!empty($output_type)){
                $tikilib->query("INSERT INTO tiki_output (entityId, objectType, outputType, version) VALUES (?,?,?,?)", array($page, 'wikiPage', $output_type, $version));
            }
            return array(
                'updated' => true,
                'value' => $output_type
            );
        }

        return false;
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

        if (!Perms::get('wiki page', $page)->edit || $user != $tikilib->get_semaphore_user($page)) {
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
