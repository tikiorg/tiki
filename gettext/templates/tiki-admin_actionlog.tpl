{title help="Action log"}{tr}Action Log{/tr}{/title}

{tabset name="admin_actionlog"}

{tab name="{tr}Report{/tr}"}
	<form method="get" action="tiki-admin_actionlog.php#Report">
		<h2>{tr}Filter{/tr}</h2>
		{if empty($nbViewedConfs)}
			{button _text="{tr}Please select some actions to be reported.{/tr}" href="tiki-admin_actionlog.php?cookietab=2"}
		{else}
		<fieldset>
			<legend>{tr}Date{/tr}</legend>
			<table>
				<tr>
					<td>{tr}Start:{/tr}</td>
					<td>{html_select_date time=$startDate prefix="startDate_" end_year="-10" field_order=$prefs.display_field_order}</td>
					<td>{tr}End:{/tr}</td>
					<td>{html_select_date time=$endDate prefix="endDate_" end_year="-10" field_order=$prefs.display_field_order}</td>
				</tr>
			</table>
		</fieldset>
				{if $tiki_p_admin eq 'y'}
			<fieldset>
				<legend>{tr}Users and Groups{/tr}</legend>
				<table class="formcolor">
					<tr>
						<td>{tr}User:{/tr}</td>
						<td>
							<select multiple="multiple" size="{if $users|@count > 5}5{else}{math equation=x+y x=$users|@count y=2}{/if}" name="selectedUsers[]">
								<option value="">{tr}All{/tr}</option>
								<option value="Anonymous">{tr}Anonymous{/tr}</option>
								{foreach key=ix item=auser from=$users}
									<option value="{$auser|escape}" {if $selectedUsers[$ix] eq 'y'}selected="selected"{/if}>{$auser|escape}</option>
								{/foreach}
							</select>
						</td>
				{else}
					<input type="hidden" name="selectedUsers[]" value="{$auser|escape}">
				{/if}
				
				{if $groups|@count >= 1}
						<td>{tr}Group:{/tr}</td>
						<td>
							<select multiple="multiple" size="{if $groups|@count > 5}5{else}{math equation=x+y x=$groups|@count y=1}{/if}" name="selectedGroups[]">
								<option value="">{tr}All{/tr}</option>
								{foreach from=$groups key=ix item=group}
									<option value="{$group|escape}" {if $selectedGroups[$group] eq 'y'}selected="selected"{/if}>{$group}</option>
								{/foreach}
							</select>
						</td>
					</tr>
				</table>
				</fieldset>
				{/if}

			<fieldset>
				<legend>{tr}Category:{/tr}</legend>
				<table class="formcolor">
				<tr>
					<td>
						<select name="categId">
							<option value="" {if $reportCateg eq '' or $reportCateg eq 0}selected="selected"{/if}>* {tr}All{/tr} *</option>
							{foreach item=category from=$categories}
								<option value="{$category.categId|escape}" {if $reportCateg eq $category.name}selected="selected"{/if}>{$category.name|escape}</option>
							{/foreach}
						</select>
						</td>
					</tr>
				</table>
				</fieldset>

				<fieldset>
				<legend>{tr}Misc.{/tr}</legend>
				<table class="formcolor">
					<tr>
						<th>{tr}Units{/tr}</th>
						<td>
							{tr}bytes{/tr}
							<input type="radio" name="unit" value="bytes"{if $unit ne 'kb'} checked="checked"{/if} /> {tr}kb{/tr}
							<input type="radio" name="unit" value="kb"{if $unit eq 'kb'} checked="checked"{/if} />
						</td>
					</tr>
					<tr>
						<th>{tr}Contibution Time{/tr}</th>
						<td>
							{tr}Week{/tr}
							<input type="radio" name="contribTime" value="w"{if $contribTime ne 'd'} checked="checked"{/if} /> 
							{tr}Day{/tr}
							<input type="radio" name="contribTime" value="d"{if $contribTime eq 'd'} checked="checked"{/if} />
						</td>
					</tr>
					<tr>
						<th>{tr}Search{/tr}</th>
						<td>
							<input type="text" name="find" value="{$find}" /> 
						</td>
					</tr>
				
					{if $prefs.feature_contribution eq 'y'}
						<tr>
							<td colspan="2">
								<input type="submit" name="graph" value="{tr}Graph Contributions{/tr}" />
								{if $prefs.feature_jpgraph eq 'y'}
									<br />
									{tr}Group Bar Plot:{/tr}
									<input type="radio" name="barPlot" value="group" /> 
									{tr}Accumulated Bar Plot:{/tr}
									<input type="radio" name="barPlot" value="acc" checked="checked" />
									<br />
									{tr}Background color:{/tr} 
									<select name="bgcolor">
										{foreach item=color from=$bgcolors}
											<option value="{$color|escape}"{if $defaultBgcolor eq $color} selected="selected"{/if}>{tr}{$color}{/tr}</option>
										{/foreach}
									</select> 
									{tr}Legend background color:{/tr} 
									<select name="legendBgcolor">
										{foreach item=color from=$bgcolors}
											<option value="{$color|escape}"{if $defaultLegendBgcolor eq $color} selected="selected"{/if}>{tr}{$color}{/tr}</option>
										{/foreach}
									</select>
									<br />
									{tr}Save graphs to image gallery:{/tr} 
									<select name="galleryId">
										<option value="" selected="selected" />
										{foreach item=gallery from=$galleries}
											<option value="{$gallery.galleryId|escape}">{$gallery.name}</option>
										{/foreach}
									</select>
								{/if}
							</td>
						</tr>
					{/if}

				</table>
				</fieldset>

				<input type="hidden" name="max" value="{$maxRecords}" />
				<span class="input_submit_container">
					<input type="submit" name="list" value="{tr}Report{/tr}" /></td>
				</span>
				{if $tiki_p_admin eq 'y'}
					<span class="input_submit_container">
						<input type="submit" name="export" value="{tr}Export{/tr}" />
					</span>
				{/if}

			</form>
		{/if}

		{if isset($actionlogs)}
		{if !empty($actionlogs)}
			{button href="#Statistics" _auto_args="*" _text="{tr}See Statistics{/tr}"}
		{/if}

		<h2 id="List">{tr}List{/tr}
			{if $selectedUsers}
				&nbsp;&mdash;&nbsp;
				{tr}User:{/tr}
				{foreach key=ix item=auser from=$users}
					{if $selectedUsers[$ix] eq 'y'} {$auser|escape}{/if}
				{/foreach}
			{/if}

			{if $selectedGroups}
				&nbsp;&mdash;&nbsp;
				{tr}Group:{/tr}
				{foreach key=ix item=group from=$groups}
					{if $selectedGroups[$group] eq 'y'} {$group|escape}{/if}
				{/foreach}
			{/if}

			{if $reportCategory}
				&nbsp;&mdash;&nbsp;{tr}Category:{/tr} {$reportCateg}
			{/if}
		</h2>
		
			{if $maxRecords gt 0}
				{if $cant gt $maxRecords}
					{self_link max=-1}{tr}All{/tr}{/self_link}
				{/if}
			{else}
					{self_link max=$prefs.maxRecords}{tr}Pagination{/tr}{/self_link}
			{/if}
		{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
			{tr}Records:{/tr} {$cant}
			<table class="normal">
				<tr>
					<th>
						<a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=user_{if $sort_mode eq 'user_desc'}asc{else}desc{/if}{$url}">{tr}User{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=lastModif_{if $sort_mode eq 'lastModif_desc'}asc{else}desc{/if}{$url}">{tr}Date{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=action_{if $sort_mode eq 'action_desc'}asc{else}desc{/if}{$url}">{tr}Action{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=objectType_{if $sort_mode eq 'objectType_desc'}asc{else}desc{/if}{$url}">{tr}Type{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=object_{if $sort_mode eq 'object_desc'}asc{else}desc{/if}{$url}">{tr}Object{/tr}</a>
					</th>
					{if !$reportCateg and $showCateg eq 'y'}
						<th>
							<a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=categName_{if $sort_mode eq 'categName_desc'}asc{else}desc{/if}{$url}">{tr}Category{/tr}</a>
						</th>
					{/if}
					<th>
						<a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=add_{if $sort_mode eq 'add_desc'}asc{else}desc{/if}{$url}">+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</a>
					</th>
					<th>
						<a href="tiki-admin_actionlog.php?startDate={$startDate}&amp;endDate={$endDate}&amp;sort_mode=del_{if $sort_mode eq 'del_desc'}asc{else}desc{/if}{$url}">-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</a>
					</th>
					{if $prefs.feature_contribution eq 'y'}
						<th>{tr}contribution{/tr}</th>
					{/if}
					{if $prefs.feature_contributor_wiki eq 'y'}
						<th>{tr}contributor{/tr}</th>
					{/if}
					{if $tiki_p_admin eq 'y' and ($prefs.feature_contribution eq 'y' or $prefs.feature_categories eq 'y')}
						<th>{tr}Action{/tr}</th>
					{/if}
				</tr>
				
				{cycle values="even,odd" print=false}
				{foreach from=$actionlogs item=actionlog}
					<tr class="{cycle}">
						<td class="username">
							{if $actionlog.user}{$actionlog.user}{else}{tr}Anonymous{/tr}{/if}
						</td>
						<td class="date">
							{$actionlog.lastModif|tiki_short_datetime}
						</td>
						<td class="text">
							{tr}{$actionlog.action}{/tr}
							{if $actionlog.action eq 'Categorized' || $actionlog.action eq 'Uncategorized'}/{$actionlog.comment|replace:"categId=":""}{/if}
						</td>
						<td class="text">
							{tr}{$actionlog.objectType}{/tr}
						</td>
						<td class="text">
							{if $actionlog.link}
								<a href="{$actionlog.link}" target="_blank" title="{tr}View{/tr}">{$actionlog.object|escape}</a>
							{else}
								{$actionlog.object|escape}
							{/if}
						</td>
						{if !$reportCateg and $showCateg eq 'y'}
							<td>
								{assign var=ic value=$actionlog.categId}{$categNames[$ic]|escape}
							</td>
						{/if}
						<td class="{if $actionlog.add} diffadded{/if}">
							{if $actionlog.add or $actionlog.add eq '0'}{$actionlog.add}{else}&nbsp;{/if}
						</td>
						<td class="{if $actionlog.del} diffdeleted{/if}">
							{if $actionlog.del or $actionlog.del eq '0'}{$actionlog.del}{else}&nbsp;{/if}
						</td>
						{if $prefs.feature_contribution eq 'y'}
							<td>
								{foreach name=contribution from=$actionlog.contributions item=contribution}
									{if !$smarty.foreach.contribution.first}, {/if}
									{$contribution.name}
								{/foreach}
							</td>
							{if $prefs.feature_contributor_wiki eq 'y'}
								<td>
									{foreach name=contributor from=$actionlog.contributors item=contributor}
										{if !$smarty.foreach.contributor.first}, {/if}
										{$contributor.login}
									{/foreach}
								</td>
							{/if}
						{/if}
						{if $tiki_p_admin eq 'y' and ($prefs.feature_contribution eq 'y' or $prefs.feature_categories eq 'y')}
							<td class="action">
								{if $actionlog.actionId}
									<a class="link" href="tiki-admin_actionlog.php?actionId={$actionlog.actionId}&amp;startDate={$startDate}&amp;endDate={$endDate}#action" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
									{self_link _class='link' remove='y' actionId=$actionlog.actionId _icon='cross' _title="{tr}Remove{/tr}"}{/self_link}
								{/if}
							</td>
						{/if}
					</tr>
				{/foreach}
			</table>
			{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
		{/if}

		{if $action}
			<a name="action">
			<h2>{tr}Edit Action{/tr}</h2>
			<form method="post" action="tiki-admin_actionlog.php">
				<input type="hidden" name="actionId" value="{$action.actionId}" />
				<input type="hidden" name="list" value="y" />
				{if $selectedUsers}<input type="hidden" name="selectedUsers" value="{$selectedUsers}" />{/if}
				{if $selectedGroups}<input type="hidden" name="selectedGroups" value="{$selectedGroups}" />{/if}
				{if $startDate}<input type="hidden" name="startDate" value="{$startDate}" />{/if}
				{if $endDate}<input type="hidden" name="endDate" value="{$endDate}" />{/if}
				{$action.action} / {$action.objectType} / {$action.object} 
				<table class="normal">
					{if $prefs.feature_contribution eq 'y'}
						{include file='contribution.tpl' section=$action.objectType}
					{/if}
					{if $prefs.feature_categories eq 'y'}
						{include file='categorize.tpl'}
					{/if}
					<tr>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="saveAction" value="{tr}Save Action{/tr}" />
						</td>
					</tr>
				</table>
			</form>
		{/if}

		{if isset($userActions)}
		<h2 id="Statistics">{tr}Statistics{/tr}
			{if $selectedUsers}
				&nbsp;&mdash;&nbsp;
				{tr}User:{/tr}
				{foreach key=ix item=auser from=$users}
					{if $selectedUsers[$ix] eq 'y'} 
						{$auser|escape}
					{/if}
				{/foreach}
			{/if}
			{if $selectedGroups}
				&nbsp;&mdash;&nbsp;
				{tr}Group:{/tr}
				{foreach key=ix item=group from=$groups}
					{if $selectedGroups[$group] eq 'y'} 
						{$group|escape}
					{/if}
				{/foreach}
			{/if}
			{if $reportCategory}
				&nbsp;&mdash;&nbsp;
				{tr}Category:{/tr} 
				{$reportCateg}
			{/if}
		</h2>
		<i>{tr}Volumes are equally distributed on each contributors/author{/tr}</i>

 		{if $showLogin eq 'y' and $logTimes|@count ne 0}
			<table class="normal">
				<caption>{tr}Log in{/tr}</caption>
				<tr>
					{if $selectedUsers|@count gt 0}<th>{tr}User{/tr}</th>{/if}
					<th>{tr}connection time{/tr}</th>
					<th>{tr}connection seconds{/tr}</th>
					<th>{tr}Log in{/tr}</th>
				</tr>
				{foreach key=auser item=time from=$logTimes}
					<tr class="{cycle}">
						{if $selectedUsers|@count gt 0}
							<td>{$auser}</td>
						{/if}
						<td>
							{$time.days} {tr}days{/tr} {$time.hours} {tr}hours{/tr} {$time.mins} {tr}mns{/tr}
						</td>
						<td>{$time.time}</td>
						<td>{$time.nbLogins}</td>
					</tr>
				{/foreach}
			</table>
		{/if}

		{if $showCateg eq 'y' and $volCateg|@count ne 0 and $tiki_p_admin eq 'y'}
			<table class="normal">
				<caption>{tr}Volumn per category{/tr}</caption>
				<tr>
					<th>{tr}Category{/tr}</th>
					{foreach item=type from=$typeVol}
						<th>{$type} (+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th>
						<th>{$type} (-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th>
						<th>{$type} ({if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th>
					{/foreach}
				</tr>
				{foreach key=categId item=vol from=$volCateg}
					<tr class="{cycle}">
						<td>{$vol.category}</td>
						{foreach item=type from=$typeVol} 
							<td class="{if $vol[$type].add} diffadded{/if}">{if $vol[$type].add}{$vol[$type].add}{else}0{/if}</td>
							<td class="{if $vol[$type].del} diffdeleted{/if}">{if $vol[$type].del}{$vol[$type].del}{else}0{/if}</td>
							<td class="{if $vol[$type].dif > 0} diffadded{elseif $vol[$type].dif < 0} diffdeleted{/if}">{if $vol[$type].dif}{$vol[$type].dif}{else}0{/if}</td>
						{/foreach}
					</tr>
				{/foreach}
			</table>
		{/if}

		{if $showCateg eq 'y' and $volUserCateg|@count ne 0}
			<table class="normal">
				<caption>{tr}Volumn per category and per user{/tr}</caption>
				<tr>
					<th>{tr}Category{/tr}</th>
					<th>{tr}User{/tr}</th>
					{foreach item=type from=$typeVol}
						<th>{$type} (+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th>
						<th>{$type} (-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th>
						<th>{$type} ({if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if})</th>
					{/foreach}
				</tr>
				{foreach key=categId item=vol from=$volUserCateg}
					<tr class="{cycle}">
						<td>{$vol.category}</td>
						<td>{$vol.user}</td>
						{foreach item=type from=$typeVol} 
							<td class="{if $vol[$type].add} diffadded{/if}">{if $vol[$type].add}{$vol[$type].add}{else}0{/if}</td>
							<td class="{if $vol[$type].del} diffdeleted{/if}">{if $vol[$type].del}{$vol[$type].del}{else}0{/if}</td>
							<td class="{if $vol[$type].dif > 0} diffadded{elseif $vol[$type].dif < 0} diffdeleted{/if}">{if $vol[$type].dif}{$vol[$type].dif}{else}0{/if}</td>
						{/foreach}
					</tr>
				{/foreach}
			</table>
		{/if}

		{if $userActions|@count ne 0}
			<table class="normal">
				<caption>{tr}Number of actions per user{/tr}</caption>
				<tr>
					<th>{tr}User{/tr}</th>
					{foreach key=title item=nb from=$userActions.0}
						{if $title ne 'user'}<th>{$title|replace:"/":" "}</th>{/if}
					{/foreach}
				</tr>
				{cycle values="even,odd" print=false}
				{foreach item=stat from=$userActions name=userActions}
					<tr class="{cycle}">
						<td class="username">{$stat.user}</td>
						{foreach key=a item=nb from=$stat}
							{if $a ne 'user'}<td class="integer">{$nb}</td>{/if}
						{/foreach}
					</tr>
				{/foreach}
			</table>
			{tr}Total number of users:{/tr} {$smarty.foreach.userActions.total}
		{/if}

		{if $objectActions|@count ne 0}
			<table class="normal">
				<caption>{tr}Number of actions per object{/tr}</caption>
				<tr>
					<th>{tr}Object{/tr}</th>
					{foreach key=title item=nb from=$objectActions[0]}
						{if $title ne 'object' and $title ne 'link'}<th>{$title|replace:"/":" "}</th>{/if}
					{/foreach}
				</tr>
				{cycle values="even,odd" print=false}
				{foreach item=stat from=$objectActions name=objectActions}
					<tr class="{cycle}">
						<td class="text">
							{if $stat.link}<a href="{$stat.link}" target="_blank" title="{tr}View{/tr}">{$stat.object|escape}</a>{else}{$stat.object|escape}{/if}
						</td>
						{foreach key=a item=nb from=$stat}
							{if $a ne 'object' and $a ne 'link'}<td class="integer">{$nb}</td>{/if}
						{/foreach}
					</tr>
				{/foreach}
			</table>
			{tr}Total number of objects:{/tr} {$smarty.foreach.objectActions.total}
		{/if}

		{if $showCateg eq 'y' and $tiki_p_admin eq 'y'}
			<table class="normal">
				<caption>{tr}Number of actions per category{/tr}</caption>
				<tr>
					<th>{tr}Category{/tr}</th>
					{foreach key=title item=nb from=$statCateg[0]}
						{if $title ne 'category'}<th>{$title|replace:"/":" "}</th>{/if}
					{/foreach}
				</tr>
				{foreach key=categId item=stat from=$statCateg}
					<tr class="{cycle}">
						<td class="text">{$stat.category}</td>
						{foreach key=a item=nb from=$statCateg[$categId]}
							{if $a ne 'category'}<td class="integer">{$nb}</td>{/if}
						{/foreach}
						<!-- {cycle} -->
					</tr>
				{/foreach}
			</table>
		{/if}

		{if $showCateg eq 'y' && $statUserCateg|@count ne 0}
			<table class="normal">
				<caption>{tr}Number of actions per category and per user{/tr}</caption>
				<tr>
					<th>{tr}Category{/tr}</th>
					<th>{tr}User{/tr}</th>
					{foreach key=title item=nb from=$userActions[0]}
						{if $title ne 'user'}<th>{$title|replace:"/":" "}</th>{/if}
					{/foreach}
				</tr>
				{foreach key=categUser item=stat from=$statUserCateg}
					<tr class="{cycle}">
						<td class="text">{$stat.category}</td>
						<td class="username">{$stat.user}</td>
						{foreach key=a item=nb from=$stat}
							{if $a ne 'category' and $a ne 'user'}
								<td class="integer">{$nb}</td>
							{/if}
						{/foreach}
					</tr>
				{/foreach}
			</table>
		{/if}

		{if $prefs.feature_contribution eq 'y' && isset($groupContributions) && $groupContributions|@count >= 1}
			<table>
				<caption>
					{if $selectedUsers}
						{tr}Volumn per the users'group and per contribution{/tr}
					{else}
						{tr}Volumn per group and per contribution{/tr}
					{/if}
				</caption>
				<tr>
					<th>{tr}Group{/tr}</th>
					<th>{tr}Contribution{/tr}</th>
					<th>+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</th>
					<th>-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</th>
				</tr>
				{foreach from=$groupContributions key=group item=contributions}
					{foreach from=$contributions key=contribution item=stat}
						<tr class="{cycle}">
							<td class="text">{$group}</td>
							<td class="text">{$contribution}</td>
							<td class="integer">{$stat.add}</td>
							<td class="integer">{$stat.del}</td>
						</tr>
					{/foreach}
				{/foreach}
			</table>
		{/if}

		{if $prefs.feature_contribution eq 'y' && isset($userContributions) && $userContributions|@count >= 1}
			<table>
				<caption>{tr}Volumn per user and per contribution{/tr}</caption>
				<tr>
					<th>{tr}User{/tr}</th>
					<th>{tr}Contribution{/tr}</th>
					<th>+{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</th>
					<th>-{if $unit eq 'kb'}{tr}kb{/tr}{else}{tr}bytes{/tr}{/if}</th>
				</tr>
				{foreach from=$userContributions key=user item=contributions}
					{foreach from=$contributions key=contribution item=stat}
						<tr class="{cycle}">
							<td class="username">{$user}</td>
							<td class="text">{$stat.name}</td>
							<td class="integer">{$stat.stat.add}</td>
							<td class="integer">{$stat.stat.del}</td>
						</tr>
					{/foreach}
				{/foreach}
			</table>
		{/if}

		{if $prefs.feature_contribution eq 'y' && isset($contributionStat)}
			<table>
				<caption>{if $selectedUsers}{tr}Volumn per users'contribution and time{/tr}{else}{tr}Volumn per contribution and time{/tr}{/if}</caption>
				<tr>
					<th>{tr}Contribution{/tr}</th>
					<th colspan="{$contributionNbCols}">{if $contribTime eq 'd'}{tr}Days{/tr}{else}{tr}Weeks{/tr}{/if}</th>
				</tr>
				<tr>
					<th></th>
					{section name=foo start=0 loop=$contributionNbCols}
						<th>{$smarty.section.foo.index+1}</th>
					{/section}
				</tr>
				{foreach from=$contributionStat key=contributionId item=contribution}
					<tr class="{cycle}">
						<td>{$contribution.name}</td>
						{foreach from=$contribution.stat item=stat}
							<td>
								{if !empty($stat.add)}<span class="diffadded">{$stat.add}</span>{/if}
							<br />
								{if !empty($stat.del)}<span class="diffdeleted">{$stat.del}</span>{/if}
								<br />
								{if !empty($stat.del) || !empty($stat.add)}{math equation=x-y x=$stat.add y=$stat.del}{/if}
								<br />
							</td>
						{/foreach}
					</tr>
				{/foreach}
			</table>
		{/if}
		{/if}

{/tab}


{* -------------------------------------------------- tab with setting --- *}
{tab name="{tr}Settings{/tr}"}
	<a name="Setting" ></a>
	<h2>{tr}Setting{/tr}</h2>
		{remarksbox type="tip" title="{tr}How{/tr}"}
		{tr}You need to check out the recorded box for each action type we may be interested to have some report later. To see a report of some action types, select the reported checkboxes of these action types, goto the Report tab and select additional filters. The report will only contains the actions that occured since the action type has been set to recorded.{/tr} {tr}Wiki page actions except viewed will always be recorded but can be not reported.{/tr}
		{/remarksbox}
	<form method="post" action="tiki-admin_actionlog.php">
		{if !empty($sort_mode)}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
		<fieldset>
		<legend>{tr}Filter{/tr}</legend>
		<label for="action_log_type">{tr}Type{/tr}</label>
		<select id="action_log_type" name="action_log_type">
				<option value="" {if empty($action_log_type)} selected="selected" {/if}>{tr}All{/tr}</option>
			{foreach from=$action_log_types item=type}
				<option value="{$type}" {if !empty($action_log_type) && $type eq $action_log_type} selected="selected" {/if}>{$type}</option>
			{/foreach}
		</select>
		<label for="action_log_action">{tr}Action{/tr}</label>
		<select id="action_log_action" name="action_log_action">
				<option value="" {if empty($action_log_action)} selected="selected" {/if}>{tr}All{/tr}</option>
				<option value="\%" {if !empty($action_log_action) && $action_log_action eq '\%'} selected="selected" {/if}>*</option>
			{foreach from=$action_log_actions item=action}
				<option value="{$action}" {if $type eq $action_log_action} selected="selected" {/if}>{$action}</option>
			{/foreach}
		</select>
			<span class="input_submit_container">
				<input type="submit" name="search" value="{tr}Search{/tr}" />
			</span>
		</fieldset>
		<br />
		<span class="input_submit_container" style="float: right">
			<input type="submit" name="save" value="{tr}Set{/tr}" />
		</span>
				<br class="clearfix" />
		<table class="formcolor">
			<tr>
				{if $tiki_p_admin eq 'y'}
					<th>{tr}Recorded{/tr}</th>
				{/if}
				<th>{tr}Reported{/tr}</th>
				<th>{tr}Action{/tr}</th>
				<th>{tr}Type{/tr}</th>
			</tr>
			{cycle values="even,odd" print=false}
			{foreach from=$action_log_conf_selected item=actionlog}
				<tr class="{cycle}">
					{if $tiki_p_admin eq 'y'}
						<td class="checkbox">
							<input type="checkbox" name="{$actionlog.code}" {if $actionlog.status eq 'y' or $actionlog.status eq 'v'}checked="checked"{/if} />
						</td>
					{/if}
					{if $tiki_p_admin eq 'y' or $actionlog.status eq 'y' or $actionlog.status eq 'v'}
						<td class="checkbox">
							<input type="checkbox" name="v_{$actionlog.code}" {if $actionlog.status eq 'v'}checked="checked"{/if} />
						</td>
						<td class="text">{tr}{$actionlog.action}{/tr}</td>
						<td class="text">{tr}{$actionlog.objectType}{/tr}</td>
					{/if}
				</tr>
			{/foreach}
			<tr>
				<td colspan="4" class="input_submit_container">
					<input type="submit" name="save" value="{tr}Set{/tr}" />
				</td>
			</tr>
		</table>
	</form>
	{/tab}
{/tabset}
