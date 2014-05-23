{* $Id$ *}

{* {title}{tr}Admin Wizard{/tr}{/title} *}

<form action="tiki-wizard_admin.php" method="post">
{include file="wizard/wizard_bar_admin.tpl"}
<div id="wizardBody">
<table class="adminWizardTable">
	<tr>
	{if !empty($wizard_toc)}
		<td class="adminWizardTOC">
			<span class="adminWizardTOCTitle">{if $useDefaultPrefs}{tr}Profiles Wizard{/tr}{elseif $useUpgradeWizard}{tr}Upgrade Wizard{/tr}{else}{tr}Admin Wizard{/tr}{/if} - {tr}steps{/tr}:</span>
			{$wizard_toc}
		</td>
	{/if}
		<td class="adminWizardBody">
			{include file="{$wizardBody}"}
		</td>
	</tr>
</table>
</div>
{include file="wizard/wizard_bar_admin.tpl"}
</form>
