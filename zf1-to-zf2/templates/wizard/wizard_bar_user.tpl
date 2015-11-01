{* $Id$ *}

<table style="width:100%">
	<tr>
		<td colspan="3" style="text-align:left; width:270px">
			<div style="float:left; width:20px">{icon name=wizard}</div>
			<input type="submit" class="btn btn-warning btn-sm" name="close" value="{tr}Close{/tr}" />
			&nbsp;&nbsp;&nbsp;
			{if !isset($firstWizardPage)}<input type="submit" class="btn btn-default btn-sm" name="back" value="{tr}Back{/tr}" />{/if}
		</td>
		<td >
		</td>
		<td style="text-align:right">
			<input type="hidden" name="url" value="{$homepageUrl}">
			<input type="hidden" name="wizard_step" value="{$wizard_step}">
			<input type="hidden" name="itemId" value="{$itemId}">
			<input type="submit" class="btn btn-primary btn-sm" name="continue" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{elseif isset($firstWizardPage)}{tr}Start{/tr}{else}{if $isEditable eq true}{tr}Save and Continue{/tr}{else}{tr}Next{/tr}{/if}{/if}" />
		</td>
	</tr>
	<tr>
		<td {if !isset($firstWizardPage)}style="width:240px"{else}style="width:120px"{/if}>
		</td>
		<td style="text-align:left">
			{if !isset($showWizardPageTitle) or $showWizardPageTitle neq 'y'}
				<h1 class="adminWizardPageTitle">{$pageTitle}</h1>
				{assign var="showWizardPageTitle" value="y" scope="root"}
			{/if}
		</td>
		<td style="text-align:right">
		</td>
	</tr>
</table>

