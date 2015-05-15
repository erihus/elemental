@extends('auth.login')

@section('content')
<div class="ui main container">
	<div class="ui red segment">
		
		<div class="ui header">Reset Password</div>
		
		@if (session('status'))
			<div class="alert alert-success">
				{{ session('status') }}
			</div>
		@endif

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

		<form class="ui form" role="form" method="POST" action="/password/email">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">

			<div class="field">
				<label for="email">E-Mail Address</label>
				<input type="email"name="email" value="{{ old('email') }}">
			</div>

			<div class="field">
				
				<button type="submit" class="ui basic red button">
					Send Password Reset Link
				</button>
				
			</div>
		</form>
		
		
	</div>
</div>
@endsection
