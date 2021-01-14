<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Simple Calendar App</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" type="text/css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicker/jquery.timepicker.min.css" type="text/css">
		<script src="https://unpkg.com/dayjs@1.8.21/dayjs.min.js" type="text/javascript"></script>
		<script src="https://unpkg.com/dayjs@1.8.21/plugin/weekOfYear.js" type="text/javascript"></script>
		<script>dayjs.extend(window.dayjs_plugin_weekOfYear)</script>
		<style>
			body {
			  font-family: sans-serif;
			  font-weight: 100;
			  font-size: 20px;
			  background-color: #f0f0ea;
			}

			h1 {
			  font-size: 26px;
			  color: #000;
			  margin: 0;
			  padding: 0;
			}
			
			.title {
				text-align: center;
				margin: 10px 0 0 0;
			}
		</style>
	</head>
	<body>
		<div class="title">
			<h1>Simple Calendar App</h1>
		</div>
		<div id="calendar"></div>
		<script src="js/app.js"></script>
		<!-- jQuery has to be loaded first -->
		<script src="https://cdn.jsdelivr.net/npm/timepicker/jquery.timepicker.min.js" type="text/javascript"></script>
	</body>
</html>