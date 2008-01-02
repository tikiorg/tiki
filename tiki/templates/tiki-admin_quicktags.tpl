{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_quicktags.tpl,v 1.29.2.1 2008-01-02 21:44:29 pkdille Exp $ *}

<h1><a class="pagetitle" href="tiki-admin_quicktags.php">{tr}Admin Quicktags{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="http://tikiwiki.org/QuickTags" target="tikihelp" class="tikihelp" title="{tr}Admin Quicktags{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_quicktags.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}tiki admin quicktags template{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}' /></a>{/if}</h1>

<h2>{tr}Create/Edit QuickTags{/tr}</h2>
<div id="quicktags-edit">
  {include file="tiki-admin_quicktags_edit.tpl"}
</div>

<h2>{tr}QuickTags{/tr}</h2>

<div id="quicktags-content">

{include file="tiki-admin_quicktags_content.tpl"}

</div>

