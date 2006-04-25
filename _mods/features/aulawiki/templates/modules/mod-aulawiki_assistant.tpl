{* $Header: /cvsroot/tikiwiki/_mods/features/aulawiki/templates/modules/mod-aulawiki_assistant.tpl,v 1.1 2006-04-25 18:31:33 jreyesg Exp $ *}
{tikimodule title="{tr}AulaWiki Assistant{/tr}" name="aulawiki_assistant" flip=$module_params.flip decorations=$module_params.decorations}
<b>{tr}Welcome to AulaWiki{/tr}!</b><br />
{tr}First configuration steps{/tr}:<br />
<ol class="estruct_index">
<li><a class='link' href="./tiki-admin_modules.php">Assign aulawiki_myworkspaces </a>to the users that you want to use AulaWiki workspaces.</li>
<li><a class='link' href="./tiki-admin.php?page=general">Change theme </a>to AulaWiki.css.</li>
<li>Define the <a class='link' href="./aulawiki-roles.php">workspace roles</a> and permission levels.</li>
<li>Define the <a class='link' href="./aulawiki-workspace_types.php">workspace types</a> , use MenuID 100 or <a class='link' href="./tiki-admin_menus.php">define your own menu</a>. Assign <img src='img/icons/change.gif' border='0' alt='Workspace type resources' title='Workspace type resources' /> default resources and <img src='img/icons/mo.png' border='0' alt='Assigned modules' title='Assigned modules' /> desktop modules.</li>
<li><a class='link' href="./aulawiki-workspaces.php">Create a workspace</a> of the previosly defined type.</li>
<li><img src='images/aulawiki/edu_group.gif' border='0' alt='Users/Groups' title='Users/Groups' />Assign workspace users and groups</a>.</li>
<li><img src='img/icons/change.gif' border='0' alt='Resources' title='Resources' />Admin the workspace resources</a>.</li>
<li><img src='img/icons/ico_preview.gif' border='0' alt='View workspace desktop' title='View workspace desktop' />View the workspace desktop</a>.</li>
<ol>
{/tikimodule}
