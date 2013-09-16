{* $Id$ *}

{title}{tr}Admin Wizard{/tr}{/title}

<form action="tiki-wizard_admin.php" method="post">
<div id="wizardBody"> 
{include file="{$wizardBody}"}
</div>

<table style="width:100%">
<tr> 
<td style="text-align:left">
	<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> {tr}Show on login{/tr}
	</td>
<td style="text-align:left">
	&nbsp;</td>
<td style="text-align:right">
	<input type="hidden" name="set_up_environment" value="y">
	<input type="hidden" name="url" value="{$homepageUrl}">
	<input type="hidden" name="wizard_step" value="{$wizard_step}">
	<input type="reset" name="cancel" value="{tr}Cancel{/tr}" onclick="window.location='{$homepageUrl}'" />
	&nbsp;&nbsp;&nbsp;
	<input type="submit" class="btn btn-default" name="continue" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{else}{tr}Save and Continue{/tr}{/if}" />
	</td>
</tr>
</table>
</form>
