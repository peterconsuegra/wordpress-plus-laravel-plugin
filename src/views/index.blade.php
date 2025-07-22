@extends('layout')

@section('content')
<div class="container-fluid">

    <div class="row align-items-center mb-5">
        <div class="col-md-6 text-center text-md-left mb-4 mb-md-0">
            <img src="/pete.png" alt="WordPress Pete" class="img-responsive" style="max-height:200px">
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <a href="{{ url('/wordpress_plus_laravel/create') }}" class="btn btn-primary btn-lg w-100">
                <span class="glyphicon glyphicon-plus"></span>&nbsp;Create WordPress&nbsp;+&nbsp;Laravel Instance
            </a>
            <p class="text-muted mb-0">Turn WordPress into a full-scale marketing and sales engine — with landing pages, subscriptions, payments, tracking, and unlimited plugins — while Laravel delivers the custom features your users need.</p>
        </div>
    </div>

    {{-- flash & validation ---------------------------------------------- --}}
    <div id="update_area_info"></div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Please fix the following:
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- integrations table --------------------------------------------- --}}
    <div class="row">
        <div class="col-12">
            <div class="panel panel-default">
                <div class="panel-heading d-flex justify-content-between align-items-center">
                    <h3 class="panel-title mb-0">My WordPress&nbsp;+&nbsp;Laravel Integrations</h3>
                    @if($sites)
                        <small class="text-muted">{{ count($sites) }} total</small>
                    @endif
                </div>
                <div class="table-responsive">
                    @if($sites && count($sites))
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Project Name</th>
                                    <th>URL</th>
                                    <th>Integration</th>
                                    <th class="text-center" width="260">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sites as $site)
                                    <tr>
                                        <td>{{ $site->id }}</td>
                                        <td>{{ $site->name }}</td>
                                        <td>
                                            <a href="http://{{ $site->url }}" target="_blank">{{ $site->url }}</a>
                                        </td>
                                        <td>
                                            Laravel version: {{$site->laravel_version}} <br />
                                            {{ $site->integration_type === 'inside_wordpress' ? 'Same Domain' : 'Separate Subdomain' }}
                                        </td>
                                        {{-- action buttons ---------------------------------------------------- --}}
                                        <td class="text-right" style="vertical-align:middle;">
                                            <div class="btn-group btn-group-sm"
                                                 role="group"
                                                 style="display:flex;align-items:center;gap:.25rem;">

                                                {{-- SSL (only for separate sub‑domain) --}}
                                                @if($site->integration_type !== 'inside_wordpress')
                                                    <button type="button"
                                                            class="btn btn-default generate_ssl_action"
                                                            data-site-id="{{ $site->id }}">
                                                        <span class="glyphicon glyphicon-lock"></span> SSL
                                                    </button>
                                                @endif

                                                {{-- Logs --}}
                                                <a  href="/wordpress_plus_laravel/logs/{{ $site->id }}"
                                                    class="btn btn-info">
                                                    <span class="glyphicon glyphicon-list-alt"></span> Logs
                                                </a>

                                                {{-- Delete --}}
                                                <form  action="/wordpress_plus_laravel/delete"
                                                       method="POST"
                                                       class="m-0 p-0 d-inline-flex align-items-center"
                                                       onsubmit="return confirm('Delete this integration? This action cannot be undone.');">
                                                    @csrf
                                                    <input type="hidden" name="site_id" value="{{ $site->id }}">
                                                    <button type="submit" id="delete_{{$site->name}}" class="btn btn-danger">
                                                        <span class="glyphicon glyphicon-trash" style="font-size:10px;"></span> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="panel-body text-center">
                            <p class="lead mb-0">No integrations yet — click “Create WordPress + Laravel Instance” to get started.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- scripts ------------------------------------------------------------ --}}
@push('scripts')
<script>
(function ($) {
    'use strict';

    function loader(on) {
        $('#loadMe').modal(on ? 'show' : 'hide');
    }

    /** SSL ----------------------------------------------------------- */
    $(document).on('click', '.generate_ssl_action', function () {
        if (!confirm('Generate a new SSL certificate? This may replace the current certificate.')) return;
        loader(true);
        $.post('/generate_ssl', {site_id: $(this).data('site-id')}, (res) => {
            loader(false);
            if (res.message) {
                $.notify({message: res.message}, {type: 'info', delay: 4000});
            } else {
                window.location.reload();
            }
        });
    });
})(jQuery);
</script>
@endpush
@endsection
