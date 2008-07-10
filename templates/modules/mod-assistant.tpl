{* $Id$ *}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}Tikiwiki Assistant{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="assistant" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<div align="center"><strong>{tr}Thank you for installing Tikiwiki{/tr}!</strong></div>
{if $tiki_p_admin eq 'y'}
 <p><img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" /><strong>{tr}To configure your Tiki{/tr}</strong>:<br />
 {tr}Select{/tr} <a class="link" href="tiki-admin.php" title="{tr}Admin Home{/tr}">{tr}Admin{/tr} &gt; {tr}Admin Home{/tr}</a> {tr}from the menu{/tr}.</p>
 <p>{tr}Read the <a class="link" href="http://doc.tikiwiki.org/Configuration" title="Tikiwiki Documentation" target="_blank">configuration documentation</a>{/tr}.</p>
 <p>{tr}Watch the <a class="link" href="http://tikiwiki.org/TikiMovies" title="Demos" target="_blank">demo movies{/tr}</a>.</p>
 <p><img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" /><strong>{tr}To remove this module{/tr}</strong>:<br />
 {tr}Select{/tr} <a class="link" href="tiki-admin_modules.php#leftmod" title="{tr}Admin Modules{/tr}">{tr}Admin{/tr} &gt; {tr}Modules{/tr}</a> {tr}and remove the assistant module{/tr}. {tr}You can also add other modules{/tr}.</p>
 <p><img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" /><strong>{tr}To customize the menu{/tr}</strong>:<br />
 {tr}Select{/tr} <a class="link" href="tiki-admin_menus.php" title="{tr}Admin Menus{/tr}">{tr}Admin{/tr} &gt; {tr}Menus{/tr}</a> {tr}and edit menu ID 42{/tr}.<br />{tr}Or, create your own menu and add it to a module{/tr}.</p>
 <hr />
{else}
 <p><a href="tiki-login.php" title="{tr}Login{/tr}"><img src="pics/icons/accept.png" alt="{tr}Login{/tr}" style="border:0;margin-right:2px;vertical-align:middle" align="left" /></a>{tr}To begin configuring Tiki, please{/tr} <a href="tiki-login.php" title="{tr}Login{/tr}">{tr}login{/tr}</a> {tr}as the Admin{/tr}.</p>
{/if}
 <p><a href="http://www.tikiwiki.org" title="{tr}The Tikiwiki Community{/tr}" target="_blank"><img src="favicon.png" alt="{tr}The Tikiwiki Community{/tr}" style="border:0;margin-right:2px;vertical-align:middle" align="left" /></a>{tr}To learn more, visit: <a href="http://www.tikiwiki.org" title="The Tikiwiki Community" target="_blank">http://www.tikiwiki.org</a>{/tr}.</p>
 <p><a href="http://doc.tikiwiki.org" title="{tr}Tikiwiki Documentation{/tr}" target="_blank"><img src="pics/icons/help.png" alt="{tr}Tikiwiki Documentation{/tr}" style="border:0px;margin-right:2px;vertical-align:middle" align="left" /></a>{tr}For help, visit <a href="http://doc.tikiwiki.org" title="Tikiwiki Documentation" target="_blank">http://doc.tikiwiki.org</a>{/tr}.</p>
{/tikimodule}
