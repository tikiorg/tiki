{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_quicktags.tpl,v 1.29.2.2 2008-01-29 19:53:15 nyloth Exp $ *}

<h1><a class="pagetitle" href="tiki-admin_quicktags.php">{tr}Admin Quicktags{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="http://tikiwiki.org/QuickTags" target="tikihelp" class="tikihelp" title="{tr}Admin Quicktags{/tr}">{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_quicktags.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}tiki admin quicktags template{/tr}">{icon _id='shape_square_edit'}</a>{/if}

</h1>

<h2>{tr}Create/Edit QuickTags{/tr}</h2>
<div id="quicktags-edit">
  {include file="tiki-admin_quicktags_edit.tpl"}
</div>

<h2>{tr}QuickTags{/tr}</h2>

<div id="quicktags-content">

{include file="tiki-admin_quicktags_content.tpl"}

</div>

