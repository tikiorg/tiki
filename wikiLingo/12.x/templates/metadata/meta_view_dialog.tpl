{* $Id$ *}
<div id="{$id|escape}" title="{tr}Image Metadata for{/tr} {$filename|escape}" style="display:none">
	{if $type eq 'data'}
		{if isset($metarray.basiconly) and $metarray.basiconly}
			<span>
				{tr}<em>Note: only basic metadata processed for this file type</em>{/tr}
			</span>
		{/if}
		<ul>
			{foreach $metarray as $subtypes}
				{if $subtypes@key ne 'basiconly'}
					<li>
						<a href="#tabs-{$subtypes@iteration}">
							{if $subtypes@key|count_words gt 1}
								{tr}{$subtypes@key|escape}{/tr}
							{else}
								{$subtypes@key|upper|escape}
							{/if}
						</a>
					</li>
				{/if}
			{/foreach}
		</ul>
		{foreach $metarray as $subtypes}
			<table id="tabs-{$subtypes@iteration}">
				{foreach $subtypes as $fields}
					{if $fields|count gt 0 and $subtypes@key ne 'basiconly'}
						<tr>
							<td colspan="2">
								<div class="meta-section">
									{tr}{$fields@key|lower|capitalize|escape}{/tr}
								</div>
							</td>
						</tr>
						{foreach $fields as $fieldarray}
							<tr>
								<td>
									<div class="meta-col1">
										{if isset($fieldarray.label) && $fieldarray.label ne 'li'}
											{tr}{$fieldarray.label|escape}{/tr}
										{else}
											{tr}{$fieldarray@key|escape}{/tr}
										{/if}
									</div>
								</td>
								<td>
									<div class="meta-col2">
										{$fieldarray.newval|escape}
										{if isset($fieldarray.suffix)}
											{if !empty($fieldarray.newval)}
												&nbsp;
											{/if}
											{tr}{$fieldarray.suffix|escape}{/tr}
										{/if}
									</div>
								</td>
							</tr>
						{/foreach}
					{/if}
				{/foreach}
			</table>
		{/foreach}
	{else}
		{tr}No metadata found{/tr}
	{/if}
</div>

{jq}
	$("#{{$id}}").css('z-index', '1005').dialog({
		autoOpen: false,
		width: 675,
		zIndex: 1005
	});
	$("#{{$id_link}}").click(function() {
		$("#{{$id}}").tabs({
			heightStyle: "content",
			collapsible: true
		}).dialog('open');
		return false;
	});
{/jq}
