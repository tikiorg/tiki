{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-freetag.tpl,v 1.2 2006-10-03 15:22:29 mose Exp $ *}

{eval var="{tr}Folksonomy{/tr}" assign="tpl_module_title"}
{tikimodule title=$tpl_module_title name="folksonomy_tagging" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="0" cellspacing="0">
  <tr><td>
  {tr}Tag{/tr} <b>{$page}</b> ({tr}signed{/tr} {$user})
	<form name="addTags" method="post" action="modules/mod-freetag.php">
	<input type="hidden" name="page_id" value="{$pageid}" />
	<input type="text" name="tags" maxlength="40" />
	<input type="submit" name="Add" value="Add" />
	</form>
  </td></tr>
  </table>
{/tikimodule}
