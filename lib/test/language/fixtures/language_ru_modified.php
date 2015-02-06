<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
// "Bytecode Cache" => "Bytecode Cache",
// "Using <strong>%0</strong>.These stats affect all PHP applications running on the server" => "Using <strong>%0</strong>.These stats affect all PHP applications running on the server",
// "Configuration setting <em>xcache.admin.enable_auth</em> prevents from accessing statistics. This will also prevent the cache from being cleared when clearing template cache" => "Configuration setting <em>xcache.admin.enable_auth</em> prevents from accessing statistics. This will also prevent the cache from being cleared when clearing template cache",
// "Used" => "Used",
// "Available" => "Available",
// "Memory" => "Memory",
// "Hit" => "Hit",
// "Miss" => "Miss",
// "Cache Hits" => "Cache Hits",
// "Few hits recorded. Statistics may not be representative" => "Few hits recorded. Statistics may not be representative",
// "Low hit ratio. %0 may be misconfigured and not used" => "Low hit ratio. %0 may be misconfigured and not used",
// "Bytecode cache is not used. Using a bytecode cache (APC, XCache) is highly recommended for production environments" => "Bytecode cache is not used. Using a bytecode cache (APC, XCache) is highly recommended for production environments",
"Errors" => "Ошибки",
"Created" => "Создано",
// "%0 enabled" => "%0 enabled",
// "%0 disabled" => "%0 disabled",
"Features" => "Возможности",
"Enable/disable Tiki features here, but configure them elsewhere" => "Здесь только включение / выключение функций Tiki. Настройка производится в других разделах",
"General" => "Общие",
"General preferences and settings" => "Общие настройки и предпочтения",
// "Login" => "Login",
"User registration, login and authentication" => "Регистрация пользователей, настройка параметров входа и метода аутентификации",
"Wiki" => "Wiki",
"Wiki settings" => "Настройки Wiki",
// "Help on \$admintitle Config" => "Help on \$admintitle Config",
"Congratulations!\n\nYour server can send emails.\n\n" => "Поздравляем!\n\nВаш сервер умеет рассылать письма.\n\n",
);
