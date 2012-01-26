<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!defined('weekInSeconds')) define('weekInSeconds', 604800);
if (!defined('dayInSeconds')) define('dayInSeconds', 86400);

class CalRecurrence extends TikiLib
{
	private $id;
	private $calendarId;
	private $start;
	private $end;
	private $allday;
	private $locationId;
	private $categoryId;
	private $nlId;
	private $priority;
	private $status;
	private $url;
	private $lang;
	private $name;
	private $description;
	private $weekly;
	private $weekday;
	private $monthly;
	private $dayOfMonth;
	private $yearly;
	private $dateOfYear; // format is mmdd
	private $nbRecurrences;
	private $startPeriod;
	private $endPeriod;
	private $user;
	private $created;
	private $lastModif;

    #region reminder

    private $reminderType;
    private $reminderFixedDate;
    private $reminderTimeOffset;
    private $reminderRelatedTo;
    private $reminderWhenRun;

    #endregion reminder

	public function CalRecurrence($param = -1) {
		parent::__construct();
		if ($param > 0)
			$this->setId($param);
		$this->load();
	}

	public function load() {
		if ($this->getId() > 0) {
			$query = "SELECT calendarId, start, end, allday, locationId, categoryId, nlId, priority, status, url, lang, name, description, weekly, weekday, monthly, dayOfMonth,"
				   . "yearly, dateOfYear, nbRecurrences, startPeriod, endPeriod, user, created, lastModif FROM tiki_calendar_recurrence "
				   . "WHERE recurrenceId = ?";
			$result = $this->query($query,array((int)$this->getId()));
			if ($row = $result->fetchRow()) {
				$this->setCalendarId($row['calendarId']);
				$this->setStart($row['start']);
				$this->setEnd($row['end']);
				$this->setAllday($row['allday']);
				$this->setLocationId($row['locationId']);
				$this->setCategoryId($row['categoryId']);
				$this->setNlId($row['nlId']);
				$this->setPriority($row['priority']);
				$this->setStatus($row['status']);
				$this->setUrl($row['url']);
				$this->setLang($row['lang']);
				$this->setName($row['name']);
				$this->setDescription($row['description']);
				$this->setWeekly($row['weekly'] == 1);
				$this->setWeekday($row['weekday']);
				$this->setMonthly($row['monthly'] == 1);
				$this->setDayOfMonth($row['dayOfMonth']);
				$this->setYearly($row['yearly'] == 1);
				$this->setDateOfYear($row['dateOfYear']);
				$this->setNbRecurrences($row['nbRecurrences']);
				$this->setStartPeriod($row['startPeriod']);
				$this->setEndPeriod($row['endPeriod']);
				$this->setUser($row['user']);
				$this->setCreated($row['created']);
				$this->setLastModif($row['lastModif']);
			}
		}
	}

	/**
	 * When updating the recurrence rule,
	 * we are offered the the option to update all the recurrent events already created
	 * (i.e. $updateManuallyChanged = true), or only the events for which the changes on the rules
	 * have no incidence on the changes done manually (i.e. fields changed in the rule are not the fields changed
	 * in the event)
	 */
	public function save($updateManuallyChangedEvents = false) {
		if (!$this->isValid())
			return false;
		if ($this->getId() > 0)
			return $this->update($updateManuallyChangedEvents);
		return $this->create();
	}

	/**
	 * Validation before storing (or updating) to the database.
	 * returns true if succeeds, false otherwise
	 */
	public function isValid() {
		// should be related to a calendar
		if (!($this->getCalendarId() > 0))
			return false;
		// should have valid start and end date
		if ( !($this->isAllday())
			 && (!($this->getStart() > 0) || !($this->getEnd() > 0) || ($this->getStart() > 2359) || ($this->getEnd() > 2359) || ($this->getStart() > $this->getEnd())))
			return false;
		// should be recurrent on "some" basis
		if (!$this->isWeekly() && !$this->isMonthly() && !$this->isYearly())
			return false;
		// recurrence should be correctly defined
		if (
			 ($this->isWeekly() && (is_null($this->getWeekday()) || $this->getWeekday() > 6 || $this->getWeekday() < 0 || $this->getWeekday() == ''))
		  || ($this->isMonthly() && (is_null($this->getDayOfMonth()) || $this->getDayOfMonth() > 31 || $this->getDayOfMonth() < 1 || $this->getDayOfMonth() == ''))
		  || ($this->isYearly() && (is_null($this->getDateOfYear()) || $this->getDateOfYear() > 1231 || $this->getDateOfYear() < 0101 || $this->getDateOfYear() == ''))
		   )
			return false;
		// recurrence period should be defined
		if (   (is_null($this->getNbRecurrences()) || ($this->getNbRecurrences() == '') || ($this->getNbRecurrences() == 0))
			&& (is_null($this->getEndPeriod()) || ($this->getEndPeriod() == '') || ($this->getEndPeriod() < $this->getStartPeriod())) )
			return false;
		//
		if (is_null($this->getNlId()))
			return false;
		// should inform the language
		if (is_null($this->getLang()) || $this->getLang() == "")
			return false;
		// should have a name
		if (is_null($this->getName()) || $this->getName() == "")
			return false;
		return true;
	}

	public function delete($fromTime=null) {
		if (is_null($fromTime))
			$fromTime = time();

        #region reminder

		$query = "DELETE FROM custom_calendar_reminder WHERE calendar_item_id IN (SELECT calitemId FROM tiki_calendar_items WHERE recurrenceId = ? AND start > ?)";
		$bindvars = array((int)$this->getId(),$fromTime);
		$this->query($query,$bindvars);

        #endregion reminder

		$query = "DELETE FROM tiki_calendar_items WHERE recurrenceId = ? AND start > ?";
		$bindvars = array((int)$this->getId(),$fromTime);
		$this->query($query,$bindvars);
		$query = "UPDATE tiki_calendar_items SET recurrenceId = NULL WHERE recurrenceId = ?";
		$bindvars = array((int)$this->getId());
		$this->query($query,$bindvars);
		$query = "DELETE FROM tiki_calendar_recurrence WHERE recurrenceId = ?";
		$bindvars = array((int)$this->getId());
		return $this->query($query,$bindvars);
	}

	private function create() {
		$query = "INSERT INTO tiki_calendar_recurrence (calendarId, start, end, allday, locationId, categoryId, nlId, priority, status, url, lang, name, description, "
			   . "weekly, weekday, monthly, dayOfMonth,yearly, dateOfYear, nbRecurrences, startPeriod, endPeriod, user, created, lastModif) "
			   . "VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$now = time();
		$bindvars = array(
						$this->getCalendarId(),
						$this->getStart(),
						$this->getEnd(),
						$this->isAllday() ? 1 : 0,
						$this->getLocationId(),
						$this->getCategoryId(),
						$this->getNlId(),
						$this->getPriority(),
						$this->getStatus(),
						$this->getUrl(),
						$this->getLang(),
						$this->getName(),
						$this->getDescription(),
						$this->isWeekly() ? 1 : 0,
						$this->getWeekday(),
						$this->isMonthly() ? 1 : 0,
						$this->getDayOfMonth(),
						$this->isYearly() ? 1 : 0,
						$this->getDateOfYear(),
						$this->getNbRecurrences(),
						$this->getStartPeriod(),
						$this->getEndPeriod(),
						$this->getUser(),
						$now,
						$now
					 );
		$result = $this->query($query,$bindvars);
		if ($result) {
			$this->setId($this->GetOne("SELECT `recurrenceId` FROM `tiki_calendar_recurrence` WHERE `created`=?",array($now)));
			if ($this->getId() > 0) {
				// create the recurrent events
				$this->createEvents();
				return true;
			}
		}
		return false;
	}

	private function update($updateManuallyChangedEvents = false) {
		$query = "UPDATE tiki_calendar_recurrence SET calendarId = ?, start = ?, end = ?, allday = ?, locationId = ?, categoryId = ?, nlId = ?, priority = ?, status = ?, "
			   . "url = ?, lang = ?, name = ?, description = ?, weekly = ?, weekday = ?, monthly = ?, dayOfMonth = ?, yearly = ?, dateOfYear = ?, nbRecurrences = ?, "
			   . "startPeriod = ?, endPeriod = ?, user = ?, lastModif = ? WHERE recurrenceId = ?";
		$now = time();
		$bindvars = array(
						$this->getCalendarId(),
						$this->getStart(),
						$this->getEnd(),
						$this->isAllday() ? 1 : 0,
						$this->getLocationId(),
						$this->getCategoryId(),
						$this->getNlId(),
						$this->getPriority(),
						$this->getStatus(),
						$this->getUrl(),
						$this->getLang(),
						$this->getName(),
						$this->getDescription(),
						$this->isWeekly() ? 1 : 0,
						$this->getWeekday(),
						$this->isMonthly() ? 1 : 0,
						$this->getDayOfMonth(),
						$this->isYearly() ? 1 : 0,
						$this->getDateOfYear(),
						$this->getNbRecurrences(),
						$this->getStartPeriod(),
						$this->getEndPeriod(),
						$this->getUser(),
						$now,
						$this->getId()
					 );
		$oldRec = new CalRecurrence($this->getId()); // we'll need old version to compare fields.
		$result = $this->query($query,$bindvars);
		if ($result) {
			// update the recurrent events, according to the way to handle the already changed events
			$this->updateEvents($updateManuallyChangedEvents, $oldRec);
			return true;
		}
		return false;
	}

	public function createEvents() {
		// calculate the date of every events to create
		$dates = array();
		$start = TikiLib::date_format2('Y/m/d',$this->getStartPeriod());
		$start = explode("/",$start);
		if ($this->getNbRecurrences() > 0) {
			$nbRec = $this->getNbRecurrences();
			if ($this->isWeekly()) {
				$startWeekday = TikiLib::date_format2('w',$this->getStartPeriod()); // 0->Sunday, 6->Saturday
				$firstEventDate = $this->getWeekday() - $startWeekday;
				if ($firstEventDate < 0)
					$firstEventDate += 7;
				for ($i=0 ; $i < $nbRec ; $i++) {
					$dates[] = TikiLib::make_time(0,0,0,$start[1],$start[2] + $firstEventDate + ($i * 7),$start[0]);
				}
			} elseif ($this->isMonthly()) {
				$firstIsNextMonth = ($this->getDayOfMonth() < $start[2]);
				$startMonth = $firstIsNextMonth ? $start[1] + 1 : $start[1];
				$occurrences = 0;
				for ($i=0 ; $occurrences < $nbRec ; $i++) {
					$nbDaysInMonth = date('t',TikiLib::make_time(0,0,0,$startMonth + $i,1,$start[0]));
					if ($this->getDayOfMonth() > $nbDaysInMonth)
						continue;
					$dates[] = TikiLib::make_time(0,0,0,$startMonth + $i,$this->getDayOfMonth(),$start[0]);
					$occurrences++;
				}
			} elseif ($this->isYearly()) {
				$yymm = TikiLib::date_format2('md',$this->getStartPeriod());
				$isLeapDay = ($this->getDateOfYear() == 229); // Feb 29th case.
				$offset = ($this->getDateOfYear() < $yymm) ? 1 : 0;
				$dt = str_pad($this->getDateOfYear(),4,"0",STR_PAD_LEFT);
				$occurrences = 0;
				for ($i=0 ; $occurrences < $nbRec ; $i++) {
					if ($isLeapDay) {
						if ( TikiLib::date_format2('L', TikiLib::make_time(0,0,0,1,1,$start[0] + $offset + $i)) == 0 ) {
							continue;
						}
					}
					$dates[] = TikiLib::make_time(0,0,0,substr($dt,0,2),substr($dt,-2),$start[0] + $offset + $i);
					$occurrences++;
				}
			} else {
				// there should be no other case
				return false;
			}
		} elseif ($this->getEndPeriod() > 0) {
			if ($this->isWeekly()) {
				$startWeekday = TikiLib::date_format2('w',$this->getStartPeriod()); // 0->Sunday, 6->Saturday
				$firstEventDate = $this->getWeekday() - $startWeekday;
				if ($firstEventDate < 0)
					$firstEventDate += 7;
				$currDate = TikiLib::make_time(0,0,0,$start[1],$start[2] + $firstEventDate,$start[0]);
				while ($currDate < $this->getEndPeriod()) {
					$dates[] = $currDate;
					$currDate += weekInSeconds;
				}
			} elseif ($this->isMonthly()) {
				$firstIsNextMonth = ($this->getDayOfMonth() < $start[2]);
				$startMonth = $firstIsNextMonth ? $start[1] + 1 : $start[1];
				$currDate = TikiLib::make_time(0,0,0,$startMonth,$this->getDayOfMonth(),$start[0]);
				$i = 0;
				while ($currDate < $this->getEndPeriod()) {
					$nbDaysInMonth = TikiLib::date_format2('t',TikiLib::make_time(0,0,0,$startMonth + $i,1,$start[0]));
					if ($this->getDayOfMonth() > $nbDaysInMonth) {
						$i++;
						$currDate = TikiLib::make_time(0,0,0,substr($dt,0,2),substr($dt,-2),$start[0] + $offset + $i);
						continue;
					}
					$dates[] = $currDate;
					$i++;
					$currDate = TikiLib::make_time(0,0,0,$startMonth + $i,$this->getDayOfMonth(),$start[0]);
				}
			} elseif ($this->isYearly()) {
				$yymm = TikiLib::date_format2('md',$this->getStartPeriod());
				$isLeapDay = ($this->getDateOfYear() == 229); // Feb 29th case.
				$offset = ($this->getDateOfYear() < $yymm) ? 1 : 0;
				$dt = str_pad($this->getDateOfYear(),4,"0",STR_PAD_LEFT);
				$currDate = TikiLib::make_time(0,0,0,substr($dt,0,2),substr($dt,-2),$start[0] + $offset);
				$i = 0;
				while ($currDate < $this->getEndPeriod()) {
					if ($isLeapDay) {
						if (TikiLib::date_format2('L',TikiLib::make_time(0,0,0,1,1,$start[0] + $offset + $i)) == 0) {
							$i++;
							$currDate = TikiLib::make_time(0,0,0,substr($dt,0,2),substr($dt,-2),$start[0] + $offset + $i);
							continue;
						}
					}
					$dates[] = $currDate;
					$i++;
					$currDate = TikiLib::make_time(0,0,0,substr($dt,0,2),substr($dt,-2),$start[0] + $offset + $i);
				}
			} else {
				// there should be no other case
				return false;
			}
		} else {
			// there should be no other case
			return false;
		}
		// create and store the events
		// get start and end hours in seconds from midnight, if event is not allday.
		$startOffset = 0;
		$endOffset = dayInSeconds - 1;
		if (!$this->isAllday()) {
			$tmp = str_pad($this->getStart(),4,'0',STR_PAD_LEFT);
			$startOffset = substr($tmp,0,2) * 60 * 60 + substr($tmp,-2) * 60;
			$tmp = str_pad($this->getEnd(),4,'0',STR_PAD_LEFT);
			$endOffset = substr($tmp,0,2) * 60 * 60 + substr($tmp,-2) * 60;
		}

		foreach($dates as $aDate) {
			$data = array(
							'calendarId'=>$this->getCalendarId(),
							'start'=>($aDate + $startOffset),
							'end'=>($aDate + $endOffset),
							'locationId'=>$this->getLocationId(),
							'categoryId'=>$this->getCategoryId(),
							'nlId'=>$this->getNlId(),
							'priority'=>$this->getPriority(),
							'status'=>$this->getStatus(),
							'url'=>$this->getUrl(),
							'lang'=>$this->getLang(),
							'name'=>$this->getName(),
							'description'=>$this->getDescription(),
							'user'=>$this->getUser(),
							'created'=>$this->getCreated(),
							'lastmodif'=>$this->getCreated(),
							'allday'=>$this->isAllday(),
							'recurrenceId'=>$this->getId(),
							'changed'=>0,

                            #region reminder

                            'reminderType' => $this->getReminderType(),
                            'reminderFixedDate' => $this->getReminderFixedDate(),
                            'reminderTimeOffset' => $this->getReminderTimeOffset(),
                            'reminderRelatedTo' => $this->getReminderRelatedTo(),
                            'reminderWhenRun' => $this->getReminderWhenRun()

                            #endregion reminder
						 );

			$calendarlib = new calendarlib($this->db);
			global $user;
			$calendarlib->set_item($user,null,$data);
		}
	}

		public function updateEvents($updateManuallyChangedEvents = false, $oldRec) {
			// retrieve the yet-to-happen events of the recurrence rule (only the relevant fields)
			$query = "SELECT calitemId,calendarId, start, end, allday, locationId, categoryId, nlId, priority, status, url, lang, name, description, "
				   . "user, created, lastModif, changed "
				   . "FROM tiki_calendar_items WHERE recurrenceId = ? ORDER BY start";
			$bindvars = array((int)$this->getId());
			$result = $this->query($query,$bindvars);
			$theEvents = array();
			$theEventsToBeChanged = array();
			if ($updateManuallyChangedEvents) {
				while($row = $result->fetchRow()) {
					$theEvents[$row['calitemId']] = $row;
					$theEventsToBeChanged[] = $row['calitemId'];
				}
			} else {
				while($row = $result->fetchRow()) {
					$theEvents[$row['calitemId']] = $row;
					if ($row['changed'] == 0)
						$theEventsToBeChanged[] = $row['calitemId'];
				}
			}
			// we'll now update the events, according to the manually changed flag.
			// we'll check only the new fields; so we should retrieve the list of changed fields first.
			$changedFields = $this->compareFields($oldRec);
			if ($updateManuallyChangedEvents == false) {
				foreach($theEvents as $evtId => $anEvent) {
					if ($anEvent['changed'] == 1)
						continue;
					foreach($changedFields as $aField) {
						$changedFieldsOfEvent = $this->compareFieldsOfEvent($anEvent,$oldRec);
						$inters = array_intersect($changedFields,$changedFieldsOfEvent);
						if (count($inters) == 0)
							$theEventsToBeChanged[] = $anEvent['calitemId'];
					}
				}
			}
			// we now update the events
			$advanced = null;
			$ChangeDateInSeconds; //will be needed if dates have been changed
			foreach ($theEventsToBeChanged as $anEvtId) {
				$anEvt = $theEvents[$anEvtId];
				$tmp = array();
                $bindvars = array();
				$doWeChangeTimeIfNeeded = true;
				foreach($changedFields as $aField) {
					if (substr($aField,0,1) != "_") {
						$tmp[] = $aField . " = ?";
                        $bindvars[] = $this->$aField;
					} else {
						$anEvtStart = TikiLib::date_format2('Y/m/d',$anEvt['start']);
						$anEvtStart = explode('/',$anEvtStart);
						$newStartHour = floor($this->getStart()/100);
						$newStartMin = $this->getStart() - 100*$newStartHour;
						$newEndHour = floor($this->getEnd()/100);
						$newEndMin = $this->getEnd() - 100*$newEndHour;
						// the tricky part...if the dates should be changed.
						// here are the rules :
						// - for weekly events :
						// if the new weekday is before (less than) the old weekday
						//		if this week's new weekday is after (greater than) now and after the startperiod
						//			then dates will be advanced
						// else
						//		then dates will be postponed
						if ($aField == "_weekday") {
							$doWeChangeTimeIfNeeded = false;
							if (is_null($advanced)) {
								if ($this->getWeekday() < $oldRec->getWeekday()) {
									if (TikiLib::date_format2('w',TikiLib::make_time()) <= $this->getWeekday()) {
										$offsetInSeconds = ($this->getWeekday() - TikiLib::date_format2('w',TikiLib::make_time())) * dayInSeconds;
										$couldBeDay = TikiLib::make_time($newStartHour,$newStartMin,0,TikiLib::date_format2('m'),TikiLib::date_format2('d'),TikiLib::date_format2('Y')) + $offsetInSeconds;
										if ($couldBeDay >= $this->getStartPeriod()) {
											$advanced = true;
										}
									}
								}
							}
							if (is_null($advanced)) {
								$advanced = false;
							}
							if (!is_null($advanced)) {
								$daysOffsetEvent = $this->getWeekday() - TikiLib::date_format2('w',TikiLib::make_time(0,0,0,$anEvtStart[1],$anEvtStart[2],$anEvtStart[0]));
								$tmp[] = "start=?";
                                $bindvars[] = TikiLib::make_time($newStartHour,$newStartMin,0,$anEvtStart[1],$anEvtStart[2] + $daysOffsetEvent,$anEvtStart[0]);
								$tmp[] = "end=?";
                                $bindvars[] = TikiLib::make_time($newEndHour,$newEndMin,0,$anEvtStart[1],$anEvtStart[2] + $daysOffsetEvent,$anEvtStart[0]);
							}
						}
						// - for monthly events :
						// if the new day of month is before (less than) the old day of month
						//		if this month's new day is after (greater than) now and after the startperiod
						//			then dates will be advanced
						// else
						//		then dates will be postponed
						elseif ($aField == "_dayOfMonth") {
							$doWeChangeTimeIfNeeded = false;
							if (is_null($advanced)) {
								if ($this->getDayOfMonth() < $oldRec->getDayOfMonth()) {
									if (TikiLib::date_format2('d',TikiLib::make_time()) <= $this->getDayOfMonth()) {
										$offsetInSeconds = ($this->getDayOfMonth() - TikiLib::date_format2('d',TikiLib::make_time())) * dayInSeconds;
										$couldBeDay = TikiLib::make_time(0,0,0,TikiLib::date_format2('m',TikiLib::make_time()),TikiLib::date_format2('d',TikiLib::make_time()),TikiLib::date_format2('Y',TikiLib::make_time())) + $offsetInSeconds;
										if ($couldBeDay >= $this->getStartPeriod()) {
											$advanced = true;
										}
									}
								}
							}
							if (is_null($advanced))
								$advanced = false;

							if ($advanced) {
								// we are on the same month
								$tmp[] = "start=?";
                                $bindvars[] = TikiLib::make_time($newStartHour, $newStartMin, 0, $anEvtStart[1], $this->getDayOfMonth(), $anEvtStart[0]);
								$tmp[] = "end=?";
                                $bindvars[] = TikiLib::make_time($newEndHour, $newEndMin, 0, $anEvtStart[1], $this->getDayOfMonth(), $anEvtStart[0]);
							} else {
								// if the new day is after the old one, we are on the same month; instead, we are on the next month.
								$offsetMonth = 0;
								if ($this->getDayOfMonth() < ($oldRec->getDayOfMonth()))
									$offsetMonth = 1;
								$tmp[] = "start=?";
                                $bindvars[] = TikiLib::make_time($newStartHour, $newStartMin, 0, $anEvtStart[1] + $offsetMonth, $this->getDayOfMonth(), $anEvtStart[0]);
								$tmp[] = "end=?";
                                $bindvars[] = TikiLib::make_time($newEndHour, $newEndMin, 0, $anEvtStart[1] + $offsetMonth, $this->getDayOfMonth(), $anEvtStart[0]);
							}
						}
						// - for yearly events :
						// if the new day of year is before (less than) the old day of year
						//		if this year's new day is after (greater than) now and after the startperiod
						//			then dates will be advanced
						// else
						//		then dates will be postponed
						elseif ($aField == "_dateOfYear") {
							$doWeChangeTimeIfNeeded = false;
							if (is_null($advanced)) {
								$thisdate = str_pad($this->getDateOfYear(),4,'0',STR_PAD_LEFT);
								$olddate = str_pad($oldRec->getDateOfYear(),4,'0',STR_PAD_LEFT);

								if ($this->getDateOfYear() < $oldRec->getDateOfYear()) {
									if (time() <= TikiLib::make_time($newStartHour,$newStartMin,0,substr($thisdate,0,2),substr($thisdate,-2),TikiLib::date_format2('Y'))) {
										$offsetInSeconds = TikiLib::make_time($newStartHour,$newStartMin,0,substr($thisdate,0,2),substr($thisdate,-2),TikiLib::date_format2('Y')) - $this->getStartPeriod();
										if ($offsetInSeconds > 0) {
											$advanced = true;
										}
									}
								}
							}
							if (is_null($advanced)) {
								$advanced = false;
							}
							if ($advanced) {
								$tmp[] = "start=?";
                                $bindvars[] = TikiLib::make_time($newStartHour, $newStartMin, 0,substr($thisdate,0,2),substr($thisdate,-2), $anEvtStart[0]);
								$tmp[] = "end=?";
                                $bindvars[] = TikiLib::make_time($newEndHour, $newEndMin, 0, substr($thisdate,0,2),substr($thisdate,-2), $anEvtStart[0]);
							} else {
								$offsetYear = 0;
								if ($this->getDateOfYear() < $oldRec->getDateOfYear())
									$offsetYear = 1;
								$tmp[] = "start=?";
                                $bindvars[] = TikiLib::make_time($newStartHour, $newStartMin, 0, substr($thisdate,0,2),substr($thisdate,-2), $anEvtStart[0] + $offsetYear);
								$tmp[] = "end=?";
                                $bindvars[] = TikiLib::make_time($newEndHour, $newEndMin, 0, substr($thisdate,0,2),substr($thisdate,-2), $anEvtStart[0] + $offsetYear);
							}
						}
/*						 elseif ($aField == "_start") {
							// on fera la modif si les trois précédents n'ont pas été concernés
						} elseif ($aField == "_end") {
								// on fera la modif si les trois précédents n'ont pas été concernés
						}
*/
					}
				}
				if (in_array("_start",$changedFields) && $doWeChangeTimeIfNeeded) {
					$anEvtStart = TikiLib::date_format2('Y/m/d',$anEvt['start']);
					$anEvtStart = explode('/',$anEvtStart);
					$newStartHour = floor($this->getStart()/100);
					$newStartMin = $this->getStart() - 100*$newStartHour;
					$tmp[] = "start=?";
                    $bindvars[] = TikiLib::make_time($newStartHour, $newStartMin, 0, $anEvtStart[1], $anEvtStart[2], $anEvtStart[0]);
				}
				if (in_array("_end",$changedFields) && $doWeChangeTimeIfNeeded) {
					$anEvtStart = TikiLib::date_format2('Y/m/d',$anEvt['start']);
					$anEvtStart = explode('/',$anEvtStart);
					$newEndHour = floor($this->getEnd()/100);
					$newEndMin = $this->getEnd() - 100*$newEndHour;
					$tmp[] = "end=?";
                    $bindvars[] = TikiLib::make_time($newEndHour, $newEndMin, 0, $anEvtStart[1], $anEvtStart[2], $anEvtStart[0]);
				}
				if (count($tmp) > 0) {
					$query = "UPDATE tiki_calendar_items SET " . implode(',',$tmp) . " WHERE calitemId = ?";
					$bindvars[] = (int) $anEvt['calitemId'];
					$this->query($query,$bindvars);
				}

                #region reminder

                $reminder = array();

                $reminder['reminder_type'] = $this->getReminderType();
                $reminder['reminder_fixed_date'] = $this->getReminderFixedDate();
                $reminder['reminder_time_offset'] = $this->getReminderTimeOffset();
                $reminder['reminder_related_to'] = $this->getReminderRelatedTo();
                $reminder['reminder_when_run'] = $this->getReminderWhenRun();

			    $calendarlib = new calendarlib($this->db);
                $calendarlib->persistReminder($anEvt['calitemId'], $reminder);

                #endregion reminder
			}
		}

	public function compareFields($oldRec) {
		$result = array();
		if ($this->getCalendarId() != $oldRec->getCalendarId())
			$result[] = "calendarId";
		if ($this->getStart() != $oldRec->getStart())
			$result[] = "_start";
		if ($this->getEnd() != $oldRec->getEnd())
			$result[] = "_end";
		if ($this->isAllday() != $oldRec->isAllday())
			$result[] = "allday";
		if ($this->getLocationId() != $oldRec->getLocationId() && !($this->getLocationId() == '' && $oldRec->getLocationId() == 0))
			$result[] = "locationId";
		if ($this->getCategoryId() != $oldRec->getCategoryId() && !($this->getCategoryId() == '' && $oldRec->getCategoryId() == 0))
			$result[] = "categoryId";
		if ($this->getNlId() != $oldRec->getNlId())
			$result[] = "nlId";
		if ($this->getPriority() != $oldRec->getPriority() && !($oldRec->getPriority() == '' && $oldRec->getPriority() == 0))
			$result[] = "priority";
		if ($this->getStatus() != $oldRec->getStatus())
			$result[] = "status";
		if ($this->getUrl() != $oldRec->getUrl())
			$result[] = "url";
		if ($this->getLang() != $oldRec->getLang())
			$result[] = "lang";
		if ($this->getName() != $oldRec->getName())
			$result[] = "name";
		if ($this->getDescription() != $oldRec->getDescription())
			$result[] = "description";
		if ($this->isWeekly() && ($this->getWeekday() != $oldRec->getWeekday()))
			$result[] = "_weekday";
		if ($this->isMonthly() && ($this->getDayOfMonth() != $oldRec->getDayOfMonth()))
			$result[] = "_dayOfMonth";
		if ($this->isYearly() && ($this->getDateOfYear() != $oldRec->getDateOfYear()))
			$result[] = "_dateOfYear";
		return $result;
	}

	public function compareFieldsOfEvent($evt,$oldRec) {
		$result = array();
		if ($evt['calendarId'] != $oldRec->getCalendarId())
			$result[] = "calendarId";
		if (TikiLib::date_format2('Hi',$evt['start']) != $oldRec->getStart())
			$result[] = "start";
		// checking the end is double check : is it the right hour ? is it the same day ?
		if ((TikiLib::date_format2('Hi',$evt['end']) != $oldRec->getEnd()) || (TikiLib::date_format2('Ymd',$evt['start']) != TikiLib::date_format2('Ymd',$evt['end'])))
			$result[] = "end";
		if ($evt['allday'] != $oldRec->isAllday())
			$result[] = "allday";
		if ($evt['locationId'] != $oldRec->getLocationId())
			$result[] = "locationId";
		if ($evt['categoryId'] != $oldRec->getCategoryId())
			$result[] = "categoryId";
		if ($evt['nlId'] != $oldRec->getNlId())
			$result[] = "nlId";
		if ($evt['priority'] != $oldRec->getPriority())
			$result[] = "priority";
		if ($evt['status'] != $oldRec->getStatus())
			$result[] = "status";
		if ($evt['url'] != $oldRec->getUrl())
			$result[] = "url";
		if ($evt['lang'] != $oldRec->getLang())
			$result[] = "lang";
		if ($evt['name'] != $oldRec->getName())
			$result[] = "name";
		if ($evt['description'] != $oldRec->getDescription())
			$result[] = "description";
		if (TikiLib::date_format2('Hi',$evt['start']) != str_pad($oldRec->getStart(),4,"0",STR_PAD_LEFT))
			$result[] = "_start";
		if (TikiLib::date_format2('Hi',$evt['end']) != str_pad($oldRec->getEnd(),4,"0",STR_PAD_LEFT))
			$result[] = "_end";
		if ($oldRec->isWeekly()) {
			if (TikiLib::date_format2('w',$evt['start']) != $oldRec->getWeekday())
				$result[] = "_weekday";
		} elseif ($oldRec->isMonthly()) {
			if (TikiLib::date_format2('d',$evt['start']) != $oldRec->getDayOfMonth())
				$result[] = "_dayOfMonth";
		} elseif ($oldRec->isYearly()) {
			if (TikiLib::date_format2('md',$evt['start']) != $oldRec->getDateOfYear())
				$result[] = "_dateOfYear";
		}
		return $result;
	}

	public function toArray() {
		return array(
		'id' => $this->getId(),
		'weekly' => $this->isWeekly(),
		'weekday' => $this->getWeekday(),
		'monthly' => $this->isMonthly(),
		'dayOfMonth' => $this->getDayOfMonth(),
		'yearly' => $this->isYearly(),
		'dateOfYear' => $this->getDateOfYear(),
		'dateOfYear_month' => floor($this->getDateOfYear()/100),
		'dateOfYear_day' => $this->getDateOfYear() - 100*floor($this->getDateOfYear()/100),
		'nbRecurrences' => $this->getNbRecurrences(),
		'startPeriod' => $this->getStartPeriod(),
		'endPeriod' => $this->getEndPeriod(),
		'user' => $this->getUser(),
		'created' => $this->getCreated(),
		'lastModif' => $this->getLastModif(),

        #region reminder

        'reminderType' => $this->getReminderType(),
        'reminderFixedDate' => $this->getReminderFixedDate(),
        'reminderTimeOffset' => $this->getReminderTimeOffset(),
        'reminderRelatedTo' => $this->getReminderRelatedTo(),
        'reminderWhenRun' => $this->getReminderWhenRun()

        #endregion reminder
		);
	}

	public function getId() { return $this->id; }
	public function setId($value) { $this->id = $value; }

	public function getCalendarId() { return $this->calendarId; }
	public function setCalendarId($value) { $this->calendarId = $value; }

	public function getStart() { return $this->start; }
	public function setStart($value) { $this->start = $value; }

	public function getEnd() { return $this->end; }
	public function setEnd($value) { $this->end = $value; }

	public function isAllday() { return $this->allday; }
	public function setAllday($value) { $this->allday = $value; }

	public function getLocationId() { return $this->locationId; }
	public function setLocationId($value) { $this->locationId = $value; }

	public function getCategoryId() { return $this->categoryId; }
	public function setCategoryId($value) { $this->categoryId = $value; }

	public function getNlId() { return $this->nlId; }
	public function setNlId($value) { $this->nlId = $value; }

	public function getPriority() { return $this->priority; }
	public function setPriority($value) { $this->priority = $value; }

	public function getStatus() { return $this->status; }
	public function setStatus($value) { $this->status = $value; }

	public function getUrl() { return $this->url; }
	public function setUrl($value) { $this->url = $value; }

	public function getLang() { return $this->lang; }
	public function setLang($value) { $this->lang = $value; }

	public function getName() { return $this->name; }
	public function setName($value) { $this->name = $value; }

	public function getDescription() { return $this->description; }
	public function setDescription($value) { $this->description = $value; }

	public function isWeekly() { return $this->weekly; }
	public function setWeekly($value) { $this->weekly = $value; }

	public function getWeekday() { return $this->weekday; }
	public function setWeekday($value) { $this->weekday = $value; }

	public function isMonthly() { return $this->monthly; }
	public function setMonthly($value) { $this->monthly = $value; }

	public function getDayOfMonth() { return $this->dayOfMonth; }
	public function setDayOfMonth($value) { $this->dayOfMonth = $value; }

	public function isYearly() { return $this->yearly; }
	public function setYearly($value) { $this->yearly = $value; }

	public function getDateOfYear() { return $this->dateOfYear; }
	public function setDateOfYear($value) { $this->dateOfYear = $value; }

	public function getNbRecurrences() { return $this->nbRecurrences; }
	public function setNbRecurrences($value) { $this->nbRecurrences = $value; }

	public function getStartPeriod() { return $this->startPeriod; }
	public function setStartPeriod($value) { $this->startPeriod = $value; }

	public function getEndPeriod() { return $this->endPeriod; }
	public function setEndPeriod($value) { $this->endPeriod = $value; }

	public function getUser() { return $this->user; }
	public function setUser($value) { $this->user = $value; }

	public function getCreated() { return $this->created; }
	public function setCreated($value) { $this->created = $value; }

	public function getLastModif() { return $this->lastModif; }
	public function setLastModif($value) { $this->lastModif = $value; }

    #region reminder

    public function getReminderType() { return $this->reminderType; }
    public function setReminderType($value) { $this->reminderType = $value; }

    public function getReminderFixedDate() { return $this->reminderFixedDate; }
    public function setReminderFixedDate($value) { $this->reminderFixedDate = $value; }

    public function getReminderTimeOffset() { return $this->reminderTimeOffset; }
    public function setReminderTimeOffset($value) { $this->reminderTimeOffset = $value; }

    public function getReminderRelatedTo() { return $this->reminderRelatedTo; }
    public function setReminderRelatedTo($value) { $this->reminderRelatedTo = $value; }

    public function getReminderWhenRun() { return $this->reminderWhenRun; }
    public function setReminderWhenRun($value) { $this->reminderWhenRun = $value; }

    #endregion reminder
}
