{* $Header: /cvsroot/tikiwiki/_mods/templates/kuro/templates/styles/kuroBK/tiki-show_page_header.tpl,v 1.2 2005-02-10 10:10:07 michael_davey Exp $ *}
<table width="100%">
<tr>
<td width="50%" class="pagedescription">
<table width="100%">

<tr>
<td>

{include file="tiki-site_header.tpl"}

</td></tr></table>
</td><td width="50%" class="wikibar">
<table align="right" width="100%">
<tr>
<td align="right">

{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categorypath eq 'y'}
{$display_catpath}
{/if}


{if $cached_page eq 'y'}
<small>(cached)</small>
{/if}

{if !$lock and ($tiki_p_edit eq 'y' or $page eq 'SandBox') and $beingEdited ne 'y'}
<a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$page|escape:"url"}"><img border="0" src="img/icons/edit.gif" alt='{tr}edit{/tr}' /></a>
{/if}


{if $cached_page eq 'y'}
<a title="{tr}refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1"><img border="0" src="img/icons/ico_redo.gif" alt='{tr}refresh{/tr}' /></a>
{/if}

<a title="{tr}print{/tr}" href="tiki-print.php?page={$page|escape:"url"}"><img border="0" src="img/icons/ico_print.gif" alt='{tr}print{/tr}' /></a>

{if $feature_wiki_pdf eq 'y'}
<a title="{tr}create pdf{/tr}" href="tiki-config_pdf.php?page={$page|escape:"url"}"><img border="0" src="img/icons/ico_pdf.gif" alt='{tr}pdf{/tr}' /></a>
{/if}

{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1"><img border="0" src="img/icons/ico_save.gif" alt="{tr}save{/tr}" /></a>
{/if}

{if $user and $feature_user_watches eq 'y'}
  {if $user_watching_page eq 'n'}
    <a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add"><img border='0' alt='{tr}monitor this page{/tr}' title='{tr}monitor this page{/tr}' src='img/icons/icon_watch.png' /></a>
  {else}
    <a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove"><img border='0' alt='{tr}stop monitoring this page{/tr}' title='{tr}stop monitoring this page{/tr}' src='img/icons/icon_unwatch.png' /></a>
  {/if}
{/if}
{if $feature_backlinks eq 'y' and $backlinks}
    </td><td>
    <form href="tiki-index.php">
      <select name="page" onchange="page.form.submit()">
	    <option value="{$page}">{tr}backlinks{/tr}...</option>
		{section name=back loop=$backlinks}
		  <option value="{$backlinks[back].fromPage}">{$backlinks[back].fromPage}</option>
		{/section}
	  </select>
    </form>
{/if}
{if isset($showstructs) and (count($showstructs) ne 0)}
    </td><td>
    <form href="tiki-index.php">
      <select name="page_ref_id" onchange="page_ref_id.form.submit()">
	    <option>{tr}Structures{/tr}...</option>
		{section name=struct loop=$showstructs}
		  <option value="{$showstructs[struct].req_page_ref_id}">
{if $showstructs[struct].page_alias}
{$showstructs[struct].page_alias}
{else}
{$showstructs[struct].pageName}
{/if}
          </option>
		{/section}
	  </select>
    </form>
{else}
    &nbsp;
{/if}
</td>
</tr>
</table>
</td>
</tr>
</table>

