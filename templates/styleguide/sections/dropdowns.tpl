<div class="dropdowns">
	<h2>Dropdowns</h2>
	<div class="row">
		<div class="col-sm-8 col-md-9">
			<div class="dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Dropdown <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="javascript:void(0);">Action</a></li>
					<li><a href="javascript:void(0);">Another action</a></li>
					<li><a href="javascript:void(0);">Something else here</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="javascript:void(0);">Separated link</a></li>
				</ul>
			</div>
		</div>

		<div class="col-sm-4 col-md-3">
			<div class="input">
				<p class="picker" data-selector=".style-guide .dropdown-menu" data-element="background-color">
					<label for="sg-dropdown-bg-color">Background:</label>
					<input id="sg-dropdown-bg-color" data-selector=".style-guide .dropdown-menu" data-element="background-color" data-var="@dropdown-bg" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".style-guide .dropdown-menu > li > a" data-element="color">
					<label for="sg-dropdown-text-color">Text color:</label>
					<input id="sg-dropdown-text-color" data-selector=".style-guide .dropdown-menu > li > a" data-element="color" data-var="@dropdown-link-color" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p> </p>
				<p>
					<label for="sg-dropdown-border-radius">Border radius:</label>
					<input id="sg-dropdown-border-radius" class="nocolor" data-selector=".style-guide .dropdown-menu" data-element="border-radius" type="text">
				</p>
				<p>
					<label for="sg-dropdown-box-shadow">Box shadow:</label>
					<input id="sg-dropdown-box-shadow" class="nocolor" data-selector=".style-guide .dropdown-menu" data-element="box-shadow" type="text">
				</p>
			</div>
		</div>
	</div>
</div>
