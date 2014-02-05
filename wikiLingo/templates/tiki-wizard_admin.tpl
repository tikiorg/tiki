{* $Id$ *}

{* {title}{tr}Admin Wizard{/tr}{/title} *}

<form action="tiki-wizard_admin.php" method="post">
{include file="wizard/wizard_bar_admin.tpl"}
<div id="wizardBody">
<div class="row">
	{if !empty($wizard_toc)}
	    <div class="col-sm-4">
		    <span class="adminWizardTOCTitle">{tr}Wizard Steps{/tr}</span>
            <ol>
			    {$wizard_toc}
            </ol>
		</div>
	{/if}
	<div class="{if !empty($wizard_toc)}col-sm-8{else}col-sm-12{/if}">
	    {include file="{$wizardBody}"}
	</div>
</div>
</div>
{include file="wizard/wizard_bar_admin.tpl"}
</form>
