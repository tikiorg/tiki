<div class="cbox">
  <div class="cbox-title">{tr}Directory{/tr}</div>
  <div class="cbox-data">
    <div class="simplebox">
      <form action="tiki-admin.php?page=directory" method="post">
        <table ><tr>
          <td class="form">{tr}Number of columns per page when listing categories{/tr}</td>
          <td><select name="directory_columns">
              <option value="1" {if $directory_columns eq 1}selected="selected"{/if}>1</option>
              <option value="2" {if $directory_columns eq 2}selected="selected"{/if}>2</option>
              <option value="3" {if $directory_columns eq 3}selected="selected"{/if}>3</option>
              <option value="4" {if $directory_columns eq 4}selected="selected"{/if}>4</option>
              <option value="5" {if $directory_columns eq 5}selected="selected"{/if}>5</option>
              <option value="6" {if $directory_columns eq 6}selected="selected"{/if}>6</option>
              </select></td>
        </tr><tr>
          <td class="form">{tr}Links per page{/tr}</td>
          <td><input type="text" name="directory_links_per_page"
               value="{$directory_links_per_page|escape}" /></td>
        </tr><tr>
          <td class="form">{tr}Validate URLs{/tr}</td>
          <td><input type="checkbox" name="directory_validate_urls"
              {if $directory_validate_urls eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form">{tr}Method to open directory links{/tr}</td>
          <td><select name="directory_open_links">
              <option value="r" {if $directory_open_links eq 'r'}selected="selected"{/if}>{tr}replace current window{/tr}</option>
              <option value="n" {if $directory_open_links eq 'n'}selected="selected"{/if}>{tr}new window{/tr}</option>
              <option value="f" {if $directory_open_links eq 'f'}selected="selected"{/if}>{tr}inline frame{/tr}</option>
              </select></td>
        </tr><tr>
          <td align="center" colspan="2"><input type="submit" name="directory"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
  </div>
</div>
