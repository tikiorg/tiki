{title help="User+Calendar"}{tr}Mini Calendar: Preferences{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-minical.php#add" class="btn btn-default" _text="{tr}Add{/tr} "}
	{button href="tiki-minical_prefs.php" class="btn btn-default" _text="{tr}Prefs{/tr}"}
	{button href="tiki-minical.php?view=daily" class="btn btn-default" _text="{tr}Daily{/tr}"}
	{button href="tiki-minical.php?view=weekly" class="btn btn-default" _text="{tr}Weekly{/tr}"}
	{button href="tiki-minical.php?view=list" class="btn btn-default" _text="{tr}List{/tr}"}
</div>

<h2>{tr}Preferences{/tr}</h2>
<form action="tiki-minical_prefs.php" method="post" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Calendar Interval in daily view{/tr}</label>
		<div class="col-sm-7">
	      	<select name="minical_interval" class="form-control">
				<option value="300" {if $minical_interval eq 300}selected="selected"{/if}>5 {tr}minutes{/tr}</option>
				<option value="600" {if $minical_interval eq 600}selected="selected"{/if}>10 {tr}minutes{/tr}</option>
				<option value="900" {if $minical_interval eq 900}selected="selected"{/if}>15 {tr}minutes{/tr}</option>
				<option value="1800" {if $minical_interval eq 1800}selected="selected"{/if}>30 {tr}minutes{/tr}</option>
				<option value="3600" {if $minical_interval eq 3600}selected="selected"{/if}>1 {tr}hour{/tr}</option>
			</select>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Start hour for days{/tr}</label>
		<div class="col-sm-7">
	      	<select name="minical_start_hour" class="form-control">
				{html_options output=$hours values=$hours selected=$minical_start_hour}
			</select>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}End hour for days{/tr}</label>
		<div class="col-sm-7">
	      	<select name="minical_end_hour" class="form-control">
				{html_options output=$hours values=$hours selected=$minical_end_hour}
			</select>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Upcoming Events{/tr}</label>
		<div class="col-sm-7">
	      	<select name="minical_upcoming" class="form-control">
				{html_options output=$upcoming values=$upcoming selected=$minical_upcoming}
			</select>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Reminders{/tr}</label>
		<div class="col-sm-7">
	      	<select name="minical_reminders" class="form-control">
				<option value="0" {if $prefs.minical_reminders eq 0}selected="selected"{/if}>{tr}no reminders{/tr}</option>
				<option value="60" {if $prefs.minical_reminders eq 60}selected="selected"{/if}>1 {tr}min{/tr}</option>
				<option value="120" {if $prefs.minical_reminders eq 120}selected="selected"{/if}>2 {tr}min{/tr}</option>
				<option value="300" {if $prefs.minical_reminders eq 300}selected="selected"{/if}>5 {tr}min{/tr}</option>
				<option value="600" {if $prefs.minical_reminders eq 600}selected="selected"{/if}>10 {tr}min{/tr}</option>
				<option value="900" {if $prefs.minical_reminders eq 900}selected="selected"{/if}>15 {tr}min{/tr}</option>
			</select>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
	      	<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
	    </div>
    </div>
</form>
<a id="import"></a>
<h2>{tr}Import CSV file{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-minical_prefs.php" method="post" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Upload file{/tr}</label>
		<div class="col-sm-7">
	      	<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000">
			<input size="16" name="userfile1" type="file">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
	      	<input type="submit" class="btn btn-default btn-sm" name="import" value="{tr}import{/tr}">
	    </div>
    </div>  
</form>

<h2>{tr}Admin Topics{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-minical_prefs.php" method="post" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Name:{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="name" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Upload file:{/tr}</label>
		<div class="col-sm-7">
	      	<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="16" name="userfile1" type="file">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Or enter path or URL:{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="path" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
	      	<input type="submit" class="btn btn-default btn-sm" name="addtopic" value="{tr}Add Topic{/tr}">
	    </div>
    </div>
</form>
{if count($topics) > 0}
	<div class="panel panel-default"><div class="panel-body">
		<table>
			<tr>
				{section name=numloop loop=$topics}
					<td>
						{if $topics[numloop].isIcon eq 'y'}
							<img src="{$topics[numloop].path}" alt="{tr}topic image{/tr}">
						{else}
							<img src="tiki-view_minical_topic.php?topicId={$topics[numloop].topicId}" alt="{tr}topic image{/tr}">
						{/if}
						{$topics[numloop].name}
						[<a class="link" href="tiki-minical_prefs.php?removetopic={$topics[numloop].topicId}">x</a>]
					</td>
					{* see if we should go to the next row *}
					{if not ($smarty.section.numloop.rownum mod $cols)}
						{if not $smarty.section.numloop.last}
							</tr>
							<tr>
						{/if}
					{/if}
					{if $smarty.section.numloop.last}
						{* pad the cells not yet created *}
						{math equation = "n - a % n" n=$cols a=$data|@count assign="cells"}
						{if $cells ne $cols}
							{section name=pad loop=$cells}
								<td>&nbsp;</td>
							{/section}
						{/if}
						</tr>
					{/if}
				{/section}
			</table>
		</div>
	</div>
{/if}
