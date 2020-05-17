<h1>My TODO List</h1>

<ul>
	@foreach($todos as $todo)
		<li>{{$todo->todo}}</li>
	@endforeach
</ul>