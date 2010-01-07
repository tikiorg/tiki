{* wrapper for site-header topbar items *}
{if $prefs.feature_top_bar eq 'y'}
{include file='tiki-top_bar_begin.tpl'}
{include file='tiki-sitemenu.tpl'}
{include file='tiki-sitelocbar.tpl'}
{include file='tiki-top_bar_end.tpl'}
{/if}
