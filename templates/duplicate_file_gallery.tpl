{* $Header: /cvsroot/tikiwiki/tiki/templates/duplicate_file_gallery.tpl,v 1.1.2.1 2008-03-01 00:47:54 nyloth Exp $ *}
{if $tiki_p_create_file_galleries eq 'y'}
<h2>{tr}Duplicate File Gallery{/tr}</h2>
<form action="tiki-list_file_gallery.php{if $filegals_manager eq 'y'}?filegals_manager{/if}" method="post">
  <table class="normal">
    <tr class="formcolor"><td>{tr}Name{/tr}</td><td><input type="text" size="50" name="name" value="{$name|escape}" /></td></tr>
    <tr class="formcolor"><td>{tr}Description{/tr}</td><td><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
    <tr class="formcolor"><td>{tr}File Gallery{/tr}</td>
      <td>
        <select name="galleryId">
        {section name=ix loop=$all_galleries}
          <option value="{$all_galleries[ix].id}">{$all_galleries[ix].name}</option>
        {/section}
        </select>
      </td>
    </tr>
    <tr class="formcolor"><td>{tr}Duplicate categories{/tr}</td><td><input type="checkbox" name="dupCateg" /></td></tr>
    <tr class="formcolor"><td>{tr}Duplicate perms{/tr}</td><td><input type="checkbox" name="dupPerms" /></td></tr>
    <tr class="formcolor"><td></td><td><input type="submit" name="duplicate" value="{tr}duplicate{/tr}" /></td></tr>
  </table>
</form>
{/if}
