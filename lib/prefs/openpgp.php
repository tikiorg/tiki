<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


function prefs_openpgp_list()
{
	return [
		'openpgp_gpg_pgpmimemail' => [
			'name' => tra('PGP/MIME encrypted email messaging'),
			'description' => tra('Use OpenPGP PGP/MIME compliant encrypted email messaging. All email-messaging/notifications/newsletters are sent as PGP/MIME-encrypted messages, signed with the signer-key, and are completely 100% opaque to outsiders. All user accounts need to be properly configured into gnupg keyring with public-keys related to their tiki-account-related email-addresses.'),
			'type' => 'flag',
			'default' => 'n',
			'warning' => tra('Enable only if gpg, keyring, and tikiaccounts are properly configured for PGP/MIME functionality. NOTE: Requires that all accounts have their public-keys configured into gnupg-keyring, so do not allow non-administred registrations (or e.g. non-configured emails for newsletters etc) to site if this feature turned on.'),
		],
		'openpgp_gpg_home' => [
			'name' => tra('Path to gnupg keyring'),
			'description' => tra('Full directory path to gnupg keyring (default /home/www/.gnupg/ ). The directory, related subdirectories (e.g. a subdirectory \'signer\'), and files must have proper permissions for tiki to access/read the directories/files, and create/delete necessary temporary workfiles there.'),
			'type' => 'text',
			'size' => 60,
			'filter' => 'text',
			'default' => '/home/www/.gnupg/',
		],
		'openpgp_gpg_path' => [
			'name' => tra('Path to gpg executable'),
			'description' => tra('Full path to gpg executable.'),
			'type' => 'text',
			'size' => 60,
			'filter' => 'text',
			'default' => '/usr/bin/gpg',
		],
		'openpgp_gpg_signer_passphrase_store' => [
			'name' => tra('Read signer pass phrase from prefs or from a file'),
			'description' => tra('Read GnuPG signer pass phrase from preferences or from a file (default is \'file\' ). With file option, configure other preference for the full path including the filename of the file containing the GnuPG signer private-key pass phrase.'),
			'type' => 'list',
			'options' => [
				'preferences' => tra('preferences'),
				'file' => tra('file'),
			],
			'default' => 'preferences',
		],
		'openpgp_gpg_signer_passphrase' => [
			'name' => tra('Signer pass phrase'),
			'description' => tra('GnuPG signer private-key passphrase. Define pass phrase either here or in a signer pass phrase file.'),
			'type' => 'text',
			'size' => 60,
			'shorthint' => tr('leave empty if read from file'),
			'default' => '',
		],
		'openpgp_gpg_signer_passfile' => [
			'name' => tra('Path to signer pass phrase filename'),
			'description' => tra('Full path including the filename of the file containing the GnuPG signer private-key pass phrase. The directory and file must have proper permissions for tiki to access/read the signer pass phrase file.'),
			'type' => 'text',
			'size' => 60,
			'default' => '/home/www/.gnupg/signer/signerpass',
		],
	];
}
