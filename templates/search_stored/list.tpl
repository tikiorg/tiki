{extends 'layout_view.tpl'}

{block name="title"}
	{title url=$url}{$title|escape}{/title}
{/block}

{block name="content"}
<table class="table">
	<thead>
		<tr>
			<th>Query</th>
			<th>Last Modification</th>
		</tr>
	</thead>
	<tbody>
		{foreach $queries as $query}
			<tr{if $query.queryId eq $queryId} class="active"{/if}>
				<td>
					<a href="{service controller=search_stored action=list queryId=$query.queryId}">{$query.label|escape}</a>
					<span class="label {$priorities[$query.priority].class|escape}">{$priorities[$query.priority].label|escape}</span>
				</td>
				<td>
					{if $query.lastModif}
						{$query.lastModif|tiki_short_datetime}
					{else}
						{tr}Never{/tr}
					{/if}
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td>
					{tr}No stored queries!{/tr}
				</td>
				<td>{tr}Never{/tr}</td>
			</tr>
		{/foreach}
	</tbody>
</table>

{if $queryId}
	{tabset}
		{if $results}
			{tab name="{tr}Results{/tr}"}
				{$results}
			{/tab}
		{/if}
		{tab name="{tr}Edit{/tr}"}
			{service_inline controller=search_stored action=edit queryId=$queryId}
		{/tab}
		{tab name="{tr}Delete{/tr}"}
			{service_inline controller=search_stored action=delete queryId=$queryId}
		{/tab}
	{/tabset}
{else}
	<h2>{tr}My Watch List{/tr}</h2>
	{wikiplugin _name=list}
	{literal}
		{filter relation={/literal}{$user}{literal} objecttype=user qualifier=tiki.watchlist.contains.invert}
		{ALTERNATE()}^{/literal}{tr}Watch List is empty.{/tr}{literal}^{ALTERNATE}
		{sort mode=modification_date_desc}
		{OUTPUT(template=table paginate=1)}
			{column label="{/literal}{tr}Title{/tr}{literal}" field="title_link" mode=raw}
			{column label="{/literal}{tr}Last Modification{/tr}{literal}" field="date"}
		{OUTPUT}

		{FORMAT(name="title_link")}{display name=title format=objectlink}{FORMAT}
		{FORMAT(name="date")}{display name=modification_date format=datetime}{FORMAT}
	{/literal}
	{/wikiplugin}
{/if}
{/block}
