<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
//
// Description
//    This plugin assists in the translation of a link for a page written in the
//    source language.
//
//    You can use the plugin to translate the name of the target language page,
//    without actually creating that target language page.
//
//    You can also use the plugin to create that target language page (if it
//    doesn't already exist), and tie it to the source language page.
//

require_once('lib/debug/Tracer.php');

function wikiplugin_translationof_info()
{
    $description =
        tra('Translate a link from one language to another');

    return array(
        'name' => tra('TranslationOf'),
        'documentation' => 'PluginTranslationOf',
        'description' => $description,
        'prefs' => array( 'feature_multilingual', 'wikiplugin_translationof' ),
        'iconname' => 'language',
        'introduced' => 12,
        'params' => array(
            'orig_page' => array(
                'required' => true,
                'name' => tra('Original Page'),
                'description' => tra('Name of the page from which this link will be translated.'),
                'since' => '12.0',
                'filter' => 'pagename',
                'default' => '',
            ),
            'translation_lang' => array(
                'required' => true,
                'name' => tra('Translation Language'),
                'description' => tr('Two letter language code of the language in which you want to translate this
                    link. Example:') . ' <code>fr</code>',
                'since' => '12.0',
                'filter' => 'alpha',
                'default' => '',
            ),
            'translation_page' => array(
                'required' => false,
                'name' => tra('Translation Name'),
                'description' => tra('Name of the page that will become the translation of the "Original Page".'),
                'since' => '12.0',
                'filter' => 'pagename',
                'default' => '',
            ),
        ),
    );
}

function wikiplugin_translationof($data, $params)
{
    global $tracer;
	$smarty = TikiLib::lib('smarty');
    extract($params, EXTR_SKIP);

    $anchor_text = $orig_page;
    if (isset($translation_page) && $translation_page != '')
    {
        $anchor_text = $translation_page;
    }

    if (isset($orig_page))
    {
        $orig_page = urlencode($orig_page);
    }

    $translation_name_arg = '';
    if (isset($translation_page))
    {
        $translation_page = urlencode($translation_page);
        $translation_name_arg = "&translation_name=$translation_page";
    }

    $translation_lang_arg = '';
    if (isset($translation_lang))
    {
        $translation_lang_arg = "&target_lang=$translation_lang";
    }

    $tracer->trace('wikiplugin_translationof', "** \$translation_lang=$translation_lang");


    $link = 'javascript:void(0)';
    $popup_html = "<a href=\"tiki-edit_translation.php?page=$orig_page$translation_lang_arg$translation_name_arg#new_translation\">".tr("Translate this link")."</a>";
    $popup_params = array( 'text'=> $popup_html, 'sticky' => true, 'trigger' => 'mouseover');
    $smarty->loadPlugin('smarty_function_popup');
    $mouseover = ' ' . smarty_function_popup($popup_params, $smarty);
    $html = "<a href=\"tiki-index.php?page=$orig_page\" $mouseover>$anchor_text</a>";

    return $html;
}
