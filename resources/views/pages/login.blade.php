<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<!-- Mirrored from seantheme.com/hud/page_login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 15 Jan 2025 15:19:27 GMT -->
<head>
	<meta charset="utf-8">
	<title>Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	
	<!-- ================== BEGIN core-css ================== -->
	<link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">
	<!-- ================== END core-css ================== -->
	<link rel="icon" href="{{ asset('assets/img/cscisaclogo.png') }}" type="image/x-icon">
	
</head>
<body class='pace-top'>
	<!-- BEGIN #app -->
	<div id="app" class="app app-full-height app-without-header">
		<!-- BEGIN login -->
		<div class="login">
			<!-- BEGIN login-content -->
			<div class="login-content">
				<form action="/login" method="POST">
                    @csrf
					<h1 class="text-center">Sign In</h1>
					<div class="mb-3">
						<label class="form-label">Email Address <span class="text-danger">*</span></label>
						<input type="email" name="email" class="form-control form-control-lg bg-inverse bg-opacity-5" placeholder="">
					</div>
					<div class="mb-3">
						<div class="d-flex">
							<label class="form-label">Password <span class="text-danger">*</span></label>
							{{-- <a href="#" class="ms-auto text-inverse text-decoration-none text-opacity-50">Forgot password?</a> --}}
						</div>
						<input type="password" name="password" class="form-control form-control-lg bg-inverse bg-opacity-5" value="" placeholder="">
					</div>
					<button type="submit" class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3">Sign In</button>
				</form>
                <a href="/" class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3">Back</a>
			</div>
		</div>
	</div>
	<!-- END #app -->
	
	<!-- ================== BEGIN core-js ================== -->
	<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
	<script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>
</html>
