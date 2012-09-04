{* $Id$ *}
<h3>
	{tr}File Metadata{/tr}
</h3>
<em>{tr}Read-only metadata extracted from the file.{/tr}</em>
{if isset($metarray.basiconly) and $metarray.basiconly}
<span>
	{tr}<em>Only basic metadata processed for this file type.</em>{/tr}
</span>
{/if}
{tabset name="metadata"}
	{foreach $metarray as $subtypes}
		{if $subtypes ne 'basiconly'}
			{if $subtypes@key|count_words gt 1}
				{$tabtitle = "{tr}{$subtypes@key|escape}{/tr}"}
			{else}
				{$tabtitle = $subtypes@key|upper|escape}
			{/if}
			{tab name=$tabtitle}
				{foreach $subtypes as $fields}
					{if $fields|count gt 0}
						<fieldset>
							<legend>
								{tr}{$fields@key|lower|capitalize|escape}{/tr}
							</legend>
							<table>
								{foreach $fields as $fieldarray}
									<tr>
										<td>
											<div class="meta-tabs-col1">
												{if isset($fieldarray.label) && $fieldarray.label ne 'li'}
													{tr}{$fieldarray.label|escape}{/tr}
												{else}
													{tr}{$fieldarray@key|escape}{/tr}
												{/if}
											</div>
										</td>
										<td>
											<div class="meta-tabs-col2">
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
							</table>
						</fieldset>
					{/if}
				{/foreach}
			{/tab}
		{/if}
	{/foreach}
{/tabset}
