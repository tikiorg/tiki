<div class="cbox">
  <div class="cbox-title">{tr}FAQs Settings{/tr}</div>
  <div class"cbox-data">
    <div class="simplebox">
      {tr}FAQ comments{/tr}<br />
      <form action="tiki-admin.php?page=faqs" method="post">
        <table class="admin"><tr>
          <td class="form">{tr}Comments{/tr}:</td>
          <td><input type="checkbox" name="feature_faq_comments"
              {if $feature_faq_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Default number of comments per page{/tr}: </td>
          <td><input size="5" type="text" name="faq_comments_per_page"
              value="{$faq_comments_per_page|escape}" /></td>
        </tr><tr>
          <td class="form">{tr}Comments default ordering{/tr}</td>
          <td><select name="faq_comments_default_ordering">
              <option value="commentDate_desc" {if $faq_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
							<option value="commentDate_asc" {if $faq_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $faq_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="faqcomprefs"
              value="{tr}Change Preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
  </div>
</div>


