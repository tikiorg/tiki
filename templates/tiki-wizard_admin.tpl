{* $Id$ *}

{title}{tr}Admin Wizard{/tr}{/title}

<div id="wizardBody"> 
{include file="{$wizardBody}"}
</div>

<table style="width:100%">
<tr> 
<td style="text-align:left">
	<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> Show on login
	</td>
<td style="text-align:left">
	&nbsp;</td>
<td style="text-align:right">
	<input type="hidden" name="set_up_environment" value="y">
	<input type="hidden" name="url" value="{$homepageUrl}">
	<input type="hidden" name="wizard_step" value="{$wizard_step}">
	<input type="button" name="Cancel" value="{tr}Cancel{/tr}" onclick="window.location='{$homepageUrl}'" />
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="continue" value="{tr}Save and Continue{/tr}" />
	</td>
</tr>
</table>
</form>
