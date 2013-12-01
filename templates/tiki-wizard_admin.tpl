{* $Id$ *}

{* {title}{tr}Admin Wizard{/tr}{/title} *}

<form action="tiki-wizard_admin.php" method="post">
{include file="wizard/wizard_bar_admin.tpl"}
<div id="wizardBody">
<h1>{$pageTitle}</h1>
<table class="adminWizardTable">
	<tr>
		<td class="adminWizardContent">
			{include file="{$wizardBody}"}
		</td>
		<td class="adminWizardTOC">
			{$wizard_toc}
		</td>
	</tr>
</table>
</div>
{include file="wizard/wizard_bar_admin.tpl"}
</form>
