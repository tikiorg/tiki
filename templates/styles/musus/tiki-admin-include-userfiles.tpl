<div class="tiki">
	<div class="tiki-title">{tr}User files{/tr}</div>
	<div class="tiki-content">
		<div class="simplebox">
			<form action="tiki-admin.php?page=userfiles" method="post">
			<table class="admin">
				<tr><td><label for="userfiles-quota">{tr}Quota (Mb){/tr}</label></td><td>
					<input type="text" id="userfiles-quota" value="{$userfiles_quota|escape}" size="5" />
				</td></tr>
  				<tr><td><label for="userfiles-db">{tr}Use database to store userfiles{/tr}:</label></td><td>
					<input type="radio" id="userfiles-db" value="y" {if $uf_use_db eq 'y'}checked="checked"{/if}/></td>
				</tr>
				<tr><td><label for="userfiles-dir">{tr}Use a directory to store userfiles{/tr}:</label></td><td>
					<input type="radio" id="userfiles-dir" value="n" {if $uf_use_db eq 'n'}checked="checked"{/if}/> 
					<label for="userfiles-path">{tr}Path{/tr}:</label><br />
					<input type="text" id="userfiles-path" value="{$uf_use_dir|escape}" size="50" /></td>
				</tr>
				<tr>
					<td colspan="2" class="button"><input type="submit" name="userfilesprefs" value="{tr}Change preferences{/tr}" /></td>
				</tr>
			</table>
			</form>
		</div>
	</div>
</div>