<div id="title-div" style="display:none;">
	<div class="container" id="title-container">
		<div id="full_width_title">
			<div class="pull-left title">
				{if $iconsrc}
					<img src="{$iconsrc}">
				{/if}
				<h1>{tr}{$title}{/tr}</h1>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { //this function allows for the page title to be copied outside the container for full-width
		$('#title-div').closest(".container").before($("#title-div"));
		$('#title-div').css('display','block');
	});
</script>