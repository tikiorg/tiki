{* $Id$ *}

{* {title}{tr}Admin Wizard{/tr}{/title} *}

<form action="tiki-wizard_admin.php" method="post">
{include file="wizard/wizard_bar_admin.tpl"}
<div id="wizardBody">
<h1 class="adminWizardPageTitle">{$pageTitle}</h1>
<table class="adminWizardTable">
	<tr>
		<td class="adminWizardTOC">
			<span class="adminWizardTOCTitle">{tr}Admin Wizard Steps{/tr}</span><br>
			{$wizard_toc}
		</td>
		<td class="adminWizardContent">
			{include file="{$wizardBody}"}
		</td>
	</tr>
</table>
</div>
{include file="wizard/wizard_bar_admin.tpl"}
</form>
