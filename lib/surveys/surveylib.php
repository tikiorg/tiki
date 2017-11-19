<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 *
 */
class SurveyLib extends TikiLib
{
	private $surveysTable;
	private $questionsTable;
	private $optionsTable;

	function __construct()
	{
		parent::__construct();

		$this->surveysTable   = $this->table('tiki_surveys');
		$this->questionsTable = $this->table('tiki_survey_questions');
		$this->optionsTable   = $this->table('tiki_survey_question_options');
		$this->votesTable     = $this->table('tiki_user_votings');
	}

	/**
	 * @param $offset
	 * @param $maxRecords
	 * @param $sort_mode
	 * @param $find
	 * @return array
	 */
	public function list_surveys($offset, $maxRecords, $sort_mode, $find, $perm = 'take_survey')
	{
		$conditions = [];
		if ($find) {
			$conditions['search'] = $this->surveysTable->expr('(`name` like ? or `description` like ?)', ["%$find%", "%$find%"]);
		}
		$surveys = $this->surveysTable->fetchAll(
			$this->surveysTable->all(),
			$conditions,
			$maxRecords,
			$offset,
			$this->surveysTable->sortMode($sort_mode)
		);
		$surveys = Perms::filter(['type' => 'survey'], 'object', $surveys, ['object' => 'surveyId'], $perm);

		foreach ($surveys as & $survey) {
			$survey['questions'] = $this->questionsTable->fetchOne(
				$this->questionsTable->count(),
				['surveyId' => $survey['surveyId']]
			);
		}

		$retval["data"] = $surveys;
		$retval["cant"] = count($surveys);
		return $retval;
	}

	/**
	 * @param $surveyId
	 */
	public function add_survey_hit($surveyId)
	{
		global $prefs, $user;

		if (StatsLib::is_stats_hit()) {
			$this->surveysTable->update(
				[
					'taken' => $this->surveysTable->increment(1),
					'lastTaken' => $this->now
				],
				['surveyId' => $surveyId]
			);
		}
	}

	/**
	 * @param $questionId
	 * @param $value
	 * @return int
	 */
	public function register_survey_text_option_vote($questionId, $value)
	{
		$conditions = [
			'questionId' => $questionId,
			'qoption' => $value,
		];

		$result = $this->optionsTable->fetchColumn('optionId', $conditions);
		if (! empty($result)) {
			$optionId = $result[0];
			$this->optionsTable->update(
				[
					'votes' => $this->optionsTable->increment(1),
				],
				$conditions
			);
		} else {
			$optionId = $this->optionsTable->insert(
				[
					'questionId' => $questionId,
					'qoption' => $value,
					'votes' => 1,
				]
			);
		}
		return $optionId;
	}

	/**
	 * @param $questionId
	 * @param $rate
	 */
	public function register_survey_rate_vote($questionId, $rate)
	{
		$conditions = ['questionId' => $questionId];

		$this->questionsTable->update(
			[
				'votes' => $this->questionsTable->increment(1),
				'value' => $this->questionsTable->increment($rate),
			],
			$conditions
		);
		$this->questionsTable->update(
			[
				'average' => $this->questionsTable->expr('`value`/`votes`'),
			],
			$conditions
		);
	}

	/**
	 * @param $questionId
	 * @param $optionId
	 */
	public function register_survey_option_vote($questionId, $optionId)
	{
		$this->optionsTable->update(
			[
				'votes' => $this->optionsTable->increment(1),
			],
			[
				'questionId' => $questionId,
				'optionId' => $optionId,
			]
		);
	}

	/**
	 * @param $surveyId
	 */
	public function clear_survey_stats($surveyId)
	{
		$conditions = ['surveyId' => $surveyId];

		$this->surveysTable->update(
			['taken' => 0],
			$conditions
		);

		$questions = $this->questionsTable->fetchAll(
			$this->questionsTable->all(),
			$conditions
		);


		// Remove all the options for each question for text, wiki and fgal types
		foreach ($questions as $question) {
			$qconditions = ['questionId' => (int) $question['questionId']];

			if (in_array($question['type'], ['t', 'g', 'x'])) {
				// same table used for options and responses (nice)
				$this->optionsTable->deleteMultiple($qconditions);
			} else {
				$this->optionsTable->updateMultiple(['votes' => 0], $qconditions);
			}
		}
		$this->questionsTable->updateMultiple(
			[
				'average' => 0,
				'value' => 0,
				'votes' => 0
			],
			$conditions
		);

		$this->get()->table('tiki_user_votings')->deleteMultiple(
			['id' => 'survey' . $surveyId]
		);
	}

	/**
	 * @param $surveyId
	 * @param $name
	 * @param $description
	 * @param $status
	 * @return mixed
	 */
	public function replace_survey($surveyId, $name, $description, $status)
	{
		$newId = $this->surveysTable->insertOrUpdate(
			[
				'name' => $name,
				'description' => $description,
				'status' => $status,
			],
			['surveyId' => $surveyId]
		);
		return $newId ? $newId : $surveyId;
	}

	/**
	 * @param $questionId
	 * @param $question
	 * @param $type
	 * @param $surveyId
	 * @param $position
	 * @param $options
	 * @param string $mandatory
	 * @param int $min_answers
	 * @param int $max_answers
	 * @return mixed
	 */
	public function replace_survey_question(
		$questionId,
		$question,
		$type,
		$surveyId,
		$position,
		$options,
		$mandatory = 'n',
		$min_answers = 0,
		$max_answers = 0
	) {

		if ($mandatory != 'y') {
			$mandatory = 'n';
		}
		$min_answers = (int) $min_answers;
		$max_answers = (int) $max_answers;

		$newId = $this->questionsTable->insertOrUpdate(
			[
				'type'        => $type,
				'position'    => $position,
				'question'    => $question,
				'options'     => $options,
				'mandatory'   => $mandatory,
				'min_answers' => $min_answers,
				'max_answers' => $max_answers,
			],
			[
				'questionId'  => $questionId,
				'surveyId'    => $surveyId,
			]
		);

		$questionId = $newId ? $newId : $questionId;

		$userOptions = $this->parse_options($options);

		$questionOptions = $this->optionsTable->fetchAll(
			['optionId','qoption'],
			['questionId'  => $questionId]
		);

		// Reset question options only if not a 'text', 'wiki' or 'filegal choice', because their options are dynamically generated
		if (! in_array($type, ['t', 'g', 'x'])) {
			foreach ($questionOptions as $qoption) {
				if (! in_array($qoption['qoption'], $userOptions)) {
					$this->optionsTable->delete([
						'questionId' => $questionId,
						'optionId' => $qoption['optionId'],
					]);
				} else {
					$idx = array_search($qoption["qoption"], $userOptions);
					unset($userOptions[$idx]);
				}
			}
			foreach ($userOptions as $option) {
				$this->optionsTable->insert([
					'questionId' => $questionId,
					'qoption' => $option,
					'votes' => 0,
				]);
			}
		}

		return $questionId;
	}

	/**
	 * @param $surveyId
	 * @return array
	 */
	public function get_survey($surveyId)
	{
		return $this->surveysTable->fetchRow(
			$this->surveysTable->all(),
			['surveyId' => $surveyId]
		);
	}

	/**
	 * @param $questionId
	 * @return bool
	 */
	public function get_survey_question($questionId)
	{
		$question = $this->questionsTable->fetchRow(
			$this->questionsTable->all(),
			['questionId' => $questionId]
		);

		$options = $this->optionsTable->fetchRow(
			$this->optionsTable->all(),
			['questionId' => $questionId]
		);

		$qoptions = [];
		$votes = 0;

		foreach ($options as $option) {
			$qoptions[] = $option;
			$votes += $option["votes"];
		}

		$question["ovotes"] = $votes;
		$question["qoptions"] = $qoptions;
		return $question;
	}

	/**
	 * @param $surveyId
	 * @param $offset
	 * @param $maxRecords
	 * @param $sort_mode
	 * @param $find
	 * @return array
	 */
	public function list_survey_questions($surveyId, $offset, $maxRecords, $sort_mode, $find, $u = '')
	{
		$filegallib = TikiLib::lib('filegal');

		$conditions = ['surveyId' => $surveyId];
		if ($find) {
			$conditions['question'] = $this->questionsTable->like('%' . $find . '%');
		}

		$questions = $this->questionsTable->fetchAll(
			$this->questionsTable->all(),
			$conditions,
			-1,
			-1,
			$this->questionsTable->sortMode($sort_mode)
		);
		$ret = [];

		if ($u) {
			$userVotedOptions = $this->get_user_voted_options($surveyId, $u);
		} else {
			$userVotedOptions = [];
		}

		foreach ($questions as & $question) {
			// save user options
			$userOptions = $this->parse_options($question["options"]);

			if (! empty($question['options'])) {
				if (in_array($question['type'], ['g', 'x', 'h'])) {
					$question['explode'] = $userOptions;
				} elseif (in_array($question['type'], ['r', 's'])) {
					$question['explode'] = array_fill(1, $question['options'], ' ');
				} elseif (in_array($question['type'], ['t'])) {
					$question['cols'] = $question['options'];
				}
			}

			$questionOptions = $this->optionsTable->fetchAll(
				$this->optionsTable->all(),
				['questionId' => $question["questionId"]],
				-1,
				-1,
				$question['type'] === 'g' ?
					['votes' => 'desc'] :
					['optionId' => 'asc']
			);
			$question["options"] = count($questionOptions);

			if ($question["type"] == 'r') {
				$maxwidth = 5;
			} else {
				$maxwidth = 10;
			}
			$question["width"] = $question["average"] * 200 / $maxwidth;
			$ret2 = [];
			$votes = 0;
			$total_votes = 0;
			foreach ($questionOptions as & $questionOption) {
				$total_votes += (int) $questionOption['votes'];
			}

			$ids = [];
			TikiLib::lib('smarty')->loadPlugin('smarty_modifier_escape');

			foreach ($questionOptions as & $questionOption) {
				if (in_array($questionOption['optionId'], $userVotedOptions)) {
					$questionOption['uservoted'] = true;
				} else {
					$questionOption['uservoted'] = false;
				}

				if ($total_votes) {
					$average = ($questionOption["votes"] / $total_votes) * 100;
				} else {
					$average = 0;
				}

				$votes += $questionOption["votes"];
				$questionOption["average"] = $average;
				$questionOption["width"] = $average * 2;
				$questionOption['qoptionraw'] = $questionOption['qoption'];
				if ($question['type'] == 'x') {
					$questionOption['qoption'] = TikiLib::lib('parser')->parse_data($questionOption['qoption']);
				} else {
					$questionOption['qoption'] = smarty_modifier_escape($questionOption['qoption']);
				}

				// when question with multiple options
				// we MUST respect the user defined order
				if (in_array($question['type'], ['m', 'c'])) {
					$ret2[array_search($questionOption['qoptionraw'], $userOptions)] = $questionOption;
				} else {
					$ret2[] = $questionOption;
				}

				$ids[$questionOption['qoption']] = true;
			}

			// For a multiple choice from a file gallery, show all files in the stats results, even if there was no vote for those files
			if ($question['type'] == 'g' && $question['options'] > 0) {
				$files = $filegallib->get_files(0, -1, '', '', $userOptions[0], false, false, false, true, false, false, false, false, '', false, false);
				foreach ($files['data'] as $f) {
					if (! isset($ids[$f['id']])) {
						$ret2[] = [
							'qoption' => $f['id'],
							'votes' => 0,
							'average' => 0,
							'width' => 0
						];
					}
				}
				unset($files);
			}

			$question["qoptions"] = $ret2;
			$question["ovotes"] = $votes;
			$ret[] = $question;
		}

		$retval = [];
		$retval["data"] = $ret;
		$retval["cant"] = count($questions);
		return $retval;
	}

	/**
	 * @param $questionId
	 * @return bool
	 */
	public function remove_survey_question($questionId)
	{
		$conditions = ['questionId' => $questionId];

		$this->optionsTable->deleteMultiple($conditions);
		$this->questionsTable->delete($conditions);
		return true;
	}

	/**
	 * @param $surveyId
	 * @return bool
	 */
	public function remove_survey($surveyId)
	{

		$conditions = ['surveyId' => $surveyId];

		$this->surveysTable->delete($conditions);

		$questions = $this->questionsTable->fetchColumn('questionId', $conditions);

		foreach ($questions as $question) {
			$this->optionsTable->deleteMultiple(['questionId' => (int) $question['questionId']]);
		}
		$this->questionsTable->deleteMultiple($conditions);

		$this->remove_object('survey', $surveyId);

		$this->get()->table('tiki_user_votings')->deleteMultiple(
			['id' => 'survey' . $surveyId]
		);
		return true;
	}

	// Check mandatory fields and min/max number of answers and register vote/answers if ok
	/**
	 * @param $surveyId
	 * @param $questions
	 * @param $answers
	 * @param null $error_msg
	 * @return bool
	 */
	public function register_answers($surveyId, $questions, $answers, &$error_msg = null)
	{
		global $user;

		if ($surveyId <= 0 || empty($questions)) {
			return false;
		}

		$errors = [];
		foreach ($questions as $question) {
			$key = 'question_' . $question['questionId'];
			$nb_answers = empty($answers[$key]) ? 0 : 1;
			$multiple_choice = in_array($question['type'], ['m', 'g']);
			if ($multiple_choice) {
				$nb_answers = is_array($answers[$key]) ? count($answers[$key]) : 0;
				if ($question['max_answers'] < 1) {
					$question['max_answers'] = $nb_answers;
				}
			}
			$q = empty($question['question']) ? '.' : ' "<b>' . $question['question'] . '</b>".';
			if ($multiple_choice) {
				if ($question['mandatory'] == 'y') {
					$question['min_answers'] = max(1, $question['min_answers']);
				}

				if ($question['min_answers'] == $question['max_answers'] && $nb_answers != $question['min_answers']) {
					$errors[] = sprintf(tra('%d choice(s) must be made for the question'), $question['min_answers']) . $q;
				} elseif ($nb_answers < $question['min_answers']) {
					$errors[] = sprintf(tra('At least %d choice(s) must be made for the question'), $question['min_answers']) . $q;
				} elseif ($question['max_answers'] > 0 && $nb_answers > $question['max_answers']) {
					$errors[] = sprintf(tra('Fewer than %d choice(s) must be made for the question'), $question['max_answers']) . $q;
				}
			} elseif ($question['mandatory'] == 'y' && $nb_answers == 0 && $question["type"] !== 'h') {
				$errors[] = sprintf(tra('At least %d choice(s) must be made for the question'), 1) . $q;
			}
		}

		if (count($errors) > 0) {
			if ($error_msg !== null) {
				$error_msg = $errors;
			}
			return false;
		} else {
			// no errors, so record answers
			//
			// format for answers recorded in tiki_user_votings is "surveyX.YY"
			//   where X is surveyId and YY is the questionId
			//   and optionId is the id in tiki_survey_question_options

			$this->register_user_vote($user, 'survey' . $surveyId, 0);

			foreach ($questions as $question) {
				$questionId = $question["questionId"];

				if (isset($answers["question_" . $questionId])) {
					if ($question["type"] == 'm') {
						// If we have a multiple question
						$ids = array_keys($answers["question_" . $questionId]);

						// Now for each of the options we increase the number of votes
						foreach ($ids as $optionId) {
							$this->register_survey_option_vote($questionId, $optionId);
							$this->register_user_vote($user, 'survey' . $surveyId . '.' . $questionId, $optionId);
						}
					} elseif ($question["type"] == 'g') {
						// If we have a multiple choice of file from a gallery
						$ids = $answers["question_" . $questionId];

						// Now for each of the options we increase the number of votes
						foreach ($ids as $optionId) {
							$this->register_survey_text_option_vote($questionId, $optionId);
							$this->register_user_vote($user, 'survey' . $surveyId . '.' . $questionId, $optionId);
						}
					} elseif ($question["type"] !== 'h') {
						$value = $answers["question_" . $questionId];

						if ($question["type"] == 'r' || $question["type"] == 's') {
							$this->register_survey_rate_vote($questionId, $value);
							$this->register_user_vote($user, 'survey' . $surveyId . '.' . $questionId, $value);
						} elseif ($question["type"] == 't' || $question["type"] == 'x') {
							$optionId = $this->register_survey_text_option_vote($questionId, $value);
							$this->register_user_vote($user, 'survey' . $surveyId . '.' . $questionId, $optionId);
						} else {
							$this->register_survey_option_vote($questionId, $value);
							$this->register_user_vote($user, 'survey' . $surveyId . '.' . $questionId, $value);
						}
					}
				}
			}
		}

		return true;
	}

	public function reorderQuestions($surveyId, $questionIds)
	{
		$counter = 1;
		foreach ($questionIds as $id) {
			$this->questionsTable->update(
				['position' => $counter],
				[
					'questionId' => $id,
					'surveyId' => $surveyId,
				]
			);
			$counter++;
		}
	}

	/**
	 * @return array question types: initial => translated label
	 */
	public function get_types()
	{
		return [
			'c' => tra('One choice'),
			'm' => tra('Multiple choices'),
			'g' => tra('Thumbnails'),
			't' => tra('Short text'),
			'x' => tra('Wiki textarea'),
			'r' => tra('Rate (1 to 5)'),
			's' => tra('Rate (1 to 10)'),
			'h' => tra('Heading'),
		];
	}

	/**
	 * @param string $options	comma separated options string (use \, to include a comma)
	 * @return array
	 */
	private function parse_options($options)
	{
		if (! empty($options)) {
			$comma = '~COMMA~';
			$options = str_replace('\,', $comma, $options);
			$options = explode(',', $options);
			foreach ($options as & $option) {
				$option = trim(str_replace($comma, ',', $option));
			}
		} else {
			$options = [];
		}
		return $options;
	}

	private function get_user_voted_options($surveyId, $u)
	{
		$conditions['id'] = $this->votesTable->like('survey' . $surveyId . '%');
		$conditions['user'] = $u;
		$result = $this->votesTable->fetchAll(['optionId'], $conditions);
		foreach ($result as $r) {
			$ret[] = $r['optionId'];
		}
		return $ret;
	}

	function list_users_that_voted($surveyId)
	{
		$conditions['id'] = 'survey' . $surveyId;
		$conditions['optionId'] = 0;
		$result = $this->votesTable->fetchAll(['user'], $conditions);
		foreach ($result as $r) {
			$ret[] = $r['user'];
		}
		return array_unique($ret);
	}
}

$srvlib = new SurveyLib;
