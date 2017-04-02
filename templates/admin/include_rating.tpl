{* $Id$ *}

<form class="admin form-horizontal" id="performance" name="performance" action="tiki-admin.php?page=rating" method="post">
	{include file='access/include_ticket.tpl'}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			{include file='admin/include_apply_top.tpl'}
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
		{preference name=feature_wiki_ratings}
		{preference name=wiki_simple_ratings}
		<div class="adminoptionboxchild" id="wiki_simple_ratings_childcontainer">
			{preference name=wiki_simple_ratings_options}
		</div>
	</fieldset>

	<fieldset>
		<legend>{tr}Articles{/tr}</legend>
		{preference name=article_user_rating}
		<div class="adminoptionboxchild" id="article_user_rating_childcontainer">
			{preference name=article_user_rating_options}
		</div>
	</fieldset>

	<fieldset>
		<legend>{tr}Comments{/tr}</legend>
		{preference name=comments_vote}
		{preference name=wiki_comments_simple_ratings}
		<div class="adminoptionboxchild" id="wiki_comments_simple_ratings_childcontainer">
			{preference name=wiki_comments_simple_ratings_options}
			{tr}This preference needs to be disabled:{/tr}{preference name=wiki_comments_form_displayed_default}
		</div>
	</fieldset>

	<fieldset>
		<legend>{tr}Forums{/tr}</legend>
		{remarksbox title="{tr}Enabling ratings for forums{/tr}"}
			{tr}You need to:{/tr}
			<ul>
				<li>{tr}Create or edit a forum and enable:{/tr} "<strong>{tr}Posts can be rated{/tr}</strong>"</li>
				<li>{tr}While editing the forum, choose whether to show the "User information display > <strong>Topic Rating</strong>" by each user{/tr}</li>
				<li>{tr}Set the rating options at{/tr} "{tr}Control Panels{/tr}" > {tr}Ratings{/tr}" > "{tr}Comments{/tr}" > "{tr}Simple wiki comment ratings{/tr}" > "<strong>{tr}Wiki rating options:{/tr}</strong>" ({tr}see above{/tr})</li>
			</ul>
		{tr}Permissions involved:{/tr} forum_vote ({tr}forums{/tr}), ratings_view_results ({tr}tiki{/tr})
		{/remarksbox}
	</fieldset>

	<fieldset>
		<legend>{tr}Trackers{/tr}</legend>
		{preference name=trackerfield_rating}
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
	{include file='admin/include_apply_bottom.tpl'}
</form>

<div id="rating_advanced_childcontainer">
	{foreach from=$configurations item=config}
		<form class="config" method="post" action="">
			{include file='access/include_ticket.tpl'}
			<fieldset>
				<legend>{$config.name|escape} <small>(ID: {$config.ratingConfigId|escape}, Search Field: <em>adv_rating_{$config.ratingConfigId|escape}</em>)</small></legend>
				<input type="hidden" name="config" value="{$config.ratingConfigId|escape}">
				<div class="form-group">
					<label class="control-label col-sm-4" for="rating_name_{$config.ratingConfigId|escape}">
						{tr}Name{/tr}
					</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="name" value="{$config.name|escape}" id="rating_name_{$config.ratingConfigId|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="rating_expiry_{$config.ratingConfigId|escape}">
						{tr}Cache duration{/tr}
					</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" class="form-control" name="expiry" value="{$config.expiry|escape}" id="rating_expiry_{$config.ratingConfigId|escape}">
							<span class="input-group-addon">{tr}seconds{/tr}</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="formula">
						{tr}Formula{/tr}
					</label>
					<div class="col-sm-8">
						<textarea name="formula" class="form-control" rows="5" style="width: 100%;">
							{$config.formula|escape}
						</textarea>
					</div>
				</div>
				<div class="form-group text-center">
					<div class="col-sm-12"><br>
						<input type="submit" class="btn btn-default btn-sm timeout" name="edit" value="{tr}Save{/tr}">
					</div>
				</div>
			</fieldset>
		</form><br>
	{/foreach}
	<form method="post" action="">
		{include file='access/include_ticket.tpl'}
		<fieldset>
			<legend>{tr}Create new{/tr}</legend>
			<label class="control-label col-sm-4" for="rating_config_new">
				{tr}Name{/tr}
			</label>
			<div class="col-sm-8">
				<div class="input-group">
					<input type="text" class="form-control" name="name" id="rating_config_new">
					<span class="input-group-btn">
			<input type="submit" class="btn btn-default timeout" name="create" value="{tr}Create{/tr}">
				</span>
				</div>
			</div>
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
