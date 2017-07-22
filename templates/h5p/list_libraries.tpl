{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if not empty($results.name)}
		<h2>{$results.name|escape} {$results.majorVersion|escape}.{$results.minorVersion|escape}</h2>
		{* TOTO add update etc info here *}
		{$name = $results.name}
		{$results = $results.libraries}
	{/if}

	{if $results}
		<table class="table-responsive table">
			<tr>
				<th>
					{tr}Name{/tr}
				</th>
				<th>
					{tr}Title{/tr}
				</th>
				<th>
					{tr}Major Version{/tr}
				</th>
				<th>
					{tr}Minor Version{/tr}
				</th>
				<th>
					{tr}Tutorial URL{/tr}
				</th>
				<th>
					{tr}Restricted?{/tr}
				</th>
			</tr>
			{foreach $results as $result}
				<tr>
					<td>
						<a href="{service controller='h5p' action='list_libraries' machineName=$result.name|escape majorVersion=$result.majorVersion|escape minorVersion=$result.minorVersion|escape}">
							{$result.name|escape}
						</a>
					</td>
					<td>
						{$result.title|escape}
					</td>
					<td>
						{$result.majorVersion|escape}
					</td>
					<td>
						{$result.minorVersion|escape}
					</td>
					<td>
						<a href="{$result.tutorialUrl|escape:'url'}">{$result.tutorialUrl|escape}</a>
					</td>
					<td>
						{if $result.restricted}{icon name='check'}{/if}
					</td>
				</tr>
			{/foreach}
		</table>
	{/if}

	{if not empty($name)}
		<a href="{service controller='h5p' action='list_libraries'}" class="btn btn-link">{tr}Return to list{/tr}</a>
	{/if}

{/block}
