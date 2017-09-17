<div class="navbars">
	<h2>Navbar</h2>
	<div class="row">
		<div class="col-sm-8 col-md-9">
			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="#"></a>
					</div>

					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li class="active">
								<a href="javascript:void(0);">Link <span class="sr-only">(current)</span></a>
							</li>
							<li>
								<a href="javascript:void(0);">Link</a>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
									Dropdown <span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li><a href="javascript:void(0);">Action</a></li>
									<li><a href="javascript:void(0);">Another action</a></li>
									<li><a href="javascript:void(0);">Something else here</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="javascript:void(0);">Separated link</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="javascript:void(0);">One more separated link</a></li>
								</ul>
							</li>
						</ul>
						<form class="navbar-form navbar-left" role="search">
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Search">
							</div>
							<button type="submit" class="btn btn-default">Submit</button>
						</form>
						<ul class="nav navbar-nav navbar-right">
							<li><a href="javascript:void(0);">Link</a></li>
							<li class="dropdown">
								<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
									Dropdown <span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li><a href="javascript:void(0);">Action</a></li>
									<li><a href="javascript:void(0);">Another action</a></li>
									<li><a href="javascript:void(0);">Something else here</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="javascript:void(0);">Separated link</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</div>

		<div class="col-sm-4 col-md-3">
			<div class="input">
				<p class="picker" data-selector=".style-guide .navbar-default" data-element="background-color">
					<label for="sg-navbar-bg-color">Background:</label>
					<input id="sg-navbar-bg-color" data-selector=".style-guide .navbar-default" data-element="background-color" data-var="@navbar-default-bg" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".style-guide .navbar-default" data-element="border-color">
					<label for="sg-navbar-border-color">Border:</label>
					<input id="sg-navbar-border-color" data-selector=".style-guide .navbar-default" data-element="border-color" data-var="@navbar-default-border" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".style-guide .navbar-default .navbar-nav > li > a" data-element="color">
					<label for="sg-navbar-link-color">Text color:</label>
					<input id="sg-navbar-link-color" data-selector=".style-guide .navbar-default .navbar-nav > li > a" data-element="color" data-var="@navbar-default-link-color" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p class="picker" data-selector=".style-guide .navbar-default .navbar-nav > .active > a" data-element="background-color">
					<label for="sg-navbar-active-link-color">Active menu:</label>
					<input id="sg-navbar-active-link-color" data-selector=".style-guide .navbar-default .navbar-nav > .active > a" data-element="background-color" data-var="@navbar-default-link-active-bg" type="text">
					<span class="input-group-addon"><i></i></span>
				</p>
				<p>
					<label for="sg-navbar-border-radius">Border radius:</label>
					<input id="sg-navbar-border-radius" class="nocolor" data-selector=".navbar" data-element="border-radius" data-var="@navbar-border-radius" type="text">
				</p>
			</div>
		</div>

	</div>
</div>
