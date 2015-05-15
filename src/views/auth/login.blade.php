<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Elemental CMS</title>

    <link href="/js/bower_components/semantic-ui/dist/semantic.css" rel='stylesheet' type='text/css'>
    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<main style="width: 900px; margin: 0px auto;">
	@section('content')
	<div class="ui main container">
		<div class="ui red segment">
			
			<div class="ui header">CMS Login</div>
			
			@if (count($errors) > 0)
				<div class="ui visible error message">
					<strong>Whoops!</strong> There were some problems with your input.<br><br>
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<form class="ui form" role="form" method="POST" action="/auth/login">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">

				<div class="field">
					<label for="email">E-Mail Address</label>
					<input type="email" name="email" value="{{ old('email') }}">
				</div>

				<div class="field">
					<label for="password">Password</label>
					<input type="password" name="password">
					
				</div>

				<div class="inline field">
					<div class="ui checkbox">
						<input type="checkbox" name="remember">
						<label> Remember Me</label>
					</div>
				</div>

				
				<div class="field">
					<button type="submit" class="ui red basic button" style="margin-right: 15px;">
						Login
					</button>

					<a style="color: #d95c5c;" href="/password/email">Forgot Your Password?</a>
				</div>
				
			</form>
			
		</div>
	</div>
	@show
</main>

 <script src="/js/bower_components/jquery/dist/jquery.min.js"></script>
 <script src="/js/bower_components/semantic-ui/dist/semantic.js"></script>
 <script>
 	$('.ui.checkbox').checkbox();
 </script>

</body>
</html>
