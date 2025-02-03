<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<!-- Mirrored from seantheme.com/hud/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 15 Jan 2025 15:18:58 GMT -->
<head>
	<meta charset="utf-8">
	<title>Dashboard ISIF</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	
	<!-- ================== BEGIN core-css ================== -->
	<link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">
	<!-- ================== END core-css ================== -->
	
	<!-- ================== BEGIN page-css ================== -->
	<link href="{{ asset('assets/plugins/jvectormap-next/jquery-jvectormap.css') }}" rel="stylesheet">
	<!-- ================== END page-css ================== -->

</head>
<body>
	<!-- BEGIN #app -->
	<div id="app" class="app">
		<!-- BEGIN #header -->
		@include('components.navbar')
		<!-- END #header -->
		
		<!-- BEGIN #sidebar -->
		@include('components.sidebar')
		<!-- END #sidebar -->
			
		<!-- BEGIN mobile-sidebar-backdrop -->
		<button class="app-sidebar-mobile-backdrop" data-toggle-target=".app" data-toggle-class="app-sidebar-mobile-toggled"></button>
		<!-- END mobile-sidebar-backdrop -->
		
		<!-- BEGIN #content -->
        <div id="content" class="app-content">
            @yield('content')
        </div>

		<!-- END #content -->
		
		<!-- END theme-panel -->
		<!-- BEGIN btn-scroll-top -->
		<a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>
		<!-- END btn-scroll-top -->
	</div>
	<!-- END #app -->
	
	<!-- ================== BEGIN core-js ================== -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
	<script src="{{ asset('assets/js/app.min.js') }}"></script>
	<!-- ================== END core-js ================== -->
	
	<!-- ================== BEGIN page-js ================== -->
	<script src="{{ asset('assets/plugins/jvectormap-next/jquery-jvectormap.min.js') }}"></script>
	<script src="{{ asset('assets/plugins/jvectormap-content/world-mill.js') }}"></script>
	<script src="{{ asset('assets/plugins/apexcharts/dist/apexcharts.min.js') }}"></script>
	<script src="{{ asset('assets/js/demo/dashboard.demo.js') }}"></script>
</body>
</html>
