<div class="tiki">
	<div class="tiki-title">{tr}Webmail{/tr}</div>
	<div class="tiki-content">
		<div class="simplebox">
			<form action="tiki-admin.php?page=webmail" method="post">
				<table class="admin">
					<tr><td><label for="webmail-html">{tr}Allow viewing HTML mails?{/tr}</label></td>
						<td><input type="checkbox" id="webmail-html" {if $webmail_view_html eq 'y'}checked="checked"{/if} /></td>
					</tr>
					<tr><td><label for="webmail-attach">{tr}Maximum size for each attachment{/tr}:</label></td>
						<td><select name="webmail_max_attachment" id="webmail-attach">
							<option value="500000" {if $webmail_max_attachment eq 500000}selected="selected"{/if}>500Kb</option>
							<option value="1000000" {if $webmail_max_attachment eq 1000000}selected="selected"{/if}>1Mb</option>
							<option value="1500000" {if $webmail_max_attachment eq 1500000}selected="selected"{/if}>1.5Mb</option>
							<option value="2000000" {if $webmail_max_attachment eq 2000000}selected="selected"{/if}>2Mb</option>
							<option value="2500000" {if $webmail_max_attachment eq 2500000}selected="selected"{/if}>2.5Mb</option>
							<option value="3000000" {if $webmail_max_attachment eq 3000000}selected="selected"{/if}>3Mb</option>
							<option value="100000000" {if $webmail_max_attachment eq 100000000}selected="selected"{/if}>Unlimited</option>
						</select>
					</tr>
					<tr><td colspan="2" class="button">
						<input type="submit" name="webmail" value="{tr}Change preferences{/tr}" />
					</td></tr>
				</table>
			</form>
		</div>
	</div>
</div>