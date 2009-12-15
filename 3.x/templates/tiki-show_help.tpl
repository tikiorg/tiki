{* $Id$ *}
{if $prefs.javascript_enabled eq 'y' && $prefs.feature_floating_help eq 'y'}
<div class="help" id="tikihelp">
	<div class="help_icon">	
		<a href="#" onclick="javascript:flip('help_sections'); return false"><strong>{tr}Help{/tr}</strong>{icon class="arrow" _id="pics/next_anim.gif" alt="»" }{icon _id="pics/help_icon.png" alt="{tr}Help{/tr}: {tr}Click Here{/tr}" width="48" height="48" }</a>
	</div>
	<div class="help_sections" id="help_sections" style="display:none">
{/if}
	{foreach item=help from=$help_sections}
		<div class="help_section_select">
			<a href="#{$help.id}"{if $prefs.javascript_enabled eq 'y'} onclick="javascript:flip('{$help.id}'); return false" title="{tr}toggle{/tr}"{/if}><img src="pics/icons/add.png" alt="{tr}toggle{/tr}" />
			{$help.title}</a>
			{* It will be nice to have shadowbox when js is on but floating help off. But when the shadowbox is closed, there is no way to get back to help as there is no icon to click *}
			{if $prefs.javascript_enabled eq 'y' && $prefs.feature_floating_help eq 'y' && $prefs.feature_shadowbox eq 'y' and ($prefs.feature_jquery eq 'y' or $prefs.feature_mootools eq 'y')}<a href="#{$help.id}" onclick="javascript:flip('help_sections'); return false" rel="shadowbox[help];title={tr}Help{/tr}: {$help.title}">{icon _id="arrow_out" alt="{tr}Fullscreen{/tr}"}</a>{/if}
		</div>
		<div {if $prefs.javascript_enabled eq 'y'}class="help_section" id="{$help.id}" style="display:block"{/if}>
		{$help.content}
		</div>
	{/foreach}
{if $prefs.javascript_enabled eq 'y' && $prefs.feature_floating_help eq 'y'}
	</div>
</div>
{/if}
