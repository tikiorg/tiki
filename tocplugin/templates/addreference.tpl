{* $Id$ *}
{if $prefs.wikiplugin_addreference eq 'y' && $showBiblioSection eq '1'}


<script type="text/javascript">
var ajaxURL = '{$ajaxURL|escape}';
var dataMain = 'page='+encodeURIComponent('{$page|escape}');

var edit_references = '{$edit_references}';
var use_references = '{$use_references}';

</script>

{jq}
jQuery(document).ready(function(){

	jQuery('#e_submit').click(function(e){
		e.preventDefault();

		var dataString = dataMain+'&action=e_ref';
		dataString += '&editreference=editreference';
		dataString += '&ref_id='+encodeURIComponent(jQuery('#e_ref_id').val());
		dataString += '&ref_biblio_code='+encodeURIComponent(jQuery('#e_ref_biblio_code').val());
		dataString += '&ref_author='+encodeURIComponent(jQuery('#e_ref_author').val());
		dataString += '&ref_title='+encodeURIComponent(jQuery('#e_ref_title').val());
		dataString += '&ref_year='+encodeURIComponent(jQuery('#e_ref_year').val());
		dataString += '&ref_part='+encodeURIComponent(jQuery('#e_ref_part').val());
		dataString += '&ref_uri='+encodeURIComponent(jQuery('#e_ref_uri').val());
		dataString += '&ref_code='+encodeURIComponent(jQuery('#e_ref_code').val());
		dataString += '&ref_style='+encodeURIComponent(jQuery('#e_ref_style').val());
		dataString += '&ref_template='+encodeURIComponent(jQuery('#e_ref_template').val());
		dataString += '&ref_publisher='+encodeURIComponent(jQuery('#e_ref_publisher').val());
		dataString += '&ref_location='+encodeURIComponent(jQuery('#e_ref_location').val());

		jQuery.ajax({
			url: ajaxURL+'references.php',
			type: 'POST',
			data: dataString,
			dataType: 'json',
			beforeSend: function( xhr ) {
				jQuery('#e_status').html('{tr}Saving...{/tr}');
			},
			success: function( data ) {
				if('success'==data['result']){
					jQuery('#ref_list').find('li').css('font-weight','normal');
					var ref_id = jQuery('#e_ref_id').val();
					var ref_biblio_code = jQuery('#e_ref_biblio_code').val();
					var ref_author = jQuery('#e_ref_author').val();
					var ref_title = jQuery('#e_ref_title').val();
					var ref_year = jQuery('#e_ref_year').val();
					var ref_part = jQuery('#e_ref_part').val();
					var ref_uri = jQuery('#e_ref_uri').val();
					var ref_code = jQuery('#e_ref_code').val();
					var ref_style = jQuery('#e_ref_style').val();
					var ref_template = jQuery('#e_ref_template').val();
					var ref_publisher = jQuery('#e_ref_publisher').val();
					var ref_location = jQuery('#e_ref_location').val();

					jQuery('#ref_list').find('li#'+ref_id).remove();
					var htm = '<li id="'+ref_id+'" style="border-bottom: 1px dotted #161C17;font-weight:bold;">';
					htm += ref_biblio_code + '&nbsp;&nbsp;';
					htm += '<a class="edit_ref" onclick="edit_ref('+ref_id+',\''+ref_biblio_code+'\', \''+ref_author+'\', \''+ref_title+'\', \''+ref_year+'\', \''+ref_part+'\', \''+ref_uri+'\', \''+ref_code+'\', \''+ref_style+'\', \''+ref_template+'\', \''+ref_publisher+'\', \''+ref_location+'\')" href="javascript:;" title="Edit" alt="Edit">' + '<img width="16" height="16" class="icon" title="Edit" alt="Edit" src="img/icons/pencil.png"></a>';
					htm += '<a onclick="delete_ref('+ref_id+')" title="Delete"><img width="16" height="16" class="icon" title="Remove" alt="Remove" src="img/icons/cross.png"></a>';
					if(data['is_library'] < 1 && use_references == '1' && edit_references == '1'){
						htm += '<a class="add_lib_btn" onclick="add_lib('+ref_id+',\''+ref_biblio_code+'\', \''+ref_author+'\', \''+ref_title+'\', \''+ref_year+'\', \''+ref_part+'\', \''+ref_uri+'\', \''+ref_code+'\', \''+ref_style+'\', \''+ref_template+'\', \''+ref_publisher+'\', \''+ref_location+'\')" href="javascript:;" title="Add to library" alt="Add to library">' + '<img width="16" height="16" class="icon" title="Add to library" alt="Add to library" src="img/icons/world_add.png"></a>';
					}
					htm += '</li>';
					jQuery('#e_status').html(data['message']);
					jQuery('#ref_list').find('ul').append(htm);
				}else if('failure'==data['result']){
					jQuery('#e_status').html(data['message']);
				}
			}
		});
	});

	jQuery('#a_submit').click(function(e){
		e.preventDefault();

		var ref_biblio_code = jQuery('#e_ref_biblio_code').val();
		var ref_author = jQuery('#e_ref_author').val();
		var ref_title = jQuery('#e_ref_title').val();
		var ref_year = jQuery('#e_ref_year').val();
		var ref_part = jQuery('#e_ref_part').val();
		var ref_uri = jQuery('#e_ref_uri').val();
		var ref_code = jQuery('#e_ref_code').val();
		var ref_style = jQuery('#e_ref_style').val();
		var ref_template = jQuery('#e_ref_template').val();
		var ref_publisher = jQuery('#e_ref_publisher').val();
		var ref_location = jQuery('#e_ref_location').val();

		var dataString = dataMain+'&action=a_ref';
		dataString += '&addreference=addreference';
		dataString += '&ref_biblio_code='+encodeURIComponent(ref_biblio_code);
		dataString += '&ref_author='+encodeURIComponent(ref_author);
		dataString += '&ref_title='+encodeURIComponent(ref_title);
		dataString += '&ref_year='+encodeURIComponent(ref_year);
		dataString += '&ref_part='+encodeURIComponent(ref_part);
		dataString += '&ref_uri='+encodeURIComponent(ref_uri);
		dataString += '&ref_code='+encodeURIComponent(ref_code);
		dataString += '&ref_style='+encodeURIComponent(ref_style);
		dataString += '&ref_template='+encodeURIComponent(ref_template);
		dataString += '&ref_publisher='+encodeURIComponent(ref_publisher);
		dataString += '&ref_location='+encodeURIComponent(ref_location);

		jQuery.ajax({
			url: ajaxURL+'references.php',
			type: 'GET',
			data: dataString,
			dataType: 'json',
			beforeSend: function( xhr ) {
				jQuery('#a_status').html('Saving...');
			},
			success: function( data ) {
				if('success'==data['result']){
					jQuery('#e_ref_id').val('');
					jQuery('#e_ref_biblio_code').val('');
					jQuery('#e_ref_author').val('');
					jQuery('#e_ref_title').val('');
					jQuery('#e_ref_year').val('');
					jQuery('#e_ref_part').val('');
					jQuery('#e_ref_uri').val('');
					jQuery('#e_ref_code').val('');
					jQuery('#e_ref_style').val('');
					jQuery('#e_ref_template').val('');
					jQuery('#e_ref_publisher').val('');
					jQuery('#e_ref_location').val('');
					jQuery('#a_status').html('{tr}Bibliography saved.{/tr}');

					jQuery('#ref_list').show();

					var ref_id = data["id"];
					var htm = '<li id="'+ref_id+'" style="border-bottom: 1px dotted #161C17;">';
					htm += ref_biblio_code + '&nbsp;&nbsp;';
					htm += '<a class="edit_ref" onclick="edit_ref('+ref_id+',\''+ref_biblio_code+'\', \''+ref_author+'\', \''+ref_title+'\', \''+ref_year+'\', \''+ref_part+'\', \''+ref_uri+'\', \''+ref_code+'\', \''+ref_style+'\', \''+ref_template+'\', \''+ref_publisher+'\', \''+ref_location+'\')" href="javascript:;" title="Edit" alt="Edit">' + '<img width="16" height="16" class="icon" title="Edit" alt="Edit" src="img/icons/pencil.png"></a>';
					htm += '<a onclick="delete_ref('+ref_id+')" title="Delete"><img width="16" height="16" class="icon" title="Remove" alt="Remove" src="img/icons/cross.png"></a>';
					if(data['is_library'] < 1 && use_references == '1' && edit_references == '1'){
						htm += '<a class="add_lib_btn" onclick="add_lib('+ref_id+',\''+ref_biblio_code+'\', \''+ref_author+'\', \''+ref_title+'\', \''+ref_year+'\', \''+ref_part+'\', \''+ref_uri+'\', \''+ref_code+'\', \''+ref_style+'\', \''+ref_template+'\', \''+ref_publisher+'\', \''+ref_location+'\')" href="javascript:;" title="Add to library" alt="Add to library">' + '<img width="16" height="16" class="icon" title="Add to library" alt="Add to library" src="img/icons/world_add.png"></a>';
					}
					htm += '</li>';
					jQuery('#ref_list').find('ul').append(htm);
				}else{
					jQuery('#a_status').html(data['result']);
				}
				if('failure'==data['result'] && data["id"] == '-1'){
					jQuery('#a_status').html('This biblio code already exists.');
				}
			}
		});
	});

	jQuery('#e_cancel, #a_cancel').click(function(){
		jQuery('#ref_edit_block').hide();
		jQuery('#ref_list').find('li').css('font-weight','normal');
	});

	jQuery('a.edit_ref').on('click', function(){
		jQuery('#ref_list').find('li').css('font-weight','normal');
		jQuery(this).parent().css('font-weight','bold');
	});

	jQuery('#u_lib').click(function(e){
		e.preventDefault();

		var ref_id = jQuery('#lib_ref').val();
		var dataString = dataMain+'&action=u_lib';
		dataString += '&ref_id='+encodeURIComponent(ref_id);

		jQuery.ajax({
			url: ajaxURL+'references.php',
			type: 'GET',
			data: dataString,
			dataType: 'json',
			beforeSend: function( xhr ) {
				jQuery('#u_lib_status').html('{tr}Adding...{/tr}');
			},
			success: function( data ) {
				if('success'==data['result']){
					jQuery('#u_lib_status').html(data['message']);

					var ref_id = data['id'];
					var ref_biblio_code = escape(data['ref_biblio_code']);
					var ref_author = escape(data['ref_author']);
					var ref_title = escape(data['ref_title']);
					var ref_year = escape(data['ref_year']);
					var ref_part = escape(data['ref_part']);
					var ref_uri = escape(data['ref_uri']);
					var ref_code = escape(data['ref_code']);
					var ref_style = escape(data['ref_style']);
					var ref_template = escape(data['ref_template']);
					var ref_publisher = escape(data['ref_publisher']);
					var ref_location = escape(data['ref_location']);

					var htm = '<li id="'+ref_id+'" style="border-bottom: 1px dotted #161C17;">';
					htm += ref_biblio_code + '&nbsp;&nbsp;';
					htm += '<a class="edit_ref" onclick="edit_ref('+ref_id+',\''+ref_biblio_code+'\', \''+ref_author+'\', \''+ref_title+'\', \''+ref_year+'\', \''+ref_part+'\', \''+ref_uri+'\', \''+ref_code+'\', \''+ref_style+'\', \''+ref_template+'\', \''+ref_publisher+'\', \''+ref_location+'\')" href="javascript:;" title="Edit" alt="Edit">' + '<img width="16" height="16" class="icon" title="Edit" alt="Edit" src="img/icons/pencil.png"></a>';
					htm += '<a onclick="delete_ref('+ref_id+')" title="Delete"><img width="16" height="16" class="icon" title="Remove" alt="Remove" src="img/icons/cross.png"></a>';
					htm += '</li>';
					jQuery('#ref_list').find('ul').append(htm);
				}else if('failure'==data['result']){
					jQuery('#u_lib_status').html(data['message']);
				}else{
					jQuery('#u_lib_status').html(data['message']);
				}
			}
		});
	});
});
{/jq}

<script type="text/javascript">
function add_ref(){
	jQuery('#ref_list').find('li').css('font-weight','normal');
	jQuery('#ref_edit_block').show();
	jQuery('#a_btns').show();
	jQuery('#e_btns').hide();
	jQuery('#a_status').html('');

	jQuery('#e_ref_id').val('');
	jQuery('#e_ref_biblio_code').val('');
	jQuery('#e_ref_author').val('');
	jQuery('#e_ref_title').val('');
	jQuery('#e_ref_year').val('');
	jQuery('#e_ref_part').val('');
	jQuery('#e_ref_uri').val('');
	jQuery('#e_ref_code').val('');
	jQuery('#e_ref_style').val('');
	jQuery('#e_ref_template').val('');
	jQuery('#e_ref_publisher').val('');
	jQuery('#e_ref_location').val('');
}

function add_lib(ref_id, biblio_code, ref_author, ref_title, ref_year, ref_part, ref_uri, ref_code, ref_style, ref_template, ref_publisher, ref_location){
	jQuery('#ref_list').find('li').css('font-weight','normal');

	var c = confirm('Are you sure you want to add this reference to library?');
	if(!c){
		return false;
	}

	var dataString = dataMain+'&action=a_lib';
	dataString += '&addlibreference=addlibreference';
	dataString += '&ref_id='+encodeURIComponent(ref_id);
	dataString += '&ref_biblio_code='+encodeURIComponent(biblio_code);
	dataString += '&ref_author='+encodeURIComponent(ref_author);
	dataString += '&ref_title='+encodeURIComponent(ref_title);
	dataString += '&ref_year='+encodeURIComponent(ref_year);
	dataString += '&ref_part='+encodeURIComponent(ref_part);
	dataString += '&ref_uri='+encodeURIComponent(ref_uri);
	dataString += '&ref_code='+encodeURIComponent(ref_code);
	dataString += '&ref_style='+encodeURIComponent(ref_style);
	dataString += '&ref_template='+encodeURIComponent(ref_template);
	dataString += '&ref_publisher='+encodeURIComponent(ref_publisher);
	dataString += '&ref_location='+encodeURIComponent(ref_location);

	jQuery.ajax({
		url: ajaxURL+'references.php',
		type: 'GET',
		data: dataString,
		dataType: 'json',
		beforeSend: function( xhr ) {
			jQuery('#'+ref_id).css('background-color', 'yellow');
		},
		success: function( data ) {
			if('success'==data['result']){
				alert(data['message']);
				jQuery('#'+ref_id).find('a.add_lib_btn').remove();
				jQuery('#'+ref_id).css('background-color', '');
				jQuery('#lib_ref').append('<option value="'+data['id']+'">'+biblio_code+'</option>')
			}else if('failure'==data['result']){
				alert(data['message']);
				jQuery('#'+ref_id).css('background-color', '');
			}
		}
	});
}

function edit_ref(ref_id, biblio_code, ref_author, ref_title, ref_year, ref_part, ref_uri, ref_code, ref_style, ref_template, ref_publisher, ref_location){
	jQuery('#ref_list').find('li').css('font-weight','normal');
	jQuery('#ref_edit_block').show();
	jQuery('#e_btns').show();
	jQuery('#a_btns').hide();
	jQuery('#e_status').html('');

	jQuery('#e_ref_id').val(ref_id);
	jQuery('#e_ref_biblio_code').val(unescape(biblio_code));
	jQuery('#e_ref_author').val(unescape(ref_author));
	jQuery('#e_ref_title').val(unescape(ref_title));
	jQuery('#e_ref_year').val(unescape(ref_year));
	jQuery('#e_ref_part').val(unescape(ref_part));
	jQuery('#e_ref_uri').val(unescape(ref_uri));
	jQuery('#e_ref_code').val(unescape(ref_code));
	jQuery('#e_ref_style').val(unescape(ref_style));
	jQuery('#e_ref_template').val(unescape(ref_template));
	jQuery('#e_ref_publisher').val(unescape(ref_publisher));
	jQuery('#e_ref_location').val(unescape(ref_location));

	return false;
}
function delete_ref(ref_id){

	var c = confirm('Are you sure you want to delete this bibliography?');

	if(c){
		var dataString = dataMain+'&action=e_del';
		dataString += '&ref_id='+encodeURIComponent(ref_id);

		jQuery.ajax({
			url: ajaxURL+'references.php',
			type: 'POST',
			data: dataString,
			beforeSend: function( xhr ) {
				//jQuery('#e_status').html('Saving...');
			},
			success: function( data ) {
				jQuery('#'+ref_id).remove();
				jQuery('#ref_edit_block').hide();
			}
		});
	}
	return false;
}
</script>

		<div class="form-group">
				<div class="col-sm-12">
						<a href="javascript:;" id="add_ref" class="btn btn-link" onclick="add_ref()">{tr}Add Reference{/tr}</a>
				</div>
		</div>
		<div class="form-group">
		<div id="ref_list" style="display:{$display}">
			{tr}References Available{/tr}:
			<ul style="list-style-type:none; padding-left: 0;">
			{section name=i loop=$references}
				{if $references[i].is_present eq 1}
					<li id="{$references[i].ref_id|escape}" style='background-color:#D3FDDA;border-bottom: 1px dotted #161C17;'>
				{else}
					<li id="{$references[i].ref_id|escape}" style='border-bottom: 1px dotted #161C17;'>
				{/if}
					{$references[i].biblio_code|escape}&nbsp;&nbsp;
					<a class="edit_ref" title="{tr}Edit{/tr}" href="javascript:;" onclick="edit_ref('{$references[i].ref_id|escape}','{$references[i].biblio_code|escape}','{$references[i].author|escape}','{$references[i].title|escape}','{$references[i].year|escape}','{$references[i].part|escape}','{$references[i].uri|escape}','{$references[i].code|escape}','{$references[i].style|escape}','{$references[i].template|escape}','{$references[i].publisher|escape}','{$references[i].location|escape}')">{icon name='edit' alt="{tr}Edit{/tr}"}</a>
					<a title="{tr}Delete{/tr}" onclick="delete_ref('{$references[i].ref_id|escape}')" >{icon name='remove' alt="{tr}Remove{/tr}"}</a>
					{if $references[i].is_library lt 1 && $use_references eq 1 && $edit_references eq 1}
						<a class="add_lib_btn" title="{tr}Add to library{/tr}" onclick="add_lib('{$references[i].ref_id|escape}','{$references[i].biblio_code|escape}','{$references[i].author|escape}','{$references[i].title|escape}','{$references[i].year|escape}','{$references[i].part|escape}','{$references[i].uri|escape}','{$references[i].code|escape}','{$references[i].style|escape}','{$references[i].template|escape}','{$references[i].publisher|escape}','{$references[i].location|escape}')" >{icon name='add' alt="{tr}Add to library{/tr}"}</a>
					{/if}
				</li>
			{/section}
			</ul>

			{if $use_references eq 1}
				{if $libReferencesCant gt 0}
					{tr}Library References{/tr}:<br>
					<select name="lib_ref" id="lib_ref">
						{section name=i loop=$libReferences}
							<option value="{$libReferences[i].ref_id|escape}">{$libReferences[i].biblio_code|escape}</option>
						{/section}
					</select>
					<br>
					<input class="wikiaction btn btn-default" type="submit" value="{tr}Use{/tr}" id="u_lib" name="u_lib">
					<br><span id="u_lib_status"></span>
				{/if}
			{/if}
			{if $edit_references eq 1 && $libReferencesCant gt 0}
				<br><a href="tiki-references.php" target="_blank">{tr}Edit Library References{/tr}</a>
			{/if}

		</div>

		<div id="ref_edit_block" style="display:none;">
			<div>
				<input type="hidden" name="e_ref_id" id="e_ref_id" value="">
				<input type="hidden" name="page" value="{$page|escape}">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_biblio_code">{tr}Biblio Code{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_biblio_code" id="e_ref_biblio_code" maxlength="50" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_author">{tr}Author{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_author" id="e_ref_author" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_title">{tr}Title{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_title" id="e_ref_title" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_year">{tr}Year{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_year" id="e_ref_year" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_part">{tr}Part{/tr}:</label>
					<div class="col-sm-10">
					<input type="text" class="form-control wikiedit" name="e_ref_part" id="e_ref_part" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_uri">{tr}URI{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_uri" id="e_ref_uri" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_biblio_code">{tr}Code{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_code" id="e_ref_code" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_publisher">{tr}Publisher{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_publisher" id="e_ref_publisher" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_location">{tr}Location{/tr}:</label>
					<div class="col-sm-10">
					<input type="text" class="form-control wikiedit" name="e_ref_location" id="e_ref_location" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_style">{tr}Style{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_style" id="e_ref_style" value="">
						<span class="help-block">{tr}Enter the CSS class name to be added in the 'li' tag for listing this reference.{/tr}</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="e_ref_template">{tr}Template{/tr}:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control wikiedit" name="e_ref_template" id="e_ref_template" value="">
						<span class="help-block">
							{tr}Enter template format in which you want to display the reference details in the bibliography listing. For example{/tr}: ~title~ (~year~) ~author~
						</span>
						<span class="help-block">
							{tr}All the codes must be in lower case letters separated with spaces.{/tr}
						</span>
					</div>
				</div>
				<div class="form-group">
					<div id="e_btns">
						<input class="wikiaction btn btn-default" type="submit" value="Save" id="e_submit" name="e_submit">
						<input class="wikiaction btn btn-warning" type="reset" value="Cancel" id="e_cancel" name="e_cancel">
						<span id="e_status" style="margin: 0 0 0 10px;"></span>
					</div>
					<div id="a_btns">
						<input class="wikiaction btn btn-default" type="submit" value="{tr}Add{/tr}" id="a_submit" name="a_submit">
						<input class="wikiaction btn btn-warning" type="reset" value="{tr}Cancel{/tr}" id="a_cancel" name="a_cancel">
						<span id="a_status" style="margin: 0 0 0 10px;"></span>
					</div>
				</div>
			</div>
		</div>
	</div>


{else}
	{tr}Please save the page before creating the bibliography.{/tr}
{/if}
