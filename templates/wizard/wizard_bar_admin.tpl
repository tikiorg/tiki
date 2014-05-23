{* $Id$ *}

<table style="width:100%">
<tr>
<td colspan="3" style="text-align:left; width:270px">
	{if !isset($showOnLoginDisplayed) or $showOnLoginDisplayed neq 'y'}
		<div style="float:left; width:20px"><img src="img/icons/wizard16x16.png" alt="{tr}Wizard{/tr}" title="{tr}Wizard{/tr}" /></div>
		<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> {tr}Show on admin login{/tr}
		{assign var="showOnLoginDisplayed" value="y" scope="root"}
	{else}
		&nbsp;
	{/if}
        &nbsp;&nbsp;
    {if $prefs.connect_feature eq "y"}
        {if !isset($provideFeedback) or $provideFeedback neq 'y'}
            <label>
                <input type="checkbox" id="connect_feedback_cbx" {if !empty($connect_feedback_showing)}checked="checked"{/if}>
                {tr}Provide Feedback{/tr}
                <a href="http://doc.tiki.org/Connect" target="tikihelp" class="tikihelp" title="{tr}Provide Feedback:{/tr}
                {tr}Once selected, some icon/s will be shown next to all features so that you can provide some on-site feedback about them{/tr}.
                <br/><br/>
                <ul>
                    <li>{tr}Icon for 'Like'{/tr} <img src=img/icons/connect_like.png></li>
<!--				<li>{tr}Icon for 'Fix me'{/tr} <img src=img/icons/connect_fix.png></li> -->
<!--				<li>{tr}Icon for 'What is this for?'{/tr} <img src=img/icons/connect_wtf.png></li> -->
                </ul>
                <br/>
                {tr}Your votes will be sent when you connect with mother.tiki.org (currently only by clicking the 'Connect > <strong>Send Info</strong>' button){/tr}
                <br/><br/>
                {tr}Click to read more{/tr}
	    	    ">
                    <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                </a>
            </label>
            {$headerlib->add_jsfile("lib/jquery_tiki/tiki-connect.js")}

            {assign var="provideFeedback" value="y" scope="root"}
        {else}
            &nbsp;
        {/if}
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
    {if isset($useUpgradeWizard)}
        <input type="hidden" name="use-upgrade-wizard" value="{$useUpgradeWizard}">
    {/if}
	<input type="submit" class="btn btn-default" name="{if isset($firstWizardPage)}use-default-prefs{else}continue{/if}" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{elseif isset($firstWizardPage)}{tr}Start{/tr}{else}{if $isEditable eq true}{tr}Save and Continue{/tr}{else}{tr}Next{/tr}{/if}{/if}" />
	</td>
</tr>
</table>

