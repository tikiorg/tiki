{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
		<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Main new features and settings in Tiki 15{/tr}.
	<a href="http://doc.tiki.org/Tiki15" target="tikihelp" class="tikihelp" title="{tr}Tiki15:{/tr}
			{tr}Tiki15 is an LTS version{/tr}.
			{tr}It will be supported until ...(XXX to be written){/tr}.
			{tr}The requirements are ...(XXX to be written){/tr}.
			<br/><br/>
			{tr}Click to read more{/tr}
		">
		{icon name="help" size=1}
	</a>
	<br/><br/><br/>
	<div class="media-body">
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Tiki Addons{/tr}</legend>
			{tr}Addons allow a way for developers to add an even broader range of functionality{/tr}
			<a href="http://doc.tiki.org/Addons" target="tikihelp" class="tikihelp" title="{tr}Addons:{/tr}
				{tr}Tiki is already one of the most feature-rich social business/web content management platforms that exist today, where hundreds of developers have contributed directly to its codebase{/tr}.
				<br/><br/>
				{tr}Nevertheless, in Tiki 14, the Tiki Addons feature was added to allow a way for developers to add an even broader range of functionality that can be used with Tiki{/tr}.
				{tr}And in Tiki 15 ...(XXX to be written){/tr}.
				<br/><br/>
				{tr}Click to read more{/tr}
			">
				{icon name="help" size=1}
			</a>
			{foreach $addonprefs as $addon}
				{preference name="{$addon|escape}"}
			{/foreach}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Other new features{/tr}</legend>
				Tours (through Plugin Tour)
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Improved and extended features{/tr}</legend>
				...(XXX to be written)
		</fieldset>
		<i>{tr}See the full list of changes{/tr}.</i>
		<a href="http://doc.tiki.org/Tiki15" target="tikihelp" class="tikihelp" title="{tr}Tiki15:{/tr}
			{tr}Click to read more{/tr}
		">
			{icon name="help" size=1}
		</a>
	</div>
</div>
