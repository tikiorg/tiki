{title help="Kaltura" admpage="video"}{tr}Upload Media{/tr}{/title}

<div class="t_navbar">
	{if $tiki_p_list_videos eq 'y'}
	{button class="btn btn-default" _text="{tr}List Media{/tr}" href="tiki-list_kaltura_entries.php"}
	{/if}
</div>
<div id="upload-container">
</div>
<p id="more-media">
	{button class="btn btn-default" _text="{tr}Add more media{/tr}" href="tiki-kaltura_upload.php"}
</p>
{jq}
var url = $.service('kaltura', 'upload');
$('#more-media .btn').click(function () {
	$('#more-media').hide();

	$('#upload-container').load(url, function () {
		$('form', this).submit(function () {
			$('#upload-container').load(url, $(this).serialize(), function () {
				$('#more-media').show();
			});
			return false;
		});
	});
	return false;
}).click();
{/jq}
