{* $Id$ *}
{strip}
{if $files|count}
	<ul class="localfiles">
		{foreach item=file from=$files}
			<li>
				{if $isIE}
					<a href="file:\\\{$file.path|escape}" title="{$file.path|escape}">
						{if $file.icon}
							{$file.icon}&nbsp;
						{/if}
						{$file.name|escape}
					</a>
				{else}
					{if $file.icon}
						{$file.icon}&nbsp;
					{/if}
					<span>{$file.path|escape}</span>
				{/if}
			</li>
		{/foreach}
	</ul>
{/if}
{/strip}
