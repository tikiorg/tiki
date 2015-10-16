{* $Id$ *}

<form class="admin form-horizontal" id="performance" name="performance" action="tiki-admin.php?page=rating" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>

	<fieldset>
		<legend>{tr}Global configuration{/tr}</legend>
		{preference name=rating_recalculation}
		<div class="rating_recalculation_childcontainer randomvote randomload">
			{preference name=rating_recalculation_odd}
			{preference name=rating_recalculation_count}
		</div>
		{preference name=ip_can_be_checked}
	</fieldset>

	<fieldset>
		<legend>{tr}Wiki{/tr}</legend>
		<table>
		<tr>
		<td style="width:49%">
		{preference name=feature_wiki_ratings}
		{preference name=wiki_simple_ratings}
		<div class="adminoptionboxchild" id="wiki_simple_ratings_childcontainer">
			{preference name=wiki_simple_ratings_options}
		</div>
				</td>
				<td style="width:2%"><td>
				<td style="width:49%"><div class="adminoptionboxchild" id="wiki_simple_ratings_perms_childcontainer">
				{tr}Permissions involved:{/tr}
				<ul>
					<li>{tr}wiki{/tr} > wiki_vote_ratings</li>
					<li>{tr}wiki{/tr} > wiki_view_ratings</li>
					<li>{tr}tiki{/tr} > ratings_view_results</li>
				</ul>
				</div>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{tr}Articles{/tr}</legend>
		<table>
			<tr>
				<td style="width:49%">
					{preference name=article_user_rating}
					<div class="adminoptionboxchild" id="article_user_rating_childcontainer">
						{preference name=article_user_rating_options}
					</div>
					<ul>
						<li>{tr}You also need to set:{/tr} "{tr}Admin Types{/tr} > <strong>{tr}Comment can rate article{/tr}</strong>"</li>
					</ul>
				</td>
				<td style="width:2%"></td>
				<td style="width:49%">
					<div class="adminoptionboxchild" id="articles_simple_ratings_perms_childcontainer">
						{tr}Permissions involved:{/tr}
						<ul>
							<li>{tr}articles{/tr} > rate_article</li>
							<li>{tr}tiki{/tr} > ratings_view_results</li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{tr}Comments{/tr}</legend>
		<table>
			<tr>
				<td style="width:49%">
					{preference name=comments_vote}
					{preference name=wiki_comments_simple_ratings}
					<div class="adminoptionboxchild" id="wiki_comments_simple_ratings_childcontainer">
						{preference name=wiki_comments_simple_ratings_options}
						{tr}This preference needs to be disabled:{/tr}{preference name=wiki_comments_form_displayed_default}
					</div>
				</td>
				<td style="width:2%"></td>
				<td style="width:49%">
					<div class="adminoptionboxchild" id="wiki_comments_simple_ratings_perms_childcontainer">
						{tr}Permissions involved:{/tr}
						<ul>
							<li>{tr}comments{/tr} > vote_comments</li>
							<li>{tr}wiki{/tr} > wiki_view_comments</li>
							<li>{tr}tiki{/tr} > ratings_view_results</li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{tr}Forums{/tr}</legend>
		<table>
			<tr>
				<td style="width:49%">{tr}You need to:{/tr}
					<ul>
						<li>{tr}Create or Edit a forum and enable:{/tr} "<strong>{tr}Posts can be rated{/tr}</strong>"</li>
						<li>{tr}While editing the forum, choose whether to show the "User information display > <strong>Topic Rating</strong>" by each user{/tr}</li>
						<li>{tr}Set the rating options at{/tr} "{tr}Control Panels{/tr}" > {tr}Ratings{/tr}" > "{tr}Comments{/tr}" > "{tr}Simple wiki comment ratings{/tr}" > "<strong>{tr}Wiki rating options:{/tr}</strong>" ({tr}see above{/tr})</li>
					</ul>
				</td>
				<td style="width:2%"></td>
				<td style="width:49%">
					<div class="adminoptionboxchild" id="forums_ratings_perms_childcontainer">
						{tr}Permissions involved:{/tr}
						<ul>
							<li>{tr}forums{/tr} > forum_vote</li>
							<li>{tr}tiki{/tr} > ratings_view_results</li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{tr}Trackers{/tr}</legend>
		<table style="width:100%">
			<tr>
				<td style="width:49%">{tr}You need to enable the settings:{/tr}
					<div class="adminoptionboxchild" id="rating_trackers_settings_childcontainer">{tr}Tracker Field:{/tr}
						{preference name=trackerfield_rating}
					</div>
				</td>
				<td style="width:2%"></td>
				<td style="width:49%">
					<div class="adminoptionboxchild" id="trackers_ratings_perms_childcontainer">
						{tr}Permissions involved:{/tr}
						<ul>
							<li>{tr}trackers{/tr} > tracker_vote_ratings</li>
							<li>{tr}trackers{/tr} > tracker_revote_ratings</li>
							<li>{tr}trackers{/tr} > tracker_view_ratings</li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{tr}User Interface{/tr}</legend>
		{preference name=rating_results_detailed}
		<div class="adminoptionboxchild" id="rating_results_detailed_childcontainer">
			{preference name=rating_results_detailed_percent}
		</div>
		{preference name=rating_smileys}
		{*{preference name=rating_options_reversed}*}
	</fieldset>

	<fieldset>
		<legend>{tr}Advanced{/tr}</legend>
		{preference name=rating_advanced}
	</fieldset>

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
</form>

<div id="rating_advanced_childcontainer">
	{foreach from=$configurations item=config}
		<form class="config" method="post" action="">
			<input type="hidden" name="ticket" value="{$ticket|escape}">
			<fieldset>
				<legend>{$config.name|escape} (ID: {$config.ratingConfigId|escape}, Search Field: <em>adv_rating_{$config.ratingConfigId|escape}</em>)</legend>
				<input type="hidden" name="config" value="{$config.ratingConfigId|escape}"/>
				<div>
					<label for="rating_name_{$config.ratingConfigId|escape}">{tr}Name{/tr}</label>
					<input type="text" name="name" value="{$config.name|escape}" id="rating_name_{$config.ratingConfigId|escape}"/>
				</div>
				<div>
					<label for="rating_expiry_{$config.ratingConfigId|escape}">{tr}Cache duration{/tr}</label>
					<input type="text" name="expiry" value="{$config.expiry|escape}" id="rating_expiry_{$config.ratingConfigId|escape}"/>
				</div>
				<div>
					<textarea name="formula" rows="5" style="width: 100%;">{$config.formula|escape}</textarea>
				</div>
				<div class="alert alert-danger"></div>
				<input type="submit" class="btn btn-default btn-sm" name="edit" value="{tr}Save{/tr}"/>
			</fieldset>
		</form>
	{/foreach}
	<form method="post" action="">
		<fieldset>
			<legend>{tr}Create New{/tr}</legend>
			<label for="rating_config_new">{tr}Name{/tr}</label>
			<input type="text" name="name" id="rating_config_new"/>
			<input type="submit" class="btn btn-default btn-sm" name="create" value="{tr}Create{/tr}"/>
		</fieldset>
	</form>
</div>
{jq}
	$('form.config').submit( function( e ) {
		return ! $(this).find('input[type=submit]').attr('disabled');
	} );
	$('form.config .error').hide();
	$('form.config textarea').change( function( e ) {
		var text = this;
		e.preventDefault();
		var submit = $(this).closest('form').find('input[type=submit]').attr('disabled', true);
		$.getJSON( window.location.href, { test: $(this).val() }, function( data ) {
			submit.attr( 'disabled', ! data.valid );
			if( data.valid ) {
				$(text).closest('form').find('.error').hide();
			} else {
				$(text).closest('form').find('.error').show().text( data.message );
			}
		} );
	} );
{/jq}
