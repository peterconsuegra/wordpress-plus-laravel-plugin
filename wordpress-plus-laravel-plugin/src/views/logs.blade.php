{{-- vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/views/logs.blade.php --}}
@extends('layout')

@section('content')

<div class="row" style="margin-top: 20px">
        <div class="col-md-12">
				
					<p>Integration type:
					@if($site->integration_type == "inside_wordpress")
						Same Domain
					@else
						Separate Subdomain
					@endif
					</p>
					 
					@if($site->integration_type=="separate_subdomain")
                     	<p>URL: <a href="http://{{$site->wordpress_laravel_url}}" target="_blank">{{$site->wordpress_laravel_url}}</a></p>
					@else
					 	<p>URL: <a href="http://{{$target_site->url}}/{{$site->name}}" target="_blank">{{$target_site->url}}/{{$site->name}}</a></p>
					@endif
				
					@if($site->integration_type=="separate_subdomain")
	                 	<p>Check the built-in examples: <a href="http://{{$site->url}}/wordpress_plus_laravel_examples" target="_blank">{{$site->url}}/wordpress_plus_laravel_examples</a></p>
					@else
	                 	<p>Check the built-in examples: <a href="http://{{$target_site->url}}/{{$site->name}}/wordpress_plus_laravel_examples" target="_blank">{{$target_site->url}}/{{$site->name}}/wordpress_plus_laravel_examples</a></p>		
					@endif		
        </div>
		
    </div>

    {{-- ── Site output ───────────────────────────────────────────── --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Terminal output</strong>
            <small class="text-muted"></small>
        </div>
        <pre style="max-height:400px;overflow:auto">{{ $site->output }}</pre>
    </div>

    {{-- ── Apache error.log ───────────────────────────────────────────── --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Apache error.log</strong>
            <small class="text-muted">{{ $web_server_error_file }}</small>
        </div>
        <pre style="max-height:400px;overflow:auto">{{ $web_server_error_file_content }}</pre>
    </div>

    {{-- ── Apache access.log ──────────────────────────────────────────── --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Apache access.log</strong>
            <small class="text-muted">{{ $web_server_access_file }}</small>
        </div>
        <pre style="max-height:400px;overflow:auto">{{ $web_server_access_file_content }}</pre>
    </div>
@endsection
