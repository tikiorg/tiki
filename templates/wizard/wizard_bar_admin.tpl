{* $Id$ *}

<div class="row">
    <div class="form-group">
	{if !isset($showOnLoginDisplayed) or $showOnLoginDisplayed neq 'y'}
		<div class="col-sm-1"><img src="img/icons/wizard16x16.png" alt="{tr}Tiki Admin Wizard{/tr}" /></div>
		<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> {tr}Show on admin login{/tr}
		{assign var="showOnLoginDisplayed" value="y" scope="root"}
	{else}
		&nbsp;
	{/if}
    </div>
    <div class="form-group">

    <div class="col-sm-2 pull-left">
	    <input type="submit" class="btn btn-warning btn-sm" name="close" value="{tr}Close{/tr}" />
	    &nbsp;&nbsp;&nbsp;
	    {if !isset($firstWizardPage)}<input type="submit" class="btn btn-default btn-sm" name="back" value="{tr}Back{/tr}" />{/if}
	</div>
    <div class="col-sm-8 text-center">
	{if !isset($showWizardPageTitle) or $showWizardPageTitle neq 'y'}
		<h1 class="adminWizardPageTitle">{$pageTitle}</h1>
		{assign var="showWizardPageTitle" value="y" scope="root"}
	{/if}
	</div>
<div class="col-sm-2 pull-right">
	<input type="hidden" name="url" value="{$homepageUrl}">
	<input type="hidden" name="wizard_step" value="{$wizard_step}">
	{if isset($useDefaultPrefs)}
		<input type="hidden" name="use-default-prefs" value="{$useDefaultPrefs}">
	{/if}
	<input type="submit" class="btn btn-default btn-sm" name="continue" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{elseif isset($firstWizardPage)}{tr}Start{/tr}{else}{if $isEditable eq true}{tr}Save and Continue{/tr}{else}{tr}Next{/tr}{/if}{/if}" />
</div>
    </div>
</div>