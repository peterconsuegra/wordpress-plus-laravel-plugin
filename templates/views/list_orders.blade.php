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
			
			<br />		
			 <h3>WooCommerce orders</h3>  
			 
			<table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Moneda</th>
                <th>Cliente (ID)</th>
                <th>Email Cliente</th>
                <th>Teléfono Cliente</th>
                <th>Items</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order['id'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($order['date_created'])->format('Y-m-d H:i') }}</td>
                    <td>{{ ucfirst($order['status']) }}</td>
                    <td>{{ number_format($order['total'], 2) }}</td>
                    <td>{{ $order['currency'] }}</td>
                    <td>{{ $order['customer_id'] }}</td>
                    <td>{{ $order['billing_email'] }}</td>
                    <td>{{ $order['billing_phone'] }}</td>
                    <td>{{ $order['item_count'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No se encontraron pedidos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
			
			<br />
			<a href="/wordpress_plus_laravel_examples">List examples</a>
		
		</div>
		
    </body>
</html>