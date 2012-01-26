{* $Id: tiki-show_help.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
<div class="help" id="tikihelp">
	<div class="help_sections" id="help_sections" style="display:none">
		<ul>
			{foreach item=help from=$help_sections}
				<li>
					<a href="#{$help.id}">
						{$help.title}
					</a>
				</li>
			{/foreach}
		</ul>
		{foreach item=help from=$help_sections}
			<div id="{$help.id}" class="">
				{$help.content}
			</div>
		{/foreach}
	</div>
	{if $prefs.feature_jquery_ui eq "y"}{jq} $(function() {$("#help_sections").tabs({});}); {/jq}{/if}
</div>
