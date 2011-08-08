{strip}
 <div class="wp_galleriffic" id="container">
 	<div id="gallery" class="gcontent">
		<div id="controls" class="controls"></div>
		<div class="slideshow-container">
			<div id="loading" class="loader"></div>
			<div id="slideshow" class="slideshow"></div>
		</div>
		<div id="caption" class="caption-container"></div>
	</div>
	<div id="thumbs"  class="navigation">
		<ul class="thumbs noscript">
			{foreach from=$images key=i item=image name=wpmosaicbig}
			<li>
				<a class="thumb" href="tiki-download_file.php?fileId={$image.fileId}&amp;display&amp;max={$imgWidth|replace:'px':''}" title="">
				   {literal}{img fileId={/literal}{$image.fileId}{literal} thumb=y link=""}{/literal}
				</a>
				<div class="caption">
					<div class="image-title"></div>
					<div class="image-desc"></div>
				</div>
			</li>
			{/foreach}
		</ul>
	</div>
	<div style="clear: both;"></div>
</div>
{/strip}
