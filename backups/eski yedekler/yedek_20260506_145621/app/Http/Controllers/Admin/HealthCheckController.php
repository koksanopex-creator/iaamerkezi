<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class HealthCheckController extends Controller
{
    /**
     * Sağlık Kontrolü Paneli Ana Sayfası
     */
    public function index()
    {
        return view('admin.health.index');
    }

    /**
     * Sistemi Tara (AJAX ile çağrılacak)
     */
    /**
     * Taramayı Başlat (Başlangıç Verileri)
     */
    public function init()
    {
        return response()->json([
            'routes' => $this->getRoutesToCheck(),
            'roles' => $this->getRolesMetadata(),
            'role_users' => $this->getRepresentativeUsersList()
        ]);
    }

    /**
     * Tek Bir Rotayı Belirli Roller İçin Tara
     */
    public function scan(Request $request)
    {
        $routeName = $request->input('route');
        $moduleName = $request->input('module');
        $selectedRoles = $request->input('roles', []); // Frontend'den gelen seçili roller

        if (!Route::has($routeName)) {
            return response()->json(['error' => 'Rota bulunamadı'], 404);
        }

        $rolesMetadata = $this->getRolesMetadata();
        $representativeUsers = $this->getRepresentativeUsers();

        $roleResults = [];
        $totalFail = 0;

        foreach ($rolesMetadata as $roleName => $shortCode) {
            // Eğer rol seçili değilse atla
            if (!empty($selectedRoles) && !in_array($roleName, $selectedRoles)) {
                continue;
            }

            $testUser = $representativeUsers[$roleName] ?? null;

            if (!$testUser) {
                $roleResults[$roleName] = [
                    'status' => 'info',
                    'code' => '-',
                    'message' => 'Temsilci Kullanıcı Yok',
                    'short' => $shortCode
                ];
                continue;
            }

            $response = $this->simulateRequest($routeName, $testUser);
            $roleResults[$roleName] = [
                'status' => $response['status'],
                'code' => $response['code'],
                'message' => $response['message'],
                'short' => $shortCode
            ];

            if ($response['status'] === 'danger') {
                $totalFail++;
            }
        }

        return response()->json([
            'result' => [
                'name' => $request->input('name'),
                'module' => $moduleName,
                'role_results' => $roleResults,
                'url' => route($routeName, [], false)
            ],
            'fail_count' => $totalFail


        ]);
    }

    /**
     * Taranacak Rotaları Döndürür
     */
    private function getRoutesToCheck()
    {
        return [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'module' => 'Genel'],
            ['name' => 'Kullanıcı Listesi', 'route' => 'admin.users.index', 'module' => 'Sistem'],
            ['name' => 'Bölüm Yönetimi', 'route' => 'admin.bolumler.index', 'module' => 'Sistem'],
            ['name' => 'İAA Yönetimi', 'route' => 'admin.iaa-yonetim.index', 'module' => 'IAA'],
            ['name' => 'Müşteri Şikayetleri', 'route' => 'admin.sikayetler.index', 'module' => 'Şikayet'],
            ['name' => 'Disiplin Dosyaları', 'route' => 'admin.disiplin.index', 'module' => 'Disiplin'],
            ['name' => 'Disiplin Ayarları', 'route' => 'admin.disiplin.settings.index', 'module' => 'Disiplin'],
            ['name' => 'Sistem Ayarları', 'route' => 'admin.sistem-ayarlari.index', 'module' => 'Sistem'],
            ['name' => 'Arabuluculuk Listesi', 'route' => 'admin.arabuluculuk.index', 'module' => 'Arabuluculuk'],
            ['name' => 'Yeni Arabuluculuk', 'route' => 'admin.arabuluculuk.create', 'module' => 'Arabuluculuk'],
            ['name' => 'Arabulucu Listesi', 'route' => 'admin.arabulucular.index', 'module' => 'Arabuluculuk'],
            ['name' => 'Madde Değişiklik Logları', 'route' => 'admin.arabuluculuk.tanim.showAllLogs', 'module' => 'Arabuluculuk'],
            ['name' => 'Arabulucu Sistem Logları', 'route' => 'admin.arabulucular.logs', 'module' => 'Arabuluculuk'],
            ['name' => 'Dış Avukatlar', 'route' => 'admin.dis_avukatlar.index', 'module' => 'Arabuluculuk'],
            ['name' => 'Bölüm Kategorileri', 'route' => 'admin.bolum-kategorileri.index', 'module' => 'Sistem'],
            ['name' => 'Makine Logları', 'route' => 'machine-logs.index', 'module' => 'Sistem'],
            ['name' => 'Puan Durumu', 'route' => 'puan-durumu', 'module' => 'Genel'],
        ];
    }

    /**
     * Tüm Roller ve Kısaltmaları
     */
    private function getRolesMetadata()
    {
        $roles = Role::orderBy('name')->pluck('name')->toArray();
        $metadata = [];
        $usedCodes = [];

        foreach ($roles as $role) {
            $words = explode(' ', $role);
            if (count($words) >= 2) {
                $code = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[count($words) - 1], 0, 1));
            } else {
                $code = strtoupper(mb_substr($role, 0, 2));
            }

            // Benzersizlik kontrolü (Çakışma varsa sonraki harfi al)
            $i = 1;
            $originalCode = $code;
            while (isset($usedCodes[$code])) {
                $code = $originalCode . $i++;
            }
            
            $usedCodes[$code] = $role;
            $metadata[$role] = $code;
        }

        return $metadata;
    }

    /**
     * Temsilci Kullanıcıları Bul
     */
    private function getRepresentativeUsers()
    {
        $roles = Role::pluck('name')->toArray();
        $users = [];
        foreach ($roles as $role) {
            $u = User::role($role)->first();
            if ($u) {
                $users[$role] = $u;
            }
        }
        return $users;
    }

    /**
     * Frontend için Kullanıcı Durum Listesi
     */
    private function getRepresentativeUsersList()
    {
        $roles = Role::pluck('name')->toArray();
        $list = [];
        foreach ($roles as $role) {
            $list[$role] = User::role($role)->exists();
        }
        return $list;
    }

    /**
     * Modellerin toplu atama (Mass Assignment) korumasını kontrol eder.
     */
    private function checkModels()
    {
        $modelPath = app_path('Models');
        if (!is_dir($modelPath)) return [];

        $files = glob($modelPath . '/*.php');
        $issues = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $filename = basename($file);
            $className = str_replace('.php', '', $filename);

            $hasFillable = str_contains($content, 'protected $fillable') || str_contains($content, 'public $fillable');
            $hasGuarded = str_contains($content, 'protected $guarded') || str_contains($content, 'public $guarded');
            
            preg_match('/class\s+' . $className . '.*\{(.*)\}/s', $content, $matches);
            $body = isset($matches[1]) ? trim($matches[1]) : '';
            
            $bodyClean = preg_replace('/\/\*.*?\*\/|\/\/.*/s', '', $body);
            $bodyClean = trim($bodyClean);

            if (!$hasFillable && !$hasGuarded) {
                if (empty($bodyClean)) {
                    $issues[] = [
                        'model' => $className,
                        'type' => 'danger',
                        'message' => 'Model tamamen boş ve korumasız! (MassAssignment risk)'
                    ];
                } else {
                    $issues[] = [
                        'model' => $className,
                        'type' => 'warning',
                        'message' => '$fillable veya $guarded tanımlanmamış.'
                    ];
                }
            }
        }

        return $issues;
    }

    /**
     * Dahili istek simülasyonu
     */
    private function simulateRequest($routeName, $testUser)
    {
        try {
            $originalUser = Auth::user();
            $originalSessionId = session()->getId();
            
            $request = Request::create(route($routeName), 'GET');
            $request->headers->set('X-Is-Simulation', 'true');
            
            $request->setUserResolver(function () use ($testUser) {
                return $testUser;
            });

            // Oturumu geçici olarak bu kullanıcıya ayarla
            Auth::setUser($testUser);

            // Laravel'in global Exception Handler'ı hataları yakalayıp 500 response döndürdüğü için 
            // hatanın kendisini yakalamak adına Pipeline'ı manuel tetikleyebiliriz 
            // ya da Kernel üzerinden geçerken hatayı takip edebiliriz.
            
            $response = app()->handle($request);
            $code = $response->getStatusCode();

            // Geri yükle
            Auth::setUser($originalUser);
            if (session()->getId() !== $originalSessionId) {
                session()->setId($originalSessionId);
            }

            if ($code >= 200 && $code < 300) {
                return ['status' => 'success', 'code' => $code, 'message' => 'Çalışıyor'];
            } elseif ($code == 403) {
                return ['status' => 'warning', 'code' => $code, 'message' => 'Yetki Engeli'];
            } elseif ($code == 302) {
                if (str_contains($response->headers->get('Location'), 'login')) {
                    return ['status' => 'warning', 'code' => $code, 'message' => 'Oturum Gerekli'];
                }
                return ['status' => 'info', 'code' => $code, 'message' => 'Yönlendirme'];
            } elseif ($code == 500) {
                // Eğer response içinde exception varsa onu alalım
                if (isset($response->exception) && $response->exception instanceof \Throwable) {
                    $e = $response->exception;
                    $file = basename($e->getFile());
                    $line = $e->getLine();
                    return [
                        'status' => 'danger',
                        'code' => $code,
                        'message' => "Hata: [{$file}:{$line}] " . Str::limit($e->getMessage(), 50)
                    ];
                }
                return ['status' => 'danger', 'code' => $code, 'message' => 'Sistem Hatası (500)'];
            } else {
                return ['status' => 'danger', 'code' => $code, 'message' => 'Hata'];
            }
        } catch (\Exception $e) {
            if (isset($originalUser)) Auth::setUser($originalUser);
            
            $file = basename($e->getFile());
            $line = $e->getLine();
            $traceMessage = "[{$file}:{$line}] " . $e->getMessage();
            
            return [
                'status' => 'danger', 
                'code' => '500', 
                'message' => 'Çökme: ' . $traceMessage
            ];
        }
    }
}
