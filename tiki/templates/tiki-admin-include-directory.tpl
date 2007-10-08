
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To configure your directory, find "Admin directory" under "Directory" on the application menu, or{/tr} <a class="rbox-link" href="tiki-directory_admin.php">{tr}Click Here{/tr}</a>.</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->title}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=directory" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="directory-columns">{tr}Number of columns per page when listing categories{/tr}</label></td>
          <td><select name="directory_columns" id="directory-columns">
              <option value="1" {if $prefs.directory_columns eq 1}selected="selected"{/if}>1</option>
              <option value="2" {if $prefs.directory_columns eq 2}selected="selected"{/if}>2</option>
              <option value="3" {if $prefs.directory_columns eq 3}selected="selected"{/if}>3</option>
              <option value="4" {if $prefs.directory_columns eq 4}selected="selected"{/if}>4</option>
              <option value="5" {if $prefs.directory_columns eq 5}selected="selected"{/if}>5</option>
              <option value="6" {if $prefs.directory_columns eq 6}selected="selected"{/if}>6</option>
              </select></td>
        </tr><tr>
          <td class="form"><label for="directory-links">{tr}Links per page{/tr}</label></td>
          <td><input type="text" name="directory_links_per_page" id="directory-links"
               value="{$prefs.directory_links_per_page|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="directory-validate">{tr}Validate URLs{/tr}</label></td>
          <td><input type="checkbox" name="directory_validate_urls" id="directory-validate"
              {if $prefs.directory_validate_urls eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="directory-cool-sites">{tr}enable cool sites{/tr}</label></td>
          <td><input type="checkbox" name="directory_cool_sites" id="directory-cool-sites"
              {if $prefs.directory_cool_sites eq 'y'}checked="checked"{/if}></td>
 	 </tr><tr>
          <td class="form"><label for="directory-country-flag">{tr}Show Country Flag{/tr}</label></td>
          <td><input type="checkbox" name="directory_country_flag" id="directory-country-flag"
              {if $prefs.directory_country_flag eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="directory-openlinks">{tr}Method to open directory links{/tr}</label></td>
          <td><select name="directory_open_links" id="directory-openlinks">
              <option value="r" {if $prefs.directory_open_links eq 'r'}selected="selected"{/if}>{tr}replace current window{/tr}</option>
              <option value="n" {if $prefs.directory_open_links eq 'n'}selected="selected"{/if}>{tr}New Window{/tr}</option>
              <option value="f" {if $prefs.directory_open_links eq 'f'}selected="selected"{/if}>{tr}inline frame{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="directory"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>
