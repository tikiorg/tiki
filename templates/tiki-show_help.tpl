{* $Id$ *}
{if $prefs.javascript_enabled eq 'y'}
<div class="help" id="tikihelp">
	<div class="help_icon">
		<a href="#" onclick="javascript:flip('help_sections'); return false"><strong>{tr}Help{/tr}</strong>{icon class="arrow" _id="pics/next_anim.gif" alt="»" }{icon _id="pics/help_icon.png" alt="{tr}Help{/tr}: {tr}Click Here{/tr}" width="48" height="48" }</a>
	</div>
	<div class="help_sections" id="help_sections" style="display:none">
{/if}
	{foreach item=help from=$help_sections}
		<div class="help_section_select">
			<img {if $prefs.javascript_enabled eq 'y'}onclick="javascript:flip('{$help.id}');"{/if} src="pics/icons/add.png" />
		{$help.title}
		</div>
		<div {if $prefs.javascript_enabled eq 'y'}class="help_section" id="{$help.id}" style="display:all"{/if}>
		{$help.content}
		</div>
	{/foreach}
{if $prefs.javascript_enabled eq 'y'}
	</div>
</div>
{/if}
