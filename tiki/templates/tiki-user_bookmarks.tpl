<h1><a class="pagetitle" href="tiki-user_bookmarks.php">{tr}User Bookmarks{/tr}</a>
{if $feature_help eq 'y'}
<a href="{$helpurl}User+Bookmarks" target="tikihelp" class="tikihelp" title="{tr}User Bookmarks{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_bookmarks.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Bookmarks tpl{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}edit template{/tr}' /></a>
{/if}</h1>


{include file=tiki-mytiki_bar.tpl}
<br />
<br />
{if $parentId>0}[<a class="link" href="tiki-user_bookmarks.php">{tr}top{/tr}</a>] {/if}{tr}Current folder{/tr}: {$path}<br />
<h2>{tr}Folders{/tr}</h2>
<table class="normal">
<tr>
  <td class="heading">{tr}name{/tr}</td>
  <td  class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$folders}
<tr>
  <td class="{cycle advance=false}"><a href="tiki-user_bookmarks.php?parentId={$folders[ix].folderId}"><img border="0" src="img/icons/folderin.gif" alt='{tr}Folder in{/tr}'/></a>&nbsp;{$folders[ix].name} ({$folders[ix].urls})</td>
  <td class="{cycle}">
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;removefolder={$folders[ix].folderId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove folder{/tr}' border='0' /></a>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editfolder={$folders[ix].folderId}">{html_image file='img/icons/edit.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}'}</a>
  </td>
</tr>
{/section}
</table>


<h2>{tr}Bookmarks{/tr}</h2>
<table class="normal">
<tr>
  <td  class="heading">{tr}name{/tr}</td>
  <td  class="heading">{tr}url{/tr}</td>
  <td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$urls}
<tr>
  <td class="{cycle advance=false}"><a class="link" target="_blank" href="{$urls[ix].url}">{$urls[ix].name}</a>
  {if $tiki_p_cache_bookmarks eq 'y' and $urls[ix].datalen > 0}
  (<a href="tiki-user_cached_bookmark.php?urlid={$urls[ix].urlId}" class="link" target="_blank">{tr}cache{/tr}</a>)
  {/if}
  </td>
  <td class="{cycle advance=false}">{textformat wrap="60" wrap_cut=true wrap_char="<br />"}{$urls[ix].url}{/textformat}</td>
  <td class="{cycle}">
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;removeurl={$urls[ix].urlId}"><img src="img/icons2/delete.gif" border="0" width="16" height="16"  alt='{tr}remove{/tr}' title='{tr}remove{/tr}' /></a>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editurl={$urls[ix].urlId}">{html_image file='img/icons/edit.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}'}</a>
    {if $tiki_p_cache_bookmarks eq 'y' and $urls[ix].datalen > 0}
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;refreshurl={$urls[ix].urlId}"><img src='img/icons/refresh.gif' alt="{tr}refresh cache{/tr}" title="{tr}refresh cache{/tr}" border='0' /></a>
    {/if}
  </td>
</tr>
{/section}
</table>
<br />
<h2>{tr}Admin folders and bookmarks{/tr}</h2>
<table class="normal" cellpadding="0" cellspacing="0">
<tr>
  <td >
    <b>{tr}Add or edit folder{/tr}</b>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editfolder=0">{tr}new{/tr}</a>
    {* form to add a category *}
    <table >
      <form action="tiki-user_bookmarks.php" method="post">
      <input type="hidden" name="editfolder" value="{$editfolder|escape}" />
      <input type="hidden" name="parentId" value="{$parentId|escape}" />
      <tr><td class="formcolor">{tr}name{/tr}:</td>
          <td class="formcolor"><input type="text" size = "40" name="foldername" value="{$foldername|escape}" /></td>
      </tr>
      <tr><td class="formcolor">&nbsp;</td>
          <td class="formcolor">
	  <input type="submit" name="addfolder"  value="{tr}add{/tr}" /></td>
      </tr>
      </form>
    </table>
  </td>
  <td >
    {* form to add a url *}
    <b>{tr}Add or edit a URL{/tr}</b>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editurl=0">{tr}new{/tr}</a>
    <table >
      <form action="tiki-user_bookmarks.php" method="post">
      <input type="hidden" name="editurl" value="{$editurl|escape}" />
      <input type="hidden" name="parentId" value="{$parentId|escape}" />
      <tr><td class="formcolor">{tr}Name{/tr}:</td>
          <td class="formcolor"><input type="text" size = "40"  name="urlname" value="{$urlname|escape}" /></td>
      </tr>
      <tr><td class="formcolor">{tr}URL{/tr}:</td>
          <td class="formcolor"><input type="text" size = "40" name="urlurl" value="{$urlurl|escape}" /></td>
      </tr>
      <tr><td class="formcolor">&nbsp;</td>
          <td class="formcolor"><input type="submit" name="addurl" value="{tr}add{/tr}" /></td>
      </tr>
      </form>
    </table>
  </td>
</tr>
</table>
