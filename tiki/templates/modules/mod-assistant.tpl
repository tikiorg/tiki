{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-assistant.tpl,v 1.5 2005-03-12 16:50:59 mose Exp $ *}
{tikimodule title="{tr}TikiWiki Assistant{/tr}" name="assistant" flip=$module_params.flip decorations=$module_params.decorations}
<div>{tr}Thank you for installing TikiWiki{/tr}<br />
{tr}Click the :: option on the menu for more options.{/tr} {tr}Please also see{/tr} <a class="link" href="http://tikiwiki.org/TikiMovies">TikiMovies</a> {tr}for more setup details.{/tr}
{if $tiki_p_admin eq 'y'}
  {tr}You can remove this module in{/tr}
  <a class="link" href="tiki-admin_modules.php">{tr}Admin->Modules{/tr}</a>.
{/if}
</div>
{/tikimodule}
