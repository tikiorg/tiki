<a class="pagetitle" href="tiki-user_bookmarks.php">{tr}User Bookmarks{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
{if $parentId>0}[<a class="link" href="tiki-user_bookmarks.php">{tr}top{/tr}</a>] {/if}{tr}Current folder{/tr}: {$path}<br/>
<table class="normal">
<tr>
  <td class="heading">{tr}name{/tr}</td>
  <td class="heading">{tr}url{/tr}</td>
  <td class="heading">{tr}action{/tr}</td>
</tr>
{section name=ix loop=$folders}
<tr>
  <td><a href="tiki-user_bookmarks.php?parentId={$folders[ix].folderId}"><img border="0" src="img/icons/icon_folder.gif" /></a>&nbsp;{$folders[ix].name} ({$folders[ix].urls})</td>
  <td>&nbsp;</td>
  <td>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;removefolder={$folders[ix].folderId}">{tr}remove{/tr}</a>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editfolder={$folders[ix].folderId}">{tr}edit{/tr}</a>
  </td>
</tr>
{/section}
{section name=ix loop=$urls}
<tr>
  <td><a class="link" target="_blank" href="{$urls[ix].url}">{$urls[ix].name}</a>
  {if $tiki_p_cache_bookmarks eq 'y' and $urls[ix].datalen > 0}
  (<a href="tiki-user_cached_bookmark.php?urlid={$urls[ix].urlId}" class="link" target="_blank">{tr}cache{/tr}</a>)
  {/if}
  </td>
  <td>{$urls[ix].url}</td>
  <td>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;removeurl={$urls[ix].urlId}">{tr}remove{/tr}</a>
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editurl={$urls[ix].urlId}">{tr}edit{/tr}</a>
    {if $tiki_p_cache_bookmarks eq 'y'}
    <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;refreshurl={$urls[ix].urlId}">{tr}refresh{/tr}</a>
    {/if}
  </td>
</tr>
{/section}
</table>
<br/>
<table class="normal" cellpadding="0" cellspacing="0">
<tr> 
  <td width="50%">
    {tr}Add or edit folder{/tr}
    <!-- form to add a category -->
    <table width="100%">
      <form action="tiki-user_bookmarks.php" method="post">
      <input type="hidden" name="editfolder" value="{$editfolder}" />
      <input type="hidden" name="parentId" value="{$parentId}" />
      <tr><td class="formcolor">{tr}name{/tr}:</td>
          <td class="formcolor"><input type="text" name="foldername" value="{$foldername}" /></td>
      </tr>
      <tr><td class="formcolor">&nbsp;</td>
          <td class="formcolor"><input type="submit" name="addfolder" value="{tr}add{/tr}" /></td>
      </tr>
      </form>
    </table>
  </td>
  <td width="50%">
    <!-- form to add a url -->
    {tr}Add or edit a URL{/tr}
    <table width="100%">
      <form action="tiki-user_bookmarks.php" method="post">
      <input type="hidden" name="editurl" value="{$editurl}" />
      <input type="hidden" name="parentId" value="{$parentId}" />
      <tr><td class="formcolor">{tr}Name{/tr}:</td>
          <td class="formcolor"><input type="text" name="urlname" value="{$urlname}" /></td>
      </tr>
      <tr><td class="formcolor">{tr}URL{/tr}:</td>
          <td class="formcolor"><input type="text" name="urlurl" value="{$urlurl}" /></td>
      </tr>
      <tr><td class="formcolor">&nbsp;</td>
          <td class="formcolor"><input type="submit" name="addurl" value="{tr}add{/tr}" /></td>
      </tr>
      </form>
    </table>
  </td>
</tr>
</table>
