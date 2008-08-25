<?php
require_once ('tiki-setup.php');
include_once ('lib/admin/magiclib.php');

$featureChain = $_REQUEST['featurechain'];
$featurePage = explode('/', $featureChain);
$featureId = $featurePage[count($featurePage) - 1];

$feature = $magiclib->get_feature($featureId);
$smarty->assign('feature', $feature);
$smarty->assign('title', $feature['feature_name']);

/*
 * $$feature['permission'] is slightly magic.  It's checking the value of the name of the variable that is in feature['permission'].
 * If feature['permission'] is 'tiki_p_wiki_admin', it is checking if $tiki_p_wiki_admin has the value 'y'.
 */
if ($$feature['permission'] != 'y' && $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if( isset( $_REQUEST['refresh'] ) ) {
	$magiclib->reload_features();
}

if( isset($_REQUEST['tabs']) && in_array( $_REQUEST['tabs'], array('y','n') ) ) {
	$smarty->assign( 'tabs', $_REQUEST['tabs'] );
}

$headerlib->add_cssfile('css/spanky.css');
$template ='tiki-magic';

// If there's an assigned template for a feature;  show it if the feature has no children (i.e. it's a leaf feature), or if 
// the feature is a container type (to allow overriding the setting list behaviour).
// For a feature/system thing; the template shouldn't be used, as that would prevent you from configuring it.
if ($feature['template'] != '' 
	&& $feature['feature_count'] == 0 
	&& $magiclib->is_container($feature) ) {

	// The value of $feature is sometimes clobbered,  so store these values before the include.
	$template = $feature['template'];
	
	include_once($template . '.php');
	$mid = $smarty->get_template_vars('mid');
	if( is_null( $mid ) ) {
		$smarty->assign('mid', $template . '.tpl');
		
		// Containers need to display the tiki, and that's just the way it is.
		$smarty->display("tiki.tpl");
	}
	exit;
}

$enumerations = array();
// since these are static, populate them each time.  Maybe.  There might be lots, soon...
$enumerations['commentordering'] = array('points_desc'=>tra('points'), 'commentDate_desc'=>tra('newest'), 'commentDate_asc'=>tra('oldest'));
$enumerations['alignment'] = array('left'=>tra('on left side'), 'center'=>tra('on center'), 'right'=>tra('on right side'));

$enumerations['bloguser'] = array('disabled'=>tra('Disabled'),'text'=>tra('Plain text'), 'link'=>tra('Link to user information'), 'avatar'=>tra('User avatar'));
$enumerations['blogorder'] = array('created_desc'=>tra('Creation date (desc)'), 'lastModif_desc'=>tra('Last modification date (desc)'), 'title_asc'=>tra('Blog title (asc)'), 'posts_desc'=>tra('Number of posts (desc)'), 'hits_desc'=>tra('Visits (desc)'),'activity_desc'=>tra('Activity (desc)'));

$enumerations['barlocation'] = array('top'=>tra('Top bar'), 'bottom'=>tra('Bottom bar'), 'both'=>('Both'));
$enumerations['cachelength'] = array('0'=>tra('no cache'), '60'=>'1 ' . tra('minute'), '300'=>'5 ' . tra('minutes'), '600'=>'10 ' . tra('minutes'), '900'=>'15 '. tra('minutes'), '1800'=>'30 ' . tra('minutes'), '3600'=>'1 ' . tra('hour'), '7200'=>'2 ' . tra('hours'));
$enumerations['wikiauthor'] = array('classic'=>tra('as Creator &amp; Last Editor'), 'business'=>tra('Business style'), 'collaborative'=>tra('Collaborative style'), 'lastmodif'=>tra('Page last modified on'), 'none'=>tra('no (disabled)'));
$enumerations['idletimeout'] = array(1=>1,2=>2,5=>5,10=>10,15=>15,30=>30);
$enumerations['wikitablesyntax'] = array('old'=>tra('|| for rows'),'new'=>tra('\n for rows'));
$enumerations['wikidiffs'] = array('old'=>tra('Only with last version'), 'minsidediff'=>tra('Any 2 versions'));
$enumerations['wikilinkformat'] = array('complete'=>tra('complete'), 'full'=>tra('latin'), 'strict'=>tra('english'));

$enumerations['calendartimespan'] = array('1'=>'1 ' . tra('minute'), '5'=>'5 ' . tra('minutes'), '10'=>'10 ' . tra('minutes'), '15'=>'15 ' . tra('minutes'), '30'=>'30 ' . tra('minutes'));
$enumerations['calendarviewmode'] = array('day'=>tra('Day'), 'week'=>tra('Week'), 'month'=>tra('Month'), 'quarter'=>tra('Quarter'), 'semester'=>tra('Semester'), 'year'=>tra('Year'));
$enumerations['firstdayofweek'] = array('6'=>tra('Saturday'),'0'=>tra('Sunday'), '1'=>tra('Monday'),'user'=>tra('Depends user language'));
$enumerations['errorreportinglevel'] = array ('0'=>tra('No error reporting'),'1'=>tra('Report all PHP errors'),'2'=>tra('Report all errors except notices'));
$enumerations['forumordering'] = array ('created_asc'=>tra('Creation Date (asc)'),'created_desc'=>tra('Creation Date (desc)'),'threads_desc'=>tra('Topics (desc)'),'comments_desc'=>tra('Threads (desc)'),'lastPost_desc'=>tra('Last post (desc)'),'hits_desc'=>tra('Visits (desc)'),'name_desc'=>tra('Name (desc)'),'name_asc'=>tra('Name (asc)'));
$enumerations['userssortorder'] = array('score_asc'=>tra('Score ascending'),'score_desc'=>tra('Score descending'),'pref:realName_asc'=>tra('Name ascending'),'pref:realName_desc'=>tra('Name descending'),'login_asc'=>tra('Login ascending'),'login_desc'=>tra('Login descending'));
$enumerations['directorycolumns'] = array ('0'=>tra(1),'1'=>tra(2),'2'=>tra(3),'3'=>tra(4),'4'=>tra(5),'5'=>tra(6));
$enumerations['directoryopenlinks'] = array ('r'=>tra('replace current window'),'n'=>tra('new window'),'f'=>tra('inline frame'));
$enumerations['faqcommentsdefaultordering'] = array ('commentDate_desc'=>tra('Newest first'),'commentDate_asc'=>tra('Oldest first'),'points_desc'=>tra('Points'));
$enumerations['faqprefix'] = array ('None'=>tra('None'),'QA'=>tra('Q and A'),'question_id'=>tra('Question ID'));
//tikiversioncheckfrequency doesnt work...dont know why
$enumerations['tikiversioncheckfrequency'] = array (86400=>tra('Each day'),604800=>tra('Each week'),2592000=>tra('Each month'));
$enumerations['defaultmailcharset'] = array ('utf-8'=>tra('utf-8'),'iso-8859-1'=>tra('iso-8859-1'));
$enumerations['mailcrlf'] = array ('0'=>tra('CRLF (standard)'),'1'=>tra('LF (some Unix MTA)'));
//need this foreach because of dynamically driven languages of tikiwiki (copying $languages from $prefs array)
$enumerations['availablelanguages'] = array();
	foreach($languages as $key => $value){
	$enumerations['availablelanguages'][$key] = $value;
	}
$enumerations['remembertime'] = array ('0'=>tra('5 minutes'),'1'=>tra('15 minutes'),'2'=>tra('30 minutes'),'3'=>tra('1 hour'),'4'=>tra('2 hours'),'5'=>tra('10 hours'),'6'=>tra('20 hours'),'7'=>tra('1 day'),'8'=>tra('1 week'),'9'=>tra('1 month'),'10'=>tra('1 year'));
$enumerations['httpslogin'] = array ('0'=>tra('Disabled'),'1'=>tra('Allow secure (https) login'),'2'=>tra('Encourage secure (https) login'),'3'=>tra('Consider we are in always in HTTPS, but do not check'),'4'=>tra('Require secure (https) login'));
//featurecryptpasswords not working
$enumerations['featurecryptpasswords'] = array ('crypt-md5'=>tra('crypt-md5'),'crypt-des'=>tra('crypt-des'),'tikihash'=>tra('tikihash (old)'));
$enumerations['authmethod'] = array ('0'=>tra('Just Tiki'),'1'=>tra('Web Server'),'2'=>tra('Tiki and PEAR::Auth'),'3'=>tra('Tiki and PAM'),'4'=>tra('CAS (Central Authentication Service)'),'5'=>tra('Shibboleth'),'6'=>tra('OpenID and Tiki'));
$enumerations['highlightgroup'] = array ('0'=>tra('choose a group ...'),'1'=>tra('Registered'),'2'=>tra('Anonymous'),'3'=>tra('Admins'));
$enumerations['availablestyles'] = array ('0'=>tra('darkroom.css'),'1'=>tra('feb12.css'),'2'=>tra('simple-amette.css'),'3'=>tra('simple.css'),'3'=>tra('spanky.css'),'3'=>tra('thenews.css'),'3'=>tra('tikineat.css'),'3'=>tra('tikinewt.css'));
$enumerations['transitionstylever'] = array ('none'=>tra('Never use transition css'),'css_specified_only'=>tra('Use @version:x.x specified in theme css or none if not specified'),'1.9'=>tra('Use @version:x.x specified in theme css or 1.9 if not specified'),'2.0'=>tra('Use @version:x.x specified in theme css or 2.0 if not specified'));
$enumerations['wikilistsortorder'] = array ('pageName'=>tra('Name'),'lastModif'=>tra('LastModif'),'created'=>tra('Created'),'creator'=>tra('Creator'),'hits'=>tra('Hits'),'user'=>tra('Last editor'),'page_size'=>tra('Size'));
$enumerations['wikicommentsdefaultordering'] = array ('commentDate_desc'=>tra('Newest first'),'commentDate_asc'=>tra('Oldest first'),'points_desc'=>tra('Points'));













$pagefeatures = array($feature);
$containers = array();
$hasCategories = false;
$hasLanguages = false;
$hasTimezones = false;

if ($featureId != '' && is_numeric($featureId)) {
	get_features($featureId);
} else {
	get_features(0);
	$featureId = 0;
}

if ($_POST['submit'] != '') { /* Warning Level Fix:  Check if the 'submit' is a key first */
	check_ticket('tiki_magic');
	foreach($pagefeatures as $feature) {
		if ($feature['feature_type'] == 'flag') {
			simple_set_toggle($feature['setting_name']);
		} else if ($feature['feature_type'] == 'int') {
			simple_set_int($feature['setting_name']);
		} else if ($feature['feature_type'] == 'simple') {
			simple_set_value($feature['setting_name']);
		} else if ($feature['feature_type'] == 'byref') {
			byref_set_value($feature['setting_name']);
		} else if ($feature['feature_type'] == 'feature') {
			simple_set_toggle($feature['setting_name']); // save the toggling of features.
		} else {
			// All of the special settings are simple underneath.
			simple_set_value($feature['setting_name']);
		}
	}
	
	// Reset the list of features.
	$pagefeatures = array($magiclib->get_feature($featureId));
	get_features($featureId);
}
ask_ticket('tiki_magic');
$smarty->assign_by_ref('containers', $containers);
$smarty->assign('features', $pagefeatures);

// lazy load the values which should be lazy loaded.
foreach($lazyFields as $field=>$value) {
	switch ($field) {
		case 'category':
			include_once ('lib/categories/categlib.php');
			$catree = $categlib->get_all_categories();
			$smarty->assign('catree', $catree);
			break;
		case 'language':
			$languages = array();
			$languages = $tikilib->list_languages(false,null,true);
			$smarty->assign_by_ref("languages", $languages);
			break;
		case 'timezone':
			$smarty->assign_by_ref("timezones", TikiDate::getTimeZoneList());
			break;
	}
}

// Display the template
$smarty->assign('mid', 'tiki-magic.tpl');
$smarty->display("tiki.tpl");

$lazyFields = array();
// Recursively get the features underneath the specified feature id.
function get_features($featureid, $keepContainers = true) {
	global $magiclib, $tikilib, $pagefeatures, $containers, $enumerations, $lazyFields, $prefs;
	$features = $magiclib->get_child_features($featureid);
	$cont = array();

	/* Christine thinks maybe something weird is happening, since a feature set with one item isn't properly showing dropdown lists. */
	if ($features) {
		foreach($features as $feature) {
			if ($feature['feature_type'] == 'limitcategory' || $feature['feature_type'] == 'selectcategory') $lazyFields['category'] = true;
			if ($feature['feature_type'] == 'languages')  $lazyFields['languages'] = true;
			if ($feature['feature_type'] == 'timezone')  $lazyFields['timezone'] = true;
			// add these to the enumeration, as they will only occur once; and this allows no template changes.
			if ($feature['feature_type'] == 'sitestyle') $enumerations['sitestyle'] = $tikilib->list_styles(); 
			if ($feature['feature_type'] == 'slideshowstyle') $enumerations['slideshowstyle'] = get_slideshowstyles();

			if (array_key_exists($feature['feature_type'], $enumerations)) {
				$feature['enumeration'] = $enumerations[$feature['feature_type']];
			}

			if ($keepContainers && $magiclib->is_container($feature) && $feature['feature_count'] > 0) {
				$cont[] = $feature;
			} else {
				$pagefeatures[] = $feature;
				$pref = $feature['setting_name'];
				if( !isset($prefs[$pref]) || $prefs[$pref] == 'y' )
					get_features($feature['feature_id'], false);
			}
		}
		foreach($cont as $feature) {
			$containers[] = $feature;
			$pagefeatures[] = $feature;
			get_features($feature['feature_id'], false);
		}
	}
}

// These are helper functions, pretty much as-ganked from tiki-admin.php

function simple_set_toggle($feature) {
	global $_POST, $tikilib, $smarty, $tikifeedback, $prefs;
	$setting = $feature;
	if (isset($_POST[$setting]) && $_POST[$setting] == "on") {
		if ((!isset($prefs[$setting]) || $prefs[$setting] != 'y')) {
			// not yet set at all or not set to y
			$tikilib->set_preference($setting, 'y');
			$prefs[$setting] = 'y';
			$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s enabled"),$feature));
		}
	} else {
		if ((!isset($prefs[$setting]) || $prefs[$setting] != 'n')) {
			// not yet set at all or not set to n
			$tikilib->set_preference($feature, 'n');
			$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s disabled"),$feature));
		}
	}
}

function simple_set_value($feature, $pref = '', $isMultiple = false) {
	global $_POST, $tikilib, $prefs;
	
	if (isset($_POST[$feature])) {
		if ( $pref != '' ) {
			$tikilib->set_preference($pref, $_POST[$feature]);
			$prefs[$feature] = $_POST[$feature];
		} else {
			$tikilib->set_preference($feature, $_POST[$feature]);
		}
	} else if( $isMultiple ) {
		// Multiple selection controls do not exist if no item is selected.
		// We still want the value to be updated.
		if ( $pref != '' ) {
			$tikilib->set_preference($pref, array());
			$prefs[$feature] = $_POST[$feature];
		} else {
			$tikilib->set_preference($feature, array());
		}
	}
}

function simple_set_int($feature) {
    global $_POST, $tikilib;
	if (isset($_POST[$feature]) && is_numeric($_POST[$feature])) {
		$tikilib->set_preference($feature, $_POST[$feature]);
	}
}

function byref_set_value($feature, $pref = "") {
	global $_POST, $tikilib;
	simple_set_value($feature, $pref);
}

function get_slideshowstyles() {
	$slide_styles = array();
	$h = opendir("styles/slideshows");
	while ($file = readdir($h)) {
		if (strstr($file, "css")) {
			$slide_styles[] = $file;
		}
	}
	closedir ($h);
			
	return $slide_styles;
}
?>
