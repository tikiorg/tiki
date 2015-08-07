{* $Id$ *}

{title help="Hotwords"}{tr}Admin Hotwords{/tr}{/title}

<h2>{tr}Add Hotword{/tr}</h2>

<form method="post" action="tiki-admin_hotwords.php" class="form-horizontal">

    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Word{/tr}</label>
		<div class="col-sm-7 col-sm-offset-1">
		    <input type="text" name="word" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}URL{/tr}</label>
		<div class="col-sm-7 col-sm-offset-1">
		    <input type="text" name="url" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7 col-sm-offset-1">
		    <input type="submit" class="btn btn-default btn-sm" name="add" value="{tr}Add{/tr}">
	    </div>
    </div>
</form>

<h2>{tr}Hotwords{/tr}</h2>
{if $words}
	{include file='find.tpl'}
{/if}
<div class="table-responsive">
<table class="table table-striped table-hover">
	<tr>
		<th>
			<a href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'word_desc'}word_asc{else}word_desc{/if}">{tr}Word{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_hotwords.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>

	{section name=user loop=$words}
		<tr>
			<td class="text">{$words[user].word}</td>
			<td class="text">{$words[user].url}</td>
			<td class="action">
				<a class="tips" href="tiki-admin_hotwords.php?remove={$words[user].word|escape:"url"}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={$sort_mode}" title=":{tr}Delete{/tr}">
					{icon name='remove'}
				</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=3}
	{/section}
</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
