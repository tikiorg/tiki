{if $p.helpurl}
	<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape} {if $p.separator && $p.type neq 'multiselector'}{tr}Separator is {/tr}<b>{$p.separator|simplewiki}</b>{/if}">
		{icon name="help"}
	</a>
{elseif $p.description}
	<a class="tikihelp" title="{$p.name|escape}: {$p.description|escape} {if $p.separator && $p.type neq 'multiselector'}{tr}Separator is {/tr}<b>{$p.separator|simplewiki}</b>{/if}">
		{icon name="information"}
	</a>
{/if}

{if $p.warning}
	<a href="#" target="tikihelp" class="tikihelp text-warning" title="{tr}Warning:{/tr} {$p.warning|escape}">
		{icon name="warning"}
	</a>
{/if}

{if $p.modified and $p.available}
	<span class="pref-reset-wrapper">
		<input class="pref-reset system" type="checkbox" name="lm_reset[]" value="{$p.preference|escape}" style="display:none" data-preference-default="{if is_array($p.default)}{$p.default|implode:$p.separator|escape}{else}{$p.default|escape}{/if}">
		<a href="#" class="pref-reset-undo tips" title="{tr}Reset{/tr}|{tr}Reset to default value{/tr}">{icon name="undo"}</a>
		<a href="#" class="pref-reset-redo tips" title="{tr}Restore{/tr}|{tr}Restore current value{/tr}" style="display:none">{icon name="repeat"}</a>
	</span>
{/if}

{if !empty($p.popup_html)}
	<a class="tips" title="{tr}Actions{/tr}" href="#" style="padding:0; margin:0; border:0" {popup fullhtml=1 center="true" text=$p.popup_html|escape:"javascript"|escape:"html"}>
		{icon name="actions"}
	</a>
{/if}
{if !empty($p.voting_html)}
	{$p.voting_html}
{/if}

{$p.pages}
