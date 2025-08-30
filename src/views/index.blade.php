@extends('layout')

@section('content')
<div class="container-fluid" id="wplApp" v-cloak>

    {{-- flash / updater placeholder --}}
    <div id="update_area_info"></div>

    {{-- hero ------------------------------------------------------------- --}}
    <div class="row align-items-center mb-5 g-4">
        <div class="col-md-6 text-center text-md-start">
            <img src="/pete.png" alt="WordPress Pete" class="img-fluid" style="max-height:200px">
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <a href="{{ route('wpl.create') }}" class="btn btn-pete btn-lg w-100">
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

    {{-- small helpers / loader styles ------------------------------------ --}}
    <style>
      [v-cloak]{ display:none; }
      .table-loading-overlay{
        position:absolute; inset:0;
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        background:linear-gradient(180deg, rgba(255,255,255,.85), rgba(255,255,255,.95));
        z-index: 2; border-radius:.5rem;
      }
      .skel-row{
        height: 18px; border-radius: 6px;
        background: linear-gradient(90deg, #e9ecef 25%, #f8f9fa 37%, #e9ecef 63%);
        background-size: 400% 100%; animation: skel 1.2s ease-in-out infinite;
      }
      @keyframes skel{ 0%{background-position:100% 0} 100%{background-position:0 0} }
    </style>

    {{-- integrations table ------------------------------------------------ --}}
    <div class="row">
        <div class="col-12">
            <div class="panel position-relative">

                {{-- Loading overlay (matches sites/backups pages) --}}
                <div v-if="loadingRows" class="table-loading-overlay text-center">
                  <div class="spinner-border mb-3" role="status" aria-hidden="true"></div>
                  <div class="text-muted">Loading integrations…</div>
                </div>

                <div class="panel-heading d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fs-5">My WordPress ↔ Laravel Syncs</h3>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Per-page selector (server-driven pagination UI) --}}
                        <form method="GET" action="{{ route('wpl.index') }}" class="d-inline-block">
                            <label for="per_page" class="form-label me-2 mb-0 small text-muted">Rows per page:</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm d-inline-block w-auto"
                                    onchange="this.form.submit()">
                                @foreach([5,10,20,50] as $size)
                                    <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <small id="wplTotalCount" class="text-muted" v-if="total">@{{ total }} total</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">ID</th>
                                <th>Project Name</th>
                                <th>URL</th>
                                <th class="text-center" width="70">SSL</th>
                                <th class="text-end" width="420">Actions</th>
                            </tr>
                        </thead>

                        {{-- Skeleton rows while loading --}}
                        <tbody v-if="loadingRows">
                          <tr v-for="n in 6" :key="'skel-'+n">
                            <td><div class="skel-row" style="width:40px"></div></td>
                            <td><div class="skel-row" style="width:40%"></div></td>
                            <td><div class="skel-row" style="width:70%"></div></td>
                            <td class="text-center">
                              <div class="d-inline-block" style="width:24px"><div class="skel-row"></div></div>
                            </td>
                            <td class="text-end">
                              <div class="d-inline-block" style="width:380px; max-width:100%">
                                <div class="skel-row" style="width:100%"></div>
                              </div>
                            </td>
                          </tr>
                        </tbody>

                        {{-- Real rows (painted after "load") --}}
                        <tbody v-else-if="rows.length">
                            <tr v-for="site in rows" :key="site.id">
                                <td class="text-muted">@{{ site.id }}</td>
                                <td class="fw-semibold">@{{ site.name }}</td>
                                <td>
                                    <a :href="`http://${site.url}`" target="_blank" rel="noopener">
                                        @{{ site.url }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <i v-if="site.ssl" class="bi bi-shield-check text-success" title="SSL enabled"></i>
                                    <i v-else class="bi bi-shield-x text-danger" title="SSL disabled"></i>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- SSL (hide for inside_wordpress) -->
                                        <button
                                            v-if="site.integration_type !== 'inside_wordpress'"
                                            class="btn btn-outline-secondary"
                                            :disabled="isBusy(site.id)"
                                            @click="generateSSL(site.id)"
                                            title="Generate SSL">
                                            <i class="bi bi-lock-fill me-1"></i>
                                            <span v-if="isBusy(site.id)">Generating…</span>
                                            <span v-else>Generate SSL</span>
                                        </button>

                                        <!-- Logs -->
                                        <a :href="`${logsBase}/${site.id}`"
                                           class="btn btn-info"
                                           title="View logs">
                                            <i class="bi bi-journal-text"></i> Logs
                                        </a>

                                        <!-- Delete -->
                                        <button type="button"
                                                class="btn btn-danger"
                                                :disabled="deletingId === site.id"
                                                @click="confirmDelete(site)">
                                            <span v-if="deletingId === site.id" class="spinner-border spinner-border-sm me-1"></span>
                                            <i v-else class="bi bi-trash me-1"></i>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                        {{-- Empty state --}}
                        <tbody v-else>
                            <tr>
                              <td colspan="5" class="text-center p-5">
                                <p class="lead mb-0">No integrations yet — click “Create WordPress ↔ Laravel Sync” to get started.</p>
                              </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="panel-footer" v-if="!loadingRows">
                        <div class="d-flex justify-content-center mt-3">
                            {{ $sites->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
const { createApp, nextTick } = Vue;

createApp({
  data() {
    return {
      // table state (async paint like sites/backups)
      rows: [],
      total: 0,
      loadingRows: true,
      busyIds: new Set(),
      deletingId: null,

      // routes
      generateSslUrl: @json(route('wpl.generate-ssl', [], false)),
      deleteUrl: @json(route('wpl.delete', [], false)),
      logsBase: @json(rtrim(route('wpl.logs', 0), '/0')),

      // bootstrap payload from server
      _bootstrapRows: @json($sitesPayload ?? []),
      _bootstrapTotal: {{ (int) ($sites->total() ?? 0) }},
    };
  },

  async mounted() {
    await nextTick();
    this.loadRows();
  },

  methods: {
    async loadRows() {
      this.loadingRows = true;
      try {
        // brief delay so the overlay/skeleton is noticeable
        await new Promise(r => setTimeout(r, 180));

        this.rows = (this._bootstrapRows || []).map(s => ({
          id: Number(s.id),
          name: String(s.name ?? ''),
          url: String(s.url ?? ''),
          ssl: !!s.ssl,
          integration_type: String(s.integration_type ?? ''),
        }));
        this.total = Number(this._bootstrapTotal) || this.rows.length || 0;

        const totalEl = document.getElementById('wplTotalCount');
        if (totalEl) totalEl.textContent = `${this.total} total`;
      } finally {
        this.loadingRows = false;
      }
    },

    isBusy(id) { return this.busyIds.has(Number(id)); },

    /* ------------ SSL ------------ */
    async generateSSL(id) {
      if (!id) return;
      if (!confirm('Generate a new SSL certificate for this integration?')) return;

      this.busyIds.add(Number(id));
      try {
        const res = await fetch(this.generateSslUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ site_id: id })
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || data.error) {
          window.toast?.(data.message || 'SSL generation failed.', 'error');
          return;
        }

        window.toast?.('SSL generation started.', 'success');

        // reflect in UI (optimistic)
        const idx = this.rows.findIndex(r => r.id === Number(id));
        if (idx !== -1) this.rows[idx].ssl = true;
      } catch {
        window.toast?.('Network error. Please try again.', 'error');
      } finally {
        this.busyIds.delete(Number(id));
      }
    },

    /* ------------ DELETE ------------ */
    async confirmDelete(site) {
      if (!confirm(`Delete "${site.name}"? This action cannot be undone.`)) return;

      this.deletingId = site.id;
      try {
        const res = await fetch(this.deleteUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ site_id: site.id })
        });

        if (!res.ok) throw new Error('Delete failed');

        // Optimistic removal
        this.rows = this.rows.filter(s => s.id !== site.id);
        this.total = Math.max(0, this.total - 1);

        const totalEl = document.getElementById('wplTotalCount');
        if (totalEl) totalEl.textContent = `${this.total} total`;

        window.toast?.('Integration deleted successfully.', 'success');
      } catch (e) {
        window.toast?.('Delete failed. Please try again.', 'error');
      } finally {
        this.deletingId = null;
      }
    },
  }
}).mount('#wplApp');
</script>
@endpush
