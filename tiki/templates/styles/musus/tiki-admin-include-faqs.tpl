<div class="tiki">
  <div class="tiki-title">{tr}FAQs settings{/tr}</div>
  <div class"tiki-content">
    <div class="simplebox">{tr}FAQ comments{/tr}<br />
      <form action="tiki-admin.php?page=faqs" method="post">
        <table class="admin"><tr>
          <td><label>{tr}Comments{/tr}: </label></td>
          <td><input type="checkbox" name="feature_faq_comments" id="" {if $feature_faq_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td><label>{tr}Default number of comments per page{/tr}: </label></td>
          <td><input size="5" type="text" name="faq_comments_per_page" id="" value="{$faq_comments_per_page|escape}" /></td>
        </tr><tr>
          <td><label>{tr}Default sort order{/tr}: </label></td>
          <td><select name="faq_comments_default_ordering" id="">
              <option value="commentDate_desc" {if $faq_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
			  <option value="commentDate_asc" {if $faq_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $faq_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
          </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="faqcomprefs" value="{tr}Apply{/tr}" /></td>
        </tr></table>
      </form>
    </div>
  </div>
</div>