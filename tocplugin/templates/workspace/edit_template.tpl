{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="navigation"}
	<div class="navbar">
		<a class="btn btn-default" href="{service controller=workspace action=list_templates}" title="{tr}List{/tr}">
			{icon name="list"} {tr}Workspace Templates{/tr}
		</a>
	</div>
{/block}

{block name="content"}
	<form class="workspace-ui form"" method="post" action="{service controller=workspace action=edit_template id=$id}" role="form">
		{remarksbox type=info title="{tr}Not enough options?{/tr}"}
			<p>{tr}This is the simple edition interface offering a subset of the available features. You can switch to the advanced mode and get more power.{/tr}</p>
			<a class="ajax alert-link" href="{service controller=workspace action=advanced_edit id=$id}">{tr}Advanced Mode{/tr}</a>
		{/remarksbox}
		<div class="form-group">
			<label for="name" class="control-label">
				{tr}Name{/tr}
			</label>
			<input type="text" name="name" value="{$name|escape}" class="form-control"/>
			{if $area}
				<span class="help-block">
					<label>
						<input type="checkbox" name="area" value="1" {if $area eq 'y'}checked="checked"{/if} />
						{tr}Bind area{/tr}
					</label>
				</span>
			{/if}
		</div>


		<h3>{tr}Groups{/tr}</h3>
		<ul class="groups">
			{foreach from=$groups item=group key=key}
				<li>
					<a href="#" class="key">{$key|escape}</a> (<span class="label">{$group.name|escape}</span>)
					<ul style="display: none">
						<li>
							<input class="name" type="text" name="groups~{$key|escape}~name" value="{$group.name|escape}"/>
							<input class="permissions" type="hidden" name="groups~{$key|escape}~permissions" value="{$group.permissions|implode:','}"/>
						</li>
						<li>
							<label>
								<input class="managingGroup" type="radio" name="managingGroup" value="{$key|escape}" {if $group.managing}checked="checked"{/if} />
								{tr}Is managing group{/tr}
							</label>
						</li>
						<li>
							<label>
								<input class="autojoin" type="checkbox" name="groups~{$key|escape}~autojoin" value="1" {if $group.autojoin}checked="checked"{/if} />
								{tr}Workspace creator joins this group{/tr}
							</label>
						</li>
					</ul>
				</li>
			{/foreach}
			<li>
				<a class="add-group" href="">{tr}Add group{/tr}</a>
			</li>
		</ul>

		<a class="permission-select" href="{service controller=workspace action=select_permissions}">{tr}Select Permissions{/tr}</a>

		<h3>{tr}Wiki Pages{/tr}</h3>
		<ul class="pages">
			{foreach from=$pages item=page key=key}
				<li>
					<a href="#" class="key">
						{if $page.name eq '{namespace}'}
							{tr}Home{/tr}
						{else}
							{$page.name|escape}
						{/if}
					</a>
					<ul style="display: none">
						<li>
							<input class="name" type="text" name="pages~{$key|escape}~name" value="{$page.name|escape}"/>
							<input class="namespace" type="hidden" name="pages~{$key|escape}~namespace" value="{$page.namespace|escape}" />
						</li>
						<li>
							<input class="content" type="hidden" name="pages~{$key|escape}~content" value="{$page.content|escape}"/>
							<a class="edit-content" href="{service controller=workspace action=edit_content}">{tr}Edit content template{/tr}</a>
						</li>
					</ul>
				</li>
			{/foreach}
			<li>
				<a class="add-page" href="">{tr}Add page{/tr}</a>
			</li>
		</ul>

		<div class="submit text-center">
			<input type="submit" class="btn btn-primary" value="{tr}Save{/tr}"/>
		</div>
	</form>
{/block}
