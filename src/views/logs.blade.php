@extends('layout')

@push('head')
<style>
  /* Bounded, scrollable container for big logs */
  .log-box{
    max-width: 1024px !important;
    max-height: 50vh !important;   /* change to 420px if you prefer a fixed height */
    overflow-y: auto !important;
    overflow-x: auto;
    background: #0b1020;           /* terminal-like background */
    color: #e6e6e6;
    border-radius: .375rem;
    border: 1px solid rgba(0,0,0,.08);
    display: block;
  }

  .panel{
    max-width: 1024px !important;
  }

  /* Preserve newlines; wrap super‑long tokens; monospace for readability */
  .terminal-output{
    margin: 0;
    padding: 1rem;
    white-space: pre-wrap;      /* keep newlines, allow wrapping */
    word-break: break-word;     /* break long “words” if needed */
    overflow-wrap: anywhere;    /* last‑resort breaks for giant tokens */
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    line-height: 1.45;
    font-size: .875rem;         /* small but readable */
  }
</style>
@endpush

@section('content')
<div class="container-fluid">

  {{-- hero ------------------------------------------------------------- --}}
  <div class="row align-items-center mb-4 g-3">
    <div class="col-md-5">
      
    </div>

    @php
      $integrationUrl = $site->integration_type === 'separate_subdomain'
        ? $site->wordpress_laravel_url
        : ($target_site->url . '/' . $site->name);
    @endphp

    <div class="col-md-7 d-flex gap-2 justify-content-md-end">
      <a href="{{ url('/wordpress_plus_laravel') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to list
      </a>
      <a href="http://{{ $target_site->url }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
        <i class="bi bi-wordpress"></i> Open WordPress
      </a>
      <a href="http://{{ $integrationUrl }}" target="_blank" rel="noopener" class="btn btn-pete">
        <i class="bi bi-box-arrow-up-right"></i> Open Laravel Sync
      </a>
    </div>
  </div>

  @if($site->action_name == "New")

  {{-- details ----------------------------------------------------------- --}}
  <div class="row mb-4">
    <div class="col-12">
      <div class="panel">
        <div class="panel-heading">
          <h3 class="mb-0 fs-5">Sync Details</h3>
        </div>
        <div class="p-3">
          <div class="row gy-2 small">
            <div class="col-md-6">
            
              <div class="text-uppercase text-muted">Laravel Sync URL</div>
              <div>
                <a href="http://{{ $integrationUrl }}" target="_blank" rel="noopener">
                  {{ $integrationUrl }}
                </a>
              </div>

            </div>
            <div class="col-md-6">
              <div class="text-uppercase text-muted">Laravel Sync</div>
              <div>{{ $site->integration_type === 'inside_wordpress' ? 'Same Domain' : 'Separate Subdomain' }}</div>
            </div>
            <div class="col-md-6">
              <div class="text-uppercase text-muted">Laravel Version</div>
              <div>{{ $site->laravel_version ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-uppercase text-muted">Project Name</div>
                <div>{{ $site->name }}</div>

            </div>
          </div>
        </div>
       
      </div>
    </div>
  </div>

  @else


  <div class="row mb-4">
    <div class="col-12">
      <div class="panel">
        <div class="panel-heading">
          <h3 class="mb-0 fs-5">Laravel Sync Details</h3>
        </div>
        <div class="p-3">
          <div class="row gy-2 small">
            <div class="col-md-6">
              <div class="text-uppercase text-muted">Laravel Sync URL</div>
              <div><strong><a href="http://{{ $site->url }}">{{ $site->url }}</a></strong></div>
            </div>
            <div class="col-md-6">
              <div class="text-uppercase text-muted">Laravel Sync</div>
              <div>{{ $site->integration_type === 'inside_wordpress' ? 'Same Domain' : 'Separate Subdomain' }}</div>
            </div>
            <div class="col-md-6">
              <div class="text-uppercase text-muted">Branch</div>
              <div>{{ $site->wordpress_laravel_git_branch ?? '—' }}</div>
            </div>
            <div class="col-md-6">
              <div class="text-uppercase text-muted">GIT URL</div>
              <div>{{$site->wordpress_laravel_git}}</div>
            </div>
            <div class="col-md-6">
              <div class="text-uppercase text-muted">Project</div>
              <div>{{ $site->name }}</div>
            </div>
          </div>
        </div>
       
      </div>
    </div>
  </div>

  @endif

  @php
    // Strip ANSI color codes from output
    $clean = preg_replace('/\x1B\[[0-9;]*[A-Za-z]/', '', $site->output ?? '');
  @endphp

  {{-- terminal output --------------------------------------------------- --}}
  <div class="row g-4">
    <div class="col-12">
      <div class="panel">
        <div class="panel-heading d-flex justify-content-between align-items-center">
          <h3 class="mb-0 fs-6"><i class="bi bi-terminal me-1"></i> Terminal output</h3>
        </div>

        <div class="log-box" data-autoscroll="end">
          {{-- IMPORTANT: render plain text; no nl2br inside <pre> --}}
          <pre class="terminal-output">{{ $clean }}</pre>
        </div>
      </div>
    </div>

    {{-- Apache error.log ------------------------------------------------- --}}
    <div class="col-12">
      <div class="panel">
        <div class="panel-heading d-flex justify-content-between align-items-center">
          <h3 class="mb-0 fs-6"><i class="bi bi-exclamation-triangle me-1"></i> Apache error.log</h3>
          <small class="text-muted">{{ $web_server_error_file }}</small>
        </div>
        <pre class="mb-0 small p-3 bg-light" style="max-height:420px; overflow:auto">{{ $web_server_error_file_content }}</pre>
      </div>
    </div>

    {{-- Apache access.log ------------------------------------------------ --}}
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
  // Auto-scroll any container marked with data-autoscroll="end"
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-autoscroll="end"]').forEach(function (el) {
      el.scrollTop = el.scrollHeight;
    });
  });
</script>
@endpush
