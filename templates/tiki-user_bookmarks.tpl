<a class="pagetitle" href="tiki-user_bookmarks.php">{tr}User Bookmarks{/tr}</a><br /><br />
{include file=tiki-mytiki_bar.tpl}
<br /><br />
{if $parentId>0}[<a class="link" href="tiki-user_bookmarks.php">{tr}top{/tr}</a>] {/if}{tr}Current folder{/tr}: {$path}<br />
<h3>{tr}Folders{/tr}</h3>
<table class="normal">
<tr>
  <td class="heading">{tr}name{/tr}</td>
  <td width="8%" class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$folders}
<tr>
  <td class="{cycle advance=false}"><a href="tiki-user_bookmarks.php?parentId={$folders[ix].folderId}"><img border="0" src="img/icons/folderin.gif" /></a>&nbsp;{$folders[ix].name} ({$folders[ix].urls})</td>
  <td class="{cycle}">
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;removefolder={$folders[ix].folderId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove folder{/tr}' border='0' /></a>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editfolder={$folders[ix].folderId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' border='0' /></a>
  </td>
</tr>
{/section}
</table>

<h3>{tr}Bookmarks{/tr}</h3>
<table class="normal">
<tr>
  <td width="45%" class="heading">{tr}name{/tr}</td>
  <td width="45%" class="heading">{tr}url{/tr}</td>
  <td width="10%"class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$urls}
<tr>
  <td class="{cycle advance=false}"><a class="link" target="_blank" href="{$urls[ix].url}">{$urls[ix].name}</a>
  {if $tiki_p_cache_bookmarks eq 'y' and $urls[ix].datalen > 0}
  (<a href="tiki-user_cached_bookmark.php?urlid={$urls[ix].urlId}" class="link" target="_blank">{tr}cache{/tr}</a>)
  {/if}
  </td>
  <td class="{cycle advance=false}">{textformat wrap="10" wrap_cut=true}{$urls[ix].url}{/textformat}</td>
  <td class="{cycle}">
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;removeurl={$urls[ix].urlId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove bookmark{/tr}' border='0' /></a>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editurl={$urls[ix].urlId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' border='0' /></a>
    {if $tiki_p_cache_bookmarks eq 'y' and $urls[ix].datalen > 0}
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;refreshurl={$urls[ix].urlId}"><img src='img/icons/refresh.gif' alt='{tr}refresh cache{/tr}' title='{tr}refresh cache{/tr}' border='0' /></a>
    {/if}
  </td>
</tr>
{/section}
</table>
<br />
<h3>{tr}Admin folders and bookmarks{/tr}</h3>
<table class="normal" cellpadding="0" cellspacing="0">
<tr> 
  <td width="50%">
    <b>{tr}Add or edit folder{/tr}</b>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editfolder=0">{tr}new{/tr}</a>
    <!-- form to add a category -->
    <table width="100%">
      <form action="tiki-user_bookmarks.php" method="post">
      <input type="hidden" name="editfolder" value="{$editfolder|escape}" />
      <input type="hidden" name="parentId" value="{$parentId|escape}" />
      <tr><td class="formcolor">{tr}name{/tr}:</td>
          <td class="formcolor"><input type="text" name="foldername" value="{$foldername|escape}" /></td>
      </tr>
      <tr><td class="formcolor">&nbsp;</td>
          <td class="formcolor"><input type="submit" name="addfolder" value="{tr}add{/tr}" /></td>
      </tr>
      </form>
    </table>
  </td>
  <td width="50%">
    <!-- form to add a url -->
    <b>{tr}Add or edit a URL{/tr}</b>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editurl=0">{tr}new{/tr}</a>
    <table width="100%">
      <form action="tiki-user_bookmarks.php" method="post">
      <input type="hidden" name="editurl" value="{$editurl|escape}" />
      <input type="hidden" name="parentId" value="{$parentId|escape}" />
      <tr><td class="formcolor">{tr}Name{/tr}:</td>
          <td class="formcolor"><input type="text" name="urlname" value="{$urlname|escape}" /></td>
      </tr>
      <tr><td class="formcolor">{tr}URL{/tr}:</td>
          <td class="formcolor"><input type="text" name="urlurl" value="{$urlurl|escape}" /></td>
      </tr>
      <tr><td class="formcolor">&nbsp;</td>
          <td class="formcolor"><input type="submit" name="addurl" value="{tr}add{/tr}" /></td>
      </tr>
      </form>
    </table>
  </td>
</tr>
</table>
