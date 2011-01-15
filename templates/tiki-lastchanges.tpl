{* $Id$ *}

{title admpage="wiki" help="Using+Wiki+Pages#Last_Changes" url="tiki-lastchanges.php?days=$days"}{tr}Last Changes{/tr}{/title}

<div class="navbar">
	{if $days eq '1'}{assign var=thisclass value='highlight'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=1" _text="{tr}Today{/tr}" _class=$thisclass}
	{if $days eq '2'}{assign var=thisclass value='highlight'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=2" _text="{tr}Last 2 days{/tr}" _class=$thisclass}
	{if $days eq '3'}{assign var=thisclass value='highlight'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=3" _text="{tr}Last 3 days{/tr}" _class=$thisclass}
	{if $days eq '5'}{assign var=thisclass value='highlight'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=5" _text="{tr}Last 5 days{/tr}" _class=$thisclass}
	{if $days eq '7'}{assign var=thisclass value='highlight'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=7" _text="{tr}Last week{/tr}" _class=$thisclass}
	{if $days eq '14'}{assign var=thisclass value='highlight'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=14" _text="{tr}Last 2 weeks{/tr}" _class=$thisclass}
	{if $days eq '31'}{assign var=thisclass value='highlight'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=31" _text="{tr}Last month{/tr}" _class=$thisclass}
	{if $days eq '0'}{assign var=thisclass value='highlight'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=0" _text="{tr}All{/tr}" _class=$thisclass}
</div>

{if $lastchanges or ($find ne '')}
	{include file='find.tpl'}
	{if $findwhat != ""}
		{button href="tiki-lastchanges.php" _text="{tr}Search by Date{/tr}"}
	{/if}
{/if}

<br />

{if $findwhat!=""}
	{tr}Found{/tr} "<b>{$findwhat|escape}</b>" {tr}in{/tr} {$cant_records|escape} {tr}LastChanges{/tr} 
{/if}
<table class="normal">
	<tr>
		<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Date{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='object'}{tr}Page{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='action'}{tr}Action{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='user'}{tr}User{/tr}{/self_link}</th>
		{if $prefs.feature_wiki_history_ip ne 'n'}
			<th>{self_link _sort_arg='sort_mode' _sort_field='ip'}{tr}Ip{/tr}{/self_link}</th>
		{/if}
		<th>{self_link _sort_arg='sort_mode' _sort_field='comment'}{tr}Comment{/tr}{/self_link}</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=changes loop=$lastchanges}
		<tr class="{cycle}">
			<td class="date">{$lastchanges[changes].lastModif|tiki_short_datetime}</td>
			<td>
				<a href="{$lastchanges[changes].pageName|sefurl}" class="tablename" title="{$lastchanges[changes].pageName|escape}">
					{$lastchanges[changes].pageName|truncate:$prefs.wiki_list_name_len:"...":true|escape}
				</a> 
			</td>
			<td>{tr}{$lastchanges[changes].action|escape}{/tr}</td>
			<td class="username">{$lastchanges[changes].user|userlink}</td>
			{if $prefs.feature_wiki_history_ip ne 'n'}
				<td>{$lastchanges[changes].ip}</td>
			{/if}
			<td>{$lastchanges[changes].comment|escape}</td>
			<td class="action">
				{if $tiki_p_wiki_view_history eq 'y'} 
					{if $lastchanges[changes].version}
						<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}'>{icon _id='page_white_stack' alt="{tr}History{/tr}"}</a>{tr}v{/tr}{$lastchanges[changes].version}
	&nbsp;<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;preview={$lastchanges[changes].version|escape:"url"}' title="{tr}View{/tr}">v</a>&nbsp;
						{if $tiki_p_rollback eq 'y'}
							<a class="link" href='tiki-rollback.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;version={$lastchanges[changes].version|escape:"url"}' title="{tr}Rollback{/tr}">b</a>&nbsp;
						{/if}
						<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff={$lastchanges[changes].version|escape:"url"}' title="{tr}Compare{/tr}">c</a>&nbsp;
						<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff2={$lastchanges[changes].version|escape:"url"}' title="{tr}Diff{/tr}">d</a>&nbsp;
						{if $tiki_p_wiki_view_source eq 'y'}
							<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;source={$lastchanges[changes].version|escape:"url"}' title="{tr}Source{/tr}">s</a>
						{/if}
					{elseif $lastchanges[changes].versionlast}
						<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}'>{icon _id='page_white_stack' alt="{tr}History{/tr}"}</a>
					{/if}
				{/if}
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan="7"}
	{/section}
</table>

{pagination_links cant=$cant_records step=$prefs.maxRecords offset=$offset}{/pagination_links}
