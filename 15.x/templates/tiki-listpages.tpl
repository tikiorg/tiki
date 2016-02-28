{* $Id$ *}
{title admpage="wiki" help="Using+Wiki+Pages#List_Pages"}{tr}Pages{/tr}{/title}

{tabset name='tabs_wikipages'}
	{tab name="{tr}List Wiki Pages{/tr}"}
		<h2>{tr}List Wiki Pages{/tr}</h2>
		{if !$tsOn}
			<div class="clearfix">
				{include autocomplete='pagename' file='find.tpl' find_show_languages='y' find_show_languages_excluded='y' find_show_categories_multi='y' find_show_num_rows='y' find_in="<ul><li>{tr}Page name{/tr}</li></ul>" }
			</div>
		{else}
			{include file='find.tpl' map_only='y'}
		{/if}
		<form name="checkform" method="get">
			<input type="hidden" name="offset" value="{$offset|escape}">
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
			<input type="hidden" name="find" value="{$find|escape}">
			<input type="hidden" name="maxRecords" value="{$maxRecords|escape}">
		</form>
		{if isset($error) and $error}
			<div class="alert alert-warning">
				{$error}
			</div>
		{/if}

		{if isset($mapview) and $mapview}
			{wikiplugin _name="map" scope=".listpagesmap .geolocated" width="400" height="400"}{/wikiplugin}
		{/if}

		<div id="tiki-listpages-content">
			{if $aliases}
				<div class="aliases">
					<strong>{tr}Page aliases found:{/tr}</strong>
					{foreach from=$aliases item=alias}
						<a href="{$alias.toPage|sefurl}" title="{$alias.fromPage|escape}" class="alias">{$alias.toPage|escape};</a>
					{/foreach}
				</div>
			{/if}
			{include file='tiki-listpages_content.tpl' clean='n'}
		</div>
	{/tab}

	{if $tiki_p_edit == 'y'}
		{tab name="{tr}Create a Wiki Page{/tr}"}
			<h2>{tr}Create a Wiki Page{/tr}</h2><br>
			<div>
				<form method="get" action="tiki-editpage.php" class="form-horizontal">
					<div class="form-group">
						<label class="control-label col-sm-3">{tr}Insert name of the page you wish to create{/tr}</label>
						<div class="col-sm-9">
							<input class="form-control" id="pagename" type="text" name="page">
						</div>
					</div>
					{if $prefs.namespace_enabled == 'y' && $prefs.namespace_default}
					<div class="form-group">
						<label class="control-label col-sm-3">{tr _0=$prefs.namespace_default}Create page within %0{/tr}</label>
						<div class="col-sm-9">
							<input type="checkbox" name="namespace" value="{$prefs.namespace_default|escape}" checked="checked">
						</div>
					</div>
					{/if}
					<div class="form-group">
						<label class="control-label col-sm-3"></label>
						<div class="col-sm-9">
							<input class="btn btn-primary" type="submit" name="quickedit" value="{tr}Create Page{/tr}">
						</div>
					</div>
					
				</form>
			</div>
		{/tab}
	{/if}

{/tabset}
