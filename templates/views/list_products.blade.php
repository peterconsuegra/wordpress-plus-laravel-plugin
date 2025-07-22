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

		 <h3>Woocommerce products</h3>     

			<table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Precio</th>
                <th>Precio Regular</th>
                <th>Precio Oferta</th>
                <th>Enlace</th>
                <th>Imagen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $prod)
                <tr>
                    <td>{{ $prod['id'] }}</td>
                    <td>{{ $prod['name'] }}</td>
                    <td>{{ $prod['sku'] }}</td>
                    <td>{{ $prod['price'] }}</td>
                    <td>{{ $prod['regular_price'] }}</td>
                    <td>{{ $prod['sale_price'] ?: '—' }}</td>
                    <td>
                        <a href="{{ $prod['permalink'] }}" target="_blank">
                            Ver producto
                        </a>
                    </td>
                    <td>
                        @if($prod['image'])
                            <img src="{{ $prod['image'] }}" alt="{{ $prod['name'] }}" style="max-height:50px;">
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No se encontraron productos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
			
			<br />
			<a href="/wordpress_plus_laravel_examples">List examples</a>

		</div>

    </body>
</html>