{* $Id$ *}

{if $need_lang == 'y'}
	{include file=tiki-choose_page_language.tpl}
{elseif $translation_mode == 'y'}
	{include file='tiki-editpage-include-edit_translation.tpl'}
{else}
	{include file=tiki-editpage-include-edit.tpl}
{/if}