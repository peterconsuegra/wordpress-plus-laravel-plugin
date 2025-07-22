<html>

	<head>
		
		<!-- CSS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

		<!-- jQuery and JS bundle w/ Popper.js -->
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
		
	</head>

    <body>
		<div class="container">

			 <h3>WordPress posts</h3>     

			<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Título</th>
					<th>Fecha</th>
					<th>Extracto</th>
					<th>Enlace</th>
				</tr>
			</thead>
			<tbody>
				@forelse($posts as $post)
					<tr>
						<td>{{ $post['id'] }}</td>
						<td>{{ $post['title'] }}</td>
						<td>{{ date('Y-m-d H:i', strtotime($post['date'])) }}</td>
						<td>{{ \Illuminate\Support\Str::limit($post['excerpt'], 100) }}</td>
						<td><a href="{{ $post['link'] }}" target="_blank">Ver</a></td>
					</tr>
				@empty
					<tr>
						<td colspan="5">No hay posts publicados.</td>
					</tr>
				@endforelse
			</tbody>
		</table>
			
		<br />
		<a href="/wordpress_plus_laravel_examples">List examples</a>

	</div>

    </body>
</html>