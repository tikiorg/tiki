{* $Id$ *}
{tikimodule error=$module_params.error title=$tpl_module_title name="assistant" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div align="center">
		<strong>{tr}Thank you for installing Tiki{/tr}!</strong>
	</div>
	{if $tiki_p_admin eq 'y'}
	<p>
		<img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" />
		<strong>{tr}To configure your Tiki{/tr}</strong>:<br />
		{tr}Select{/tr} <a class="link" href="tiki-admin.php" title="{tr}Admin Home{/tr}">{tr}Admin{/tr} &gt; {tr}Admin Home{/tr}</a> {tr}from the menu{/tr}.
	</p>
	<p>
		{tr}Read the <a class="link" href="http://doc.tiki.org/Configuration" title="Tiki Documentation" target="_blank">configuration documentation</a>{/tr}.
	</p>
	<p>
		{tr}Watch the <a class="link" href="http://tiki.org/TikiMovies" title="Demos" target="_blank">demo movies{/tr}</a>.
	</p>
	<p>
		<img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" />
		<strong>{tr}To remove this module{/tr}</strong>:<br />
		{tr}Select{/tr} <a class="link" href="tiki-admin_modules.php#leftmod" title="{tr}Admin Modules{/tr}">{tr}Admin{/tr} &gt; {tr}Modules{/tr}</a> {tr}and remove the assistant module{/tr}. {tr}You can also add other modules{/tr}.
	</p>
	<p>
		<img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" />
		<strong>{tr}To customize the menu{/tr}</strong>:<br />
		{tr}Select{/tr} <a class="link" href="tiki-admin_menus.php" title="{tr}Admin Menus{/tr}">{tr}Admin{/tr} &gt; {tr}Menus{/tr}</a> {tr}and edit menu ID 42{/tr}.<br />{tr}Or, create your own menu and add it to a module{/tr}.
	</p>
	<hr />
	{else}
	<p>
		<a href="tiki-login.php" title="{tr}Login{/tr}"><img src="pics/icons/accept.png" alt="{tr}Login{/tr}" style="border:0;margin-right:2px;vertical-align:middle" align="left" /></a>{tr}To begin configuring Tiki, please{/tr} <a href="tiki-login.php" title="{tr}Login{/tr}">{tr}login{/tr}</a> {tr}as admin{/tr}.
	</p>
	{/if}
	<p>
		<a href="http://tiki.org" title="{tr}The Tiki Community{/tr}" target="_blank"><img src="favicon.png" alt="{tr}The Tiki Community{/tr}" style="border:0;margin-right:2px;vertical-align:middle" align="left" /></a>{tr}To learn more, visit: <a href="http://tiki.org" title="The Tiki Community" target="_blank">http://tiki.org</a>{/tr}.
	</p>
	<p>
		<a href="http://doc.tiki.org" title="{tr}Tiki Documentation{/tr}" target="_blank"><img src="pics/icons/help.png" alt="{tr}Tiki Documentation{/tr}" style="border:0px;margin-right:2px;vertical-align:middle" align="left" /></a>{tr}For help, visit <a href="http://doc.tiki.org" title="Tiki Documentation" target="_blank">http://doc.tiki.org</a>{/tr}.
	</p>
{/tikimodule}
