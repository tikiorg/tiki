{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-file_galleries.tpl,v 1.66.2.6 2008-03-11 15:17:55 nyloth Exp $ *}
{popup_init src="lib/overlib.js"}
<h1><a class="pagetitle" href="tiki-file_galleries.php{if $galleryId}?galleryId={$galleryId}{if isset($edit_mode) and $edit_mode ne 'n'}&amp;edit_mode=1{/if}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}{else}{if $filegals_manager eq 'y'}?filegals_manager=y{/if}{/if}">{tr}File Galleries{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}File+Galleries" target="tikihelp" class="tikihelp" title="{tr}File Galleries{/tr}">{icon _id='help'}</a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-file_galleries.tpl{if $filegals_manager eq 'y'}?filegals_manager=y{/if}" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}File Galleries tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}
{if $tiki_p_admin eq 'y' and $filegals_manager ne 'y'}
<a href="tiki-admin.php?page=fgal">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>
{if $filegals_manager eq 'y'}
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
<div class="rbox-data" name="tip">{tr}Be carefull to set the right permissions on the files you link to{/tr}.</div>
</div>
<br />
{/if}

<div class="navbar">
{if $edit_mode eq 'y' or $dup_mode eq 'y'}
<a class="linkbut" href="tiki-file_galleries.php{if $filegals_manager eq 'y'}?filegals_manager=y{/if}">{tr}List Galleries{/tr}</a>
{if $galleryId}<a class="linkbut" href="tiki-list_file_gallery.php?galleryId={$galleryId}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{tr}Browse Gallery{/tr}</a>{/if}
{/if}
{if $tiki_p_create_file_galleries eq 'y'and $edit_mode ne 'y'}
<a class="linkbut" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{tr}Create New File Gallery{/tr}</a>
{/if}

{if $tiki_p_create_file_galleries eq 'y'and $dup_mode ne 'y'}
<a class="linkbut" href="tiki-file_galleries.php?dup_mode=1{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{tr}Duplicate File Gallery{/tr}</a>
{/if}
</div>

{if $edit_mode eq 'y'}
	{include file='edit_file_gallery.tpl'}
{/if}

{if $galleryId>0}
  {if $edited eq 'y'}
  <div class="wikitext">
    {tr}You can access the file gallery using the following URL{/tr}: <a class="fgallink" href="{$url}?galleryId={$galleryId}">{$url}?galleryId={$galleryId}</a>
  </div>
  {/if}
{/if}

{if $dup_mode eq 'y'}
	{include file='duplicate_file_gallery.tpl'}
{/if}

{if $edit_mode ne 'y' and $dup_mode ne 'y'}
	{include file='find.tpl' find_show_languages='n' find_show_categories='n'}
	{include file='list_file_gallery.tpl'}
{/if}
