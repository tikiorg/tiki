<div class="forms">
	<h2>Forms</h2>
	<div class="row">
		<div class="col-sm-8 col-md-9">
			<p><label for="sg-username-example">Username</label>
				<input id="sg-username-example" class="nocolor group" type="text" value="Username">
			</p>
			<p class="has-error">
				<label for="sg-password-example">Password</label>
				<input id="sg-password-example" class="nocolor group" type="password">
				<label class="label label-warning">This field is required</label>
			</p>
			<p>
				<input id="sg-remember-example" type="checkbox"> Remember me
			</p>
			<p>
				<button class="btn btn-default">Login</button>
			</p>
			<hr/>

			<p class="has-error">
				<label for="sg-text-example">Text field</label>
				<input id="sg-text-example" class="nocolor group" type="text">
				<label class="label label-warning">This field is required</label>
			</p>
			<label for="sg-textarea-example">Textarea</label>
			<textarea id="sg-textarea-example" class="nocolor group" rows="3">This is a textarea field</textarea>
			<label for="sg-select-example">Select</label> <select id="sg-select-example" class="nocolor group">
				<option>Option 1</option>
				<option>Option 2</option>
				<option>Option 3</option>
				<option>Option 4</option>
			</select>
			<p>
				<label for="sg-checkbox-example">Checkbox</label>
				<input id="sg-checkbox-example" type="checkbox">
				This is a checkbox
			</p>
			<p>
				<label for="sg-radio-example">Radio</label>
				<input id="sg-radio-example" name="radio" type="radio">
				This is a radio button
			</p>
			<p><input name="radio" type="radio"> This is another radio button</p>
		</div>

		<div class="col-sm-4 col-md-3">
			<div class="input">
				<p class="picker" data-selector=".group" data-element="background-color">
					<label for="sg-field-bg-color">Background:</label>
					<input id="sg-field-bg-color" data-selector=".group" data-element="background-color" data-var="@input-bg" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".group" data-element="border-color">
					<label for="sg-field-border-color">Border:</label>
					<input id="sg-field-border-color" data-selector=".group" data-element="border-color" data-var="@input-border" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".group" data-element="color">
					<label for="sg-field-text-color">Text:</label>
					<input id="sg-field-text-color" data-selector=".group" data-element="color" data-var="@input-color" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p>
					<label for="sg-field-padding">Padding:</label>
					<input id="sg-field-padding" class="nocolor" data-selector=".group" data-element="padding" type="text">
				</p>
			</div>
		</div>
	</div>
</div>
