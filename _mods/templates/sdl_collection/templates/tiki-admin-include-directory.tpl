<div class="cbox">
  <div class="cbox-title">{tr}Directory{/tr}</div>
  <div class="cbox-data">
    <div class="simplebox">
      <form action="tiki-admin.php?page=directory" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="directory-columns">{tr}Number of columns per page when listing categories{/tr}</label></td>
          <td><select name="directory_columns" id="directory-columns">
              <option value="1" {if $directory_columns eq 1}selected="selected"{/if}>1</option>
              <option value="2" {if $directory_columns eq 2}selected="selected"{/if}>2</option>
              <option value="3" {if $directory_columns eq 3}selected="selected"{/if}>3</option>
              <option value="4" {if $directory_columns eq 4}selected="selected"{/if}>4</option>
              <option value="5" {if $directory_columns eq 5}selected="selected"{/if}>5</option>
              <option value="6" {if $directory_columns eq 6}selected="selected"{/if}>6</option>
              </select></td>
        </tr><tr>
          <td class="form"><label for="directory-links">{tr}Links per page{/tr}</label></td>
          <td><input type="text" name="directory_links_per_page" id="directory-links"
               value="{$directory_links_per_page|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="directory-validate">{tr}Validate URLs{/tr}</label></td>
          <td><input type="checkbox" name="directory_validate_urls" id="directory-validate"
              {if $directory_validate_urls eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="directory-openlinks">{tr}Method to open directory links{/tr}</label></td>
          <td><select name="directory_open_links" id="directory-openlinks">
              <option value="r" {if $directory_open_links eq 'r'}selected="selected"{/if}>{tr}replace current window{/tr}</option>
              <option value="n" {if $directory_open_links eq 'n'}selected="selected"{/if}>{tr}new window{/tr}</option>
              <option value="f" {if $directory_open_links eq 'f'}selected="selected"{/if}>{tr}inline frame{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="directory"
              value="{tr}Change Preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
  </div>
</div>