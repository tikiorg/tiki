{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" /></div><div class="adminWizardIconright"><img src="img/icons/large/gnome-settings-background48x48.png" alt="{tr}Set up Look & Feel{/tr}" /></div>
{tr}Configure the Tiki theme and other look & feel preferences{/tr}.
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Look & Feel options{/tr}</legend>
		<div style="position:relative;">
			<div class="adminWizardlookandfell">
				<img src="{$thumbfile}" alt="{tr}Theme Screenshot{/tr}" id="style_thumb" width="160px" height="120px">
			</div>

			<div class="adminoptionbox">
				{preference name=style}
				{preference name=style_option}
				{preference name=feature_fixed_width}
				<div class="adminoptionboxchild" id="feature_fixed_width_childcontainer">
					{preference name=layout_fixed_width}
				</div>

				{if $prefs.javascript_enabled eq 'n' or $prefs.feature_jquery eq 'n'}
					<input type="submit" class="btn btn-default" name="changestyle" value="{tr}Go{/tr}" />
				{/if}
			</div>
		</div>
		<br>
		<em>{tr}See also{/tr} <a href="tiki-admin.php?page=look&amp;alt=Look+%26+Feel" target="_blank">{tr}Look & Feel admin panel{/tr}</a></em>
</fieldset>

<fieldset>
	<legend>{tr}Title{/tr}</legend>
	{preference name=sitetitle}
	{preference name=sitesubtitle}
</fieldset>

<fieldset>
	<legend>{tr}Logo{/tr}</legend>
	{preference name=sitelogo_src}
</fieldset>

<fieldset>
	<legend>{tr}Favicon{/tr}</legend>
	{preference name=site_favicon}
	{preference name=site_favicon_type}
</fieldset>
</div>
