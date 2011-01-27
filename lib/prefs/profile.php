<?php

function prefs_profile_list()
{
	return array(
		'profile_sources' => array(
			'name' => tra('Repository URLs'),
			'description' => tra('List of URLs for the profile repositories that will be used.'),
			'type' => 'textarea',
			'size' => 5,
			'hint' => tra('Enter multiple repository URLs, one per line.'),
		),
		'profile_channels' => array(
			'name' => tra('Data Channels'),
			'description' => tra('Data channels are templates that can be applied from a post request. They can be used to automate work on more complex installations.'),
			'type' => 'textarea',
			'size' => 5,
			'hint' => tra('Data channels create a named pipe to run profiles from user space. One channel per line. Each line is comma delimited and contain __channel name, domain, profile, allowed groups__.'),
			'help' => 'http://profiles.tiki.org/Data+Channels',
			'warning' => tra('There are security considerations related to using data channels. Make sure the profile page is controlled by administrators only.'),
		),
		'profile_unapproved' => array(
			'name' => tra('Developer mode'),
			'description' => tra('For profiles under an approval workflow, always use the latest version, even if not approved.'),
			'type' => 'flag',
			'warning' => tra('Make sure you review the profiles you install.'),
		),
	);
}

