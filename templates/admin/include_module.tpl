{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}&quot;Modules&quot; are the items of content at the top &amp; bottom and in the right &amp; left columns of the site.{/tr} {tr}Select{/tr}
	<a class="alert-link" href="tiki-admin_modules.php">{tr}Admin &gt; Modules{/tr}</a> {tr}from the menu to create and edit modules{/tr}.
{/remarksbox}

<form class="form-horizontal" action="tiki-admin.php?page=module" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<input type="hidden" name="modulesetup" />

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="button" class="btn btn-link" href="tiki-admin_modules.php" title="{tr}List{/tr}">
				{icon name="list"} {tr}Modules{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	<fieldset>
		<legend>{tr}{$crumbs[$crumb]->description}{/tr}{help crumb=$crumbs[$crumb]}</legend>

		{preference name=feature_modulecontrols}
		{preference name=user_assigned_modules}
		{preference name=user_flip_modules}
		{preference name=modallgroups}
		{preference name=modseparateanon}
		{preference name=modhideanonadmin}

		<div class="adminoptionbox">
			<fieldset>
				<legend>{tr}Module zone visibility{/tr}</legend>
				{if !$smarty.get.Zone_options}
					{remarksbox type="tip" title="{tr}Hint{/tr}"}
						Module zone visibility options may not be supported anymore from Tiki 13+, but you can still access them in case you are upgrading from an earlier version
						<a href="tiki-admin.php?page=look&Zone_options=y#contentadmin_look-2">Click here for module visibility options</a>
					{/remarksbox}
				{else}
					{preference name=module_zones_top}
					{preference name=module_zones_topbar}
					{preference name=module_zones_pagetop}
					{preference name=feature_left_column}
					{preference name=feature_right_column}
					{preference name=module_zones_pagebottom}
					{preference name=module_zones_bottom}
				{/if}
				{preference name=module_file}
				{preference name=module_zone_available_extra}
			</fieldset>
		</div>

		{remarksbox type="tip" title="{tr}Hint{/tr}"}
			{tr}If you lose your login module, use tiki-login_scr.php to be able to login!{/tr}
		{/remarksbox}
	</fieldset>
	<br>{* I cheated. *}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
