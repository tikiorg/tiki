{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To add/remove polls, look for "Polls" under "Admin" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_polls.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<form method="post" action="tiki-admin.php?page=polls">
<div class="cbox">
<table class="admin"><tr><td>
<div align="center" style="padding:1em;"><input type="submit" name="calprefs" value="{tr}Change settings{/tr}" /></div>
<fieldset><legend>{tr}Polls{/tr}{if $prefs.feature_help eq 'y'} {help url="Polls+Config"}{/if}</legend>
<input type="hidden" name="pollprefs" />
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_poll_anonymous" name="feature_poll_anonymous" {if $prefs.feature_poll_anonymous eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_poll_anonymous">{tr}Anonymous voting{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_poll_revote" name="feature_poll_revote" {if $prefs.feature_poll_revote eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_poll_revote">{tr}Allow re-voting{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_poll_comments" name="feature_poll_comments" {if $prefs.feature_poll_comments eq 'y'}checked="checked" {/if}onclick="flip('usecomments');" /></div>
	<div class="adminoptionlabel"><label for="feature_poll_comments">{tr}Comments{/tr}</label>
<div class="adminoptionboxchild" id="usecomments" style="display:{if $prefs.feature_poll_comments eq 'y'}block{else}none{/if}">	
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="poll_comments_per_page">{tr}Default number per page{/tr}: </label><input size="5" type="text" name="poll_comments_per_page" id="poll_comments_per_page" value="{$prefs.poll_comments_per_page|escape}" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="poll_comments_default_ordering">{tr}Default ordering{/tr}: </label>
	<select name="poll_comments_default_ordering" id="poll_comments_default_ordering">
    <option value="commentDate_desc" {if $prefs.poll_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
		<option value="commentDate_asc" {if $prefs.poll_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
    <option value="points_desc" {if $prefs.poll_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
	</div>
</div>	
</div>	
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="poll_list_categories" name="poll_list_categories" {if $prefs.poll_list_categories eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="poll_list_categories">{tr}Show categories{/tr}</label>
	{if $prefs.feature_categories ne 'y'}<br />{icon _id=information} {tr}Categories disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>. {/if}
	
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="poll_list_objects" name="poll_list_objects" {if $prefs.poll_list_objects eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="poll_list_objects">{tr}Show objects{/tr}</label></div>
</div>


</fieldset>
<div align="center" style="padding:1em;"><input type="submit" name="calprefs" value="{tr}Change settings{/tr}" /></div>
</td></tr></table>
</div>
</form>
