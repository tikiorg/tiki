{* $Id$ *}

<div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">{tr}Wysiwyg means What You See Is What You Get, and is handled in Tikiwiki by <a href="http://fckeditor.net">FCKeditor</a>{/tr}.</div>
</div>

<br />

<div class="cbox">
  <div class="cbox-title">{tr}Wysiwyg Editor Features{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=wysiwyg" method="post">
        <table class="admin">
          <tr>
            <td class="form"><label for="wysiwyg_optional">{tr}Wysiwyg Editor is optional{/tr}:</label></td>
            <td><input type="checkbox" name="wysiwyg_optional" id="wysiwyg_optional" {if $prefs.wysiwyg_optional eq 'y'}checked="checked"{/if} /></td>
          </tr>
          
          <tr>
            <td class="form"><label for="wysiwyg_default">{tr}... and is displayed by default{/tr}:</label></td>
            <td><input type="checkbox" name="wysiwyg_default" id="wysiwyg_default" {if $prefs.wysiwyg_default eq 'y'}checked="checked"{/if} /></td>
          </tr>
          
          <tr>
            <td class="form"><label for="wysiwyg_memo">{tr}Reopen with the same editor{/tr}:</label></td>
            <td><input type="checkbox" name="wysiwyg_memo" id="wysiwyg_memo" {if $prefs.wysiwyg_memo eq 'y'}checked="checked"{/if} /></td>
          </tr>

          <tr>
            <td class="form"><label for="wysiwyg_wiki_parsed">{tr}Content is parsed like wiki page{/tr}:</label></td>
            <td><input type="checkbox" name="wysiwyg_wiki_parsed" id="wysiwyg_wiki_parsed" {if $prefs.wysiwyg_wiki_parsed eq 'y'}checked="checked"{/if} /></td>
          </tr>
          
          <tr>
            <td class="form"><label for="wysiwyg_wiki_semi_parsed">{tr}Content is partially parsed{/tr}:</label></td>
            <td><input type="checkbox" name="wysiwyg_wiki_semi_parsed" id="wysiwyg_wiki_semi_parsed" {if $prefs.wysiwyg_wiki_semi_parsed eq 'y'}checked="checked"{/if} /></td>
          </tr>
          
          <tr>
            <td class="form"><label for="wysiwyg_toolbar_skin">{tr}Toolbar skin{/tr}:</label></td>
            <td>
              <select name="wysiwyg_toolbar_skin" id="wysiwyg_toolbar_skin">
                <option value="default" {if $prefs.wysiwyg_toolbar_skin eq 'default'}selected="selected"{/if}> default</option>
                <option value="office2003" {if $prefs.wysiwyg_toolbar_skin eq 'office2003'}selected="selected"{/if}> office2003</option>
                <option value="silver" {if $prefs.wysiwyg_toolbar_skin eq 'silver'}selected="selected"{/if}> silver</option>
              </select>
            </td>
          </tr>
          
          <tr>
            <td class="form"><label for="wysiwyg_toolbar">{tr}Toolbar content{/tr}:</label></td><td>{tr}Restore defaults{/tr} <input type="checkbox" name="restore" /></td>
          </tr>
          
          <tr>
            <td colspan="2"><textarea cols="90" rows="8" name="wysiwyg_toolbar" id="wysiwyg_toolbar">{$prefs.wysiwyg_toolbar}</textarea></td>
          </tr>
          
          <tr>
            <td colspan="2" class="button"><input type="submit" name="wysiwygfeatures" value="{tr}Change preferences{/tr}" /></td>
          </tr>
        </table>
      </form>
  </div>
</div>

