<a class="pagetitle" href="tiki-admin_quicktags.php">{tr}Admin Quicktags{/tr}</a>

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=QuickTags" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin QuickTags{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_quicktags.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}tiki admin quicktags tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

 
<br /><br />
<h2>{tr}Create/Edit QuickTags{/tr}</h2>
<form action="tiki-admin_quicktags.php" method="post">
<input type="hidden" name="tagId" value="{$tagId|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Label{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="10" name="taglabel" value="{$info.taglabel|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Insert (use 'text' for figuring the selection){/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="taginsert" value="{$info.taginsert|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Path to the tag icon{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="tagicon" value="{$info.tagicon|escape}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}QuickTags{/tr}</h2>

<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_quicktags.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'taglabel_desc'}taglabel_asc{else}taglabel_desc{/if}">{tr}Label{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_quicktags.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'taginsert_desc'}taginsert_asc{else}taginsert_desc{/if}">{tr}Insert{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_quicktags.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'tagicon_desc'}tagicon_asc{else}tagicon_desc{/if}">{tr}Icon{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=tag loop=$quicktags}
<tr>
<td class="{cycle advance=false}">{$quicktags[tag].taglabel}</td>
<td class="{cycle advance=false}">{$quicktags[tag].taginsert}</td>
<td class="{cycle advance=false}">{html_image file=$quicktags[tag].tagicon} {$quicktags[tag].iconpath}</td>
<td class="{cycle}">
   <a class="link" href="tiki-admin_quicktags.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$quicktags[tag].tagId}" onClick="return confirmTheLink(this,'{tr}Are you sure you want to delete this quicktag?{/tr}')">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-admin_quicktags.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;tagId={$quicktags[tag].tagId}">{tr}Edit{/tr}</a>
</td>
</tr>
{/section}
</table>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_quicktags.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_quicktags.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_quicktags.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>


