<div id="full_width_title" class="row" style="display:none;">
	<div class="pull-left title">
		{if $iconsrc}
			<img src="{$iconsrc}">
		{/if}
		<h1>{tr}{$title}{/tr}</h1>
	</div>
</div>


<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { //this function allows for the page title to be copied outside the container for full-width
		var pt = document.getElementById("full_width_title");
		var tc = document.getElementById("title-container");  // the title container must be in the layout_view.tpl
		var td = document.getElementById("title-div");  // the title container must be in the layout_view.tpl

		if(pt && tc) { //if pagetitle and title container exists
			td.style.display="block";
			tc.innerHTML = pt.innerHTML;
			pt.style.display="none";
		}
	});
</script>