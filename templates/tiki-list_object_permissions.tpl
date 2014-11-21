{title}{tr}Object Permissions List{/tr}{/title}

<div class="t_navbar">
	{permission_link mode=button label="{tr}Manage permissions{/tr}"}
</div>

{if !empty($feedbacks)}
	{remarksbox type="note" title="{tr}Feedback{/tr}"}
		{foreach from=$feedbacks item=feedback name=feedback}
			{if !$smarty.foreach.feedback.first}<br>{/if}
			{$feedback|escape}
		{/foreach}
	{/remarksbox}
{/if}

<form method="post">
	<div class="clearfix">
	<div style="float:left;"><label for="filterGroup">{tr}Group Filter{/tr}</label></div>
	<div style="float:left;">
		<select multiple="multiple" id="filterGroup" name="filterGroup[]">
			<option value=""{if empty($filterGroup)}selected="selected"{/if}></option>
			{foreach from=$all_groups item=gr}
				<option value="{$gr|escape}" {if in_array($gr, $filterGroup)}selected="selected"{/if}>{$gr|escape}</option>
				{/foreach}
		</select>
	  </div>
	  <div style="float:left;"><input type="submit" class="btn btn-default btn-sm" name="filter" value="{tr}Filter{/tr}"></div>
	  </div>
</form>

{tabset name='tabs_list_object_permissions'}
	{foreach from=$res key=type item=content}
		{tab name="{tr}$type{/tr}"}

			<!-- ul>
				<li><a href="#tabs-1">{tr}global permissions{/tr}</a></li>
				<li><a href="#tabs-2">{tr}object permissions{/tr} ({$content.objects|@count})</a></li>
				<li><a href="#tabs-3">{tr}category permissions{/tr} ({$content.category|@count})</a></li>
			</ul-->

			{tabset}
				{capture assign=tablabel}{tr}Global Permissions{/tr} ({$content.default|@count}){/capture}
				{tab name=$tablabel}
					<div class="tabs-1">
					<form method="post">
					{foreach from=$filterGroup item=f}<input type="hidden" name="filterGroup[]" value="{$f|escape}">{/foreach}
                    <div class="table-responsive">
					<table class="table normal">
					<tr>
						<th class="checkbox-cell">{select_all checkbox_names='groupPerm[]'}</th>
						<th>{tr}Group{/tr}</th>
						<th>{tr}Permission{/tr}</th>
					</tr>

					{foreach from=$content.default item=default}
						<tr>
							<td class="checkbox-cell"><input type="checkbox" name="groupPerm[]" value='{$default|json_encode|escape}'></td>
							<td class="text">{$default.group|escape}</td>
							<td class="text">{$default.perm|escape}</td>
						</tr>
					{/foreach}
					</table>
                    </div>
					{if count($content.default)}
						<div style="float:left">{tr}Perform action with checked:{/tr}</div>
						<div style="float:left">
						{icon _id='cross' _tag='input_image' _confirm="{tr}Delete the selected permissions?{/tr}" name='delsel' alt="{tr}Delete the selected permissions{/tr}"}
							 <br>
							 <input type="text" name="toGroup"><input type="submit" class="btn btn-default btn-sm" name="dupsel" value="{tr}Duplicate the selected permissions on this group{/tr}">
	  					</div>
					{/if}
					</form>
					</div>
				{/tab}
				{capture assign=tablabel}{tr}Object Permissions{/tr} ({$content.objects|@count}){/capture}
				{tab name=$tablabel}
					<div class="tabs-2">
					{remarksbox}{tr}If an object is not listed in this section nor in the Category Permissions section, then only the global permissions apply to it.{/tr}{/remarksbox}
					<form method="post">
					{foreach from=$filterGroup item=f}<input type="hidden" name="filterGroup[]" value="{$f|escape}">{/foreach}
                    <div class="table-responsive">
					<table class="table normal">
					<tr>
						<th class="checkbox-cell">{select_all checkbox_names='objectPerm[]'}</th>
						<th>{tr}Object{/tr}</th>
						<th>{tr}Group{/tr}</th>
						<th>{tr}Permission{/tr}</th>
						<th>{tr}Reason{/tr}</th>
					</tr>
					{foreach from=$content.objects item=object}
						{if !empty($object.special)}
							{foreach from=$object.special item=special}
								<tr>
									<td class="checkbox-cell"><input type="checkbox" name="objectPerm[]" value='{$special|json_encode|escape}'></td>
									<td class="text">{$special.objectName|escape}</td>
									<td class="text">{$special.group|escape}</td>
									<td class="text">{$special.perm|escape}</td>
									<td class="text">
										{if !empty($special.objectId)}
											{* I doubt this link worked in the past, permType was not specified *}
											{permission_link mode=link type=$special.objectType id=$special.objectId title=$special.objectName label=$special.reason}
										{else}
											{$special.reason|escape}
										{/if}
										{if !empty($special.detail)}({$special.detail|escape}){/if}
									</td>
								</tr>
							{/foreach}
						{/if}
					{/foreach}
					</table>
                    </div>
					{if count($content.objects)}
						<div style="float:left">{tr}Perform action with checked:{/tr}</div>
						<div style="float:left">
							 {icon _id='cross' _tag='input_image' _confirm="{tr}Delete the selected permissions?{/tr}" name='delsel' alt="{tr}Delete the selected permissions{/tr}" style='vertical-align: middle;'}
							 <br>
							 <input type="text" name="toGroup"><input type="submit" class="btn btn-default btn-sm" name="dupsel" value="{tr}Duplicate the selected permissions on this group{/tr}">
						</div>
					{/if}
					</form>
					</div>
				{/tab}
				{capture assign=tablabel}{tr}Category Permissions{/tr} ({$content.category|@count}){/capture}
				{tab name=$tablabel}
					<div class="tabs-3">
					{remarksbox}{tr}If an object is not listed in this section nor in the Object Permissions section, then only the global permissions apply to it.{/tr}{/remarksbox}
					<form method="post">
                    <div class="table-responsive">
					<table class="table normal">
					<tr>
						<th>{tr}Object{/tr}</th>
						<th>{tr}Group{/tr}</th>
						<th>{tr}Permission{/tr}</th>
						<th>{tr}Reason{/tr}</th>
					</tr>
					{foreach from=$content.category item=object}
						{if !empty($object.category)}
							{foreach from=$object.category item=special}
								<tr>
									<td class="text">{if isset($object.objectName)}{$object.objectName|escape}{else}{$object.objectId|escape}{/if}</td>
									<td class="text">{$special.group|escape}</td>
									<td class="text">{$special.perm|escape}</td>
									<td class="text">
										{if !empty($special.objectId)}
											{* I doubt this link worked in the past, permType was not specified *}
											{permission_link mode=icon type=$special.objectType id=$special.objectId title=$special.objectName}
											{tr}{$special.reason|escape}:{/tr} {$special.objectName|escape}
										{else}
											{$special.reason|escape}: {$special.objectName}
										{/if}
										{if !empty($special.detail)}({$special.detail|escape}){/if}
									</td>
								</tr>
							{/foreach}
						{/if}
					{/foreach}
					</table>
                    </div>
					</form>
					</div>
				{/tab}
			{/tabset}
		{/tab}
	{/foreach}
{/tabset}
