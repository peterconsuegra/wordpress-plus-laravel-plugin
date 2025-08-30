<?php

declare(strict_types=1);

namespace Pete\WordPressPlusLaravel\Http;

use App\Services\OServer;
use App\Services\PeteOption;
use App\Services\PeteService;
use Illuminate\Contracts\View\View as View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Pete\WordPressPlusLaravel\Models\Site;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

/**
 * Controller for WordPress ↔ Laravel integrations (WordPress Plus Laravel).
 */
class WordPressPlusLaravelController extends Controller
{
    /**
     * Maximum number of bytes to read from each log file (1 MB).
     */
    private const LOG_MAX_BYTES = 1_048_576;

    /**
     * Forbidden app names when using "inside_wordpress" integration.
     */
    private const FORBIDDEN_NAMES = ['cache', 'ozone-speed', 'wp-admin', 'wp-content', 'wp-includes'];

    /**
     * Validation regex for site/app names.
     */
    private const NAME_REGEX = '/^[a-zA-Z0-9-_]+$/';

    /**
     * Default pagination per page for admin and non-admin users.
     */
    private const PER_PAGE_ADMIN = 50;
    private const PER_PAGE_USER  = 10;

    private PeteService $pete;

    public function __construct(PeteService $pete)
    {
        $this->middleware('auth');
        $this->pete = $pete;
    }

    /**
     * Show creation form.
     */
    public function create(Request $request): View
    {
        $viewsw       = '/wordpress-plus-laravel';
        $currentUser  = Auth::user();

        return view('wordpress-plus-laravel-plugin::create', compact('currentUser', 'viewsw'));
    }

    public function index(Request $request): View
	{
		$currentUser = Auth::user();
		$viewsw      = '/wordpress-plus-laravel';

		// Base query
		$query = Site::query()
			->where('app_name', 'WordPress+Laravel')
			->orderByDesc('id');

		// Only admins can see all sites
		if (! Gate::allows('user.admin')) {
			$query->where('user_id', $currentUser->id);
		}

		// Per-page from request with sane bounds (1..50). Default: 10
		$perPage = (int) $request->integer('per_page', 10);
		$perPage = max(1, min($perPage, 50));

		$sites = $query->paginate($perPage)->withQueryString();

        $sitesPayload = $sites->getCollection()->map(function (Site $s) {
			return [
				'id'   => (int) $s->id,
				'name' => (string) $s->name,
				'url'  => (string) $s->url,
				'ssl'  => (bool) $s->ssl,
			];
		})->values();

        return view('wordpress-plus-laravel-plugin::index', compact('sites', 'currentUser', 'viewsw','sitesPayload'));
	}

    /**
     * Create/import a new integration.
     */
    public function store(Request $request): RedirectResponse
    {
        $user         = Auth::user();
        $peteOptions  = app(PeteOption::class);
        $fields       = $request->all();

        $site                          = new Site();
        $site->output                  = '';
        $site->user_id                 = (int) $user->id;
        $site->app_name                = 'WordPress+Laravel';
        $site->action_name             = (string) $request->input('action_name');
        $site->laravel_version         = (string) $request->input('selected_version');
        $site->wordpress_laravel_target_id   = $request->input('wordpress_laravel_target');
        $site->wordpress_laravel_git_branch  = $request->input('wordpress_laravel_git_branch');
        $site->wordpress_laravel_git         = $request->input('wordpress_laravel_git');
        $site->wordpress_laravel_name        = (string) $request->input('wordpress_laravel_name');
        $site->name                           = $site->wordpress_laravel_name;
        $site->integration_type               = (string) $request->input('integration_type');

        if (isset($site->wordpress_laravel_target_id)) {
            // Expecting model method provided by the package:
            $site->set_wordpress_laravel_url($site->wordpress_laravel_target_id);
        }

        // Enrich fields for validation rules that need "url" and normalized "name"
        $fields['url']            = $site->url ?? null;
        $fields['name']           = $site->name;
        $fields['laravel_version'] = $site->laravel_version;

        // PHP version checks for selected Laravel version (only relevant for "new")
        if ($site->action_name === 'new_wordpress_laravel') {
            [$phpMajor, $phpMinor] = $this->phpVersionParts();

            if ($site->laravel_version === '10.*' && ($phpMajor < 8 || ($phpMajor === 8 && $phpMinor < 1))) {
                return redirect()
                    ->route('wpl.create')
                    ->withErrors('Unable to create WordPress + Laravel 10 integration: PHP 8.1+ is required.')
                    ->withInput();
            }

            if (($site->laravel_version === '11.*' || $site->laravel_version === '12.*')
                && ($phpMajor < 8 || ($phpMajor === 8 && $phpMinor < 2))) {
                return redirect()
                    ->route('wpl.create')
                    ->withErrors('Unable to create WordPress + Laravel 11/12 integration: PHP 8.2+ is required.')
                    ->withInput();
            }
        }

        // "inside_wordpress" safety checks
        if ($site->integration_type === 'inside_wordpress') {
            if (\in_array($site->action_name, self::FORBIDDEN_NAMES, true)) {
                return redirect()
                    ->route('wpl.create')
                    ->withErrors('Forbidden project name.')
                    ->withInput();
            }
        }

        // Validation rules depend on action
        $rules = [];
        if ($site->action_name === 'new_wordpress_laravel') {
            $rules = [
                'name'                   => ['required', "regex:" . self::NAME_REGEX, 'unique:sites'],
                'wordpress_laravel_name' => ['required', "regex:" . self::NAME_REGEX],
                'integration_type'       => ['required'],
                'wordpress_laravel_target' => ['required'],
                'laravel_version'        => ['required'],
                'url'                    => ['required', 'unique:sites'],
            ];
        } elseif ($site->action_name === 'import_wordpress_laravel') {
            $rules = [
                'name'                         => ['required', "regex:" . self::NAME_REGEX, 'unique:sites'],
                'wordpress_laravel_git'        => ['required', 'string'],
                'integration_type'             => ['required'],
                'wordpress_laravel_git_branch' => ['required', 'string'],
                'wordpress_laravel_name'       => ['required', "regex:" . self::NAME_REGEX],
                'wordpress_laravel_target'     => ['required'],
                'url'                          => ['required', 'unique:sites'],
            ];
        }

        $validator = Validator::make($fields, $rules);

        if ($validator->fails()) {
            Log::info('wordpressLaravel validation failed', ['errors' => $validator->errors()->all()]);

            if (\in_array($site->action_name, ['new_wordpress_laravel', 'import_wordpress_laravel'], true)) {
                return redirect()
                    ->route('wpl.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Provisioning
        $site->create_wordpress_laravel();
        OServer::reload_server();

        return redirect()->route('wpl.logs', ['id' => $site->id]);
    }

    /**
     * Show integration logs (web server access/error for this site).
     */
    public function logs(Request $request, int $id): View
    {
        $currentUser = Auth::user();

        /** @var Site $site */
        $site = Site::findOrFail($id);

        // Site ownership: only site authors (user_id) OR admins
		if (! \Gate::allows('sites.manage', $site)) {
			abort(403, 'You are not authorized to manage this site.');
		}

        /** @var Site $targetSite */
        $targetSite = Site::findOrFail((int) $site->wordpress_laravel_target_id);

        $appRoot = app(PeteOption::class)->get_meta_value('app_root');
        $viewsw  = '/wordpress-plus-laravel';

        $paths = [
            'web_server_error_file'  => "{$appRoot}/wwwlog/{$site->name}/error.log",
            'web_server_access_file' => "{$appRoot}/wwwlog/{$site->name}/access.log",
        ];

        $logs = [
            'web_server_error_file_content'  => $this->readTail($paths['web_server_error_file']),
            'web_server_access_file_content' => $this->readTail($paths['web_server_access_file']),
        ];

        return view(
            'wordpress-plus-laravel-plugin::logs',
            array_merge(
                [
                    'site'         => $site,
                    'viewsw'       => $viewsw,
                    'currentUser' => $currentUser,
                    'target_site'  => $targetSite,
                ],
                $paths,
                $logs
            )
        );
    }

    /**
     * Delete an existing integration.
     */
    public function delete(Request $request): RedirectResponse
    {
        Log::info('Enter in delete WordPressPlusLaravelController');

        $user = Auth::user();

        /** @var Site $site */
        $site = Site::findOrFail((int) $request->input('site_id'));

        // Site ownership: only site authors (user_id) OR admins
		if (! \Gate::allows('sites.manage', $site)) {
			abort(403, 'You are not authorized to manage this site.');
		}

        $site->delete();
        OServer::reload_server();

        return redirect()->route('wpl.index');
    }

    /**
     * Generate SSL for an integration (production only).
     */
    public function generateSsl(Request $request): Response
    {
        $peteOptions = app(PeteOption::class);

        if ($peteOptions->get_meta_value('environment') !== 'production') {
            return response()->json([
                'error'   => true,
                'message' => 'This feature is only available in production environment',
            ], 400);
        }

        $currentUser = Auth::user();

        /** @var Site $site */
        $site = Site::findOrFail((int) $request->input('site_id'));

        if (! \Gate::allows('sites.manage', $site)) {
			return response()->json([
				'error'   => true,
				'message' => 'You are not authorized to manage this site.',
			], 403);
		}

        $site->ssl = true;
        $site->save();

        // Expecting a model method to actually trigger certbot/ACME flow:
        $site->generate_ssl((string) $currentUser->email);

        return response()->json(['ok' => true, 'site_id' => $site->id]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /**
     * Parse the current PHP version into [major, minor].
     *
     * @return array{0:int,1:int}
     */
    private function phpVersionParts(): array
    {
        $phpVersion = \phpversion() ?: '0.0.0';
        $parts      = \explode('.', $phpVersion);

        $major = (int) ($parts[0] ?? 0);
        $minor = (int) ($parts[1] ?? 0);

        return [$major, $minor];
    }

    /**
     * Read up to LOG_MAX_BYTES from the end of a file. Returns a friendly message if not readable.
     */
    private function readTail(?string $path): string
    {
        if (!$path || !\is_readable($path)) {
            return 'file not found';
        }

        $size = \filesize($path);
        if ($size === false) {
            return 'file not found';
        }

        $start = ($size > self::LOG_MAX_BYTES) ? ($size - self::LOG_MAX_BYTES) : 0;

        $fh = \fopen($path, 'rb');
        if ($fh === false) {
            return 'file not found';
        }

        try {
            if ($start > 0) {
                \fseek($fh, $start);
            }
            $data = \stream_get_contents($fh);

            return $data === false ? '' : $data;
        } finally {
            \fclose($fh);
        }
    }
}
