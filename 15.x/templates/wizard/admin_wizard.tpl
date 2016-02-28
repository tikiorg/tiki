{* $Id$ *}

<div class="col-lg-10 col-lg-offset-1">
	<fieldset>
		<legend>{tr}Get Started{/tr}</legend>

		<p>
            {icon name="check" size=2} {tr _0=$tiki_version}Congratulations! You now have a working instance of Tiki %0{/tr}.
			{tr}You may <a href="tiki-index.php">start using it right away</a>, or you may configure it to better meet your needs, using one of the configuration helpers below.{/tr}
		</p>

		{remarksbox type="tip" title="{tr}Tip{/tr}"}
			{tr}Mouse over the icons to know more about the features and preferences that are new for you{/tr}.
			<a href="http://doc.tiki.org/Wizards" target="tikihelp" class="tikihelp" style="float:right" title="{tr}Help icon:{/tr}
				{tr}You will get more information about the features and preferences whenever this icon is available and you pass your mouse over it{/tr}.
				<br/><br/>{tr}Moreover, if you click on it, you'll be directed in a new window to the corresponding documentation page for further information on that feature or topic{/tr}."
			>
				{icon name="help"}
			</a>
			<a target="tikihelp" class="tikihelp" style="float:right" title="{tr}Information icon:{/tr}
				{tr}You will get more information about the features and preferences whenever this icon is available and you pass your mouse over it{/tr}.
			">
				{icon name="information"}
			</a>
			{tr}Example: {/tr}
		{/remarksbox}

		<div class="media">
			<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Profiles Wizard{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" >
				<i class="fa fa-cubes fa-stack-2x"></i>
				<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
			</span>
			<div class="media-body">
				<p>{tr}You may start by applying some of our configuration templates through the <b>Configuration Profiles Wizard</b>{/tr}. {tr}They are like the <b>Macros</b> from many computer languages{/tr}.
					<a href="http://doc.tiki.org/Profiles+Wizard" target="tikihelp" class="tikihelp" title="{tr}Configuration Profiles:{/tr}
						{tr}Each of these provides a shrink-wrapped solution that meets most of the needs of a particular kind of community or site (Personal Blog space, Company Intranet, ...) or that extends basic setup with extra features configured for you{/tr}.</p>
						<p>{tr}If you are new to Tiki administration, we recommend that you start with this approach{/tr}.</p>
						<p>{tr}If the profile you selected does not quite meet your needs, you will still have the option of customizing it further with one of the approaches below{/tr}"
					>
						{icon name="help"}
					</a>
				</p>

				<input type="submit" class="btn btn-primary" name="use-default-prefs" value="{tr}Start Configuration Profiles Wizard (Macros){/tr}" />
			</div>
		</div>
		<div class="media">
			<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Walkthrough{/tr}" title="Configuration Walkthrough">
				<i class="fa fa-gear fa-stack-2x"></i>
				<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
			</span>
			<div class="media-body">
				<p>
					{tr}Alternatively, you may use the <b>Configuration Wizard</b>{/tr}.
					{tr}This will guide you through the most common preference settings in order to customize your site{/tr}.
					<a href="http://doc.tiki.org/Admin+Wizard" target="tikihelp" class="tikihelp" title="{tr}Configuration Wizard:{/tr}
						{tr}Use this wizard if none of the <b>Configuration Profiles</b> look like a good starting point, or if you need to customize your site further{/tr}"
					>
						{icon name="help"}
					</a>
				</p>
				<input type="submit" class="btn btn-primary" name="continue" value="{tr}Start Configuration Wizard{/tr}" />
			</div>
		</div>
		<div class="media">
			<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
				<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
				<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
			</span>
			<div class="media-body">
				<p>
					{tr}Or you may use the <b>Upgrade Wizard</b>{/tr}.
					{tr}This will guide you through the most common new settings and informations in order to upgrade your site{/tr}.
					<a href="http://doc.tiki.org/Upgrade+Wizard" target="tikihelp" class="tikihelp" title="{tr}Upgrade Wizard:{/tr}
						{tr}Use this wizard if you are upgrading from previous versions of Tiki, specially if you come from the previous Long-Term Support (LTS) version.{/tr}</p>

						<p>{tr}Some of these settings are also available through the Configuration Wizard, and all of them are available through Control Panels{/tr}.
						{tr}But this wizard will let you learn about them as well as enable/disable them easily according to your needs and interests for your site{/tr}."
					>
						{icon name="help"}
					</a>
				</p>

					<input type="submit" class="btn btn-primary" name="use-upgrade-wizard" value="{tr}Start Upgrade Wizard{/tr}" />
			</div>
		</div>
		<div class="media">
			<img class="pull-left" src="img/icons/large/controlpanels48x48.png" alt="{tr}Control Panels{/tr}" />
			<div class="media-body">
				<p>{tr}Use the <b>Control Panels</b> to manually browse through the full list of preferences{/tr}.</p>

				{button href="tiki-admin.php" _text="{tr}Go to the Control Panels{/tr}"}
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{tr}Server Fitness{/tr}</legend>
		{tr _0=$tiki_version}To check if your server meets the requirements for running Tiki version %0, please visit <a href="tiki-check.php" target="_blank">Tiki Server Compatibility Check</a>{/tr}.
	</fieldset>
</div>
