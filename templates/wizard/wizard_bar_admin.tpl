{* $Id$ *}

<table style="width:100%">
<tr>
<td colspan="3" style="text-align:left; width:270px">
	{if !isset($showOnLoginDisplayed) or $showOnLoginDisplayed neq 'y'}
		<div style="float:left; width:20px"><img src="img/icons/wizard16x16.png" alt="{tr}Tiki Admin Wizard{/tr}" /></div>
		<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> {tr}Show on admin login{/tr}
		{assign var="showOnLoginDisplayed" value="y" scope="root"}
	{else}
		&nbsp;
	{/if}
	</td>
</tr>
<tr>
<td style="text-align:left">
	<input type="submit" class="btn btn-warning" name="close" value="{tr}Close{/tr}" />
	&nbsp;&nbsp;&nbsp;
	{if !isset($firstWizardPage)}<input type="submit" class="btn btn-default" name="back" value="{tr}Back{/tr}" />{/if}
	</td>
<td>
	{if !isset($showWizardPageTitle) or $showWizardPageTitle neq 'y'}
		<h1 class="adminWizardPageTitle">{$pageTitle}</h1>
		{assign var="showWizardPageTitle" value="y" scope="root"}
	{/if}
	</td>
<td style="text-align:right">
	<input type="hidden" name="url" value="{$homepageUrl}">
	<input type="hidden" name="wizard_step" value="{$wizard_step}">
	{if isset($useDefaultPrefs)}
		<input type="hidden" name="use-default-prefs" value="{$useDefaultPrefs}">
	{/if}
	<input type="submit" class="btn btn-default" name="continue" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{elseif isset($firstWizardPage)}{tr}Start{/tr}{else}{if $isEditable eq true}{tr}Save and Continue{/tr}{else}{tr}Next{/tr}{/if}{/if}" />
	</td>
</tr>
</table>

