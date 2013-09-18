{* $Id$ *}

<table style="width:100%">
<tr> 
<td style="text-align:left; width:250px">
	<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> {tr}Show on login{/tr}
	</td>
<td style="text-align:left">
	<input type="reset" class="btn btn-warning" name="cancel" value="{tr}Cancel{/tr}" onclick="window.location='{$homepageUrl}'" />
	&nbsp;&nbsp;&nbsp;
	{if !isset($firstWizardPage)}<input type="submit" class="btn btn-default" name="back" value="{tr}Back{/tr}" />{/if}
	</td>
<td style="text-align:right">
	<input type="hidden" name="url" value="{$homepageUrl}">
	<input type="hidden" name="wizard_step" value="{$wizard_step}">
	<input type="submit" class="btn btn-default" name="continue" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{else}{tr}Save and Continue{/tr}{/if}" />
	</td>
</tr>
</table>

