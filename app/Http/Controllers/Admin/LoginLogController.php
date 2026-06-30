<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoginLogController extends Controller
{
    /**
     * Tüm kullanıcıların son giriş özetlerini listeler.
     */
    /**
     * Tüm kullanıcıların son giriş özetlerini listeler.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Filtreleri Sıfırlama Mantığı
        if ($request->has('clear_filter')) {
            $request->session()->forget('login_activities_filters');
            return redirect()->route('logs.login.index');
        }

        // 2. Filtreleri Session'da Tutma / Geri Yükleme Mantığı
        if (empty($request->query())) {
            if ($request->session()->has('login_activities_filters')) {
                $filters = $request->session()->get('login_activities_filters');
                return redirect()->route('logs.login.index', $filters);
            }
        } else {
            $filters = $request->only(['tab', 'search', 'bolum_id', 'customer_id', 'sort', 'sort_by', 'sort_dir', 'page']);
            $request->session()->put('login_activities_filters', $filters);
        }

        $tab = $request->input('tab', 'personel');
        $search = $request->input('search');
        $bolumId = $request->input('bolum_id');
        $customerId = $request->input('customer_id');
        $sort = $request->input('sort', 'name');
        $sortBy = $request->input('sort_by');
        $sortDir = $request->input('sort_dir', 'asc');

        // Dashboard bazlı aktif görünüm
        $activeDashboard = session('active_dashboard_' . $user->id);

        // Eğer session yoksa, en yetkili rolüne göre belirle (DashboardController@index ile uyumlu)
        if (!$activeDashboard) {
            if ($user->hasRole('Superadmin')) $activeDashboard = 'superadmin';
            elseif ($user->hasRole('Yonetim')) $activeDashboard = 'yonetim';
            elseif ($user->hasRole('Bölüm Lideri')) $activeDashboard = 'bolum_lideri';
            elseif ($user->hasRole('Direktör')) $activeDashboard = 'direktor';
        }

        // Firmalar (Müşteriler sekmesi için)
        $firmalar = \App\Models\Customer::orderBy('name')->get();

        // Yetki Bazlı Bölüm Listesi (Filtre için)
        if ($activeDashboard === 'superadmin' || $activeDashboard === 'yonetim' || $user->hasRole(['Superadmin', 'Yonetim'])) {
            $bolumler = \App\Models\Bolum::orderBy('ad')->get();
        } elseif ($activeDashboard === 'direktor') {
            $managedBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();
            $bolumler = \App\Models\Bolum::whereIn('id', $managedBolumIds)->orderBy('ad')->get();
        } else {
            $bolumler = \App\Models\Bolum::where('id', $user->bolum_id)->get();
        }

        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $timeDiffExpr = $driver === 'sqlite'
            ? 'COALESCE(SUM(CASE WHEN last_activity_at > created_at THEN (strftime("%s", last_activity_at) - strftime("%s", created_at)) / 60 ELSE 0 END), 0)'
            : 'COALESCE(SUM(CASE WHEN last_activity_at > created_at THEN TIMESTAMPDIFF(MINUTE, created_at, last_activity_at) ELSE 0 END), 0)';

        $query = User::query()->select('users.*');

        if ($tab === 'musteri') {
            // Müşteriler Sekmesi: Şirket personeli olmayanlar
            $query->where('is_personnel', false);

            if ($customerId) {
                $query->where(function ($q) use ($customerId) {
                    $q->where('customer_id', $customerId)
                      ->orWhereHas('customers', function ($sub) use ($customerId) {
                          $sub->where('customer_id', $customerId);
                      });
                });
            }

            // Müşteriler için yetki kontrolü:
            // Superadmin, Yonetim, Bölüm Lideri, Direktör tüm müşteri hareketlerini inceleyebilir
            if (!($activeDashboard === 'superadmin' || $activeDashboard === 'yonetim' || $user->hasRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']))) {
                // Diğer durumlarda güvenlik gereği sadece kendini görebilir
                $query->where('id', $user->id);
            }
        } else {
            // Personel Sekmesi: Şirket personeli olanlar (varsayılan)
            $query->where('is_personnel', true);

            if ($bolumId) {
                $query->where('bolum_id', $bolumId);
            }

            // Yetki Bazlı Filtreleme
            if ($activeDashboard === 'bolum_lideri' && $user->bolum_id) {
                $query->where('bolum_id', $user->bolum_id);
            } elseif ($activeDashboard === 'direktor') {
                $managedBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();
                $query->whereIn('bolum_id', $managedBolumIds);
            } elseif ($activeDashboard === 'superadmin' || $activeDashboard === 'yonetim' || $user->hasRole(['Superadmin', 'Yonetim'])) {
                // Tam yetki - Filtre yok
            } else {
                // Diğer durumlarda sadece kendini görebilir (Güvenlik)
                $query->where('id', $user->id);
            }
        }

        // Genel Arama Filtresi (İsim veya E-posta)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Subqueries for Stats and Activity Details
        $query->withCount('loginActivities')
            ->addSelect([
                'last_login_at' => LoginActivity::select('created_at')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->limit(1),
                'last_activity_at' => LoginActivity::select('last_activity_at')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->limit(1),
                'last_ip' => LoginActivity::select('ip_address')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->limit(1),
                'total_online_minutes' => LoginActivity::selectRaw($timeDiffExpr)
                    ->whereColumn('user_id', 'users.id')
            ]);

        // Map old dropdown `sort` values to new `sort_by` and `sort_dir` if `sort_by` is not explicitly set
        if (!$sortBy && $request->has('sort')) {
            $oldSort = $request->input('sort');
            if ($oldSort === 'latest_login') {
                $sortBy = 'son_giris';
                $sortDir = 'desc';
            } elseif ($oldSort === 'most_logins') {
                $sortBy = 'toplam_giris';
                $sortDir = 'desc';
            } elseif ($oldSort === 'longest_online') {
                $sortBy = 'toplam_sure';
                $sortDir = 'desc';
            } elseif ($oldSort === 'name') {
                $sortBy = 'name';
                $sortDir = 'asc';
            }
        }

        // Default fallback sorting
        if (!$sortBy) {
            $sortBy = 'name';
            $sortDir = 'asc';
        }

        // Ensure sortDir is valid
        $sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

        // Set the dropdown `sort` value for the UI to match
        $sort = 'custom';
        if ($sortBy === 'name' && $sortDir === 'asc') {
            $sort = 'name';
        } elseif ($sortBy === 'son_giris' && $sortDir === 'desc') {
            $sort = 'latest_login';
        } elseif ($sortBy === 'toplam_giris' && $sortDir === 'desc') {
            $sort = 'most_logins';
        } elseif ($sortBy === 'toplam_sure' && $sortDir === 'desc') {
            $sort = 'longest_online';
        }

        // Sorting Logic
        if ($sortBy === 'bolum_firma') {
            if ($tab === 'musteri') {
                $query->leftJoin('customers', 'users.customer_id', '=', 'customers.id')
                    ->orderBy('customers.name', $sortDir);
            } else {
                $query->leftJoin('bolumler', 'users.bolum_id', '=', 'bolumler.id')
                    ->orderBy('bolumler.ad', $sortDir);
            }
        } elseif ($sortBy === 'durum') {
            $query->orderBy('last_activity_at', $sortDir);
        } elseif ($sortBy === 'son_giris') {
            $query->orderBy('last_login_at', $sortDir);
        } elseif ($sortBy === 'son_sure') {
            $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
            if ($driver === 'sqlite') {
                $query->orderByRaw('(strftime("%s", last_activity_at) - strftime("%s", last_login_at)) ' . $sortDir);
            } else {
                $query->orderByRaw('TIMESTAMPDIFF(MINUTE, last_login_at, last_activity_at) ' . $sortDir);
            }
        } elseif ($sortBy === 'toplam_giris') {
            $query->orderBy('login_activities_count', $sortDir);
        } elseif ($sortBy === 'toplam_sure') {
            $query->orderBy('total_online_minutes', $sortDir);
        } elseif ($sortBy === 'ip') {
            $query->orderBy('last_ip', $sortDir);
        } else {
            $query->orderBy('users.name', $sortDir);
        }

        // Eager load related tables to prevent N+1 queries
        $users = $query->with(['bolum', 'customer', 'customers'])
            ->paginate(10)
            ->withQueryString();

        // ═══════════════════════════════════════
        // STATS CALCULATIONS FOR THE HEADER
        // ═══════════════════════════════════════
        
        // Base stats query respecting role constraints and active tab
        $statsUserIdsQuery = User::query();
        if ($tab === 'musteri') {
            $statsUserIdsQuery->where('is_personnel', false);
            if ($customerId) {
                $statsUserIdsQuery->where(function ($q) use ($customerId) {
                    $q->where('customer_id', $customerId)
                      ->orWhereHas('customers', function ($sub) use ($customerId) {
                          $sub->where('customer_id', $customerId);
                      });
                });
            }
            if (!($activeDashboard === 'superadmin' || $activeDashboard === 'yonetim' || $user->hasRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']))) {
                $statsUserIdsQuery->where('id', $user->id);
            }
        } else {
            $statsUserIdsQuery->where('is_personnel', true);
            if ($bolumId) {
                $statsUserIdsQuery->where('bolum_id', $bolumId);
            }
            if ($activeDashboard === 'bolum_lideri' && $user->bolum_id) {
                $statsUserIdsQuery->where('bolum_id', $user->bolum_id);
            } elseif ($activeDashboard === 'direktor') {
                $managedBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();
                $statsUserIdsQuery->whereIn('bolum_id', $managedBolumIds);
            } elseif (!($activeDashboard === 'superadmin' || $activeDashboard === 'yonetim' || $user->hasRole(['Superadmin', 'Yonetim']))) {
                $statsUserIdsQuery->where('id', $user->id);
            }
        }

        // Apply general search filter if present
        if ($search) {
            $statsUserIdsQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $statsUserIds = $statsUserIdsQuery->pluck('id')->toArray();

        $totalUsersWithLogins = LoginActivity::whereIn('user_id', $statsUserIds)
            ->distinct('user_id')
            ->count();

        $todayActiveUsers = LoginActivity::whereIn('user_id', $statsUserIds)
            ->where('created_at', '>=', now()->startOfDay())
            ->distinct('user_id')
            ->count();

        $onlineNowCount = LoginActivity::whereIn('user_id', $statsUserIds)
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->distinct('user_id')
            ->count();

        // Find the user with the most online time (Trophy User)
        $mostOnlineUser = User::whereIn('id', $statsUserIds)
            ->whereHas('loginActivities')
            ->select('users.*')
            ->addSelect([
                'total_online_minutes' => LoginActivity::selectRaw($timeDiffExpr)
                    ->whereColumn('user_id', 'users.id')
            ])
            ->orderByDesc('total_online_minutes')
            ->first();

        $stats = [
            'total_users' => $totalUsersWithLogins,
            'today_active' => $todayActiveUsers,
            'online_now' => $onlineNowCount,
            'most_online_user' => $mostOnlineUser,
        ];

        return view('admin.logs.login.index', compact('users', 'search', 'bolumler', 'firmalar', 'sort', 'sortBy', 'sortDir', 'bolumId', 'customerId', 'stats', 'tab'));
    }

    /**
     * Belirli bir kullanıcının geçmiş tüm girişlerini ay ve gün bazında gruplayarak döner.
     */
    public function show(User $user)
    {
        $authUser = auth()->user();
        $activeDashboard = session('active_dashboard_' . $authUser->id);

        // Yetki Kontrolü
        $canView = false;
        if ($authUser->hasRole(['Superadmin', 'Yonetim'])) {
            $canView = true;
        } elseif ($activeDashboard === 'bolum_lideri' && $authUser->bolum_id == $user->bolum_id) {
            $canView = true;
        } elseif ($activeDashboard === 'direktor') {
            $managedBolumIds = $authUser->yonetilenBolumler->pluck('id')->toArray();
            if (in_array($user->bolum_id, $managedBolumIds)) {
                $canView = true;
            }
        } elseif ($authUser->id === $user->id) {
            $canView = true;
        }

        if (!$canView) {
            abort(403, 'Bu kullanıcının giriş kayıtlarını görme yetkiniz yok.');
        }

        $activities = $user->loginActivities()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($activity) {
                // Ay bazlı gruplandırma (Örn: Şubat 2026)
                return Carbon::parse($activity->created_at)->translatedFormat('F Y');
            })
            ->map(function ($monthGroup) {
                return $monthGroup->groupBy(function ($activity) {
                    // Gün bazlı gruplandırma (Örn: 13 Şubat Cuma)
                    return Carbon::parse($activity->created_at)->translatedFormat('d F l');
                });
            });

        return view('admin.logs.login.show', compact('user', 'activities'));
    }
}
