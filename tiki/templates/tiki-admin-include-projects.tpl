<div class="cbox">
<div class="cbox-title">{tr}Projects{/tr}</div>
<div class="cbox-data">
        <form action="tiki-admin.php?page=projects" method="post">
        <table class="admin">
        <tr><td class="form">{tr}<b>Item</b>{/tr}</td>
            <td class="form">{tr}<b>Value</b>{/tr}</td>
        </tr>
	<tr><td class="form">{tr}Users choose project categories{/tr}</td>
	    <td><input type="checkbox" name="feature_project_user_cats"
	        {if $feature_project_user_cats eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr><td class="form">{tr}Prefix for Project Admin groups{/tr}</td>
	    <td><input type="text" name="feature_project_group_prefix_admin" size="30" value="{$feature_project_group_prefix_admin}" /></td>
	</tr>
        <tr><td class="form">{tr}Prefix for Project groups{/tr}</td>
	    <td><input type="text" name="feature_project_group_prefix" size="30" value="{$feature_project_group_prefix}" /></td>
	</tr>
	<tr><td class="form">{tr}Prefix for Wiki project home page{/tr}</td>
	    <td><input type="text" name="feature_project_home_prefix" size="30" value="{$feature_project_home_prefix}" /></td>
	</tr>
	<tr><td class="form">{tr}Prefix for File Galleries{/tr}</td>
	    <td><input type="text" name="feature_project_filegal_prefix" size="30" value="{$feature_project_filegal_prefix}" /></td>
	</tr>
	<tr><td class="form">{tr}Project Admins group template{/tr}</td>
	    <td><select name="feature_project_admin_template">
	    <option value="0">{tr}choose a group ...{/tr}</option>
	    {foreach key=g item=gr from=$listgroups}
	    <option value="{$gr|escape}" {if $gr eq $feature_project_admin_template} selected="selected"{/if}>{$gr|truncate:"52":" ..."}</option>
	    {/foreach}
	    </select> <a href="tiki-admingroups.php">{tr}Create New Group{/tr}</a></td>
	</tr>
	<tr><td class="form">{tr}Project Members group template{/tr}</td>
	    <td><select name="feature_project_member_template">
	    <option value="0">{tr}choose a group ...{/tr}</option>
	    {foreach key=g item=gr from=$listgroups}
	    <option value="{$gr|escape}" {if $gr eq $feature_project_member_template} selected="selected"{/if}>{$gr|truncate:"52":" ..."}</option>
	    {/foreach}
	    </select> <a href="tiki-admingroups.php">{tr}Create New Group{/tr}</a></td>
	</tr>
	<tr><td class="form" colspan="3">&nbsp;</td></tr>
        <tr><td colspan="3" class="button"><input type="submit" name="projects" value="{tr}Change preferences{/tr}" /></td></tr>
        </table>
        </form>
</div>
</div>
