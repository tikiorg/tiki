{if $feature_help eq 'y'}
<div class="simplebox">{tr}Tip{/tr}: {tr}to add/remove polls, look for "Polls" under "Admin" on the application menu, or{/tr} <a class="link" href="tiki-admin_polls.php">{tr}click here{/tr}</a>.</div><br />
{/if}

<div class="cbox">
<div class="cbox-title">{tr}Poll settings{/tr}</div>
<div class="cbox-data">
<div class="simplebox">

<div class="titlebar">{tr}Poll general options{/tr}</div>
<table class="admin">
<tr><td class="form">{tr}New polls can be submitted{/tr}:</td><td><input type="checkbox" name="feature_poll_submissions" {if $feature_poll_submissions eq 'y'}checked="checked"{/if}/></td></tr>
</td></tr></table>

<div class="titlebar">{tr}Poll comments settings{/tr}</div>
<form method="post" action="tiki-admin.php?page=polls">
<table class="admin">
<tr><td class="form">{tr}Allow Anonymous Poll Voting{/tr}:</td><td><input type="checkbox" name="feature_poll_anonymous" {if $feature_poll_anonymous eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_poll_comments" {if $feature_poll_comments eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Comments on items{/tr}:</td><td><input type="checkbox" name="feature_poll_item_comments" {if $feature_poll_item_comments eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="poll_comments_per_page" value="{$poll_comments_per_page|escape}" /></td></tr>
<tr><td class="form">{tr}Comments default ordering{/tr}:
</td><td>
<select name="poll_comments_default_ordering">
<option value="commentDate_desc" {if $poll_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
<option value="commentDate_asc" {if $poll_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
<option value="points_desc" {if $poll_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
</select>
</td></tr>
</table>

<div class="titlebar">{tr}Save when done{/tr}</div>
<input type="submit" name="pollprefs" value="{tr}Change preferences{/tr}" />

</form>
</div>
</div>
</div>


