{* $Id$ *}

<table style="width:100%">
{* <tr> *} {* mobile *}
{* <td style="text-align:left; width:270px">  *} {* mobile *}
	{if !isset($showOnLoginDisplayed) or $showOnLoginDisplayed neq 'y'}
{* 		<div style="float:left; width:20px; margin-top:20px;"><img src="img/icons/wizard16x16.png" alt="{tr}Tiki Configuration Wizard{/tr}" /></div> *} {* mobile *}
		<input type="checkbox" name="showOnLogin" id="showOnLogin" class="custom" data-mini="true" data-theme="a" 
			{if isset($showOnLogin) AND $showOnLogin eq true}checked="checked" 
				{jq}
					$("input[name='showOnLogin']").attr("checked",true).checkboxradio("refresh");
				{/jq}
			{/if}
		/> <label for="showOnLogin"><img src="img/icons/wizard16x16.png" alt="{tr}Tiki Setup Wizards{/tr}" /> {tr}Show on admin login{/tr}</label> {* mobile *}
		{assign var="showOnLoginDisplayed" value="y" scope="root"}
	{else}
		&nbsp;
	{/if}
{* 	</td> *} {* mobile *}
{* </tr> *} {* mobile *}
<tr>
	<td style="text-align:left">
			<input type="submit" data-role="button" data-icon="delete" data-theme="a" name="close" value="{tr}Close{/tr}" /> {* mobile *}
{*			&nbsp;&nbsp;&nbsp; *} {* mobile *}
	{if !isset($firstWizardPage)}
		</td>
		<td style="text-align:left">
			<input type="submit" data-role="button" data-icon="arrow-l" data-theme="a" class="btn btn-default" name="back" value="{tr}Back{/tr}" />
	{/if} {* mobile *}
	</td>
<td style="text-align:right">
	<input type="hidden" name="url" value="{$homepageUrl}">
	<input type="hidden" name="wizard_step" value="{$wizard_step}">
	{if isset($useDefaultPrefs)}
		<input type="hidden" name="use-default-prefs" value="{$useDefaultPrefs}">
	{/if}
	{if isset($useUpgradeWizard)}
		<input type="hidden" name="use-upgrade-wizard" value="{$useUpgradeWizard}">
	{/if}	
	<input type="submit" data-icon="check" data-theme="a" name="continue" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{elseif isset($firstWizardPage)}{tr}Start{/tr}{else}{if $isEditable eq true}{tr}Save and Continue{/tr}{else}{tr}Next{/tr}{/if}{/if}" /> {* mobile *}
</td>
</tr>
</table>

