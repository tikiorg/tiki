{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-usergroup_tracker.tpl,v 1.2 2004-03-16 03:00:47 mose Exp $ *}

{if $user}
{tikimodule title="{tr}Login{/tr}" name="login_infos"}

<div><a class="linkmodule" href="tiki-logout.php">{tr}Logout{/tr}</a></div>

<div>{tr}User informations{/tr}: </div>
<div class="">
{if $userTracker}
&nbsp;&nbsp;<a href="tiki-view_tracker_item.php?view=+user" class="linkmodule">{$user}</a>
{else}
{$user}
{/if}
</div>

{if $group}
<div>{tr}Group informations{/tr}:</div>
<div class="box-data">
{if $groupTracker}
&nbsp;&nbsp;<a href="tiki-view_tracker_item.php?view=+group" class="linkmodule">{$group}</a>
{else}
{$group}
{/if}
</div>
{/if}

{/tikimodule}
{/if}
