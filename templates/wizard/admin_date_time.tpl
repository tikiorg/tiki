{* $Id$ *}

<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
	<i class="fa fa-gear fa-stack-2x"></i>
	<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
</span>
{tr}Set the site time zone and format for displaying dates and times{/tr}.</br></br>
<div class="media">
    {icon name="admin_general" size=3 iclass="adminWizardIconright"}
	<div class="media-content">
		<fieldset>
			<legend>{tr}Date and Time setup{/tr}</legend>
            {icon name="clock-o" size=2 iclass="adminWizardIconright"}
			<div class="admin clearfix featurelist">
				{preference name=server_timezone}
				{preference name=users_prefs_display_12hr_clock}
				{preference name=users_prefs_display_timezone}
				{preference name=display_field_order}
			</div>
			<br>
			<em>{tr}See also{/tr} <a href="tiki-admin.php?page=general&amp;alt=General#content4" target="_blank">{tr}Date and Time admin panel{/tr}</a></em>
		</fieldset>
		<br>
	</div>
</div>
