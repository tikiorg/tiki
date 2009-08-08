{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Wysiwyg means What You See Is What You Get, and is handled in Tikiwiki by <a href="http://fckeditor.net">FCKeditor</a>{/tr}.{/remarksbox}

<form action="tiki-admin.php?page=wysiwyg" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="wysiwygfeatures" value="{tr}Change preferences{/tr}" />
		</div>
		<fieldset class="admin">
 			 <legend>{tr}Wysiwyg Editor Features{/tr}</legend>
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
            <td colspan="2"><textarea style="width:100%" rows="8" cols="60" name="wysiwyg_toolbar" id="wysiwyg_toolbar">{$prefs.wysiwyg_toolbar|escape}</textarea></td>
          </tr>
          
        </table>
			</fieldset>
			<div class="heading input_submit_container" style="text-align: center">
				<input type="submit" name="wysiwygfeatures" value="{tr}Change preferences{/tr}" />
			</div>
     </form>

