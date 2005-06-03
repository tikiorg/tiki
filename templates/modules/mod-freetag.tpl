{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-freetag.tpl,v 1.1 2005-06-03 16:42:35 amette Exp $ *}

{eval var="{tr}Folksonomy{/tr}" assign="tpl_module_title"}
{tikimodule title=$tpl_module_title name="folksonomy_tagging" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="0" cellspacing="0">
  <tr><td>
  Tag "{$page}" as user "{$userid}"
	<form name="addTags" method="post" action="modules/mod-freetag.php">
	<input type="hidden" name="page_id" value="{$pageid}" />
	<input type="text" name="tags" maxlength="40" />
	<input type="submit" name="Add" value="Add" />
	</form>
  </td></tr>
  </table>
{/tikimodule}
