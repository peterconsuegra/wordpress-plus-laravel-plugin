@extends('layout')

@section('content')
<div class="container-fluid">

    {{-- flash / updater placeholder --}}
    <div id="update_area_info"></div>

    {{-- hero ------------------------------------------------------------- --}}
    <div class="row align-items-center mb-5 g-4">
        <div class="col-md-6 text-center text-md-start">
            <img src="/pete.png" alt="WordPress Pete" class="img-fluid" style="max-height:200px">
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <a href="{{ url('/wordpress_plus_laravel/create') }}" class="btn btn-pete btn-lg w-100">
                <i class="bi bi-plus-lg me-1"></i>Create WordPress ↔ Laravel Sync
            </a>
            <p class="text-muted mb-0 mt-2">
                Turn WordPress into a full marketing engine while Laravel powers your custom features.
            </p>
        </div>
    </div>

    {{-- flash & validation ----------------------------------------------- --}}
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">Please fix the following:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- integrations table ------------------------------------------------ --}}
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="panel-heading d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fs-5">My WordPress ↔ Laravel Syncs</h3>

                    @if(isset($sites) && method_exists($sites,'total'))
                        <small class="text-muted">{{ $sites->total() }} total</small>
                    @elseif(isset($sites) && is_countable($sites))
                        <small class="text-muted">{{ count($sites) }} total</small>
                    @endif
                </div>

                <div class="table-responsive">
                    @php
                        $hasItems = isset($sites) && (
                            (method_exists($sites,'count') && $sites->count()) ||
                            (is_countable($sites) && count($sites))
                        );
                    @endphp

                    @if($hasItems)
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Project Name</th>
                                    <th>URL</th>
                                    <th class="text-center" width="70">SSL</th>
                                    <th class="text-end" width="320">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sites as $site)
                                    <tr>
                                        <td class="text-muted">{{ $site->id }}</td>
                                        <td class="fw-semibold">{{ $site->name }}</td>
                                        <td>
                                            <a href="http://{{ $site->url }}" target="_blank" rel="noopener">
                                                {{ $site->url }}
                                            </a>
                                        </td>
                                        <td>
                                           @if($site->ssl)
                                                <i class="bi bi-shield-check text-success" title="SSL enabled"></i>
                                            @else
                                                <i class="bi bi-shield-x text-danger" title="SSL disabled"></i>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">

                                                {{-- SSL (only for separate sub-domain) --}}
                                                @if($site->integration_type !== 'inside_wordpress')
                                                    <button type="button"
                                                            class="btn btn-outline-secondary generate_ssl_action"
                                                            data-site-id="{{ $site->id }}"
                                                            title="Generate SSL">
                                                        <i class="bi bi-lock"></i> SSL
                                                    </button>
                                                @endif

                                                {{-- Logs --}}
                                                <a href="/wordpress_plus_laravel/logs/{{ $site->id }}"
                                                   class="btn btn-info"
                                                   title="View logs">
                                                    <i class="bi bi-journal-text"></i> Sync Details
                                                </a>

                                                {{-- Delete --}}
                                                <form action="/wordpress_plus_laravel/delete"
                                                      method="POST"
                                                      class="d-inline-block"
                                                      onsubmit="return confirm('Delete this integration? This action cannot be undone.');">
                                                    @csrf
                                                    <input type="hidden" name="site_id" value="{{ $site->id }}">
                                                    <button type="submit" class="btn btn-danger" title="Delete integration">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="panel-footer">
                            <div class="d-flex justify-content-center">
                                @if(method_exists($sites,'links'))
                                    {{ $sites->links() }}
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <p class="lead mb-0">No integrations yet — click “Create WordPress + Laravel Instance” to get started.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    // Always send the token with Ajax
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Loader helpers (match sites/index)
    const loader = {
        show(){ new bootstrap.Modal('#loadMe', {backdrop:'static', keyboard:false}).show(); },
        hide(){ const m = bootstrap.Modal.getInstance(document.getElementById('loadMe')); if(m) m.hide(); }
    };

    // SSL generation (reuses app's /generate_ssl endpoint)
    $(document).on('click', '.generate_ssl_action', function () {
        if (!confirm('Generate a new SSL certificate? This may replace the current certificate.')) return;

        loader.show();
        $.post('/generate_ssl', { site_id: $(this).data('site-id') })
            .done(res => {
                loader.hide();
                if (res && res.message) {
                    alert(res.message);
                } else {
                    location.reload();
                }
            })
            .fail(() => {
                loader.hide();
                alert('SSL generation failed.');
            });
    });

})(jQuery);
</script>
@endpush
