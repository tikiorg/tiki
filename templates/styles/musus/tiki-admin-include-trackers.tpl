<div class="tiki">
<div class="tiki-title">{tr}Trackers{/tr}</div>
<div class="tiki-content">
<div class="simplebox">
<form action="tiki-admin.php?page=trackers" method="post">
<table class="admin">
<tr><td><label>{tr}Use database to store files{/tr}:</label></td>
<td><input type="radio" name="t_use_db" value="y" {if $t_use_db eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr><td><label>{tr}Use a directory to store files{/tr}:</label></td>
<td><input type="radio" name="t_use_db" value="n" {if $t_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<br /><input type="text" name="t_use_dir" value="{$t_use_dir|escape}" size="50" /></td>
</tr>
<tr><td colspan="2"><input type="submit" name="trkset" value="{tr}Change preferences{/tr}" /></td></tr>    
</table>
</form>
</div>
</div>
</div>