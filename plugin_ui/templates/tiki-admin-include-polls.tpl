{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To add/remove polls, look for "Polls" under "Admin" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_polls.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<div class="cbox">
<div class="cbox-title">
  {tr}{$crumbs[$crumb]->description}{/tr}
  {help crumb=$crumbs[$crumb]}
</div>
<div class="cbox-data">
    <form method="post" action="tiki-admin.php?page=polls">
    <table class="admin">
    <tr><td class="form">{tr}Allow Anonymous Poll Voting{/tr}:</td><td><input type="checkbox" name="feature_poll_anonymous" {if $prefs.feature_poll_anonymous eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_poll_comments" {if $prefs.feature_poll_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="poll_comments_per_page" value="{$prefs.poll_comments_per_page|escape}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}:
    </td><td>
    <select name="poll_comments_default_ordering">
    <option value="commentDate_desc" {if $prefs.poll_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
		<option value="commentDate_asc" {if $prefs.poll_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
    <option value="points_desc" {if $prefs.poll_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
	<tr><td class="form">{tr}Show categories{/tr}</td><td><input type="checkbox" name="poll_list_categories" {if $prefs.poll_list_categories eq 'y'}checked="checked"{/if}/></td></tr>
	<tr><td class="form">{tr}Show objects{/tr}</td><td><input type="checkbox" name="poll_list_objects" {if $prefs.poll_list_objects eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="pollprefs" value="{tr}Change settings{/tr}" /></td></tr>
    </table>
    </form>
</div>
</div>


