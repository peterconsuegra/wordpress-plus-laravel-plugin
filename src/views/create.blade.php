@extends('layout')

@push('head')
<style>
  /* Spinner via pseudo-element; never touches the button label */
  #create_button[data-loading="1"]{ position:relative; }
  #create_button[data-loading="1"]::after{
    content:"";
    display:inline-block;
    width:.9rem;height:.9rem;margin-left:.5rem;
    border:.15rem solid currentColor;border-right-color:transparent;
    border-radius:50%;
    vertical-align:-2px;
    animation:pete-spin .6s linear infinite;
  }
  @keyframes pete-spin { to { transform: rotate(360deg); } }

  /* Pete button disabled state (matches sites/create.blade.php) */
  .btn-pete:disabled,
  .btn-pete[disabled]{
    opacity:1 !important;
    cursor:not-allowed;
    color:#fff !important;
    background-image:linear-gradient(135deg,var(--pete-blue) 0%,var(--pete-green) 100%) !important;
    box-shadow:0 3px 8px rgba(0,0,0,.2) !important;
    pointer-events:none; /* block hover/focus/active */
  }
  .btn-pete:disabled:hover,
  .btn-pete:disabled:focus,
  .btn-pete:disabled:active,
  .btn-pete[disabled]:hover,
  .btn-pete[disabled]:focus,
  .btn-pete[disabled]:active{
    background-image:linear-gradient(135deg,var(--pete-blue) 0%,var(--pete-green) 100%) !important;
    color:#fff !important;
    box-shadow:0 3px 8px rgba(0,0,0,.2) !important;
    transform:none !important;
    text-decoration:none !important;
  }
  /* Skeleton styles */
  .skel-row{
    height:14px;border-radius:6px;
    background:linear-gradient(90deg,#e9ecef 25%,#f8f9fa 37%,#e9ecef 63%);
    background-size:400% 100%;
    animation:skel 1.2s ease-in-out infinite;
  }
  @keyframes skel{ 0%{background-position:100% 0} 100%{background-position:0 0} }
  .step-dot{ width:.75rem;height:.75rem;border-radius:50%;background-color:#ced4da;display:inline-block; }
  .step.active .step-dot{ background-color:#0d6efd; }
  .step-label{ font-size:.9rem }
  [v-cloak]{ display:none; }
</style>
@endpush

@section('content')
<div id="wp-laravel-create" class="container-fluid" v-cloak>

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

  {{-- Vue-driven error block (server/422 or client) --}}
  <div v-if="Object.keys(errors).length" class="alert alert-danger">
    <div class="fw-semibold mb-2">Please fix the following:</div>
    <ul class="mb-0">
      <li v-for="(msgs, field) in errors" :key="field">
        <span v-for="(m, i) in [].concat(msgs)" :key="i">@{{ m }}</span>
      </li>
    </ul>
  </div>

  {{-- form card --------------------------------------------------------- --}}
  <div class="row">
    <div class="col-lg-9 col-xl-8">
      <div class="panel position-relative">
        <div class="panel-heading d-flex justify-content-between align-items-center">
          <h3 class="mb-0 fs-5">Integration details</h3>
          <a href="{{ route('wpl.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to list
          </a>
        </div>

        <form
          action="{{ route('wpl.store') }}"
          id="SiteForm"
          method="POST"
          class="p-3 p-md-4"
          novalidate
          @submit.prevent="submit"
        >
          @csrf

          {{-- Action (New or Import) --}}
          <div class="mb-3">
            <label for="action_name-field" class="form-label">Action</label>
            <select class="form-select" id="action_name-field" name="action_name" v-model="form.action_name" required>
              <option value="">Select Action</option>
              <option value="new_wordpress_laravel">New</option>
              <option value="import_wordpress_laravel">Import</option>
            </select>
          </div>

          {{-- Laravel version (only for New) --}}
          <div class="mb-3" id="selected_version_div" v-show="showVersion">
            <label for="selected_version" class="form-label">Laravel version</label>
            <select class="form-select" id="selected_version" name="selected_version" v-model="form.selected_version">
              <option value="">Select Laravel version</option>
              <option value="10.*">10.*</option>
              <option value="11.*">11.*</option>
              <option value="12.*">12.*</option>
            </select>
          </div>

          {{-- Import from Git (only for Import) --}}
          <div id="import_git_block" class="mb-3" v-show="showImport">
            <div class="alert alert-info small">
              Import a Laravel instance from a public HTTPS or SSH git URL.
            </div>

            <div class="row g-3">
              <div class="col-md-8">
                <label for="wordpress_laravel_git-field" class="form-label">Repository URL</label>
                <input type="text"
                       id="wordpress_laravel_git-field"
                       name="wordpress_laravel_git"
                       v-model="form.wordpress_laravel_git"
                       :class="['form-control', gitUrlClass]"
                       placeholder="https://github.com/user/project.git">
              </div>
              <div class="col-md-4">
                <label for="wordpress_laravel_git_branch-field" class="form-label">Branch</label>
                <input type="text"
                       id="wordpress_laravel_git_branch-field"
                       name="wordpress_laravel_git_branch"
                       v-model="form.wordpress_laravel_git_branch"
                       class="form-control"
                       placeholder="main">
              </div>
            </div>
          </div>

          {{-- Integration type --}}
          <div class="mb-3" id="integration_type_wrap" v-show="showCommon">
            <label for="integration_type-field" class="form-label">Laravel Sync Type</label>
            <select class="form-select" id="integration_type-field" name="integration_type" v-model="form.integration_type" @change="updateHints">
              <option value="">Select type</option>
              <option value="inside_wordpress">Same domain</option>
              <option value="separate_subdomain">Separate subdomain</option>
            </select>
            <div class="form-text">@{{ integrationHelp }}</div>
          </div>

          {{-- App name --}}
          <div class="mb-3" id="app_name_wrap" v-show="showCommon">
            <label for="wordpress_laravel_name-field" class="form-label">Laravel app name</label>
            <input type="text"
                   id="wordpress_laravel_name-field"
                   name="wordpress_laravel_name"
                   v-model="form.wordpress_laravel_name"
                   class="form-control"
                   placeholder="myapp">
            <div class="form-text">@{{ appNameHint }}</div>
          </div>

          {{-- Target WordPress site --}}
          <div class="mb-3" id="target_wrap" v-show="showCommon">
            <label for="wordpress_laravel_target-field" class="form-label">Target WordPress site</label>
            <select id="wordpress_laravel_target-field"
                    name="wordpress_laravel_target"
                    class="form-select"
                    v-model="form.wordpress_laravel_target"
                    :disabled="sitesLoading || sites.length === 0">
              <option value="">@{{ sitesLoading ? 'Loading sites…' : 'Select the WordPress instance to integrate' }}</option>
              <option v-for="s in sites" :key="s.id" :value="String(s.id)">@{{ s.url }}</option>
            </select>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button type="submit"
                    id="create_button"
                    class="btn btn-pete d-inline-flex align-items-center gap-2"
                    :disabled="submitting"
                    :data-loading="submitting ? 1 : null">
              <i class="bi bi-plus-lg" aria-hidden="true"></i>
              <span class="btn-text" v-text="submitting ? 'Creating…' : 'Create'"></span>
            </button>
            <a href="{{ route('wpl.index') }}" class="btn btn-outline-secondary">Cancel</a>

            <span id="creating_hint"
                  class="ms-2 text-muted small"
                  aria-live="polite"
                  v-show="submitting">
              <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              Provisioning…
            </span>
          </div>
        </form>

        {{-- Provisioning steps (skeleton) -------------------------------- --}}
        <div id="provision_skeleton" class="border-top px-3 px-md-4 py-3" v-show="submitting">
          <div class="d-flex align-items-center gap-3 mb-2">
            <strong class="me-1">Setting things up…</strong>
            <div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div>
          </div>

          <div class="vstack gap-2">
            <div class="d-flex align-items-center gap-2 step active">
              <span class="step-dot"></span>
              <div class="step-label">Creating Laravel app / files</div>
            </div>
            <div class="skel-row" style="width:55%"></div>

            <div class="d-flex align-items-center gap-2 step active">
              <span class="step-dot"></span>
              <div class="step-label">Linking with WordPress</div>
            </div>
            <div class="skel-row" style="width:68%"></div>

            <div class="d-flex align-items-center gap-2 step">
              <span class="step-dot"></span>
              <div class="step-label">Installing dependencies</div>
            </div>
            <div class="skel-row" style="width:62%"></div>

            <div class="d-flex align-items-center gap-2 step">
              <span class="step-dot"></span>
              <div class="step-label">Reloading web server</div>
            </div>
            <div class="skel-row" style="width:48%"></div>
          </div>
        </div>

        <div class="panel-footer small text-muted">
          WordPress Pete will provision the WordPress ↔ Laravel Sync and reload the web server automatically.
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
{{-- Vue 3 (only if not already loaded by layout) --}}
<script>
  (function ensureVue(){
    if(!window.Vue){
      var s=document.createElement('script');
      s.src='https://unpkg.com/vue@3/dist/vue.global.prod.js';
      document.head.appendChild(s);
    }
  })();
</script>

<script>
(function initVue(){
  function boot(){
    const { createApp, computed, onMounted, reactive, ref, watch } = Vue;

    createApp({
      setup(){
        const submitting   = ref(false);
        const sites        = ref([]);
        const sitesLoading = ref(false);
        const errors       = reactive({});

        // old() hydration from Blade
        const oldVals = {
          action_name:           @json(old('action_name')),
          selected_version:      @json(old('selected_version')),
          integration_type:      @json(old('integration_type')),
          wordpress_laravel_git: @json(old('wordpress_laravel_git')),
          wordpress_laravel_git_branch: @json(old('wordpress_laravel_git_branch')),
          wordpress_laravel_name: @json(old('wordpress_laravel_name')),
          wordpress_laravel_target: @json(old('wordpress_laravel_target')),
        };

        const form = reactive({
          action_name: oldVals.action_name || '',
          selected_version: oldVals.selected_version || '',
          integration_type: oldVals.integration_type || '',
          wordpress_laravel_git: oldVals.wordpress_laravel_git || '',
          wordpress_laravel_git_branch: oldVals.wordpress_laravel_git_branch || 'main',
          wordpress_laravel_name: oldVals.wordpress_laravel_name || '',
          wordpress_laravel_target: oldVals.wordpress_laravel_target ? String(oldVals.wordpress_laravel_target) : '',
        });

        const integrationHelp = ref('');
        const appNameHint     = ref('');

        const showCommon  = computed(()=> !!form.action_name);
        const showVersion = computed(()=> form.action_name === 'new_wordpress_laravel');
        const showImport  = computed(()=> form.action_name === 'import_wordpress_laravel');

        const gitUrlOk = computed(()=>{
          const v = (form.wordpress_laravel_git || '').trim();
          if(!v) return true; // neutral until user types
          return v.startsWith('https://') || v.startsWith('git@');
        });
        const gitUrlClass = computed(()=> gitUrlOk.value ? 'is-valid' : 'is-invalid');

        function updateHints(){
          if(form.integration_type === 'inside_wordpress'){
            integrationHelp.value = 'Laravel will live inside the same domain. Example URL: mywordpresssite.com/myapp';
            appNameHint.value     = 'This becomes the path segment (e.g., /myapp).';
          }else if(form.integration_type === 'separate_subdomain'){
            integrationHelp.value = 'Laravel will run on a subdomain. Example URL: myapp.mywordpresssite.com';
            appNameHint.value     = 'This becomes the subdomain (e.g., myapp.*).';
          }else{
            integrationHelp.value = '';
            appNameHint.value     = '';
          }
        }

        async function loadWordPressSites(){
          sitesLoading.value = true;
          try{
            const tokenEl = document.querySelector('#SiteForm input[name=_token]');
            const token   = tokenEl ? tokenEl.value : '';
            const res = await fetch('{{ route('sites.get.sites') }}', {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
              },
              body: JSON.stringify({ app_name: 'Wordpress+laravel' })
            });
            const data = await res.json();
            sites.value = Array.isArray(data) ? data : [];
          }catch(e){
            console.error('Failed to load sites', e);
            sites.value = [];
          }finally{
            sitesLoading.value = false;
          }
        }

        // Async submit (matches sites/create pattern)
        async function submit(){
          // clear previous errors
          for (const k of Object.keys(errors)) delete errors[k];

          // basic client checks
          if(!form.action_name){
            errors.action_name = ['Please select an action.'];
            return;
          }
          if(form.action_name === 'new_wordpress_laravel'){
            if(!form.selected_version) errors.selected_version = ['Please select a Laravel version.'];
          }
          if(form.action_name === 'import_wordpress_laravel'){
            if(!form.wordpress_laravel_git) errors.wordpress_laravel_git = ['Repository URL is required.'];
            if(!gitUrlOk.value) errors.wordpress_laravel_git = ['Use an https:// or git@ URL.'];
            if(!form.wordpress_laravel_git_branch) errors.wordpress_laravel_git_branch = ['Branch is required.'];
          }
          if(!form.integration_type) errors.integration_type = ['Please select a sync type.'];
          if(!form.wordpress_laravel_name) errors.wordpress_laravel_name = ['Please enter an app name.'];
          if(!form.wordpress_laravel_target) errors.wordpress_laravel_target = ['Please select a target WordPress site.'];

          if(Object.keys(errors).length){ return; }

          submitting.value = true;
          try{
            const token = document.querySelector('#SiteForm input[name=_token]')?.value || '';
            const res = await fetch(@json(route('wpl.store')), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
              },
              body: JSON.stringify({ ...form })
            });

            if (res.status === 422){
              const data = await res.json().catch(()=>({}));
              Object.assign(errors, data.errors || { form: [data.message || 'Validation failed.'] });
              submitting.value = false;
              return;
            }

            if (!res.ok){
              // Non-2xx (e.g., 400 for prod-only SSL, etc)
              let msg = 'Failed to create integration. Please try again.';
              try{
                const data = await res.json();
                if (data && data.message) msg = data.message;
              }catch(_){}
              window.toast && window.toast(msg, 'error');
              submitting.value = false;
              return;
            }

            // Success — controller redirects to logs, but fetch won’t follow.
            // Send user to the list (from there they can open logs).
            window.location.assign(@json(route('wpl.index')));
          }catch(err){
            console.error(err);
            window.toast && window.toast('Unexpected error. Please try again.', 'error');
            submitting.value = false;
          }
        }

        // React when user picks an action to reveal common fields & load sites
        watch(()=> form.action_name, (val)=>{
          if(val){ loadWordPressSites(); }
          updateHints();
        });

        watch(()=> form.integration_type, updateHints);

        onMounted(()=>{
          if(form.action_name){ loadWordPressSites(); }
          updateHints();

          // Reset UI if page is restored from bfcache
          window.addEventListener('pageshow', function (evt) {
            if (evt.persisted) {
              submitting.value = false;
            }
          });
        });

        return {
          form,
          submitting,
          sites,
          sitesLoading,
          errors,
          showCommon: computed(()=> showCommon.value),
          showVersion,
          showImport,
          gitUrlClass,
          integrationHelp,
          appNameHint,
          updateHints,
          submit,
        };
      }
    }).mount('#wp-laravel-create');
  }

  // If Vue was injected by CDN, wait until it’s loaded
  if(window.Vue) boot();
  else {
    const iv = setInterval(()=>{ if(window.Vue){ clearInterval(iv); boot(); } }, 25);
    setTimeout(()=> clearInterval(iv), 4000);
  }
})();
</script>
@endpush
