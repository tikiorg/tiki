<?php // -*- coding:utf-8 -*-
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: get_strings.php 25264 2010-02-16 16:08:30Z changi67 $
// Parameters:

// lang=xx    : only translates language 'xx',
//              if not given all languages are translated

// comments   : generate all comments (equal to close&module)

// close      : look for similar strings that are already translated and
//              generate a comment if a 'match' is made

// module     : generate comments that describe in which .php and/or .tpl
//              module(s) a certain string was found (useful for checking
//              translations in context)

// patch      : looks for the file 'language.patch' in the same directory
//              as the corresponding language.php and overrides any strings
//              in language.php - good if a user does not agree with
//              some translations or if only changes are sent to the maintainer

// spelling   : generates a file 'spellcheck_me.txt' that contains the
//              words used in the translation. It is then easy to check this
//              file for spelling errors (corrections must be done in
//              'language.php, however)

// groupwrite : Sets the generated files permissions to allow the generated
//              language.php also be group writable. This is good for
//              translators if they do not have root access to tiki but
//              are in the same group as the webserver. Please remember
//              to have write access removed when translation is finished
//              for security reasons. (Run script again without this
//              parameter)

// Examples:
// http://www.neonchart.com/get_strings.php?lang=sv
// Will translate language 'sv' and (almost) avoiding comment generation

// http://www.neonchart.com/get_strings.php?lang=sv&comments
// Will translate language 'sv' and generate all possible comments.
// This is the most usefull mode when working on a translation.

// http://www.neonchart.com/get_strings.php?lang=sv&nohelp&nosections
// These options will only provide the minimal amout of comments.
// Usefull mode when preparing a translation for distribution.

// http://www.neonchart.com/get_strings.php?nohelp&nosections
// Prepare all languages for release


// ### Note for translators about translation of text ending with punctuation
// ###
// ### The current list of concerned punctuation can be found in 'lib/init/tra.php'
// ### On 2009-03-02, it is: (':', '!', ';', '.', ',', '?')
// ### For clarity, we explain here only for colons: ':' but it is the same for the rest
// ###
// ### Short version: it is not a problem that string "Login:" has no translation. Only "Login" needs to be translated.
// ###
// ### Technical justification:
// ### If a string ending with colon needs translating (like "{tr}Login:{/tr}")
// ### then TikiWiki tries to translate 'Login' and ':' separately.
// ### This allows to have only one translation for "{tr}Login{/tr}" and "{tr}Login:{/tr}"
// ### and it still allows to translate ":" as "&nbsp;:" for languages that
// ### need it (like french)

$lang=Array(
"save_to" => "to",
"DATE-of" => "of",
"Jan" => "Jan.",
"Feb" => "Feb.",
"Mar" => "Mar.",
"Apr" => "Apr.",
"Jun" => "June",
"Jul" => "July",
"Aug" => "Aug.",
"Sep" => "Sep.",
"Oct" => "Oct.",
"Nov" => "Nov.",
"Dec" => "Dec.",
"_HOMEPAGE_CONTENT_" => "{GROUP(groups=Admins)}\n!Thank you for installing Tiki.\n\nThe entire Tiki Community would like to thank you and help you get introduced to Tiki.\n\n!How To Get Started\nTiki has more than 1000 features and settings.\n\nThis allows you to create both very simple and complex websites.\n\nWe understand that so many features might seem overwhelming at first. This is why we offer you two different ways to __Get Started__ with Tiki.\n\n{DIV(width=\"48%\",float=\"right\")}\n-=Manual Setup using Admin Panel=-\n!![tiki-admin.php|Get Started using Admin Panel]\n__Who Should Use This__\n*You are familiar with software Admin Panels\n*You enjoy exploring and playing with many options\n*You already know Tiki\n\n{DIV}{DIV(width=\"48%\",float=\"left\")}\n-=Easy Setup using Profiles=-\n!![tiki-admin.php?profile=&categories%5B%5D=5.x&categories%5B%5D=Featured+profiles&repository=http%3a%2f%2fprofiles.tikiwiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2|Get Started using Profiles]\n__Who Should Use This__\n*You want to get started quickly\n*You don't feel like learning the Admin Panel right away\n*You want to quickly test out some of Tiki's Features\n\n!!Featured Profiles\n\n__Collaborative Community__ ([tiki-admin.php?profile=&categories%5B%5D=5.x&categories%5B%5D=Featured+profiles&repository=http%3a%2f%2fprofiles.tikiwiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2|apply profile now])\nSetup to help subject experts and enthusiasts work together to build a Knowledge Base\n*Wiki Editing\n*Personal Member Spaces\n*Forums\n*Blogs\n\n__Personal Blog and Profile__ ([tiki-admin.php?profile=&categories%5B%5D=5.x&categories%5B%5D=Featured+profiles&repository=http%3a%2f%2fprofiles.tikiwiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2|apply profile now])\nSetup with many cool features to help you integrate the Social Web and establish a strong presence in the Blogosphere\n*Blog (Full set of blog related features)\n*Image Gallery\n*RSS Integration\n*Video Log\n\n__Company Intranet__ ([tiki-admin.php?profile=&categories%5B%5D=5.x&categories%5B%5D=Featured+profiles&repository=http%3a%2f%2fprofiles.tikiwiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2|apply profile now])\nSetup for a Corporate Intranet of a typical medium-sized business.\n*Company News Articles\n*Executive Blog\n*File Repository & Management\n*Collaborative Wiki\n\n__Small Organization Web Presence__ ([tiki-admin.php?profile=&categories%5B%5D=5.x&categories%5B%5D=Featured+profiles&repository=http%3a%2f%2fprofiles.tikiwiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2|apply profile now])\nSetup for a Web Presence of a typical small business or non-profit.\n*Company News & Updates\n*Highlight Company's Products and Services\n*File Gallery (great for Media Kit)\n*Contact Form\n\n{DIV}{ELSE}\n\n!Congratulations\nThis is the default homepage for your Tiki. If you are seeing this page, your installation was successful.\n\nYou can change this page after logging in. Please review the [http://doc.tikiwiki.org/wiki+syntax|wiki syntax] for editing details.\n\n\n!!{img src=pics/icons/star.png alt=\"Star\"} Get started.\nTo begin configuring your site:\n{FANCYLIST()}\n1) Log in with your newly created password. \n2) Manually Enable specific Tiki features. \n3) Run Tiki Profiles to quickly get up and running \n{FANCYLIST}\n\n!!{img src=pics/icons/help.png alt=\"Help\"} Need help?\nFor more information:\n*[http://info.tikiwiki.org/Learn+More|Learn more about TikiWiki].\n*[http://info.tikiwiki.org/Help+Others|Get help], including the [http://doc.tikiwiki.org|official documentation] and [http://www.tikiwiki.org/forums|support forums].\n*[http://info.tikiwiki.org/Join+the+community|Join the TikiWiki community].\n{GROUP}",
"###end###"=>"###end###");
