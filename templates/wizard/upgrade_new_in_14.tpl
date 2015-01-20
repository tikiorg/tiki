{* $Id$ *}

<div class="media">
	<img class="pull-left" src="img/icons/large/wizard_upgrade48x48.png" alt="{tr}Upgrade Wizard{/tr}" title="{tr}Upgrade Wizard{/tr}"/>
	<div class="media-body">
		{tr}Main new features and settings in Tiki 14{/tr}.
		<a href="http://doc.tiki.org/Tiki14" target="tikihelp" class="tikihelp" title="{tr}Tiki14:{/tr}
			{tr}Tiki14 is a standard non-LTS version{/tr}.
			{tr}It will be supported until 15.1 is released{/tr}.
			{tr}The requirements are the same as in the previous version (IE9, PHP 5.5){/tr}.
			{tr}Minor changes have happened, compared to post-LTS versions such as Tiki13{/tr}.
			<br/><br/>
			{tr}Click to read more{/tr}
		">
			<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
		</a>
		<br/><br/>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New modules{/tr}</legend>
			{tr}A new module called <strong>Module zone</strong> was added{/tr}
			<a href="http://doc.tiki.org/Module+zone" target="tikihelp" class="tikihelp" title="{tr}Module zone:{/tr}
				{tr}New module meant to provide a horizontal 'navigation bar' for the website{/tr}.
				<br/><br/>
				{tr}Click to read more{/tr}
			">
				<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
			</a>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Tiki Addons{/tr}</legend>
			{tr}Addons allow a way for developers to add an even broader range of functionality{/tr}
			<a href="http://doc.tiki.org/Addons" target="tikihelp" class="tikihelp" title="{tr}Addons:{/tr}
				{tr}Tiki is already one of the most feature-rich social business/web content management platforms that exist today, where hundreds of developers have contributed directly to its codebase{/tr}.
				<br/><br/>
				{tr}Nevertheless, in Tiki 14, the Tiki Addons feature was added to allow a way for developers to add an even broader range of functionality that can be used with Tiki{/tr}.
				<br/><br/>
				{tr}Click to read more{/tr}
			">
				<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
			</a>
			{foreach $addonprefs as $addon}
				{preference name="{$addon|escape}"}
			{/foreach}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Other new features{/tr}</legend>
				{preference name=tracker_tabular_enabled}
				{preference name=mustread_enabled}
				<div class="adminoptionboxchild" id="mustread_enabled_childcontainer">
					{preference name=mustread_tracker}
				</div>
				{preference name=federated_enabled}
				<div class="adminoptionboxchild" id="federated_enabled_childcontainer">
					{preference name=federated_elastic_url}
				</div>
        </fieldset>
        <fieldset class="table clearfix featurelist">
            <legend>{tr}Improved and extended features{/tr}</legend>
                {preference name=feature_jquery_tablesorter}
		</fieldset>
        <i>{tr}See the full list of changes{/tr}.</i>
        <a href="http://doc.tiki.org/Tiki14" target="tikihelp" class="tikihelp" title="{tr}Tiki14:{/tr}
			{tr}Click to read more{/tr}
		">
            <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
        </a>
    </div>
</div>
