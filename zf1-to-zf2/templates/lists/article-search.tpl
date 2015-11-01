{literal}
	<div class="form-group">
		<div class="input-group">
			{input _filter="content" _field="title,contents" type="text"  placeholder="Search... " class="form-control clearfield"}
			<span class="input-group-btn">
				{input type=reset value="Clear" class="btn btn-default clearbox"}
			   	{input type=submit value="Search" class="btn btn-search"}
			</span>
		</div>
	</div>
{/literal}
{jq}
	$(document).on('click', ".clearbox", function(e) {
	$("#customsearch_announcementsearch .clearfield").val("").trigger("change");
	});
{/jq}