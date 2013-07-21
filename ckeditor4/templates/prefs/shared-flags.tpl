{if $p.helpurl}
	<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape} {if $p.separator}{tr}Separator is {/tr}<b>{$p.separator|simplewiki}</b>{/if}">
		{icon _id=help alt=''}
	</a>
{elseif $p.description}
	<span class="tikihelp" title="{$p.name|escape}: {$p.description|escape} {if $p.separator}{tr}Separator is {/tr}<b>{$p.separator|simplewiki}</b>{/if}">
		{icon _id=information alt=''}
	</span>
{/if}

{if $p.warning}
	<a href="#" target="tikihelp" class="tikihelp" title="{tr}Warning:{/tr} {$p.warning|escape}">
		{icon _id=error alt=''}
	</a>
{/if}

{if $p.modified and $p.available}
	<input class="pref-reset system" type="checkbox" name="lm_reset[]" value="{$p.preference|escape}" style="display:none" data-preference-default="{$p.default|escape}">
{/if}

{if !empty($p.popup_html)}
	<a class="icon" title="{tr}Actions{/tr}" href="#" style="padding:0; margin:0; border:0"
			 {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml=1 center="true" text=$p.popup_html|escape:"javascript"|escape:"html"}>
		{icon _id='application_form' alt="{tr}Actions{/tr}"}
	</a>
{/if}
{if !empty($p.voting_html)}
	{$p.voting_html}
{/if}

{$p.pages}
