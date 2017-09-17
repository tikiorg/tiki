<div class="tables">
	<h2>Tables</h2>
	<div class="row">
		<div class="col-sm-8 col-md-9">
			<p>Striped table</p>
			<table class="table table-striped">
				<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Title</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>1</td>
					<td>Benoit Roy</td>
					<td>Front-End Ninja</td>
				</tr>
				<tr>
					<td>2</td>
					<td>Marc Laporte</td>
					<td>Banjo Player</td>
				</tr>
				<tr>
					<td>3</td>
					<td>Isabelle Montminy</td>
					<td>Dancing Queen</td>
				</tr>
				</tbody>
			</table>
		</div>

		<div class="col-sm-4 col-md-3">
			<div class="input">
				<p class="picker" data-selector=".table-striped > tbody > tr:nth-of-type(odd)" data-element="background-color">
					<label for="sg-striped-bgcolor-odd">Bg (odd rows):</label>
					<input id="sg-striped-bgcolor-odd" data-selector=".table-striped > tbody > tr:nth-of-type(odd)" data-element="background-color" data-var="@table-bg-accent" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".table-striped > tbody > tr:nth-of-type(even)" data-element="background-color">
					<label for="sg-striped-bgcolor-even">Bg (even rows):</label>
					<input id="sg-striped-bgcolor-even" data-selector=".table-striped > tbody > tr:nth-of-type(even)" data-element="background-color" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector="table tr" data-element="color">
					<label for="sg-table-color">Text color:</label>
					<input id="sg-table-color" data-selector="table tr" data-element="color" type="text"><span class="input-group-addon"><i></i></span>
				</p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-8 col-md-9">
			<p>Bordered table</p>
			<table class="table table-bordered">
				<thead>
				<tr>
					<th class="tb">#</th>
					<th class="tb">Name</th>
					<th class="tb">Title</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td class="tb">1</td>
					<td class="tb">Benoit Roy</td>
					<td class="tb">Front-End Ninja</td>
				</tr>
				<tr>
					<td class="tb">2</td>
					<td class="tb">Marc Laporte</td>
					<td class="tb">Banjo Player</td>
				</tr>
				<tr>
					<td class="tb">3</td>
					<td class="tb">Isabelle Montminy</td>
					<td class="tb">Dancing Queen</td>
				</tr>
				</tbody>
			</table>
		</div>

		<div class="col-sm-4 col-md-3">
			<div class="input">
				<p class="picker" data-selector=".table-bordered tr" data-element="background-color">
					<label for="sg-bordered-bgcolor">Background:</label>
					<input id="sg-bordered-bgcolor" data-selector=".table-bordered tr" data-element="background-color" data-var="@table-bg" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p>
					<label for="sg-cells-border">Border:</label>
					<input id="sg-cells-border" class="nocolor" data-selector=".table-bordered .tb" data-element="border" type="text">
				</p>
				<p>
					<label for="sg-cells-padding">Padding:</label>
					<input id="sg-cells-padding" class="nocolor" data-selector=".table > tbody > tr > td" data-element="padding" type="text">
				</p>
			</div>
		</div>
	</div>
</div>
