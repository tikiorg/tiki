<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 *
 */
class TodoLib
{
	// create a new todo
    /**
     * @param $after
     * @param $event
     * @param $objectType
     * @param null $objectId
     * @param null $from
     * @param null $to
     * @return mixed
     */
    function addTodo($after, $event, $objectType, $objectId = null, $from = null, $to = null)
	{
		$db = TikiDb::get();
		$query = 'INSERT INTO `tiki_todo` (`after`, `event`, `objectType`, `objectId`, `from`, `to`) VALUES (?, ?, ?, ?, ?, ?)';
		$from = json_encode($from);
		$to = json_encode($to);
		$db->query($query, array($after, $event, $objectType, $objectId, $from, $to));
		$query = 'SELECT `todoId` from `tiki_todo` where `after`=? and `event`=? and `objectType`=? and `objectId`=? and `from`=? and `to`=?';
		$id = $db->getOne($query, array($after, $event, $objectType, $objectId, $from, $to));
		return $id;
	}

	// list of the todos of an object
    /**
     * @param null $objectType
     * @param null $objectId
     * @param null $todoId
     * @return mixed
     */
    function listTodoObject($objectType = null, $objectId = null, $todoId = null)
	{
		$db = TikiDb::get();
		$query = 'SELECT * FROM `tiki_todo` tt ';
		$where = array();
		$bindvars = array();
		if (!empty($objectType)) {
			$where[] = 'tt.`objectType` = ?';
			$bindvars[] = $objectType;
			if (!empty($objectId)) {
				$where[] = 'tt.`objectId` = ?';
				$bindvars[] = $objectId;
			}
		}
		if (!empty($todoId)) {
			$where[] = 'tt.`todoId` = ?';
			$bindvars[] = $todoId;
		}
		if (!empty($where)) $query .= ' WHERE ' . implode(' AND ', $where);
		$query .= ' ORDER BY `todoId` asc';// so that actions arrive before notification
		$todos = $db->fetchAll($query, $bindvars);
		foreach ( $todos as $i=>$todo ) {
			$todos[$i]['from'] = json_decode($todo['from'], true);
			$todos[$i]['to'] = json_decode($todo['to'], true);
			if ($objectType == null) {
				if ($todo['objectType'] == 'todo') {
					$objects = $this->listTodoObject(null, null, $todo['objectId']);
					$todos[$i]['for'] = $objects[0];
				}
			} elseif ($objectType != 'todo') {
				$todos[$i]['notifs'] = $this->listTodoObject('todo', $todo['todoId']);
			}
		}
		return $todos;
	}

	// delete a todo
    /**
     * @param $id
     */
    function delTodo($id)
	{
		$db = TikiDb::get();
		$query = 'DELETE FROM `tiki_todo` WHERE `todoId`=? OR (`objectId`=? AND `objectType`=?)';
		$db->query($query, array($id, $id, 'todo'));
		self::cleanNotif();
	}

    /**
     * @param $objectType
     * @param $objectId
     */
    function delObjectTodo($objectType, $objectId)
	{
		$db = TikiDb::get();
		$query = 'DELETE FROM `tiki_todo_notif` WHERE `objectType`=? AND `objectId` = ?';
		$db->query($query, array($objectType, $objectId));
		$query = 'DELETE FROM `tiki_todo` WHERE `objectType`=? AND `objectId`=?';
		$db->query($query, array($objectType, $objectId));
		self::cleanNotif();
	}

	// apply a todo
    /**
     * @param $todo
     * @return bool
     */
    function applyTodo($todo)
	{
		switch ($todo['objectType']) {
			case 'todo':
				// echo '<pre>ALREADY';print_r($this->alreadyNotif ($todo['todoId'])); echo '</pre>';
				$todo['for']['after'] = $todo['after'];
				$objects = $this->listObjectsTodo($todo['for'], $this->alreadyNotif($todo['todoId']));

				if (!empty($objects)) {
					$func = 'notifyTodo_' . $todo['for']['objectType'];
					if (!method_exists($this, $func)) {
						return false;
					}
					$this->$func($todo, $objects);
				}
				// echo '<pre>MAIL';print_r($objects); echo '</pre>';
				break;

			default:
				$objects = $this->listObjectsTodo($todo);
				// echo '<pre>';print_r($objects); echo '</pre>';
				if (!empty($objects)) {
					$func = 'applyTodo_' . $todo['objectType'];
					if (!method_exists($this, $func)) {
						return false;
					}
					$this->$func($todo, $objects);
					$this->delNotif($todo['objectId']);
				}
		}
	}

	// list the objects selected by a todo
    /**
     * @param $todo
     * @param null $except
     * @return bool
     */
    function listObjectsTodo($todo, $except=null)
	{
		$func = 'listObjectsTodo_'.$todo['objectType'];
		if (!method_exists($this, $func)) {
			return false;
		}
		return $this->$func($todo, $except);
	}

    /**
     * @param $todoId
     * @return mixed
     */
    function alreadyNotif ($todoId)
	{
		$db = TikiDb::get();
		$query = 'SELECT `objectId` FROM `tiki_todo_notif` WHERE `todoId`=?';
		$objects = $db->fetchAll($query, array($todoId));
		foreach ($objects as $i=>$object) {
			$objects[$i] = $object['objectId'];
		}
		return $objects;
	}

    /**
     * @param $todoId
     * @param $objectType
     * @param $objectId
     */
    function addNotif ($todoId, $objectType, $objectId)
	{
		$db = TikiDb::get();
		$query = 'INSERT INTO `tiki_todo_notif` (`todoId`, `objectType`, `objectId`) VALUES(?,?,?)';
		$db->query($query, array($todoId, $objectType, $objectId));
	}

    /**
     * @param $todoId
     */
    function delNotif ($todoId)
	{
		$db = TikiDb::get();
		$query = 'DELETE FROM `tiki_todo_notif` WHERE `todoId`=?';
		$db->query($query, array($todoId));
	}

	function cleanNotif ()
	{
		$db = TikiDb::get();
		$query = 'DELETE FROM `tiki_todo_notif` WHERE `todoId` NOT IN (SELECT `todoId` FROM `tiki_todo`)';
	}

    /**
     * @param $todo
     * @param $to
     * @param string $default_subject
     * @param string $default_body
     */
    function mailTodo($todo, $to, $default_subject='Change notification', $default_body='')
	{
		global $prefs;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');
		if (empty($to['email']) && !empty($to['user'])) {
			$to['email'] = $userlib->get_user_email($to['user']);
		}
		if (empty($to['email'])) {
			return;
		}
		$lang = empty($to['user']) ? $prefs['site_language']: $tikilib->get_user_preference($u, 'language', $prefs['site_language']);
		if (empty($todo['to']['subject'])) {
			$todo['to']['subject'] = tra($default_subject, $lang);
		}
		if (empty($todo['to']['body'])) {
			$todo['to']['body'] = tra($default_body, $lang);
		} else {
			$todo['to']['body'] = $smarty->fetchLang($lang, $todo['to']['body']);
		}
		include_once ('lib/webmail/tikimaillib.php');
		$mail = new TikiMail(empty($to['user'])?null:$to['user']);
		$mail->setSubject($todo['to']['subject']);
		$mail->setHtml($todo['to']['body']);
		$mail->send(array($to['email']));
	}

	/////////////////////////////////////////////////
    /**
     * @param $todo
     * @param null $except
     * @return array
     */
    function listObjectsTodo_tracker($todo, $except=null)
	{
		global $tikilib;
		$trklib = TikiLib::lib('trk');

		switch ($todo['event']) {
			case 'creation':
				$filter = array('createdBefore' => $tikilib->now - $todo['after']);
				break;

			case 'modification':
				$filter = array('lastModifBefore' => $tikilib->now - $todo['after']);
				break;
		}

		$definition = Tracker_Definition::get($todo['objectId']);
		$fieldId = $definition->getUserField();

		if ($fieldId) {
			$filterFields = array($fieldId => $trklib->get_tracker_field($fieldId));
		} else {
			$filterFields = '';
		}

		$objects = $trklib->list_items(
			$todo['objectId'],
			0,
			-1,
			'created_asc',
			$filterFields,
			'',
			'',
			$todo['from']['status'],
			'',
			'',
			$filter
		);

		// todo in list_items:
		// return $objects['data'];
		if (empty($except))
			return $objects['data'];
		$res = array();
		foreach ($objects['data'] as $object) {
			if (!in_array($object['itemId'], $except)) {
				$res[] = $object;
			}
		}
		return $res;
	}

    /**
     * @param $todo
     * @param $objects
     */
    function applyTodo_tracker($todo, $objects)
	{
		$trklib = TikiLib::lib('trk');
		$trklib->change_status($objects, $todo['to']['status']);
	}

    /**
     * @param $todo
     * @param $objects
     */
    function notifyTodo_tracker($todo, $objects)
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		$trklib = TikiLib::lib('trk');
		$tikilib = TikiLib::lib('tiki');
		foreach ($objects as $object) {
			// get the creator
			$u = $object['field_values'][0]['value'];
			if (empty($u)) {
				$u = $object['itemUser'];
			}
			if (!empty($todo['to']['body'])) { // assign whatever needed
				$smarty->assign('todo_itemId', $object['itemId']);
				$status = $trklib->status_types();
				$smarty->assign('todo_tostatus', $status[$todo['for']['to']['status']]['label']);
				$smarty->assign('todo_fromstatus', $status[$todo['for']['from']['status']]['label']);
				$smarty->assign('todo_after', $todo['to']['before']);
				$smarty->assign('todo_desc', $trklib->get_isMain_value($object['trackerId'], $object['itemId']));
			}
			// mail creator
			$this->mailTodo($todo, array('user'=>$u), 'Tracker item status will be changed');
			//register as been mailed
			$this->addNotif($todo['todoId'], 'trackeritem', $object['itemId']);
		}
	}
}
