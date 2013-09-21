{* $Id$ *}

<table style="width:100%">
<tr> 
<td style="text-align:left; width:270px">
	<div style="float:left; width:20px"><img src="img/icons/wizard16x16.png" alt="{tr}Tiki Admin Wizard{/tr}"></div>
	<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> {tr}Show on login{/tr}
	</td>
<td style="text-align:left">
	<input type="submit" class="btn btn-warning" name="close" value="{tr}Close{/tr}" />
	&nbsp;&nbsp;&nbsp;
	{if !isset($firstWizardPage)}<input type="submit" class="btn btn-default" name="back" value="{tr}Back{/tr}" />{/if}
	</td>
<td style="text-align:right">
	<input type="hidden" name="url" value="{$homepageUrl}">
	<input type="hidden" name="wizard_step" value="{$wizard_step}">
	<input type="submit" class="btn btn-default" name="continue" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{elseif isset($firstWizardPage)}{tr}Start{/tr}{else}{tr}Save and Continue{/tr}{/if}" />
	</td>
</tr>
</table>

