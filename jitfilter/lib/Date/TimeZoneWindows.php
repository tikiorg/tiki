<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The PHP Group                                     |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Ross Smith                                                  |
// |                                                                      |
// +----------------------------------------------------------------------+
//
// $Id: TimeZoneWindows.php,v 1.8 2005-05-18 10:59:50 mose Exp $
//
// Date_TimeZone Class Windows Support File
//

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * This class includes Windows time zone data (from zoneinfo) in the form of a global array,
 * $_DATE_TIMEZONE_DATA_WINDOWS.
 *
 * @author Ross Smith
 * @package Date
 * @access public
 * @version 1.0
 */

$GLOBALS['_DATE_TIMEZONE_DATA_WINDOWS'] = array(
    'Dateline Standard Time'            => 'Etc/GMT+12',                # (GMT-12:00) Eniwetok, Kwajalein   Dateline Daylight Time
    'Samoa Standard Time'               => 'Pacific/Samoa',             # (GMT-11:00) Midway Island, Samoa  Samoa Daylight Time
    'Hawaiian Standard Time'            => 'HST',                       # (GMT-10:00) Hawaii    Hawaiian Daylight Time
    'Alaskan Standard Time'             => 'AST',                       # (GMT-09:00) Alaska    Alaskan Daylight Time
    'Pacific Standard Time'             => 'PST',                       # (GMT-08:00) Pacific Time (US & Canada); Tijuana   Pacific Daylight Time
    'Mountain Standard Time'            => 'MST',                       # (GMT-07:00) Mountain Time (US & Canada)   Mountain Daylight Time
    'US Mountain Standard Time'         => 'US/Mountain',               # (GMT-07:00) Arizona   US Mountain Daylight Time
    'Canada Central Standard Time'      => 'Canada/Central',            # (GMT-06:00) Saskatchewan  Canada Central Daylight Time
    'Mexico Standard Time'              => 'Mexico/General',            # (GMT-06:00) Mexico City   Mexico Daylight Time
    'Central Standard Time'             => 'CST',                       # (GMT-06:00) Central Time (US & Canada)    Central Daylight Time
    'Central America Standard Time'     => 'CST',                       # (GMT-06:00) Central America   Central America Daylight Time
    'US Eastern Standard Time'          => 'EST',                       # (GMT-05:00) Indiana (East)    US Eastern Daylight Time
    'Eastern Standard Time'             => 'EST',                       # (GMT-05:00) Eastern Time (US & Canada)    Eastern Daylight Time
    'SA Pacific Standard Time'          => 'EST',                       # (GMT-05:00) Bogota, Lima, Quito   SA Pacific Daylight Time
    'Pacific SA Standard Time'          => 'America/Anguilla',          # (GMT-04:00) Santiago  Pacific SA Daylight Time
    'SA Western Standard Time'          => 'America/Anguilla',          # (GMT-04:00) Caracas, La Paz   SA Western Daylight Time
    'Atlantic Standard Time'            => 'America/Anguilla',          # (GMT-04:00) Atlantic Time (Canada)    Atlantic Daylight Time
    'Newfoundland Standard Time'        => 'America/St_Johns',          # (GMT-03:30) Newfoundland  Newfoundland Daylight Time
    'Greenland Standard Time'           => 'America/Godthab',           # (GMT-03:00) Greenland Greenland Daylight Time
    'SA Eastern Standard Time'          => 'America/Araguaina',         # (GMT-03:00) Buenos Aires, Georgetown  SA Eastern Daylight Time
    'E. South America Standard Time'    => 'America/Araguaina',         # (GMT-03:00) Brasilia  E. South America Daylight Time
    'Mid-Atlantic Standard Time'        => 'Atlantic/South_Georgia',    # (GMT-02:00) Mid-Atlantic  Mid-Atlantic Daylight Time
    'Cape Verde Standard Time'          => 'Atlantic/Cape_Verde',       # (GMT-01:00) Cape Verde Is.    Cape Verde Daylight Time
    'Azores Standard Time'              => 'Atlantic/Azores',           # (GMT-01:00) Azores    Azores Daylight Time
    'Greenwich Standard Time'           => 'GMT',                       # (GMT+00:00) Casablanca, Monrovia  Greenwich Daylight Time
    'GMT Standard Time'                 => 'GMT',                       # (GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London   GMT Daylight Time
    'W. Europe Standard Time'           => 'ECT',                       # (GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna  W. Europe Daylight Time
    'Central Europe Standard Time'      => 'ECT',                       # (GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague Central Europe Daylight Time
    'Romance Standard Time'             => 'ECT',                       # (GMT+01:00) Brussels, Copenhagen, Madrid, Paris   Romance Daylight Time
    'Central European Standard Time'    => 'ECT',                       # (GMT+01:00) Sarajevo, Skopje, Sofija, Vilnius, Warsaw, Zagreb Central European Daylight Time
    'W. Central Africa Standard Time'   => 'ECT',                       # (GMT+01:00) West Central Africa   W. Central Africa Daylight Time
    'GTB Standard Time'                 => 'ART',                       # (GMT+02:00) Athens, Istanbul, Minsk   GTB Daylight Time
    'E. Europe Standard Time'           => 'EET',                       # (GMT+02:00) Bucharest E. Europe Daylight Time
    'Egypt Standard Time'               => 'Egypt',                     # (GMT+02:00) Cairo Egypt Daylight Time
    'South Africa Standard Time'        => 'Africa/Johannesburg',       # (GMT+02:00) Harare, Pretoria  South Africa Daylight Time
    'FLE Standard Time'                 => 'ART',                       # (GMT+02:00) Helsinki, Riga, Tallinn   FLE Daylight Time
    'Jerusalem Standard Time'           => 'Israel',                    # (GMT+02:00) Jerusalem Jerusalem Daylight Time
    'Arabic Standard Time'              => 'Asia/Aden',                 # (GMT+03:00) Baghdad   Arabic Daylight Time
    'Arab Standard Time'                => 'Asia/Riyadh',               # (GMT+03:00) Kuwait, Riyadh    Arab Daylight Time
    'Russian Standard Time'             => 'Europe/Moscow',             # (GMT+03:00) Moscow, St. Petersburg, Volgograd Russian Daylight Time
    'E. Africa Standard Time'           => 'EAT',                       # (GMT+03:00) Nairobi   E. Africa Daylight Time
    'Iran Standard Time'                => 'Asia/Tehran',               # (GMT+03:30) Tehran    Iran Daylight Time
    'Arabian Standard Time'             => 'Asia/Dubai',                # (GMT+04:00) Abu Dhabi, Muscat Arabian Daylight Time
    'Caucasus Standard Time'            => 'Asia/Baku',                 # (GMT+04:00) Baku, Tbilisi, Yerevan    Caucasus Daylight Time
    'Afghanistan Standard Time'         => 'Asia/Kabul',                # (GMT+04:30) Kabul Afghanistan Daylight Time
    'Ekaterinburg Standard Time'        => 'Asia/Yekaterinburg',        # (GMT+05:00) Ekaterinburg  Ekaterinburg Daylight Time
    'West Asia Standard Time'           => 'PLT',                       # (GMT+05:00) Islamabad, Karachi, Tashkent  West Asia Daylight Time
    'India Standard Time'               => 'IST',                       # (GMT+05:30) Calcutta, Chennai, Mumbai, New Delhi  India Daylight Time
    'Nepal Standard Time'               => 'Asia/Katmandu',             # (GMT+05:45) Kathmandu Nepal Daylight Time
    'N. Central Asia Standard Time'     => 'Asia/Novosibirsk',          # (GMT+06:00) Almaty, Novosibirsk   N. Central Asia Daylight Time
    'Central Asia Standard Time'        => 'Asia/Dacca',                # (GMT+06:00) Astana, Dhaka Central Asia Daylight Time
    'Sri Lanka Standard Time'           => 'Asia/Colombo',              # (GMT+06:00) Sri Jayawardenepura   Sri Lanka Daylight Time
    'Myanmar Standard Time'             => 'Asia/Rangoon',              # (GMT+06:30) Rangoon   Myanmar Daylight Time
    'SE Asia Standard Time'             => 'Asia/Bangkok',              # (GMT+07:00) Bangkok, Hanoi, Jakarta   SE Asia Daylight Time
    'North Asia Standard Time'          => 'Asia/Krasnoyarsk',          # (GMT+07:00) Krasnoyarsk   North Asia Daylight Time
    'China Standard Time'               => 'Asia/Chongqing',            # (GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi China Daylight Time
    'North Asia East Standard Time'     => 'Asia/Irkutsk',              # (GMT+08:00) Irkutsk, Ulaan Bataar North Asia East Daylight Time
    'Malay Peninsula Standard Time'     => 'Asia/Kuala_Lumpur',         # (GMT+08:00) Kuala Lumpur, Singapore   Malay Peninsula Daylight Time
    'W. Australia Standard Time'        => 'Australia/Perth',           # (GMT+08:00) Perth W. Australia Daylight Time
    'Taipei Standard Time'              => 'Asia/Taipei',               # (GMT+08:00) Taipei    Taipei Daylight Time
    'Tokyo Standard Time'               => 'Asia/Tokyo',                # (GMT+09:00) Osaka, Sapporo, Tokyo Tokyo Daylight Time
    'Korea Standard Time'               => 'Asia/Seoul',                # (GMT+09:00) Seoul Korea Daylight Time
    'Yakutsk Standard Time'             => 'Asia/Yakutsk',              # (GMT+09:00) Yakutsk   Yakutsk Daylight Time
    'Cen. Australia Standard Time'      => 'Australia/Adelaide',        # (GMT+09:30) Adelaide  Cen. Australia Daylight Time
    'AUS Central Standard Time'         => 'Australia/Darwin',          # (GMT+09:30) Darwin    AUS Central Daylight Time
    'E. Australia Standard Time'        => 'Australia/Brisbane',        # (GMT+10:00) Brisbane  E. Australia Daylight Time
    'AUS Eastern Standard Time'         => 'Australia/Sydney',          # (GMT+10:00) Canberra, Melbourne, Sydney   AUS Eastern Daylight Time
    'West Pacific Standard Time'        => 'Pacific/Guam',              # (GMT+10:00) Guam, Port Moresby    West Pacific Daylight Time
    'Tasmania Standard Time'            => 'Australia/Hobart',          # (GMT+10:00) Hobart    Tasmania Daylight Time
    'Vladivostok Standard Time'         => 'Asia/Vladivostok',          # (GMT+10:00) Vladivostok   Vladivostok Daylight Time
    'Central Pacific Standard Time'     => 'Pacific/Noumea',            # (GMT+11:00) Magadan, Solomon Is., New Caledonia   Central Pacific Daylight Time
    'New Zealand Standard Time'         => 'NST',                       # (GMT+12:00) Auckland, Wellington  New Zealand Daylight Time
    'Fiji Standard Time'                => 'Pacific/Fiji',              # (GMT+12:00) Fiji, Kamchatka, Marshall Is. Fiji Daylight Time
    'Tonga Standard Time'               => 'Pacific/Tongatapu',         # (GMT+13:00) Nuku'alofa    Tonga Daylight Time
);

?>
