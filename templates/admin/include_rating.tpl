{* $Id$ *}

<form class="admin" id="performance" name="performance" action="tiki-admin.php?page=rating" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" class="btn btn-default btn-sm" value="{tr}Apply{/tr}" />
		<input type="reset" class="btn btn-warning" value="{tr}Reset{/tr}" />
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
		<td style="width:48%">
		{preference name=wiki_simple_ratings}
		<div class="adminoptionboxchild" id="wiki_simple_ratings_childcontainer">
			{preference name=wiki_simple_ratings_options}
		</div>
				</td>
				<td style="width:4%"><td>
				<td style="width:48%"><div class="adminoptionboxchild" id="wiki_simple_ratings_perms_childcontainer">
				{tr}Permissions involved:{/tr}
				<ul>
					<li>{tr}wiki{/tr} > tiki_p_wiki_vote_ratings</li>
					<li>{tr}tiki{/tr} > tiki_p_ratings_view_results</li>
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
		<td style="width:48%">
		{preference name=article_user_rating}
		<div class="adminoptionboxchild" id="article_user_rating_childcontainer">
			{preference name=article_user_rating_options}
		</div> 
				<ul>
					<li>{tr}You also need to set:{/tr} "{tr}Admin Types{/tr} > <strong>{tr}Comment can rate article{/tr}</strong>"</li>
				</ul>
				</td>
				<td style="width:4%"><td>
				<td style="width:48%"><div class="adminoptionboxchild" id="articles_simple_ratings_perms_childcontainer">
				{tr}Permissions involved:{/tr}
				<ul>
					<li>{tr}articles{/tr} > tiki_p_rate_article</li>
					<li>{tr}tiki{/tr} > tiki_p_ratings_view_results</li>
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
		<td style="width:48%">
		{preference name=wiki_comments_simple_ratings}
		<div class="adminoptionboxchild" id="wiki_comments_simple_ratings_childcontainer">
			{preference name=wiki_comments_simple_ratings_options}
		</div>
				</td>
				<td style="width:4%"><td>
				<td style="width:48%"><div class="adminoptionboxchild" id="wiki_comments_simple_ratings_perms_childcontainer">
				{tr}Permissions involved:{/tr}
				<ul>
					<li>{tr}comments{/tr} > tiki_p_vote_comments</li>
					<li>{tr}tiki{/tr} > tiki_p_ratings_view_results</li>
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
				<td style="width:48%">{tr}You need to:{/tr} 
				<ul>
					<li>{tr}Create or Edit a forum and enable:{/tr} "<strong>{tr}Posts can be rated{/tr}</strong>"</li>
					<li>{tr}Set the rating options at{/tr} "{tr}Admin Home{/tr}" > {tr}Ratings{/tr}" > "{tr}Comments{/tr}" > "{tr}Simple wiki comment ratings{/tr}" > "<strong>{tr}Wiki rating options:{/tr}</strong>" ({tr}see above{/tr})</li>
				</ul>
				</td>
				<td style="width:4%"><td>
				<td style="width:48%"><div class="adminoptionboxchild" id="forums_ratings_perms_childcontainer">
				{tr}Permissions involved:{/tr}
				<ul>
					<li>{tr}forums{/tr} > tiki_p_forum_vote</li>
					<li>{tr}tiki{/tr} > tiki_p_ratings_view_results</li>
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
				<td style="width:48%">{tr}You need to enable the settings:{/tr}
				<div class="adminoptionboxchild" id="rating_trackers_settings_childcontainer">{tr}Tracker Field:{/tr}
				{preference name=trackerfield_rating}</div>
				</td>
				<td style="width:4%"><td>
				<td style="width:48%"><div class="adminoptionboxchild" id="trackers_ratings_perms_childcontainer">
				{tr}Permissions involved:{/tr}
				<ul>
					<li>{tr}trackers{/tr} > tiki_p_tracker_vote_ratings</li>
					<li>{tr}trackers{/tr} > tiki_p_tracker_revote_ratings</li>
					<li>{tr}trackers{/tr} > tiki_p_tracker_view_ratings</li>
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
	</fieldset>

	<fieldset>
		<legend>{tr}Advanced{/tr}</legend>
		{preference name=rating_advanced}
	</fieldset>
	
	<div class="input_submit_container" style="margin-top: 5px; text-align: center">
		<input type="submit" class="btn btn-default btn-sm" value="{tr}Apply{/tr}" />
	</div>
</form>
<div id="rating_advanced_childcontainer">
	{foreach from=$configurations item=config}
		<form class="config" method="post" action="">
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
