{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_actionlog.tpl,v 1.1 2006-11-24 12:51:21 sylvieg Exp $ *}

<h1><a href="tiki-edit_actionlog.php" class="pagetitle">{tr}Edit action{/tr}</a></h1>
<form method="post" action="tiki-edit_actionlog.php">
<input type="hidden" name="actionId" value="{$actionId}" />
{$action.action} / {$action.objectType} / {$action.object} 
<table class="normal">
{include file="contribution.tpl"}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
