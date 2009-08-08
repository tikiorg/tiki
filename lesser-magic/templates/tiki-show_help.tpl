{* $Id$ *}
<div class="help" id="tikihelp">
	<div class="help_sections" id="help_sections" style="display:none">
	{foreach item=help from=$help_sections}
		<div class="help_section_select clearfix">
			<div class="help_title">
				<a href="#{$help.id}"{if $prefs.javascript_enabled eq 'y'} onclick="javascript:flip('{$help.id}'); return false" title="{tr}toggle{/tr}"{/if}><img src="pics/icons/add.png" alt="{tr}toggle{/tr}" />{$help.title}</a>
			</div>
			<div class="help_close_icon">
				<a href="#" onclick="javascript:hide('help_sections'); return false">{icon _id="cross" alt="{tr}Close{/tr}"}</a>
			</div>
			{if $prefs.feature_shadowbox eq 'y' and $prefs.feature_jquery eq 'y'}
			<div class="help_fullscreen_icon">
				<a href="#{$help.id}" onclick="javascript:flip('help_sections'); return false" rel="shadowbox[help];title={tr}Help{/tr}: {$help.title}">{icon _id="arrow_out" alt="{tr}Fullscreen{/tr}"}</a>
			</div>
			{/if}
		</div>
		<div {if $prefs.javascript_enabled eq 'y'}class="help_section clearfix" id="{$help.id}" style="display:block"{/if}>
		{$help.content}
		</div>
	{/foreach}
	</div>
</div>
