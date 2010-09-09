{include file='header_simple.tpl'}
		<div class="center itranslator" style="clear: both">

			<h1>{tr}Interactive translator{/tr}</h1>

{if $error eq 'y'}
			<h2>{$msg}</h2>
{else}
			<strong><em>{$analysed_word}</em></strong>

	{if $update eq 'y'}
			{tr}has been updated{/tr}
			<p>
				{button href="#" _onclick="window.opener.location.reload(); self.close(); return false" _text="{tr}Close this window{/tr}"}
				{button href="#" _onclick="history.go(-1);" _text="{tr}Go Back{/tr}"}
			</p>
			<p><small>*{tr}Clicking 'Close this window' will reload the main window from where it was opened{/tr}</small></p>
	{else}
			<p>
				{button href="#" _onclick="self.close();" _text="{tr}Close this window{/tr}"}
			</p>
			
			<form><table class='normal'>
		{assign var=first value=""}
		{cycle values="odd,even" print=false}
		{section name=ix loop=$entries}
			{if  $first ne $entries[ix].lang }
				{assign var=first value=$entries[ix].lang}
				<tr><td colspan='2'>&nbsp;</td></tr>
				<tr><td colspan=2 class='heading'><b>{tr}Language{/tr}:<i> {$entries[ix].lang|escape}</i></b></td></tr>
			{/if}
				<tr class="{cycle}"><td width='40%'>
				   	<input type="hidden" name='lang' value='{$entries[ix].lang|escape}'>
					<input type="hidden" name='src' value='{$entries[ix].urlsource|escape}'>{$entries[ix].source|escape}
				</td><td>
					<input type="text" name='dst' value='{$entries[ix].trans|escape}' style='width:190px;' />
					<input type="submit" name='submit' value="{tr}Submit{/tr}" />
			   	</td></tr>
		{/section}
			</table></form>
	{/if}{*End of not-update*}
{/if}{*End of not-error*}

		</div>
	</body>
</html>
