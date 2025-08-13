@extends('layout')

@section('content')
<div class="container-fluid">

  {{-- hero ------------------------------------------------------------- --}}
  <div class="row align-items-center mb-4 g-3">
    <div class="col-md-7">
      <h2 class="mb-1">Integration Logs</h2>
      <p class="text-muted mb-0">for <strong><a href="http://{{ $site->url }}">{{ $site->url }}</strong></a></p>
    </div>

    @php
      $integrationUrl = $site->integration_type === 'separate_subdomain'
        ? $site->wordpress_laravel_url
        : ($target_site->url . '/' . $site->name);
    @endphp

    <div class="col-md-5 d-flex gap-2 justify-content-md-end">
      <a href="{{ url('/wordpress_plus_laravel') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to list
      </a>
      <a href="http://{{ $target_site->url }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
        <i class="bi bi-wordpress"></i> Open WordPress
      </a>
      <a href="http://{{ $integrationUrl }}" target="_blank" rel="noopener" class="btn btn-pete">
        <i class="bi bi-box-arrow-up-right"></i> Open Integration
      </a>
    </div>
  </div>

  {{-- details ---------------------------------------------------------- --}}
  <div class="row mb-4">
    <div class="col-12">
      <div class="panel">
        <div class="panel-heading">
          <h3 class="mb-0 fs-5">Details</h3>
        </div>
        <div class="p-3">
          <div class="row gy-2 small">
            <div class="col-md-3">
              <div class="text-uppercase text-muted">Project</div>
              <div class="fw-semibold">{{ $site->name }}</div>
            </div>
            <div class="col-md-3">
              <div class="text-uppercase text-muted">Integration</div>
              <div>{{ $site->integration_type === 'inside_wordpress' ? 'Same Domain' : 'Separate Subdomain' }}</div>
            </div>
            <div class="col-md-3">
              <div class="text-uppercase text-muted">Laravel</div>
              <div>{{ $site->laravel_version ?? '—' }}</div>
            </div>
            <div class="col-md-3">
              <div class="text-uppercase text-muted">Integration URL</div>
              <div>
                <a href="http://{{ $integrationUrl }}" target="_blank" rel="noopener">
                  {{ $integrationUrl }}
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-footer small text-muted">
          Paths below are truncated to the most relevant files.
        </div>
      </div>
    </div>
  </div>

  {{-- terminal output -------------------------------------------------- --}}
  <div class="row g-4">
    <div class="col-12">
      <div class="panel">
        <div class="panel-heading d-flex justify-content-between align-items-center">
          <h3 class="mb-0 fs-6"><i class="bi bi-terminal me-1"></i> Terminal output</h3>
        </div>
        <pre class="mb-0 small p-3 bg-light" style="max-height:420px; overflow:auto" data-autoscroll="end">{{ $site->output }}</pre>
      </div>
    </div>

    {{-- Apache error.log ----------------------------------------------- --}}
    <div class="col-12">
      <div class="panel">
        <div class="panel-heading d-flex justify-content-between align-items-center">
          <h3 class="mb-0 fs-6"><i class="bi bi-exclamation-triangle me-1"></i> Apache error.log</h3>
          <small class="text-muted">{{ $web_server_error_file }}</small>
        </div>
        <pre class="mb-0 small p-3 bg-light" style="max-height:420px; overflow:auto">{{ $web_server_error_file_content }}</pre>
      </div>
    </div>

    {{-- Apache access.log ---------------------------------------------- --}}
    <div class="col-12">
      <div class="panel">
        <div class="panel-heading d-flex justify-content-between align-items-center">
          <h3 class="mb-0 fs-6"><i class="bi bi-list-check me-1"></i> Apache access.log</h3>
          <small class="text-muted">{{ $web_server_access_file }}</small>
        </div>
        <pre class="mb-0 small p-3 bg-light" style="max-height:420px; overflow:auto">{{ $web_server_access_file_content }}</pre>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  // auto-scroll the "Terminal output" box to the end
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-autoscroll="end"]').forEach(function (el) {
      el.scrollTop = el.scrollHeight;
    });
  });
</script>
@endpush
