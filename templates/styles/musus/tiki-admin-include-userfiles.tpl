<div class="tiki">
	<div class="tiki-title">{tr}User files{/tr}</div>
		<div class="tiki-content">
			<div class="simplebox">
				<form action="tiki-admin.php?page=userfiles" method="post">
				<table class="admin">
				<tr><td>{tr}Quota (Mb){/tr}</td><td>
				<input type="text" name="userfiles_quota" value="{$userfiles_quota|escape}" size="5" />
				</td></tr>
  				<tr><td>{tr}Use database to store userfiles{/tr}:</td><td><input type="radio" name="uf_use_db" value="y" {if $uf_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    			<tr><td>{tr}Use a directory to store userfiles{/tr}:</td><td><input type="radio" name="uf_use_db" value="n" {if $uf_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<br /><input type="text" name="uf_use_dir" value="{$uf_use_dir|escape}" size="50" /> </td></tr>
    			<tr><td colspan="2" class="button"><input type="submit" name="userfilesprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    			</table>
				</form>
			</div>
		</div>
</div>
