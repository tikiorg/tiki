<?php
//error_reporting(E_ALL);
include_once('class.iCal.inc.php');
$days = (array) array (2,3);
$organizer = (array) array('Kurt', 'kurt2@flaimo.com');
$categories = array('Freetime','Party');
$attendees = (array) array(
						  'Michi' => 'flaimo2@gmx.net,1',
						  'Felix' => ' ,2',
						  'Walter' => 'flaimo2@gmx.net,3'
						  );  // Name => e-mail,role (see iCalEvent class)

$fb_times = (array) array(
						  time()+23456 => time()+24456 . ',0', // timestamp start => 'timestamp end,status' (for status see class)
						  time()+93956 => time()+95956 . ',1'
						  );

$alarm = (array) array(
					  0, // Action: 0 = DISPLAY, 1 = EMAIL, (not supported: 2 = AUDIO, 3 = PROCEDURE)
					  150,  // Trigger: alarm before the event in minutes
					  'Wake Up!', // Title
					  '...and go shopping', // Description
					  $attendees, // Array (key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON])
					  5, // Duration between the alarms in minutes
					  3  // How often should the alarm be repeated
					  );

$ex_dates = (array) array(12345667,78643453);

$iCal = (object) new iCal('', 0, ''); // (ProgrammID, Method (1 = Publish | 0 = Request), Download Directory)

$iCal->addEvent(
				$organizer, // Organizer
				1048806000, // Start Time (timestamp; for an allday event the startdate has to start at YYYY-mm-dd 00:00:00)
				1048899000, // End Time (write 'allday' for an allday event instead of a timestamp)
				'Vienna', // Location
				0, // Transparancy (0 = OPAQUE | 1 = TRANSPARENT)
				$categories, // Array with Strings
				'See homepage for more details...', // Description
				'Air and Style Snowboard Contest', // Title
				2, // Class (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
				$attendees, // Array (key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON])
				5, // Priority = 0-9
				5, // frequency: 0 = once, secoundly - yearly = 1-7
				10, // recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
				2, // Interval for frequency (every 2,3,4 weeks...)
				$days, // Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
				0, // Startday of the Week ( 0 = Sunday - 6 = Saturday)
				'', // exeption dates: Array with timestamps of dates that should not be includes in the recurring event
				$alarm,  // Sets the time in minutes an alarm appears before the event in the programm. no alarm if empty string or 0
				1, // Status of the event (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
				'http://flaimo.com/', // optional URL for that event
				'de', // Language of the Strings
                '' // Optional UID for this event
			   );

//$iCal->addEvent(add more events...);

$iCal->addToDo(
			   'Air and Style Snowboard Contest', // Title
			   'See handout for more details...', // Description
			   'Vienna', // Location
			   time()+3600, // Start time
			   300, // Duration in minutes
			   '', // End time
			   45, // Percentage complete
			   5, // Priority = 0-9
			   1, // Status of the event (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
			   0, // Class (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
			   $organizer, // Organizer
			   $attendees, // Array (key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON])
			   $categories, // Array with Strings
			   time(), // Last Modification
			   $alarm, // Sets the time in minutes an alarm appears before the event in the programm. no alarm if empty string or 0
			   5, // frequency: 0 = once, secoundly - yearly = 1-7
			   10, // recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
			   1, // Interval for frequency (every 2,3,4 weeks...)
			   $days, // Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
			   0, // Startday of the Week ( 0 = Sunday - 6 = Saturday)
			   '', // exeption dates: Array with timestamps of dates that should not be includes in the recurring event
			   'http://flaimo.com/', // optional URL for that event
			   'de', // Language of the Strings
               '' // Optional UID for this ToDo
			  );


$iCal->addFreeBusy(
				   time()+3600, // Start Time
				   time()+7200, // End Time
				   300, // Duration in minutes
				   $organizer, // Organizer
				   $attendees, // Array (key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON])
				   $fb_times, // Array with all the free/busy times
				   '', // optional URL for that FreeBusy
                   '' // Optional UID for this FreeBusy
				  );


$iCal->addJournal(
				  'Air and Style Snowboard Contest', // Title
				  'See homepage for more details...', // Description
				  time()+3600, // Start time
				  time(), // Created
				  time(), // Last modification
				  1, // Status of the event (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
				  0, // Class (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
				  $organizer, // Organizer
				  $attendees, // Array (key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON])
				  $categories, // Array with Strings
				  5, // frequency: 0 = once, secoundly - yearly = 1-7
				  10, // recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
				  1, // Interval for frequency (every 2,3,4 weeks...)
				  $days, // Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
				  0, // Startday of the Week ( 0 = Sunday - 6 = Saturday)
				  $ex_dates, // exeption dates: Array with timestamps of dates that should not be includes in the recurring event
				  'http://flaimo.com/', // optional URL for that event
				  'de', // Language of the Strings
                  '' // Optional UID for this Journal
				  );

//echo $iCal->countiCalObjects();
$iCal->outputFile('ics'); // output file as ics (xcs and rdf possible)
?>
