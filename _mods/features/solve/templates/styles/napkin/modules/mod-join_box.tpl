{* $Header: /cvsroot/tikiwiki/_mods/features/solve/templates/styles/napkin/modules/mod-join_box.tpl,v 1.1 2005-09-21 21:13:00 michael_davey Exp $ *}
{if !$user && $allowRegister eq 'y'}
{tikimodule title="{tr}Join{/tr}" name="join_box" flip=$module_params.flip decorations=$module_params.decorations}
<div align="left" style="margin: 6px; margin-bottom: 9px;">
{tr}Sign up for a free Artimi Connection online membership to download tools, receive monthly developer news, or purchase support services and products.<br /><br />If you have an existing account at Artimi, please log in above.{/tr}
<br /><br />
<a class="linkmodule" href="{$register_url}">
{tr}register{/tr}
</a>
</div>
{/tikimodule}
{/if}
