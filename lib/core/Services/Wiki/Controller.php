<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Wiki_Controller
{

	function setUp()
	{
		Services_Exception_Disabled::check('feature_wiki');
	}

	/**
	 * @param $input
	 * @return array
	 * @throws Services_Exception_NotFound
	 */
	function action_get_page($input)
	{
		$page = $input->page->text();
		$info = TikiLib::lib('wiki')->get_page_info($page);
		if (! $info) {
			throw new Services_Exception_NotFound(tr('Page "%0" not found', $page));
		}
		$canBeRefreshed = false;
		$data = TikiLib::lib('wiki')->get_parse($page, $canBeRefreshed);
		return ['data' => $data];
	}

	/**
	 * @param $input
	 * @return array
	 */
	function action_regenerate_slugs($input)
	{
		global $prefs;
		Services_Exception_Denied::checkGlobal('admin');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$pages = TikiDb::get()->table('tiki_pages');

			$initial = TikiLib::lib('slugmanager');
			$tracker = new Tiki\Wiki\SlugManager\InMemoryTracker;
			$manager = clone $initial;
			$manager->setValidationCallback($tracker);

			$list = $pages->fetchColumn('pageName', []);
			$pages->updateMultiple(['pageSlug' => null], []);

			foreach ($list as $page) {
				$slug = $manager->generate($prefs['wiki_url_scheme'], $page, $prefs['url_only_ascii'] === 'y');

				$count = 1;
				while ($pages->fetchCount(['pageSlug' => $slug]) && $count < 100) {
					$count++;
					$slug = $manager->generate($prefs['wiki_url_scheme'], $page . ' ' . $count, $prefs['url_only_ascii'] === 'y');
				}

				$tracker->add($page);
				$pages->update(['pageSlug' => $slug], ['pageName' => $page]);
			}

			TikiLib::lib('access')->redirect('tiki-admin.php?page=wiki');
		}

		return [
			'title' => tr('Regenerate Wiki URLs'),
		];
	}

	/**
	 * List pages "perform with checked" but with no action selected
	 *
	 * @param $input
	 */
	public function action_no_action($input)
	{
		Services_Utilities::modalException(tra('No action was selected. Please select an action before clicking OK.'));
	}


	/**
	 * Remvove pages action, either all versions (from tiki-listpages.php checkbox action) or last version
	 * (page remove button or remove action for an individual page in page listing)
	 *
	 * @param $input
	 * @return array
	 */
	function action_remove_pages($input)
	{
		global $user;

		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (! empty($check['ticket'])) {
			$items = $input->asArray('checked');
			$fitems = Perms::simpleFilter('wiki page', 'pageName', 'remove', $items);
			if (count($fitems) > 0) {
				$v = $input->version->text();
				if (count($fitems) == 1) {
					$versions = TikiLib::lib('hist')->get_nb_history($fitems[0]);
					$one = $versions == 1;
				} else {
					$one = false;
				}
				$pdesc = count($fitems) === 1 ? 'page' : 'pages';
				if ($one) {
					$vdesc = tr('the only version of');
				} elseif ($v === 'all') {
					$vdesc = tr('all versions of');
				} elseif ($v === 'last') {
					$vdesc = tr('the last version of');
				}
				$msg = tr('Delete %0 the following %1?', $vdesc, $pdesc);
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				$included_by = [];
				$wikilib = TikiLib::lib('wiki');
				foreach ($items as $page) {
					$included_by = array_merge($included_by, $wikilib->get_external_includes($page));
				}
				if (sizeof($included_by) == 0) {
					$included_by = null;
				}
				return [
					'title' => tra('Please confirm'),
					'confirmAction' => $input->action->word(),
					'confirmController' => 'wiki',
					'customMsg' => $msg,
					'confirmButton' => tra('Delete'),
					'items' => $fitems,
					'extra' => ['referer' => $referer, 'version' => $v, 'one' => $one],
					'ticket' => $check['ticket'],
					'confirm' => 'y',
					'modal' => '1',
					'included_by' => $included_by,
				];
			} else {
				if (count($items) > 0) {
					Services_Utilities::modalException(tra('You do not have permission to remove the selected page(s)'));
				} else {
					Services_Utilities::modalException(tra('No pages were selected. Please select one or more pages.'));
				}
			}
			//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//delete page
			$items = json_decode($input['items'], true);
			$extra = json_decode($input['extra'], true);
			//checkbox in popup where user can change from all to last and vice versa
			$all = ! empty($input['all']) && $input['all'] === 'on';
			$last = ! empty($input['last']) && $input['last'] === 'on';
			//only use default when not overriden by checkbox
			$all = $all || ($extra['version'] === 'all' && ! $last);
			$last = $last || ($extra['version'] === 'last' && ! $all);
			$error = false;
			$count = count($items);
			foreach ($items as $page) {
				$result = false;
				//get page info before deletion in case this was the page the user was on
				//used later to redirect to the tiki index page
				$allinfo = TikiLib::lib('tiki')->get_page_info($page, false, true);
				$history = false;
				if ($all || $extra['one']) {
					$result = TikiLib::lib('tiki')->remove_all_versions($page);
				} elseif ($last) {
					$result = TikiLib::lib('wiki')->remove_last_version($page);
				} elseif (! empty($extra['version']) && is_numeric($extra['version'])) {
					$result = TikiLib::lib('hist')->remove_version($page, $extra['version']);
					$history = true;
				}
				if (! $result) {
					$error = true;
					$versionText = $history ? tr('Version') . ' ' : '';
					$feedback = [
						'tpl' => 'action',
						'mes' => tr('An error occurred. %0%1 could not be deleted.', $versionText, $page),
					];
					Feedback::error($feedback, 'session');
				}
			}
			//prepare feedback
			if (! $error) {
				if ($all || $extra['one']) {
					$vdesc = tr('All versions');
					$verb = 'have';
					$noversionsleft = true;
				} elseif ($last) {
					$vdesc = tr('The last version');
					$verb = 'has';
				} else {
					//must be a version number
					$vdesc = tr('Version %0', $extra['version']);
					$verb = 'has';
				}
				if ($count === 1) {
					$msg = tr('%0 of the following page %1 been deleted:', $vdesc, $verb);
				} else {
					$msg = tr('%0 of the following pages %1 been deleted:', $vdesc, $verb);
				}
				$feedback = [
					'tpl' => 'action',
					'mes' => $msg,
					'items' => $items,
				];
				Feedback::success($feedback, 'session');
				// Create a Semantic Alias (301 redirect) if this option was selected by user.
				$createredirect = ! empty($input['create_redirect']) && $input['create_redirect'] === 'y';
				if ($createredirect && $noversionsleft) {
					$destinationPage = $input['destpage'];
					if ($destinationPage == "") {
						$msg = tr('Redirection page not specified. 301 redirect not created.');
						$feedback = [
							'tpl' => 'action',
							'mes' => $msg
						];
						Feedback::warning($feedback, 'session');
					} else {
						$appendString = "";
						foreach ($items as $page) {
							// Append on the destination page's content the following string,
							// where $page is the name of the deleted page:
							// "\r\n~tc~(alias($page))~/tc~"
							// We use the ~tc~ so that it doesn't make the destination page look ugly
							$pageHyphensForSpaces = str_replace(" ", "-", $page); // Otherwise pages with spaces won't work
							if (sizeof($items) > 1) {
								$comment = tr('Semantic aliases (301 Redirects) to this page were created when other pages were deleted');
							} else {
								$comment = tr('A semantic alias (301 Redirect) to this page was created when page %0 was deleted', $page);
							}
							$appendString .= "\r\n~tc~ (alias($pageHyphensForSpaces)) ~/tc~";
						}
						if (TikiLib::lib('tiki')->page_exists($destinationPage)) {
							// Get wiki page content
							$infoDestinationPage = TikiLib::lib('tiki')->get_page_info($destinationPage);
							$page_data = $infoDestinationPage['data'];
							$page_data .= $appendString;
							TikiLib::lib('tiki')->update_page($destinationPage, $page_data, $comment, $infoDestinationPage['user'], TikiLib::lib('tiki')->get_ip_address());
							if (sizeof($items) > 1) {
								$msg = tr('301 Redirects to the following page were created:');
							} else {
								$msg = tr('A 301 Redirect to the following page was created:');
							}
						} else {
							if (sizeof($items) > 1) {
								$page_data = tr("THIS PAGE WAS CREATED AUTOMATICALLY when other pages were removed. Please edit and write the definitive contents.");
							} else {
								$page_data = tr("THIS PAGE WAS CREATED AUTOMATICALLY when another page was removed. Please edit and write the definitive contents.");
							}
							$page_data .= $appendString;
							// Create a new page
							TikiLib::lib('tiki')->create_page($destinationPage, 0, $page_data, TikiLib::lib('tiki')->now, $comment, $user, TikiLib::lib('tiki')->get_ip_address());
							if (sizeof($items) > 1) {
								$msg = tr('The following page and 301 Redirects to it were created:');
							} else {
								$msg = tr('The following page and a 301 Redirect to it were created:');
							}
						}
						$feedback = [
							'tpl' => 'action',
							'mes' => $msg,
							'items' => $destinationPage,
						];
						Feedback::note($feedback, 'session');
					}
				}
			}
			//return to page
			if ($count === 1 && ($all || $extra['one'])
				&& strpos($_SERVER['HTTP_REFERER'], $allinfo['pageName']) !== false) {
				//go to tiki index if the page the user was on has been deleted - avoids no page found error.
				global $prefs, $base_url;
				return Services_Utilities::redirect($base_url . $prefs['tikiIndex']);
			}
			return Services_Utilities::refresh($extra['referer']);
		}
	}

	/**
	 * Remove page versions action on the tiki-pagehistory.php page
	 *
	 * @param $input
	 * @return array
	 */
	function action_remove_page_versions($input)
	{
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (! empty($check['ticket'])) {
			$p = $input->page->text();
			Services_Exception_Denied::checkObject('remove', 'wiki page', $p);
			$items = $input->asArray('checked');
			if (count($items) > 0) {
				$vdesc = count($items) === 1 ? 'version' : 'versions';
				$msg = tr('Delete the following %0 of %1?', $vdesc, $p);
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'confirmAction' => $input->action->word(),
						'confirmController' => 'wiki',
						'customMsg' => $msg,
						'confirmButton' => tra('Delete'),
						'items' => $items,  //version numbers
						'extra' => ['referer' => $referer, 'page' => $p],
						'ticket' => $check['ticket'],
						'confirm' => 'y',
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No version were selected. Please select one or more versions.'));
			}
			//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//delete page
			$items = json_decode($input['items'], true);
			$extra = json_decode($input['extra'], true);
			$histlib = TikiLib::lib('hist');
			$pageinfo = TikiLib::lib('tiki')->get_page_info($extra['page']);
			$error = false;
			if ($pageinfo['flag'] != 'L') {
				$result = false;
				foreach ($items as $version) {
					$result = $histlib->remove_version($extra['page'], $version);
				}
				if (! $result) {
					$error = true;
					$feedback = [
						'tpl' => 'action',
						'mes' => tr('An error occurred. Version %0 could not be deleted.', $version),
					];
					Feedback::error($feedback, 'session');
				}
			}
			if (! $error) {
				//prepare feedback
				if (count($items) === 1) {
					$msg = tr('The following version of %0 has been deleted:', $extra['page']);
				} else {
					$msg = tr('The following versions of %0 have been deleted:', $extra['page']);
				}
				$feedback = [
					'tpl' => 'action',
					'mes' => $msg,
					'items' => $items,
				];
				Feedback::success($feedback, 'session');
			}
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}

	/**
	 * Listpages "perform with checked" action to print pages
	 *
	 * @param $input
	 * @return array
	 */
	function action_print_pages($input)
	{
		Services_Exception_Disabled::check('feature_wiki_multiprint');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (! empty($check['ticket'])) {
			$items = $input->asArray('checked');
			$fitems = Perms::simpleFilter('wiki page', 'pageName', 'view', $items);
			if (count($fitems) > 0) {
				if (count($fitems) === 1) {
					$msg = tr('Print the following page?');
				} else {
					$msg = tr('Print the following pages?');
				}
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'confirmAction' => $input->action->word(),
						'confirmController' => 'wiki',
						'customMsg' => $msg,
						'confirmButton' => tra('Print'),
						'items' => $items,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'confirm' => 'y',
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No pages were selected. Please select one or more pages.'));
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			if (! empty($input['items'])) {
				return ['url' => 'tiki-print_multi_pages.php?print=y&printpages=' . urlencode($input['items'])];
			} else {
				Feedback::error(tr('No page specified.'));
				$extra = json_decode($input['extra'], true);
				return Services_Utilities::refresh($extra['referer']);
			}
		}
	}

	function action_export_pdf($input)
	{
		Services_Exception_Disabled::check('feature_wiki_multiprint');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (! empty($check['ticket'])) {
			$items = $input->asArray('checked');
			$fitems = Perms::simpleFilter('wiki page', 'pageName', 'view', $items);
			if (count($fitems) > 0) {
				include_once 'lib/pdflib.php';
				$pdf = new PdfGenerator();
				if (! empty($pdf->error)) {
					Services_Utilities::modalException($pdf->error);
				} else {
					if (count($fitems) === 1) {
						$msg = tr('Export the following page to PDF?');
					} else {
						$msg = tr('Export the following pages to PDF?');
					}
					//provide redirect if js is not enabled
					$referer = Services_Utilities::noJsPath();
					return [
						'FORWARD' => [
							'controller' => 'access',
							'action' => 'confirm',
							'confirmAction' => $input->action->word(),
							'confirmController' => 'wiki',
							'customMsg' => $msg,
							'confirmButton' => tra('PDF'),
							'items' => $items,
							'extra' => ['referer' => $referer],
							'ticket' => $check['ticket'],
							'confirm' => 'y',
							'modal' => '1',
						]
					];
				}
			} else {
				Services_Utilities::modalException(tra('No pages were selected. Please select one or more pages.'));
			}
		//after confirm submit - perform action
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$extra = json_decode($input['extra'], true);
			if (! empty($input['items'])) {
				include_once 'lib/pdflib.php';
				$pdf = new PdfGenerator();
				if (empty($pdf->error)) {
					return ['url' => 'tiki-print_multi_pages.php?display=pdf&printpages=' . $input['items']];
				} else {
					Feedback::error($pdf->error);
				}
			} else {
				Feedback::error(tr('No page specified.'));
			}
			return Services_Utilities::closeModal($extra['referer']);
		}
	}

	/**
	 * Listpages "perform with checked" action to lock pages
	 *
	 * @param $input
	 * @return array
	 */
	function action_lock_pages($input)
	{
		Services_Exception_Disabled::check('feature_wiki_usrlock');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (! empty($check['ticket'])) {
			$items = $input->asArray('checked');
			$fitems = Perms::simpleFilter('wiki page', 'pageName', 'lock', $items);
			foreach ($fitems as $key => $page) {
				if (TikiLib::lib('wiki')->is_locked($page)) {
					unset($fitems[$key]);
				}
			}
			if (count($fitems) > 0) {
				if (count($fitems) === 1) {
					$msg = tr('Lock the following page?');
				} else {
					$msg = tr('Lock the following pages?');
				}
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				$ret = [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'confirmAction' => $input->action->word(),
						'confirmController' => 'wiki',
						'customMsg' => $msg,
						'confirmButton' => tra('Lock'),
						'items' => $fitems,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'confirm' => 'y',
						'modal' => '1',
					]
				];
				if ($items > $fitems) {
					$ret['FORWARD']['help'] = tr('Excludes selected pages already locked or for which you lack permission to lock.');
				}
				return $ret;
			} else {
				if ($items > $fitems) {
					Services_Utilities::modalException(tra('You do not have permission to lock the selected pages or they have already been locked.'));
				} else {
					Services_Utilities::modalException(tra('No pages were selected. Please select one or more pages.'));
				}
			}
			//after confirm submit - perform action
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = json_decode($input['items'], true);
			$extra = json_decode($input['extra'], true);
			$errorpages = [];
			foreach ($items as $page) {
				$res = TikiLib::lib('wiki')->lock_page($page);
				if (! $res) {
					$errorpages[] = $page;
				}
			}
			$locked = array_diff($items, $errorpages);
			//prepare and send feedback
			if (count($errorpages) > 0) {
				if (count($errorpages) === 1) {
					$msg1 = tr('The following page was not locked due to an error:');
				} else {
					$msg1 = tr('The following pages were not locked due to an error:');
				}
				$feedback1 = [
					'tpl' => 'action',
					'mes' => $msg1,
					'items' => $errorpages,
				];
				Feedback::error($feedback1, 'session');
			}
			if (count($locked) > 0) {
				if (count($locked) === 1) {
					$msg2 = tr('The following page has been locked:');
				} else {
					$msg2 = tr('The following pages have been locked:');
				}
				$feedback2 = [
					'tpl' => 'action',
					'mes' => $msg2,
					'items' => $locked,
				];
				Feedback::success($feedback2, 'session');
			}
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}
	/**
	 * Listpages "perform with checked" action to unlock pages
	 *
	 * @param $input
	 * @return array
	 */
	function action_unlock_pages($input)
	{
		Services_Exception_Disabled::check('feature_wiki_usrlock');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (! empty($check['ticket'])) {
			$items = $fitems = $input->asArray('checked');
			$admin = Perms::get()->admin_wiki;
			global $user;
			foreach ($fitems as $key => $page) {
				$pinfo = TikiLib::lib('tiki')->get_page_info($page);
				if (! ($pinfo['flag'] == 'L' &&
						($admin || ($user == $pinfo['lockedby']) || (! $pinfo['lockedby'] && $user == $pinfo['user']))
					)
				) {
					unset($fitems[$key]);
				}
			}
			if (count($fitems) > 0) {
				if (count($fitems) === 1) {
					$msg = tr('Unlock the following page?');
				} else {
					$msg = tr('Unlock the following pages?');
				}
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				$ret = [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'confirmAction' => $input->action->word(),
						'confirmController' => 'wiki',
						'customMsg' => $msg,
						'confirmButton' => tra('Unlock'),
						'items' => $fitems,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'confirm' => 'y',
						'modal' => '1',
					]
				];
				if ($items > $fitems) {
					$ret['FORWARD']['help'] = tr('Excludes selected pages already unlocked or for which you lack permission to unlock.');
				}
				return $ret;
			} else {
				if ($items > $fitems) {
					Services_Utilities::modalException(tra('You do not have permission to unlock the selected pages or they have already been unlocked.'));
				} else {
					Services_Utilities::modalException(tra('No pages were selected. Please select one or more pages.'));
				}
			}
			//after confirm submit - perform action
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = json_decode($input['items'], true);
			$extra = json_decode($input['extra'], true);
			$errorpages = [];
			foreach ($items as $page) {
				$res = TikiLib::lib('wiki')->unlock_page($page);
				if (! $res) {
					$errorpages[] = $page;
				}
			}
			$locked = array_diff($items, $errorpages);
			//prepare and send feedback
			if (count($errorpages) > 0) {
				if (count($errorpages) === 1) {
					$msg1 = tr('The following page was not unlocked due to an error:');
				} else {
					$msg1 = tr('The following pages were not unlocked due to an error:');
				}
				$feedback1 = [
					'tpl' => 'action',
					'mes' => $msg1,
					'items' => $errorpages,
				];
				Feedback::error($feedback1, 'session');
			}
			if (count($locked) > 0) {
				if (count($locked) === 1) {
					$msg2 = tr('The following page has been unlocked:');
				} else {
					$msg2 = tr('The following pages have been unlocked:');
				}
				$feedback2 = [
					'tpl' => 'action',
					'mes' => $msg2,
					'items' => $locked,
				];
				Feedback::success($feedback2, 'session');
			}
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}
	/**
	 * Listpages "perform with checked" action to zip pages
	 *
	 * @param $input
	 * @return array
	 */
	function action_zip($input)
	{
		Services_Exception_Denied::checkGlobal('admin');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (! empty($check['ticket'])) {
			$items = $input->asArray('checked');
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tr('Download a zipped file of the following page?');
				} else {
					$msg = tr('Download a zipped file of the following pages?');
				}
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'confirmAction' => $input->action->word(),
						'confirmController' => 'wiki',
						'customMsg' => $msg,
						'confirmButton' => tra('Zip'),
						'items' => $items,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'confirm' => 'y',
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No pages were selected. Please select one or more pages.'));
			}
		//after confirm submit - perform action
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = json_decode($input['items'], true);
			$extra = json_decode($input['extra'], true);
			include_once('lib/wiki/xmllib.php');
			$xmllib = new XmlLib;
			$zipFile = 'dump/xml.zip';
			$config['debug'] = false;
			if ($xmllib->export_pages($items, null, $zipFile, $config)) {
				if (! $config['debug']) {
					global $base_url;
					return ['url' => $base_url . $zipFile];
				}
			} else {
				Feedback::error(['mes' => $xmllib->get_error()], 'session');
			}
			//return to page
			return Services_Utilities::closeModal($extra['referer']);
		}
	}

	/**
	 * Listpages "perform with checked" action to add page name as title to pages
	 *
	 * @param $input
	 * @return array
	 */
	function action_title($input)
	{
		Services_Exception_Denied::checkGlobal('admin');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (! empty($check['ticket'])) {
			$items = $input->asArray('checked');
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tr('Add page name as header of the following page?');
				} else {
					$msg = tr('Add page name as header of the following pages?');
				}
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'confirmAction' => $input->action->word(),
						'confirmController' => 'wiki',
						'customMsg' => $msg,
						'confirmButton' => tra('Add'),
						'items' => $items,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'confirm' => 'y',
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No pages were selected. Please select one or more pages.'));
			}
			//after confirm submit - perform action
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = json_decode($input['items'], true);
			$extra = json_decode($input['extra'], true);
			$errorpages = [];
			foreach ($items as $page) {
				$pageinfo = TikiLib::lib('tiki')->get_page_info($page);
				if ($pageinfo) {
					$pageinfo['data'] = "!$page\r\n" . $pageinfo['data'];
					$table = TikiLib::lib('tiki')->table('tiki_pages');
					$table->update(['data' => $pageinfo['data']], ['page_id' => $pageinfo['page_id']]);
				} else {
					$errorpages[] = $page;
				}
			}
			if (count($errorpages) > 0) {
				if (count($errorpages) === 1) {
					$msg1 = tr('The following page was not found:');
				} else {
					$msg1 = tr('The following pages were not found:');
				}
				$feedback1 = [
					'tpl' => 'action',
					'mes' => $msg1,
					'items' => $errorpages,
				];
				Feedback::error($feedback1, 'session');
			}
			$fitems = array_diff($items, $errorpages);
			if (count($fitems) > 0) {
				if (count($fitems) === 1) {
					$msg2 = tr('The page name was added as header to the following page:');
				} else {
					$msg2 = tr('The page name was added as header to the following pages:');
				}
				$feedback2 = [
					'tpl' => 'action',
					'mes' => $msg2,
					'items' => $fitems,
				];
				Feedback::success($feedback2, 'session');
			}
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}
}
