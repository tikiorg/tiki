<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Note for translators about translation of text ending with punctuation
//
// The current list of concerned punctuation can be found in 'lib/init/tra.php'
// On 2009-03-02, it is: (':', '!', ';', '.', ',', '?')
// For clarity, we explain here only for colons: ':' but it is the same for the rest
//
// Short version: it is not a problem that string "Login:" has no translation. Only "Login" needs to be translated.
//
// Technical justification:
// If a string ending with colon needs translating (like "{tr}Login:{/tr}")
// then Tiki tries to translate 'Login' and ':' separately.
// This allows to have only one translation for "{tr}Login{/tr}" and "{tr}Login:{/tr}"
// and it still allows to translate ":" as " :" for languages that
// need it (like French)
// Note: the difference is invisible but " :" has an UTF-8 non-breaking-space, not a regular space, but the UTF-8 equivalent of the HTML &nbsp;.
// This allows correctly displaying emails and JavaScript messages, not only web pages as would happen with &nbsp;.

$lang = array(
"Log out" => "ออกจากระบบ",
);
