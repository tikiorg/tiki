{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-assistant.tpl,v 1.3 2004-02-10 20:43:36 ggeller Exp $ *}
{tikimodule title="{tr}TikiWiki Assistant{/tr}" name="assistant"}
<div>{tr}Thankyou for installing TikiWiki{/tr}<br />
{tr}Click the :: option on the menu for more options.{/tr} {tr}Please also see{/tr} <a class="link" href="http://tikiwiki.org/TikiMovies">TikiMovies</a> {tr}for more setup details.{/tr}
{if $tiki_p_admin eq 'y'}
  {tr}You can remove this module in{/tr}
  <a class="link" href="tiki-admin_modules.php">{tr}Admin->Modules{/tr}</a>.
{/if}
</div>
{/tikimodule}
