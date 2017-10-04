<div class="forms">
	<h2>Forms</h2>
	<div class="row">
		<div class="col-sm-8 col-md-9">
			<form class="sg-form" method="post" action="#">
				<fieldset>
					<p class="form-group">
						<label for="sg-username-example">Username</label>
						<input id="sg-username-example" class="nocolor form-control" type="text" value="Username">
					</p>
					<p class="has-error form-group">
						<label for="sg-password-example">Password</label>
						<input id="sg-password-example" class="nocolor form-control" type="password">
						<label class="label label-warning">This field is required</label>
					</p>
					<p class="form-group">
						<input id="sg-remember-example" type="checkbox"> Remember me
					</p>
					<p class="form-group">
						<button class="btn btn-default">Login</button>
					</p>
					<hr/>

					<p class="has-error form-group">
						<label for="sg-text-example">Text field</label>
						<input id="sg-text-example" class="nocolor form-control" type="text">
						<label class="label label-warning">This field is required</label>
					</p>
					<p class="form-group">
						<label for="sg-textarea-example">Textarea</label>
						<textarea id="sg-textarea-example" class="nocolor form-control" rows="3">This is a textarea field</textarea>
					</p>
					<p class="form-group">
						<label for="sg-select-example">Select</label> <select id="sg-select-example" class="nocolor form-control">
							<option>Option 1</option>
							<option>Option 2</option>
							<option>Option 3</option>
							<option>Option 4</option>
						</select>
					</p>
					<p class="form-group">
						<label for="sg-checkbox-example">Checkbox</label>
						<input id="sg-checkbox-example" type="checkbox">
						This is a checkbox
					</p>
					<p class="form-group">
						<label for="sg-radio-example">Radio</label>
						<input id="sg-radio-example" name="radio" type="radio">
						This is a radio button
					</p>
					<p><input name="radio" type="radio"> This is another radio button</p>
				</fieldset>
			</form>
		</div>

		<div class="col-sm-4 col-md-3">
			<div class="input">
				<p class="picker" data-selector=".form-control" data-element="background-color">
					<label for="sg-field-bg-color">Background:</label>
					<input id="sg-field-bg-color" data-selector=".form-control" data-element="background-color" data-var="@input-bg" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".form-control" data-element="border-color">
					<label for="sg-field-border-color">Border:</label>
					<input id="sg-field-border-color" data-selector=".form-control" data-element="border-color" data-var="@input-border" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".form-control" data-element="color">
					<label for="sg-field-text-color">Text:</label>
					<input id="sg-field-text-color" data-selector=".form-control" data-element="color" data-var="@input-color" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p>
					<label for="sg-field-padding">Padding:</label>
					<input id="sg-field-padding" class="nocolor" data-selector=".form-control" data-element="padding" type="text">
				</p>
			</div>
		</div>
	</div>
</div>
