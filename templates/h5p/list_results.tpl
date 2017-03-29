{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<table class="table-responsive table">
		<tr>
			<th>
				Id
			</th>
			<th>
				User
			</th>
			<th>
				Content
			</th>
			<th>
				Score
			</th>
			<th>
				Maximum
			</th>
			<th>
				Time
			</th>
		</tr>
		{foreach $results as $result}
			<tr>
				<td>
					{$result.id}
				</td>
				<td>
					{$result.login|userlink}
				</td>
				<td>
					<a href="{service controller='h5p' action='embed' fileId=$result.file_id}">{$result.title}</a>
				</td>
				<td>
					{$result.score}

				</td>
				<td>
					{$result.max_score}
				</td>
				<td>
					{$result.time}
				</td>

			</tr>
		{/foreach}
	</table>
{/block}
