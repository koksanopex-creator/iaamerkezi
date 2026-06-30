<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportRoleAuthorization extends Model
{
    protected $table = 'report_role_authorizations';

    protected $fillable = [
        'report_name',
        'role_name',
        'data_scope',
        'specific_department_ids',
    ];

    protected function casts(): array
    {
        return [
            'specific_department_ids' => 'array',
        ];
    }

    /**
     * Veri kapsamı seçenekleri (UI dropdown için)
     */
    public const DATA_SCOPE_OPTIONS = [
        'all'                     => 'Tüm Veriler',
        'own_department'          => 'Kendi Bölümü',
        'responsible_departments' => 'Sorumlu Olduğu Bölümler',
        'specific_departments'    => 'Seçili Belirli Bölümler',
    ];

    /**
     * Verilen kullanıcı için analiz raporu erişim yetkisini kontrol eder.
     * Öncelik sırası:
     *   1. Kişisel yetki (report_user_authorizations)
     *   2. Rol bazlı yetki (report_role_authorizations)
     */
    public static function getAuthorizationForUser($user, $reportName = 'analiz_raporu'): ?array
    {
        // 0. Superadmin ve Yonetim rolleri her zaman tüm verilere erişebilir
        if ($user->hasAnyRole(['Superadmin', 'Yonetim', 'Yönetim'])) {
            return [
                'source' => 'system',
                'data_scope' => 'all',
                'specific_department_ids' => null,
            ];
        }

        // 1. Önce kişisel yetki kontrolü
        $personalAuth = ReportUserAuthorization::where('user_id', $user->id)
            ->where('report_name', $reportName)
            ->first();
        if ($personalAuth) {
            return [
                'source' => 'user',
                'data_scope' => $personalAuth->data_scope,
                'specific_department_ids' => $personalAuth->specific_department_ids,
            ];
        }

        // 2. Rol bazlı yetki kontrolü
        $userRoles = $user->getRoleNames()->toArray();
        if (empty($userRoles)) {
            return null;
        }

        $scopePriority = ['all' => 4, 'responsible_departments' => 3, 'specific_departments' => 2, 'own_department' => 1];

        $authorizations = self::whereIn('role_name', $userRoles)
            ->where('report_name', $reportName)
            ->get();
        if ($authorizations->isEmpty()) {
            return null;
        }

        $bestAuth = $authorizations->sortByDesc(function ($auth) use ($scopePriority) {
            return $scopePriority[$auth->data_scope] ?? 0;
        })->first();

        return [
            'source' => 'role',
            'data_scope' => $bestAuth->data_scope,
            'specific_department_ids' => $bestAuth->specific_department_ids,
        ];
    }

    /**
     * Verilen kullanıcı için izin verilen bölüm ID'lerini hesaplar.
     * '*' döndürürse tüm bölümlere erişim var demektir.
     */
    public static function getAllowedBolumIdsForUser($user, $reportName = 'analiz_raporu'): array|string
    {
        $auth = self::getAuthorizationForUser($user, $reportName);

        if (!$auth) {
            return []; // Hiç yetkisi yok
        }

        switch ($auth['data_scope']) {
            case 'all':
                return '*';
            case 'own_department':
                return $user->bolum_id ? [$user->bolum_id] : [];
            case 'responsible_departments':
                return $user->getResponsibleDepartments();
            case 'specific_departments':
                return $auth['specific_department_ids'] ?? [];
            default:
                return [];
        }
    }
}
