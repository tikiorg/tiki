<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Feedback
 *
 * Class for adding feedback to the top of the page either through the php SESSION['tikifeedback'] global variable or
 * through a {$tikifeedback} Smarty template variable. The {remarksbox} Smarty function is used so that errors,
 * warnings, notes, success feedback types and related styling are available.
 *
 * Through this class and the use of the smarty function {feedback} in the basic page layout templates (layout_view.tpl),
 * such feedback can be sent, retreived and displayed without any Smarty template coding needed. Custom templates can
 * also be added for additional use cases.
 *
 */
class Feedback
{

	/**
	 * Add error feedback
	 *
	 * This is a specific application of the add function below for errors.
	 *
	 * @param $feedback
	 * @param string $method
	 */
	public static function error($feedback, $method = 'tpl')
	{
		$feedback = self::checkFeedback($feedback);
		$feedback['type'] = 'error';
		$feedback['title'] = empty($feedback['title']) ? tr('Error') : $feedback['title'];
		$feedback['icon'] = empty($feedback['icon']) ? 'error' : $feedback['icon'];
		self::add($feedback, $method);
	}

	/**
	 * Add note feedback
	 *
	 * This is a specific application of the add function below for notes.
	 *
	 * @param $feedback
	 * @param string $method
	 */
	public static function note($feedback, $method = 'tpl')
	{
		$feedback = self::checkFeedback($feedback);
		$feedback['type'] = 'note';
		$feedback['title'] = empty($feedback['title']) ? tr('Note') : $feedback['title'];
		$feedback['icon'] = empty($feedback['icon']) ? 'information' : $feedback['icon'];
		self::add($feedback, $method);
	}

	/**
	 * Add success feedback
	 *
	 * This is a specific application of the add function below for success feedback.
	 *
	 * @param $feedback
	 * @param string $method
	 */
	public static function success($feedback, $method = 'tpl')
	{
		$feedback = self::checkFeedback($feedback);
		$feedback['type'] = 'feedback';
		$feedback['title'] = empty($feedback['title']) ? tr('Success') : $feedback['title'];
		$feedback['icon'] = empty($feedback['icon']) ? 'success' : $feedback['icon'];
		self::add($feedback, $method);
	}

	/**
	 * Add warning feedback
	 *
	 * This is a specific application of the add function below for warnings.
	 *
	 * @param $feedback
	 * @param string $method
	 */
	public static function warning($feedback, $method = 'tpl')
	{
		$feedback = self::checkFeedback($feedback);
		$feedback['type'] = 'warning';
		$feedback['title'] = empty($feedback['title']) ? tr('Warning') : $feedback['title'];
		$feedback['icon'] = empty($feedback['icon']) ? 'warning' : $feedback['icon'];
		self::add($feedback, $method);
	}

	/**
	 * Add feedback to a global or smarty variable
	 *
	 * Adds feedback to either the PHP $_SESSION['tikifeedback'] global variable or to a Smarty {$tikifeedback}
	 * variable. Typically one of the custom functions above that use this function and that are specific for errors,
	 * warnings, notes and success feedback will be used in the individual php file where the error is generated.
	 *
	 * @param $feedback
	 *          - Must at least contain at least a string message
	 *          - Can be an array of messages too, in which case the array key 'mes' should be used
	 *          - Other array keys can be used that correspond to remarksbox parameters, such as 'type', 'title',
	 *              and 'icon'
	 *          - A custom smarty template can be indicated using the 'tpl' array key (otherwise
	 *              templates/feedback/default.tpl is used). The specified Smarty template will need to be added to the
	 *              templates/feedback directory. E.g., including 'tpl' => 'pref' in the $feedback array would cause
	 *              the templates/feedback/pref.tpl to be used
	 *          - Other custom array keys can be added for use on custom templates
	 * @param string $method
	 *          - Two choices:
	 *              - 'tpl' (default) to cause the feedback to be added to the {$tikifeedback} Smarty variable
	 *              - 'session' to add to the PHP $_SESSION['tikifeedback'] global variable
	 * @return array or bool
	 */
	public static function add($feedback, $method = 'tpl')
	{
		$feedback = self::checkFeedback($feedback);
		//add feedback to either the SESSION global variable or to smarty tpl variable
		switch ($method) {
			case 'session':
				if (!isset($_SESSION['tikifeedback'])) {
					$_SESSION['tikifeedback'] = [];
				}
				$_SESSION['tikifeedback'][] = $feedback;
				break;
			case 'tpl':
				$smarty = TikiLib::lib('smarty');
				$smarty->append('tikifeedback', $feedback);
				break;
		}
	}

	/**
	 * Utility to ensure $feedback parameter is in the right format
	 *
	 * @param $feedback
	 * @return array|bool
	 */
	private static function checkFeedback($feedback)
	{
		if (empty($feedback)) {
			trigger_error(tr('Feedback class called with no feedback provided.'), E_NOTICE);
			return false;
		} elseif (!is_array($feedback)) {
			$feedback = ['mes' => $feedback];
		} else {
			if (empty($feedback['mes'])) {
				trigger_error(tr('Feedback class called with no feedback provided.'), E_NOTICE);
				return false;
			} elseif (!is_array($feedback['mes'])) {
				$feedback['mes'] = [$feedback['mes']];
			}
		}
		return $feedback;
	}

	/**
	 * Gets feedback that has been added to either the global PHP $_SESSION['tikifeedback'] or Smarty {$tikifeedback}
	 * variable
	 *
	 * This function is mainly used and already included in the Smarty {feedback} function included in the basic
	 * layout_view templates to retrieve and display any feedback that has been added. Normally there isn't a need for
	 * developers to use this function otherwise.
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	public static function get()
	{
		$result = false;
		$smarty = TikiLib::lib('smarty');
		//handle tikifeedback that has either been sent to the SESSION variable or passed to smarty
		$tpl = $smarty->getTemplateVars('tikifeedback');
		$smarty->clearAssign('tikifeedback');
		if (isset($_SESSION['tikifeedback']) || $tpl) {
			//get feedback from session variable
			if (isset($_SESSION['tikifeedback'])) {
				$session = $_SESSION['tikifeedback'];
				unset($_SESSION['tikifeedback']);
			} else {
				$session = [];
			}
			//get feedback from smarty template variables
			//merge the feedback arrays
			if (!empty($tpl)) {
				$feedback = array_merge($session, $tpl);
			} else {
				$feedback = $session;
			}
			//add default tpl if not set
			foreach($feedback as $key => $item) {
				$feedback[$key] = array_merge([
					'tpl' => 'default',
					'type' => 'feedback',
					'icon' => '',
					'title' => tr('Note')
				], $item);
				if (empty($item['tpl'])) {
					$feedback[$key]['tpl'] = 'default';
				}
				if (!isset($item['type'])) {}
			}
			//make the tpl the first level array key
			$fbbytpl = [];
			foreach($feedback as $key => $item) {
				$tplkey = $item['tpl'];
				unset($item['tpl']);
				$fbbytpl[$tplkey][] = $item;
			}
			if (!empty($fbbytpl)) {
				$result = $fbbytpl;
			}
		}
		return $result;
	}

	/**
	 * Add feedback through ajax
	 *
	 * @throws Exception
	 */
	public static function send_headers()
	{
		require_once 'lib/smarty_tiki/function.feedback.php';
		header('X-Tiki-Feedback: ' . str_replace(array("\n", "\r", "\t"), '', smarty_function_feedback([], 
				TikiLib::lib('smarty'))));
	}

}