<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @return array
 */
function prefs_pass_list()
{

    /**
     * reads available password list files from disk and returns a sorted array of files
     *
     * @return array
     */

    function genIndexedBlacks(){

    $blacklist_options = array_diff(scandir('lib/pass_blacklists', SCANDIR_SORT_ASCENDING), array('..', '.', 'index.php', '.htaccess', '.svn', '.DS_Store', 'readme.txt'));
    $fileindex = array();
    foreach ($blacklist_options as $blacklist_file) {
        $blacklist_file = substr($blacklist_file, 0, -4);
        $fileindex[$blacklist_file] = explode('-', $blacklist_file);
        $fileindex[$blacklist_file]['0'] = ' Num & Let '.$fileindex[$blacklist_file]['0'];
        $fileindex[$blacklist_file]['1'] = ' Special '.$fileindex[$blacklist_file]['1'];
        $fileindex[$blacklist_file]['2'] = ' Min Len '.$fileindex[$blacklist_file]['2'];
        $fileindex[$blacklist_file]['3'] = ' Custom '.$fileindex[$blacklist_file]['3'];
        $fileindex[$blacklist_file]['4'] = ' Word Count '.$fileindex[$blacklist_file]['4'];
       // $fileindex[$blacklist_file] = implode($fileindex[$blacklist_file]);
    }
    return $fileindex;
}

	return array(
        'pass_chr_num' => array(
            'name' => tra('Require characters and numerals'),
            'description' => tra('For improved security, require users to include a mix of alphabetical characters and numerals in passwords.'),
            'type' => 'flag',
            'default' => 'n',
        ),
        'pass_blacklist' => array(
            'name' => tra('Prevent Common Passwords'),
            'description' => tra('For improved security, prevent passwords in your password blacklist from being used.'),
            'type' => 'list',
            'default' => 'n',
            'options' => array_merge(array('n' => 'Disable',
                          'auto' => 'Automatatically Select Blacklist'),
                genIndexedBlacks()),
        ),

		'pass_due' => array(
			'name' => tra('Password expires after'),
            'description' => tra('password expiry period (in days)'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'int',
			'shorthint' => tra('days'),
			'hint' => tra('Use "-1" for never'),
			'default' => -1,
		),
		'pass_chr_case' => array(
			'name' => tra('Require alphabetical characters in lower and upper case'),
			'description' => tra('Password must contain at least one lowercase alphabetical character like "a" and one uppercase character like "A".'),
            'type' => 'flag',
			'default' => 'n',
		),
		'pass_chr_special' => array(
			'name' => tra('Require special characters'),
			'description' => tra('Password must contain at least one special character in lower case like " / $ % ? & * ( ) _ + ...'),
            'type' => 'flag',
			'default' => 'n',
		),
		'pass_repetition' => array(
			'name' => tra('Require no consecutive repetition of the same character'),
			'description' => tra('Password must not contain a consecutive repetition of the same character such as "111" or "aab".'),
            'type' => 'flag',
			'default' => 'n',
		),
		'pass_diff_username' => array(
			'name' => tra('The password must be different from the user\'s log-in name'),
			'description' => tra('The password must be different from the user\'s log-in name.'),
            'type' => 'flag',
			'default' => 'y',
		),	);	
}
