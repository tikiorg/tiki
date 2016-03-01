{* $Id$ *}
{title help="Banners"}{tr}Create or edit banners{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_banners.php" _class="btn btn-link" _type="link" _icon_name="list" _text="{tr}List banners{/tr}"}
</div>

<form action="tiki-edit_banner.php" method="post" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="bannerId" value="{$bannerId|escape}">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}URL to link the banner{/tr}</label>
				<div class="col-sm-7 margin-bottom-sm">
			      	<input type="text" name="url" value="{$url|escape}" class="form-control">
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}Client{/tr}</label>
				<div class="col-sm-7 margin-bottom-sm">
			      	{user_selector user=$client name='client'}
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}Maximum impressions{/tr}</label>
				<div class="col-sm-7">
			      	<input type="text" name="maxImpressions" value="{$maxImpressions|escape}" maxlength="7" class="form-control">
			      	<div class="help-block">
			      		{tr}-1 for unlimited{/tr}
			      	</div>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}Maximum number of impressions for a user{/tr}</label>
				<div class="col-sm-7">
			      	<input type="text" name="maxUserImpressions" value="{$maxUserImpressions|escape}" maxlength="7" class="form-control">
  				    <div class="help-block">
			      		{tr}-1 for unlimited{/tr}
			      	</div>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}Maximum clicks{/tr}</label>
				<div class="col-sm-7">
			      	<input type="text" name="maxClicks" value="{$maxClicks|escape}" maxlength="7" class="form-control">
			      	<div class="help-block">
			      		{tr}-1 for unlimited{/tr}
			      	</div>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}URIs where the banner appears only{/tr}</label>
				<div class="col-sm-7">
			      	<input type="text" name="onlyInURIs" value="{$onlyInURIs|escape}" class="form-control">
			      	<div class="help-block">
			      		{tr}Type each URI enclosed with the # character. Exemple:#/this_page#/tiki-index.php?page=this_page#{/tr}
			      	</div>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}URIs where the banner will not appear{/tr}</label>
				<div class="col-sm-7">
			      	<input type="text" name="exceptInURIs" value="{$exceptInURIs|escape}" class="form-control">
			      	<div class="help-block">
			      		{tr}Type each URI enclosed with the # character. Exemple:#/this_page#/tiki-index.php?page=this_page#{/tr}
			      	</div>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}Zone{/tr}</label>
				<div class="col-sm-7">
			      	<select name="zone"{if !$zones} disabled="disabled"{/if} class="form-control">
						{section name=ix loop=$zones}
							<option value="{$zones[ix].zone|escape}" {if $zone eq $zones[ix].zone}selected="selected"{/if}>{$zones[ix].zone|escape}</option>
						{sectionelse}
							<option value="" disabled="disabled" selected="selected">{tr}None{/tr}</option>
						{/section}
					</select>
			      	<div class="help-block">
			      		{tr}Or, create a new zone{/tr}
			      	</div>
			    </div>
			    <label class="col-sm-3 control-label">{tr}New Zone{/tr}</label>
			    <div class="col-sm-7">
			    	<input type="text" name="zoneName" maxlength="10" class="form-control">
		    	</div>
		    </div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7">
			      	<input type="submit" class="btn btn-default btn-sm" name="create_zone" value="{tr}Create{/tr}">
			    </div>
		    </div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body">
			<h4>{tr}Show the banner only between these dates:{/tr}</h4>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}From date:{/tr}</label>
				<div class="col-sm-7">
			      	{html_select_date time=$fromDate prefix="fromDate_" end_year="+2" field_order=$prefs.display_field_order}
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}To date:{/tr}</label>
				<div class="col-sm-7">
			      	{html_select_date time=$fromDate prefix="fromDate_" end_year="+2" field_order=$prefs.display_field_order}
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}Use dates:{/tr}</label>
				<div class="col-sm-7">
					<label class="checkbox-inline"><input type="checkbox" name="useDates" {if $useDates eq 'y'}checked='checked'{/if}>Yes</label>
			    </div>
		    </div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body">
			<h4>{tr}Show the banner only in these hours:{/tr}</h4>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}from{/tr}</label>
				<div class="col-sm-7">
			      	{html_select_time time=$fromTime display_seconds=false prefix='fromTime' use_24_hours=$use_24hr_clock}
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}to{/tr}</label>
				<div class="col-sm-7">
			      	{html_select_time time=$toTime display_seconds=false prefix='toTime' use_24_hours=$use_24hr_clock}
			    </div>
		    </div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body">
			<h4>{tr}Show the banner only on:{/tr}</h4>
			<div class="col-sm-12">
			<div class="form-group">
			      	<label class="checkbox-inline"><input type="checkbox" name="Dmon" {if $Dmon eq 'y'}checked="checked"{/if}>{tr}Mon{/tr}</label>
			      	<label class="checkbox-inline"><input type="checkbox" name="Dtue" {if $Dtue eq 'y'}checked="checked"{/if}>{tr}Tue{/tr}</label>
			      	<label class="checkbox-inline"><input type="checkbox" name="Dwed" {if $Dwed eq 'y'}checked="checked"{/if}>{tr}Wed{/tr}</label>
			      	<label class="checkbox-inline"><input type="checkbox" name="Dthu" {if $Dthu eq 'y'}checked="checked"{/if}>{tr}Thu{/tr}</label>
			      	<label class="checkbox-inline"><input type="checkbox" name="Dfri" {if $Dfri eq 'y'}checked="checked"{/if}>{tr}Fri{/tr}</label>
			      	<label class="checkbox-inline"><input type="checkbox" name="Dsat" {if $Dsat eq 'y'}checked="checked"{/if}>{tr}Sat{/tr}</label>
			      	<label class="checkbox-inline"><input type="checkbox" name="Dsun" {if $Dsun eq 'y'}checked="checked"{/if}>{tr}Sun{/tr}</label>
		    </div></div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body">
			<h4>{tr}Select ONE method for the banner:{/tr}</h4>
			<div class="form-group">
				<label class="col-sm-3 control-label"><label class="radio-inline"><input type="radio" name="use" value="useHTML" {if $use eq 'useHTML'}checked="checked"{/if}>{tr}Use HTML{/tr}</label></label>
				<div class="col-sm-7">
					<textarea class="form-control" rows="5" name="HTMLData">{if $use ne 'useFlash'}{$HTMLData|escape}{/if}</textarea>
					<div class="help-block">
						{tr}HTML code{/tr}
					</div>
			    </div>
		    </div>
	    	<div class="form-group">
				<label class="col-sm-3 control-label"><label class="radio-inline"><input type="radio" name="use" value="useImage" {if $use eq 'useImage'}checked="checked"{/if}>{tr}Use Image{/tr}</label></label>
				<div class="col-sm-7">
					<input type="hidden" name="imageData" value="{$imageData|escape}">
					<input type="hidden" name="imageName" value="{$imageName|escape}">
					<input type="hidden" name="imageType" value="{$imageType|escape}">
					<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
					<input name="userfile1" type="file">
			    </div>
		    </div>
		    <div class="form-group">
			    {if $hasImage eq 'y'}
			    <label class="col-sm-3 control-label">{tr}Current Image{/tr}</label>
				<div class="col-sm-7">
					{$imageName}: <img src="{$tempimg}" alt="{tr}Current Image{/tr}">
			    </div>
			    {/if}
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label"><label class="radio-inline"><input type="radio" name="use" value="useFixedURL" {if $use eq 'useFixedURL'}checked="checked"{/if}>{tr}Use Image from URL{/tr}</label></label>
				<div class="col-sm-7">
					<input type="text" name="fixedURLData" value="{$fixedURLData|escape}" class="form-control">
					<div class="help-block">
						{tr}(the image will be requested at the URL for each impression){/tr}
					</div>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label"><label class="radio-inline"><input type="radio" name="use" value="useFlash" {if $use eq 'useFlash'}checked="checked"{/if}>{tr}Use Flash{/tr}</label></label>
		    	{if $use eq 'useFlash'}
			    	<div class="col-sm-7">
		    			{banner id="$bannerId"}
			    	</div>
		    	{/if}
		    </div>
		    <div class="form-group">
			    <label class="col-sm-3 control-label">{tr}Movie URL{/tr}</label>
			    <div class="col-sm-7 margin-bottom-sm">
			    	 <input type="text" name="movieUrl" value="{$movie.movie|escape}" class="form-control">
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">{tr}Movie Size{/tr}</label>
			    <div class="col-sm-3">
			    	<input type="text" name="movieWidth" value="{$movie.width|escape}" class="form-control" placeholder="{tr}width in pixels{/tr}">
			    	<div class="help-block">
						{tr}Pixels{/tr}
					</div>
			    </div>
			    <div class="col-sm-3">
			    	<input type="text" name="movieHeight" value="{$movie.height|escape}" class="form-control" placeholder="{tr}height in pixels{/tr}">
			    	<div class="help-block">
						{tr}Pixels{/tr}
					</div>
		    	</div>
			</div>
			<div class="form-group">
	    		<label class="col-sm-3 control-label">{tr}FlashPlugin min version{/tr}</label>
			    <div class="col-sm-7 margin-bottom-sm">
			    	<input type="text" name="movieVersion" value="{$movie.version|escape}" class="form-control">
			    	<div class="help-block">
			    		({tr}ex:{/tr}9.0.0)
		    		</div>
			    </div>
			    <div class="col-sm-7 col-sm-offset-4">
				    <div class="help-block">
				    	Note: To be managed with tiki , your flash banner link should be: <a class="link" href="banner_click.php?id={$bannerId}&amp;url={$url}">banner_click.php?id={$bannerId}&amp;url={$url}</a>
			    	</div>
		    	</div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label"><label class="radio-inline"><input type="radio" name="use" value="useText" {if $use eq 'useText'}checked="checked"{/if}>{tr}Use Text{/tr}</label></label>
				<div class="col-sm-7">
					<textarea class="form-control" rows="5" name="textData">{$textData|escape}</textarea>
			    </div>
		    </div>
		</div>
	</div>
	<input type="submit" class="btn btn-default" name="save" value="{tr}Save the Banner{/tr}">
</form>

{if $zones}
	<div align="left" class="panel panel-default">
		<div class="panel-body">
			<h2>{tr}Remove zones (info entered for any banner in the zones will be lost){/tr}</h2>
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<tr>
						<th>{tr}Name{/tr}</th>
						<th></th>
					</tr>

					{section name=ix loop=$zones}
						<tr>
							<td class="text">{$zones[ix].zone|escape}</td>
							<td class="action">
								<a class="tips" title=":{tr}Remove{/tr}" href="tiki-edit_banner.php?removeZone={$zones[ix].zone|escape:url}">
									{icon name='remove'}
								</a>
							</td>
						</tr>
					{/section}
				</table>
			</div>
		</div>
	</div>
{/if}
