{title help="Banners"}{tr}Edit or create banners{/tr}{/title}

<div class="navbar">
	{button href="tiki-list_banners.php" _text="{tr}List banners{/tr}"}
</div>

<form action="tiki-edit_banner.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="bannerId" value="{$bannerId|escape}" />
	<div class="simplebox">
		<table class="formcolor">
			<tr>
				<td>{tr}URL to link the banner{/tr}</td>
				<td>
					<input type="text" name="url" value="{$url|escape}" />
				</td>
			</tr>
			<tr>
				<td>{tr}Client:{/tr}</td>
				<td>
					<select name="client">
						{section name=ix loop=$clients}
							<option value="{$clients[ix].user|escape}" {if $client eq $clients[ix].user}selected="selected"{/if}>{$clients[ix].user|escape}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td>{tr}Max impressions:{/tr}</td>
				<td>
					<input type="text" name="maxImpressions" value="{$maxImpressions|escape}" size="7" /> <i>{tr}-1 for unlimited{/tr}</i>
				</td>
			</tr>
			<tr>
				<td>{tr}Max impressions for a user:{/tr}</td>
				<td>
					<input type="text" name="maxUserImpressions" value="{$maxUserImpressions|escape}" size="7" /> <i>{tr}-1 for unlimited{/tr}</i>
				</td>
			</tr>
			<tr>
				<td>{tr}Max clicks:{/tr}</td>
				<td>
					<input type="text" name="maxClicks" value="{$maxClicks|escape}" size="7" /><i>{tr}-1 for unlimited{/tr}</i>
				</td>
			</tr>
			<tr>
				<td>{tr}URIs where the banner appears only{/tr}</td>
				<td><input type="text" name="onlyInURIs" value="{$onlyInURIs|escape}" /><br /><i>Type each URI enclosed with the # character. Exemple:#/this_page#/tiki-index.php?page=this_page#</i>
			</tr>
			<tr>
				<td>{tr}Zone:{/tr}</td>
				<td>
					<select name="zone"{if !$zones} disabled="disabled"{/if}>
						{section name=ix loop=$zones}
							<option value="{$zones[ix].zone|escape}" {if $zone eq $zones[ix].zone}selected="selected"{/if}>{$zones[ix].zone|escape}</option>
						{sectionelse}
							<option value="" disabled="disabled" selected="selected">{tr}None{/tr}</option>
						{/section}
					</select>
					<br />
					{tr}Or, create a new zone:{/tr}
					<br />
					<input type="text" name="zoneName" size="10" />
					<input type="submit" name="create_zone" value="{tr}Create{/tr}" />
				</td>
			</tr>
		</table>
	</div>

	<div class="simplebox">
		<table class="formcolor">
			<tr>
				<td colspan="2">{tr}Show the banner only between these dates:{/tr}</td>
			</tr>
			<tr>
				<td>{tr}From date:{/tr}</td>
				<td>
					{html_select_date time=$fromDate prefix="fromDate_" end_year="+2" field_order=$prefs.display_field_order}
				</td>
			</tr>
			<tr>
				<td>{tr}To date:{/tr}</td>
				<td>
					{html_select_date time=$toDate prefix="toDate_" end_year="+2" field_order=$prefs.display_field_order}
				</td>
			</tr>
			<tr>
				<td>{tr}Use dates{/tr}</td>
				<td>
					<input type="checkbox" name="useDates" {if $useDates eq 'y'}checked='checked'{/if}/>
				</td>
			</tr>
		</table>
	</div>

	<div class="simplebox">
		<table class="formcolor">
			<tr>
				<td colspan="2">{tr}Show the banner only in this hours:{/tr}</td>
			</tr>
			<tr>
				<td>{tr}from:{/tr}</td>
				<td>{html_select_time time=$fromTime display_seconds=false prefix='fromTime' use_24_hours=$use_24hr_clock}</td>
			</tr>
			<tr>
				<td>{tr}to:{/tr}</td>
				<td>{html_select_time time=$toTime display_seconds=false prefix='toTime' use_24_hours=$use_24hr_clock}</td>
			</tr>
		</table>
	</div>

	<div class="simplebox">
		<table class="formcolor">
			<tr>
				<td colspan="7">{tr}Show the banner only on:{/tr}</td>
			</tr>
			<tr>
				<td>
					{tr}Mon:{/tr}<input type="checkbox" name="Dmon" {if $Dmon eq 'y'}checked="checked"{/if} />
				</td>
				<td>
					{tr}Tue:{/tr}<input type="checkbox" name="Dtue" {if $Dtue eq 'y'}checked="checked"{/if} />
				</td>
				<td>
					{tr}Wed:{/tr}<input type="checkbox" name="Dwed" {if $Dwed eq 'y'}checked="checked"{/if} />
				</td>
				<td>
					{tr}Thu:{/tr}<input type="checkbox" name="Dthu" {if $Dthu eq 'y'}checked="checked"{/if} />
				</td>
				<td>
					{tr}Fri:{/tr}<input type="checkbox" name="Dfri" {if $Dfri eq 'y'}checked="checked"{/if} />
				</td>
				<td>
					{tr}Sat:{/tr}<input type="checkbox" name="Dsat" {if $Dsat eq 'y'}checked="checked"{/if} />
				</td>
				<td>
					{tr}Sun:{/tr}<input type="checkbox" name="Dsun" {if $Dsun eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
		</table>
	</div>

	<div class="simplebox">
		{tr}Select ONE method for the banner{/tr}
		<table class="formcolor">
			<tr>
				<td>
					<input type="radio" name="use" value="useHTML" {if $use eq 'useHTML'}checked="checked"{/if}/>
				</td>
				<td>
					{tr}Use HTML{/tr}
					<table>
						<tr>
							<td>{tr}HTML code:{/tr}</td>
							<td>
								<textarea rows="5" cols="50" name="HTMLData">{if $use ne 'useFlash'}{$HTMLData|escape}{/if}</textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="use" value="useImage" {if $use eq 'useImage'}checked="checked"{/if}/>
				</td>
				<td>
					{tr}Use image{/tr}
					<table class="formcolor">
						<tr>
							<td>{tr}Image:{/tr}</td>
							<td>
								<input type="hidden" name="imageData" value="{$imageData|escape}" />
								<input type="hidden" name="imageName" value="{$imageName|escape}" />
								<input type="hidden" name="imageType" value="{$imageType|escape}" />
								<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
								<input name="userfile1" type="file" />
							</td>
						</tr>
						{if $hasImage eq 'y'}
							<tr>
								<td>{tr}Current Image{/tr}</td>
								<td>
									{$imageName}: <img src="{$tempimg}" alt="{tr}Current Image{/tr}"/>
								</td>
							</tr>
						{/if}
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="use" value="useFixedURL" {if $use eq 'useFixedURL'}checked="checked"{/if}/>
				</td>
				<td>
					{tr}Use image generated by URL (the image will be requested at the URL for each impression){/tr}
					<table>
						<tr>
							<td>{tr}URL:{/tr}</td>
							<td>
								<input type="text" name="fixedURLData" value="{$fixedURLData|escape}" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="use" value="useFlash" {if $use eq 'useFlash'}checked="checked"{/if}/>
				</td>
				<td>
					{tr}Use Flash{/tr}
					<table>
						{if $use eq 'useFlash'}
							<tr>
								<td colspan=2>
									{banner id="$bannerId"}
								</td>
							</tr>
							{/if}
						<tr>
							<td>
								{tr}Movie URL{/tr} <input type="text" size="50" name="movieUrl" value="{$movie.movie|escape}" />
								<br />
								{tr}Movie Size:{/tr} <input type="text" size="4" name="movieWidth" value="{$movie.width|escape}" /> {tr}Pixels{/tr} x <input type="text" size="4" name="movieHeight" value="{$movie.height|escape}" /> {tr}Pixels{/tr}
								<br />
								{tr}FlashPlugin min version:{/tr} <input type="text" name="movieVersion" value="{$movie.version|escape}" />({tr}ex:{/tr}9.0.0)
								<br />
								Note: To be managed with tiki , your flash banner link should be: <a class="link" href="banner_click.php?id={$bannerId}&amp;url={$url}">banner_click.php?id={$bannerId}&amp;url={$url}</a> 
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="use" value="useText" {if $use eq 'useText'}checked="checked"{/if}/>
				</td>
				<td>
					{tr}Use text{/tr}
					<table class="formcolor">
						<tr>
							<td>{tr}Text:{/tr}</td>
							<td>
								<textarea rows="8" cols="20" name="textData">{$textData|escape}</textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>

	<input type="submit" name="save" value="{tr}Save the Banner{/tr}" />
</form>

{if $zones}
	<div align="left" class="simplebox">
		<h2>{tr}Remove Zones (you lose entered info for the banner){/tr}</h2>
		<table class="normal">
			<tr>
				<th>{tr}Name{/tr}</th>
				<th>{tr}Action{/tr}</th>
			</tr>
			{cycle print=false values="even,odd"}
			{section name=ix loop=$zones}
				<tr class="{cycle}">
					<td class="text">{$zones[ix].zone|escape}</td>
					<td class="action">
						<a class="link" href="tiki-edit_banner.php?removeZone={$zones[ix].zone|escape:url}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
					</td>
				</tr>
			{/section}
		</table>
	</div>
{/if}
