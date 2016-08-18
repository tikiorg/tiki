{* $Id$ *}

{title help="Contribution"}{tr}Admin Contributions{/tr}{/title}

{if $contribution}
	<h2>{tr}Edit the contribution:{/tr} {$contribution.name|escape}</h2>
	<form enctype="multipart/form-data" action="tiki-admin_contribution.php" method="post" class="form-horizontal" role="form">
		<input type="hidden" name="contributionId" value="{$contribution.contributionId}">
		<div class="form-group">
			<label class="col-sm-3 control-label" for="name">{tr}Name{/tr}</label>
			<div class="col-sm-9">
					<input type="text" name="name" class="form-control" id="name" {if $contribution.name} value="{$contribution.name|escape}"{/if}>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="description">{tr}Description{/tr}</label>
			<div class="col-sm-9">
				<input type="text" name="description" id="description" class="form-control" maxlength="250"{if $contribution.description} value="{$contribution.description|escape}"{/if}>
			</div>
		</div>
		<div class="form-group text-center">
			<input type="submit" class="btn btn-default btn-sm" name="replace" value="{tr}Save{/tr}">
		</div>
	</form>
{/if}

<h2>{tr}Settings{/tr}</h2>
<form action="tiki-admin_contribution.php?page=features" method="post" class="form-horizontal" role="form">

	<div class="form-group">
		<div class="checkbox">
			<label class="col-sm-11 col-sm-offset-1" for=feature_contribution_mandatory">
				<input type="checkbox" name="feature_contribution_mandatory" id="feature_contribution_mandatory" {if $prefs.feature_contribution_mandatory eq 'y'}checked="checked"{/if}>
				{tr}Contributions are mandatory in wiki pages{/tr}
			</label>
		</div>
	</div>
	<div class="form-group">
		<div class="checkbox">
			<label class="col-sm-11 col-sm-offset-1" for="feature_contribution_mandatory_forum">
				<input type="checkbox" name="feature_contribution_mandatory_forum" id="feature_contribution_mandatory_forum" {if $prefs.feature_contribution_mandatory_forum eq 'y'}checked="checked"{/if}>
				{tr}Contributions are mandatory in forums{/tr}
			</label>
		</div>
	</div>
	<div class="form-group">
		<div class="checkbox">
			<label class="col-sm-11 col-sm-offset-1" for="feature_contribution_mandatory_comment">
				<input type="checkbox" name="feature_contribution_mandatory_comment" id="feature_contribution_mandatory_comment" {if $prefs.feature_contribution_mandatory_comment eq 'y'}checked="checked"{/if}>
				{tr}Contributions are mandatory in comments{/tr}
			</label>
		</div>
	</div>
	<div class="form-group">
		<div class="checkbox">
			<label class="col-sm-11 col-sm-offset-1" for="feature_contribution_mandatory_blog">
				<input type="checkbox" name="feature_contribution_mandatory_blog" id="feature_contribution_mandatory_blog" {if $prefs.feature_contribution_mandatory_blog eq 'y'}checked="checked"{/if}>
				{tr}Contributions are mandatory in blogs{/tr}
			</label>
		</div>
	</div>
	<div class="form-group">
		<div class="checkbox">
			<label class="col-sm-11 col-sm-offset-1" for="feature_contribution_display_in_comment">
				<input type="checkbox" name="feature_contribution_display_in_comment" name="feature_contribution_display_in_comment" {if $prefs.feature_contribution_display_in_comment eq 'y'}checked="checked"{/if}>
				{tr}Contributions are displayed in the comment/post{/tr}
			</label>
		</div>
	</div>
	<div class="form-group">
		<div class="checkbox">
			<label class="col-sm-11 col-sm-offset-1" for="feature_contributor_wiki">
			<input type="checkbox" name="feature_contributor_wiki" name="feature_contributor_wiki" {if $prefs.feature_contributor_wiki eq 'y'}checked="checked"{/if}>
			{tr}Contributors{/tr}
			</label>
		</div>
	</div>
	<div class="form-group text-center">
		<input type="submit" class="btn btn-default btn-sm" name="setting" value="{tr}Save{/tr}">
	</div>
</form>


<h2>{tr}Create a new contribution{/tr}</h2>

<form enctype="multipart/form-data" action="tiki-admin_contribution.php" method="post" class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-3 control-label" for="new_contribution_name">{tr}Name{/tr}</label>
		<div class="col-sm-9">
			<input type="text" name="new_contribution_name" id="new_contribution_name" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="new_contribution_name">{tr}Description{/tr}</label>
		<div class="col-sm-9">
			<input type="text" name="description" class="form-control" maxlength="250">
		</div>
	</div>
	<div class="form-group text-center">
		<input type="submit" class="btn btn-default btn-sm" name="add" value="{tr}Add{/tr}">
	</div>
</form>
<h2>{tr}List of contributions{/tr}</h2>
{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}
<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
	<table class="table table-striped table-hover">
		<tr>
			<th>{tr}Name{/tr}</th>
			<th>{tr}Description{/tr}</th>
			<th></th>
		</tr>

		{section name=ix loop=$contributions}
			<tr>
				<td class="text">{$contributions[ix].name|escape}</td>
				<td class="text">{$contributions[ix].description|truncate|escape}</td>
				<td class="action">
					{capture name=contribution_actions}
						{strip}
							{$libeg}<a href="tiki-admin_contribution.php?contributionId={$contributions[ix].contributionId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-admin_contribution.php?remove={$contributions[ix].contributionId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.contribution_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.contribution_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=3}
		{/section}
	</table>
</div>
