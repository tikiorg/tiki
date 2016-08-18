{* $Id$ *}
{title help="Docs"}{$name}{/title}

<span class="editState" {if $edit eq "false"} style="display: none;" {/if}>
	{button _class="saveButton" _text="{tr}Save{/tr}" _htmlelement="role_main" fileId="$fileId" _title="{tr}Tiki Docs{/tr} | {tr}Save file{/tr}"}
	{button _class="cancelButton" _text="{tr}Cancel{/tr}" _htmlelement="role_main" fileId="$fileId" _title="{tr}Tiki Docs{/tr} | {tr}Cancel editing file{/tr}"}
</span>

<span class="viewState" {if $edit eq "true"} style="display: none;" {/if}>
	{button _id="editButton" _class="editButton" _text="{tr}Edit{/tr}" _template="tiki-edit_docs.tpl" edit="edit" _auto_args="*" _htmlelement="role_main" _title="{tr}Tiki Docs{/tr} | {tr}Editing file{/tr}"}
</span>

<input id="fileId" type="hidden" value="{$fileId}">

<div id="tiki_doc" class="" style="border: 2px outset;">
</div>