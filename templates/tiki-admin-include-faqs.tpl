
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To add/remove FAQs, look for "Admin FAQs" under "FAQs" on the application menu, or{/tr} <a class="rbox-link" href="tiki-list_faqs.php">{tr}Click Here{/tr}</a>.</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=faqs" method="post">
        <table class="admin"><tr>
          <td class="form">{tr}Comments{/tr}:</td>
          <td><input type="checkbox" name="feature_faq_comments"
              {if $prefs.feature_faq_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Default number of comments per page{/tr}: </td>
          <td><input size="5" type="text" name="faq_comments_per_page"
              value="{$prefs.faq_comments_per_page|escape}" /></td>
        </tr><tr>
          <td class="form">{tr}Comments default ordering{/tr}</td>
          <td><select name="faq_comments_default_ordering">
              <option value="commentDate_desc" {if $prefs.faq_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
							<option value="commentDate_asc" {if $prefs.faq_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $prefs.faq_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
              </select></td>
        </tr><tr>
          <td class="form">{tr}Question and Answer prefix on Answers{/tr}</td>
          <td><select name="faq_prefix">
              <option value="none" {if $prefs.faq_prefix eq 'none'}selected="selected"{/if}>{tr}None{/tr}</option>
							<option value="QA" {if $prefs.faq_prefix eq 'QA'}selected="selected"{/if}>{tr}Q and A{/tr}</option>
              <option value="question_id" {if $prefs.faq_prefix eq 'question_id'}selected="selected"{/if}>{tr}Question ID{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="faqcomprefs"
              value="{tr}Change settings{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

