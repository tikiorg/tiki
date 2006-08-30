{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tikimodule title="{tr}AulaWiki Assistant{/tr}" name="aulawiki_assistant" flip=$module_params.flip decorations=$module_params.decorations}
<b>{tr}Welcome to AulaWiki{/tr}!</b><br />
{tr}First configuration steps{/tr}:<br />
<ol class="estruct_index">
<li><a class='link' href="./tiki-admin_modules.php">{tr}Assign{/tr} workspaces_my {tr}module{/tr} </a>{tr}to the users that you want to use AulaWiki workspaces{/tr}.</li>
<li><a class='link' href="./tiki-admin.php?page=general">{tr}Change theme{/tr} </a>{tr}to workspaces.css{/tr}.</li>
<li>{tr}Define the <a class='link' href="./tiki-workspaces_roles.php">workspace roles</a> and permission levels.{/tr}</li>
<li>{tr}Define the <a class='link' href="./tiki-workspaces_types.php">workspace types</a> , use MenuID 100 or <a class='link' href="./tiki-admin_menus.php">define your own menu</a>. Assign <img src='img/icons/change.gif' border='0' alt='Workspace type resources' title='Workspace type resources' /> default resources and <img src='img/icons/mo.png' border='0' alt='Assigned modules' title='Assigned modules' /> desktop modules.{/tr}</li>
<li><a class='link' href="./tiki-workspaces_admin.php">{tr}Create a workspace{/tr}</a> {tr}of the previosly defined type{/tr}.</li>
<li><img src='images/workspaces/edu_group.gif' border='0' alt='Users/Groups' title='Users/Groups' /> {tr}Assign workspace users and groups{/tr}</a>.</li>
<li><img src='img/icons/change.gif' border='0' alt='Resources' title='Resources' />{tr}Admin the workspace resources{/tr}</a>.</li>
<li><img src='img/icons/ico_preview.gif' border='0' alt='View workspace desktop' title='View workspace desktop' />{tr}View the workspace desktop{/tr}</a>.</li>
<ol>
{/tikimodule}

