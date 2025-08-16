@extends('layout')

@section('content')
<div class="container-fluid">

  {{-- hero ------------------------------------------------------------- --}}
  <div class="row align-items-center mb-5 g-4">
    <div class="col-md-6 text-center text-md-start">
      <img src="/pete.png" alt="WordPress Pete" class="img-fluid" style="max-height:200px">
    </div>
    <div class="col-md-6 d-flex flex-column justify-content-center">
      <h2 class="mb-1">Create WordPress ↔ Laravel Sync</h2>
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

  {{-- form card --------------------------------------------------------- --}}
  <div class="row">
    <div class="col-lg-9 col-xl-8">
      <div class="panel">
        <div class="panel-heading d-flex justify-content-between align-items-center">
          <h3 class="mb-0 fs-5">Integration details</h3>
          <a href="{{ url('/wordpress_plus_laravel') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to list
          </a>
        </div>

        <form action="/wordpress_plus_laravel/store" id="SiteForm" method="POST" class="p-3 p-md-4" novalidate>
          @csrf

          {{-- Action (New or Import) --}}
          <div class="mb-3">
            <label for="action_name-field" class="form-label">Action</label>
            <select class="form-select" id="action_name-field" name="action_name" required>
              <option value="">Select Action</option>
              <option value="new_wordpress_laravel" {{ old('action_name')==='new_wordpress_laravel' ? 'selected' : '' }}>New</option>
              <option value="import_wordpress_laravel" {{ old('action_name')==='import_wordpress_laravel' ? 'selected' : '' }}>Import</option>
            </select>
          </div>

          {{-- Laravel version (only for New) --}}
          <div class="mb-3 d-none" id="selected_version_div">
            <label for="selected_version" class="form-label">Laravel version</label>
            <select class="form-select" id="selected_version" name="selected_version">
              <option value="">Select Laravel version</option>
              <option value="10.*" {{ old('selected_version')==='10.*' ? 'selected' : '' }}>10.*</option>
              <option value="11.*" {{ old('selected_version')==='11.*' ? 'selected' : '' }}>11.*</option>
              <option value="12.*" {{ old('selected_version')==='12.*' ? 'selected' : '' }}>12.*</option>
            </select>
          </div>

          {{-- Import from Git (only for Import) --}}
          <div id="import_git_block" class="d-none">
            <div class="alert alert-info small">
              Import a Laravel instance from a public HTTPS or SSH git URL.
            </div>

            <div class="row g-3">
              <div class="col-md-8">
                <label for="wordpress_laravel_git-field" class="form-label">Repository URL</label>
                <input type="text"
                       id="wordpress_laravel_git-field"
                       name="wordpress_laravel_git"
                       value="{{ old('wordpress_laravel_git') }}"
                       class="form-control"
                       placeholder="https://github.com/user/project.git">
              </div>
              <div class="col-md-4">
                <label for="wordpress_laravel_git_branch-field" class="form-label">Branch</label>
                <input type="text"
                       id="wordpress_laravel_git_branch-field"
                       name="wordpress_laravel_git_branch"
                       value="{{ old('wordpress_laravel_git_branch') }}"
                       class="form-control"
                       placeholder="main">
              </div>
            </div>
          </div>

          {{-- Integration type --}}
          <div class="mb-3 d-none" id="integration_type_wrap">
            <label for="integration_type-field" class="form-label">Laravel Sync Type</label>
            <select class="form-select" id="integration_type-field" name="integration_type">
              <option value="">Select type</option>
              <option value="inside_wordpress" {{ old('integration_type')==='inside_wordpress' ? 'selected' : '' }}>Same domain</option>
              <option value="separate_subdomain" {{ old('integration_type')==='separate_subdomain' ? 'selected' : '' }}>Separate subdomain</option>
            </select>
            <div class="form-text" id="integration_help"></div>
          </div>

          {{-- App name --}}
          <div class="mb-3 d-none" id="app_name_wrap">
            <label for="wordpress_laravel_name-field" class="form-label">Laravel app name</label>
            <input type="text"
                   id="wordpress_laravel_name-field"
                   name="wordpress_laravel_name"
                   value="{{ old('wordpress_laravel_name') }}"
                   class="form-control"
                   placeholder="myapp">
            <div class="form-text" id="app_name_hint"></div>
          </div>

          {{-- Target WordPress site --}}
          <div class="mb-3 d-none" id="target_wrap">
            <label for="wordpress_laravel_target-field" class="form-label">Target WordPress site</label>
            <select id="wordpress_laravel_target-field" name="wordpress_laravel_target" class="form-select">
              <option value="">Loading sites…</option>
            </select>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" id="create_button" class="btn btn-pete">
              <i class="bi bi-plus-lg me-1"></i>Create
            </button>
            <a href="{{ url('/wordpress_plus_laravel') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>

        <div class="panel-footer small text-muted">
          WordPress Pete will provision the WordPress ↔ Laravel Sync and reload the web server automatically.
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

  // handy toggles
  function show(el){ el.classList.remove('d-none'); }
  function hide(el){ el.classList.add('d-none'); }

  const actionSel     = document.getElementById('action_name-field');
  const versionWrap   = document.getElementById('selected_version_div');
  const importWrap    = document.getElementById('import_git_block');
  const typeWrap      = document.getElementById('integration_type_wrap');
  const appNameWrap   = document.getElementById('app_name_wrap');
  const targetWrap    = document.getElementById('target_wrap');
  const typeSel       = document.getElementById('integration_type-field');
  const appNameInput  = document.getElementById('wordpress_laravel_name-field');
  const appNameHint   = document.getElementById('app_name_hint');
  const integrationHelp = document.getElementById('integration_help');
  const targetSel     = document.getElementById('wordpress_laravel_target-field');

  // Load WP sites into the select
  function loadWordPressSites() {
    // loader spot in sidebar (kept from layout)
    $('#loading_area').html('<div class="small text-muted p-2">Loading WordPress sites…</div>');
    $.ajax({
      url: "/sites/get_sites",
      type: "GET",
      dataType: "json",
      data: { app_name: "Wordpress+laravel" },
    }).done(function(data){
      $('#loading_area').empty();
      targetSel.innerHTML = '<option value="">Select the WordPress instance to integrate</option>';
      (data || []).forEach(function(site){
        const opt = document.createElement('option');
        opt.value = site.id;
        opt.textContent = site.url;
        if ("{{ old('wordpress_laravel_target') }}" === String(site.id)) opt.selected = true;
        targetSel.appendChild(opt);
      });
    }).fail(function(){
      $('#loading_area').html('<div class="text-danger small p-2">Failed to load sites.</div>');
      targetSel.innerHTML = '<option value="">Error loading sites</option>';
    });
  }

  // Update hints based on integration type
  function updateIntegrationHints(){
    const t = typeSel.value;
    if (!t) {
      integrationHelp.textContent = '';
      appNameHint.textContent = '';
      return;
    }
    if (t === 'inside_wordpress') {
      integrationHelp.textContent = 'Laravel will live inside the same domain. Example URL: mywordpresssite.com/myapp';
      appNameHint.textContent = 'This becomes the path segment (e.g., /myapp).';
    } else {
      integrationHelp.textContent = 'Laravel will run on a subdomain. Example URL: myapp.mywordpresssite.com';
      appNameHint.textContent = 'This becomes the subdomain (e.g., myapp.*).';
    }
  }

  // Toggle fields based on action
  function applyActionLogic() {
    const action = actionSel.value;
    hide(versionWrap);
    hide(importWrap);
    hide(typeWrap);
    hide(appNameWrap);
    hide(targetWrap);

    if (!action) return;

    // Shared fields
    show(typeWrap);
    show(appNameWrap);
    show(targetWrap);
    loadWordPressSites();

    if (action === 'new_wordpress_laravel') {
      show(versionWrap);
    } else if (action === 'import_wordpress_laravel') {
      show(importWrap);
    }
    updateIntegrationHints();
  }

  // Validate git URL styling (purely visual)
  $('#wordpress_laravel_git-field').on('keyup', function(){
    const v = $(this).val();
    const ok = v.startsWith('https://') || v.startsWith('git@');
    $(this).toggleClass('is-valid', ok).toggleClass('is-invalid', !ok && v.length > 0);
  });

  // Events
  actionSel.addEventListener('change', applyActionLogic);
  typeSel.addEventListener('change', updateIntegrationHints);

  // Initialize from old() values
  applyActionLogic();

  // Submit: disable button + show loader modal from layout
  const form = document.getElementById('SiteForm');
  const btn  = document.getElementById('create_button');
  form.addEventListener('submit', function(e){
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating…';
    if (typeof activate_loader === 'function') activate_loader();
  });
})(jQuery);
</script>
@endpush
