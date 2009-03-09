<?php
/*
 * This is a set of enumerations which are used for populating drop down lists for the settings that require them.
 * 
 * The array keys match the feature_type column of the settings that use it.
 */

$enumerations = array();
// since these are static, populate them each time.  Maybe.  There might be lots, soon...

$enumerations['commentordering'] = array('points_desc'=>'points', 'commentDate_desc'=>'newest', 'commentDate_asc'=>'oldest');
//get_strings('points_desc'=>tra('points'), 'commentDate_desc'=>tra('newest'), 'commentDate_asc'=>tra('oldest'));

$enumerations['alignment'] = array('left'=>'on left side', 'center'=>'on center', 'right'=>'on right side');
//get_strings('left'=>tra('on left side'), 'center'=>tra('on center'), 'right'=>tra('on right side'));

$enumerations['bloguser'] = array('disabled'=>'Disabled','text'=>'Plain text', 'link'=>'Link to user information', 'avatar'=>'User avatar');
//get_strings('disabled'=>tra('Disabled'),'text'=>tra('Plain text'), 'link'=>tra('Link to user information'), 'avatar'=>tra('User avatar'));

$enumerations['blogorder'] = array('created_desc'=>'Creation date (desc)', 'lastModif_desc'=>'Last modification date (desc)', 'title_asc'=>'Blog title (asc)', 'posts_desc'=>'Number of posts (desc)', 'hits_desc'=>'Visits (desc)','activity_desc'=>'Activity (desc)');
//get_strings('created_desc'=>tra('Creation date (desc)'), 'lastModif_desc'=>tra('Last modification date (desc)'), 'title_asc'=>tra('Blog title (asc)'), 'posts_desc'=>tra('Number of posts (desc)'), 'hits_desc'=>tra('Visits (desc)'),'activity_desc'=>tra('Activity (desc)'));

$enumerations['barlocation'] = array('top'=>'Top bar', 'bottom'=>'Bottom bar', 'both'=>'Both');
//get_strings('top'=>tra('Top bar'), 'bottom'=>tra('Bottom bar'), 'both'=>('Both'));

$enumerations['cachelength'] = array('0'=>'no cache', '60'=>'1 ' . 'minute', '300'=>'5 ' . 'minutes', '600'=>'10 ' . 'minutes', '900'=>'15 '. 'minutes', '1800'=>'30 ' . 'minutes', '3600'=>'1 ' . 'hour', '7200'=>'2 ' . 'hours');
//get_strings('0'=>tra('no cache'), '60'=>'1 ' . tra('minute'), '300'=>'5 ' . tra('minutes'), '600'=>'10 ' . tra('minutes'), '900'=>'15 '. tra('minutes'), '1800'=>'30 ' . tra('minutes'), '3600'=>'1 ' . tra('hour'), '7200'=>'2 ' . tra('hours'));

$enumerations['wikiauthor'] = array('classic'=>'as Creator &amp; Last Editor', 'business'=>'Business style', 'collaborative'=>'Collaborative style', 'lastmodif'=>'Page last modified on', 'none'=>'no (disabled)');
//get_strings('classic'=>tra('as Creator &amp; Last Editor'), 'business'=>tra('Business style'), 'collaborative'=>tra('Collaborative style'), 'lastmodif'=>tra('Page last modified on'), 'none'=>tra('no (disabled)'));

$enumerations['idletimeout'] = array(1=>1,2=>2,5=>5,10=>10,15=>15,30=>30);
//get_strings(1=>1,2=>2,5=>5,10=>10,15=>15,30=>30);

$enumerations['wikitablesyntax'] = array('old'=>'|| for rows','new'=>'<return> for rows');
//get_strings('old'=>tra('|| for rows'),'new'=>tra('<return> for rows'));

$enumerations['wikidiffs'] = array('old'=>'Only with last version', 'minsidediff'=>'Any 2 versions');
//get_strings('old'=>tra('Only with last version'), 'minsidediff'=>tra('Any 2 versions'));

$enumerations['wikilinkformat'] = array('complete'=>'complete', 'full'=>'latin', 'strict'=>'english');
//get_strings('complete'=>tra('complete'), 'full'=>tra('latin'), 'strict'=>tra('english'));

$enumerations['calendartimespan'] = array('1'=>'1 ' . 'minute', '5'=>'5 ' . 'minutes', '10'=>'10 ' . 'minutes', '15'=>'15 ' . 'minutes', '30'=>'30 ' . 'minutes');
//get_strings('1'=>'1 ' . tra('minute'), '5'=>'5 ' . tra('minutes'), '10'=>'10 ' . tra('minutes'), '15'=>'15 ' . tra('minutes'), '30'=>'30 ' . tra('minutes'));

$enumerations['calendarviewmode'] = array('day'=>'Day', 'week'=>'Week', 'month'=>'Month', 'quarter'=>'Quarter', 'semester'=>'Semester', 'year'=>'Year');
//get_strings('day'=>tra('Day'), 'week'=>tra('Week'), 'month'=>tra('Month'), 'quarter'=>tra('Quarter'), 'semester'=>tra('Semester'), 'year'=>tra('Year'));

$enumerations['firstdayofweek'] = array('6'=>'Saturday','0'=>'Sunday', '1'=>'Monday','user'=>'Depends user language');
//get_strings('6'=>tra('Saturday'),'0'=>tra('Sunday'), '1'=>tra('Monday'),'user'=>tra('Depends user language'));

$enumerations['errorreportinglevel'] = array ('0'=>'No error reporting','1'=>'Report all PHP errors','2'=>'Report all errors except notices');
//get_strings ('0'=>tra('No error reporting'),'1'=>tra('Report all PHP errors'),'2'=>tra('Report all errors except notices'));

$enumerations['forumordering'] = array ('created_asc'=>'Creation Date (asc)','created_desc'=>'Creation Date (desc)','threads_desc'=>'Topics (desc)','comments_desc'=>'Threads (desc)','lastPost_desc'=>'Last post (desc)','hits_desc'=>'Visits (desc)','name_desc'=>'Name (desc)','name_asc'=>'Name (asc)');
//get_strings ('created_asc'=>tra('Creation Date (asc)'),'created_desc'=>tra('Creation Date (desc)'),'threads_desc'=>tra('Topics (desc)'),'comments_desc'=>tra('Threads (desc)'),'lastPost_desc'=>tra('Last post (desc)'),'hits_desc'=>tra('Visits (desc)'),'name_desc'=>tra('Name (desc)'),'name_asc'=>tra('Name (asc)'));

$enumerations['userssortorder'] = array('score_asc'=>'Score ascending','score_desc'=>'Score descending','pref:realName_asc'=>'Name ascending','pref:realName_desc'=>'Name descending','login_asc'=>'Login ascending','login_desc'=>'Login descending');
//get_strings('score_asc'=>tra('Score ascending'),'score_desc'=>tra('Score descending'),'pref:realName_asc'=>tra('Name ascending'),'pref:realName_desc'=>tra('Name descending'),'login_asc'=>tra('Login ascending'),'login_desc'=>tra('Login descending'));

$enumerations['directorycolumns'] = array ('0'=>1,'1'=>2,'2'=>3,'3'=>4,'4'=>5,'5'=>6);

$enumerations['directoryopenlinks'] = array ('r'=>'replace current window','n'=>'new window','f'=>'inline frame');
//get_strings ('r'=>tra('replace current window'),'n'=>tra('new window'),'f'=>tra('inline frame'));

$enumerations['faqprefix'] = array ('None'=>'None','QA'=>'Q and A','question_id'=>'Question ID');
//get_strings ('None'=>tra('None'),'QA'=>tra('Q and A'),'question_id'=>tra('Question ID'));

$enumerations['tikiversioncheckfrequency'] = array (86400=>'Each day',604800=>'Each week',2592000=>'Each month');
//get_strings (86400=>tra('Each day'),604800=>tra('Each week'),2592000=>tra('Each month'));

$enumerations['defaultmailcharset'] = array ('utf-8'=>'utf-8','iso-8859-1'=>'iso-8859-1');
//get_strings ('utf-8'=>tra('utf-8'),'iso-8859-1'=>tra('iso-8859-1'));

$enumerations['mailcrlf'] = array ('0'=>'CRLF (standard)','1'=>'LF (some Unix MTA)');
//get_strings ('0'=>tra('CRLF (standard)'),'1'=>tra('LF (some Unix MTA)'));

$enumerations['remembertime'] = array ('0'=>'5 minutes','1'=>'15 minutes','2'=>'30 minutes','3'=>'1 hour','4'=>'2 hours','5'=>'10 hours','6'=>'20 hours','7'=>'1 day','8'=>'1 week','9'=>'1 month','10'=>'1 year');
//get_strings ('0'=>tra('5 minutes'),'1'=>tra('15 minutes'),'2'=>tra('30 minutes'),'3'=>tra('1 hour'),'4'=>tra('2 hours'),'5'=>tra('10 hours'),'6'=>tra('20 hours'),'7'=>tra('1 day'),'8'=>tra('1 week'),'9'=>tra('1 month'),'10'=>tra('1 year'));

$enumerations['httpslogin'] = array ('0'=>'Disabled','1'=>'Allow secure (https) login','2'=>'Encourage secure (https) login','3'=>'Consider we are in always in HTTPS, but do not check','4'=>'Require secure (https) login');
//get_strings ('0'=>tra('Disabled'),'1'=>tra('Allow secure (https) login'),'2'=>tra('Encourage secure (https) login'),'3'=>tra('Consider we are always in HTTPS, but do not check'),'4'=>tra('Require secure (https) login'));

$enumerations['featurecryptpasswords'] = array ('crypt-md5'=>'crypt-md5','crypt-des'=>'crypt-des','tikihash'=>'tikihash (old)');
//get_strings ('crypt-md5'=>tra('crypt-md5'),'crypt-des'=>tra('crypt-des'),'tikihash'=>tra('tikihash (old)'));

$enumerations['authmethod'] = array ('0'=>'Just Tiki','1'=>'Web Server','2'=>'Tiki and PEAR::Auth','3'=>'Tiki and PAM','4'=>'CAS (Central Authentication Service)','5'=>'Shibboleth','6'=>'OpenID and Tiki');
//get_strings ('0'=>tra('Just Tiki'),'1'=>tra('Web Server'),'2'=>tra('Tiki and PEAR::Auth'),'3'=>tra('Tiki and PAM'),'4'=>tra('CAS (Central Authentication Service)'),'5'=>tra('Shibboleth'),'6'=>tra('OpenID and Tiki'));

$enumerations['highlightgroup'] = array ('0'=>'choose a group ...','1'=>'Registered','2'=>'Anonymous','3'=>'Admins');
//get_strings ('0'=>tra('choose a group ...'),'1'=>tra('Registered'),'2'=>tra('Anonymous'),'3'=>tra('Admins'));

$enumerations['availablestyles'] = $tikilib->list_styles();

$enumerations['transitionstylever'] = array ('none'=>'Never use transition css','css_specified_only'=>'Use @version:x.x specified in theme css or none if not specified','1.9'=>'Use @version:x.x specified in theme css or 1.9 if not specified','2.0'=>'Use @version:x.x specified in theme css or 2.0 if not specified');
//get_strings ('none'=>tra('Never use transition css'),'css_specified_only'=>tra('Use @version:x.x specified in theme css or none if not specified'),'1.9'=>tra('Use @version:x.x specified in theme css or 1.9 if not specified'),'2.0'=>tra('Use @version:x.x specified in theme css or 2.0 if not specified'));

$enumerations['wikilistsortorder'] = array ('pageName'=>'Name','lastModif'=>'LastModif','created'=>'Created','creator'=>'Creator','hits'=>'Hits','user'=>'Last editor','page_size'=>'Size');
//get_strings ('pageName'=>tra('Name'),'lastModif'=>tra('LastModif'),'created'=>tra('Created'),'creator'=>tra('Creator'),'hits'=>tra('Hits'),'user'=>tra('Last editor'),'page_size'=>tra('Size'));

$enumerations['wikiattachmentstorage'] = array('y'=>'Use database to store files', 'n'=>'Use a directory to store files');
//get_strings('y'=>tra('Use database to store files'), 'n'=>tra('Use a directory to store files'));

$enumerations['sortorder'] = array('asc'=>'Ascending', 'desc'=>'Descending');
//get_strings('asc'=>tra('Ascending'), 'desc'=>tra('Descending'));
