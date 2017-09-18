<div class="colors">
	<h2>Colors</h2>
	<div class="input">
		{if $prefs.site_layout eq 'classic' or $prefs.site_layout eq 'header_middle_footer_containers_3-6-3'}
			{$header_selector = '.header_outer'}
		{else}
			{$header_selector = '.page-header,.topbar'}
		{/if}
		<p class="regular">Header</p>
		<p class="picker" data-selector="{$header_selector}" data-element="background-color">
			<span class="input-group-addon"><i></i></span>
			<input id="sg-header-color" data-selector="{$header_selector}" data-element="background-color" type="text">
		</p>
	</div>

	<div class="input">
		<p class="regular">Body</p>
		<p class="picker" data-selector="body" data-element="background-color">
			<span class="input-group-addon"><i></i></span>
			<input id="sg-body-color" data-selector="body" data-element="background-color" data-var="@body-bg" type="text">
		</p>
	</div>

	<div class="input">
		<p class="regular">Footer</p>
		<p class="picker" data-selector="footer" data-element="background-color">
			<span class="input-group-addon"><i></i></span>
			<input id="sg-footer-color" data-selector="footer" data-element="background-color" type="text">
		</p>
	</div>

	<div class="input">
		<p class="regular">Text</p>
		<p class="picker" data-selector="body" data-element="color">
			<span class="input-group-addon"><i></i></span>
			<input id="sg-text-color" data-selector="body" data-element="color" data-var="@text-color" type="text">
		</p>
	</div>

	<div class="input">
		<p class="regular">Links</p>
		<p class="picker" data-selector=".style-guide p > a" data-element="color">
			<span class="input-group-addon"><i></i></span>
			<input id="sg-link-color" data-selector=".style-guide p > a" data-element="color" data-var="@link-color" type="text">
		</p>
	</div>

	<p>Sample Text</p>
	<p>In mauris integer etiam aliquet integer duis rhoncus <a href="#">ultricies cras</a> in habitasse ac sociis
		porttitor placerat ac porttitor, in ac. A, tristique <a href="#">dapibus mauris</a>, vut et porta? Enim, porta
		penatibus, augue egestas aliquam eu velit placerat, sociis hac et, pulvinar <a href="#">tincidunt amet</a> ut
		turpis dapibus. Dolor. Lundium rhoncus elementum vel. Tempor sit nisi aliquam ut augue tincidunt tincidunt cum
		<a href="#">egestas massa</a>, nunc etiam ac scelerisque auctor sed sed facilisis!
	</p>
</div>
