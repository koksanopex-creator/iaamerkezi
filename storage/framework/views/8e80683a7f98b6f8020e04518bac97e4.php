<?php $__env->startPush('pageTitle'); ?>
    Hukuk Yetki Matrisi | 
<?php $__env->stopPush(); ?>

<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tighter flex items-center gap-3">
            <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            </div>
            <?php echo e(__('Hukuk Yetki Matrisi')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <?php $__env->startPush('styles'); ?>
    <style>
        :root {
            --color-background-primary: #ffffff;
            --color-background-secondary: #f8fafc;
            --color-border-tertiary: #e2e8f0;
            --color-text-primary: #1e293b;
            --color-text-secondary: #64748b;
            --border-radius-lg: 1rem;
        }

        .matrix-page-root { padding: 5px 0; font-family: 'Figtree', sans-serif; position: relative; }

        .info-bar {
            display: flex; align-items: center; justify-content: space-between;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 16px 24px; margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .info-left { display: flex; align-items: center; gap: 14px; }
        .shield {
            width: 42px; height: 42px; border-radius: 12px;
            background: #fffbeb;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            border: 1px solid #fef3c7;
        }
        .info-title { font-size: 14px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.025em; }
        .info-sub   { font-size: 11.5px; color: #64748b; margin-top: 3px; max-width: 550px; line-height: 1.5; }
        .info-sub strong { color: #4f46e5; font-weight: 700; }
        
        .stats { display: flex; align-items: center; gap: 24px; }
        .stat { text-align: center; }
        .stat-n { font-size: 24px; font-weight: 800; line-height: 1; }
        .stat-n.a { color: #d97706; }
        .stat-n.i { color: #4f46e5; }
        .stat-l { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; font-weight: 600; }
        .vdiv { width: 1px; height: 36px; background: #e2e8f0; }

        .matrix-wrap {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            /* overflow: hidden; -- Dropdownların kesilmemesi için kaldırıldı */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .matrix-scroll { 
            overflow-x: auto; 
            max-height: calc(100vh - 280px);
            overflow-y: auto;
            border-radius: 1rem;
        }

        .matrix-table { border-collapse: separate; border-spacing: 0; width: 100%; table-layout: auto; }

        /* sticky cols & headers - Z-INDEX DÜZENLEMESİ (Navbar z-50'dir) */
        .col-icon  { width: 44px; min-width: 44px; }
        .col-label { width: 240px; min-width: 240px; }
        .col-p     { width: 130px; min-width: 130px; }

        .matrix-table thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: bottom;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .th-icon {
            left: 0; z-index: 15 !important;
            padding: 15px 10px;
            border-right: 1px solid #e2e8f0;
        }
        .accent-bar { height: 4px; width: 20px; background: #4f46e5; border-radius: 2px; margin: 0 auto; }

        .th-label {
            left: 44px; z-index: 15 !important;
            padding: 15px 20px;
            text-align: left;
            border-right: 1px solid #e2e8f0;
            font-size: 11px; font-weight: 800; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.1em;
        }

        .th-person {
            padding: 15px 10px; text-align: center;
            border-right: 1px solid #e2e8f0;
            vertical-align: bottom;
        }
        .th-person:last-child { border-right: none; }

        .avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: #e0e7ff;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #4f46e5;
            margin: 0 auto 8px; position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; }
        
        .avatar-badge {
            position: absolute; top: -4px; right: -4px;
            width: 15px; height: 15px; border-radius: 5px;
            background: #4f46e5; color: white;
            font-size: 7px; font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #f8fafc;
        }
        .p-name { font-size: 12px; font-weight: 700; color: #1e293b; line-height: 1.2; text-decoration: none !important; }
        .p-dept { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; font-weight: 600; }

        .mgr-btn {
            margin-top: 10px; padding: 4px 10px;
            border-radius: 8px; font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            border: none; cursor: pointer; transition: all 0.2s ease;
            display: inline-block;
        }
        .mgr-btn.on  { background: #4f46e5; color: white; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3); }
        .mgr-btn.on:hover { background: #4338ca; transform: translateY(-1px); }
        .mgr-btn.off { background: #ffffff; color: #64748b; border: 1px solid #e2e8f0; }
        .mgr-btn.off:hover { background: #f1f5f9; border-color: #cbd5e1; }

        /* group rows */
        .matrix-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .matrix-table tbody tr:last-child { border-bottom: none; }
        .matrix-table tbody tr:hover td { background: #f8fafc; }
        
        .td-icon {
            position: sticky; left: 0; z-index: 5;
            padding: 0;
            border-right: 1px solid #e2e8f0;
            text-align: center; vertical-align: middle;
            background: white;
        }
        .group-stripe {
            width: 44px; height: 100%;
            display: flex; align-items: center; justify-content: center;
            padding: 12px 0;
            min-height: 50px;
        }
        .group-dot {
            width: 7px; height: 7px; border-radius: 50%;
        }

        .td-label {
            position: sticky; left: 44px; z-index: 5;
            background: #ffffff;
            padding: 12px 20px;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .label-group { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 4px; }
        .label-name  { font-size: 12px; font-weight: 500; color: #1e293b; line-height: 1.4; }

        .td-switch {
            text-align: center; vertical-align: middle;
            padding: 0 10px;
            border-right: 1px solid #f1f5f9;
        }
        .td-switch:last-child { border-right: none; }

        .toggle { position: relative; display: inline-block; width: 32px; height: 16px; cursor: pointer; }
        .toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
        .t-track {
            position: absolute; inset: 0;
            background: #e2e8f0;
            border-radius: 999px; transition: background 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .toggle input:checked + .t-track { background: #f59e0b; }
        .toggle input:disabled + .t-track { opacity: 0.25; cursor: not-allowed; }
        .t-thumb {
            position: absolute; top: 2px; left: 2px;
            width: 12px; height: 12px;
            border-radius: 50%; background: white;
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle input:checked ~ .t-thumb { transform: translateX(16px); }

        /* group color themes */
        .gc-amber  { color: #b45309; }
        .gc-blue   { color: #1d4ed8; }
        .gc-green  { color: #15803d; }
        .gc-purple { color: #7c3aed; }
        .gc-rose   { color: #be123c; }
        .gc-teal   { color: #0f766e; }

        .dot-amber  { background: #f59e0b; }
        .dot-blue   { background: #3b82f6; }
        .dot-green  { background: #22c55e; }
        .dot-purple { background: #a855f7; }
        .dot-rose   { background: #f43f5e; }
        .dot-teal   { background: #14b8a6; }

        .bg-amber  { background: #fffbeb; }
        .bg-blue   { background: #eff6ff; }
        .bg-green  { background: #f0fdf4; }
        .bg-purple { background: #faf5ff; }
        .bg-rose   { background: #fff1f2; }
        .bg-teal   { background: #f0fdfa; }

        .footer {
            padding: 12px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            background: #f8fafc;
        }
        .legend { display: flex; align-items: center; gap: 20px; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 10px; color: #64748b; font-weight: 600; }
        .ldot { width: 10px; height: 10px; border-radius: 3px; }
        .footer-brand { font-size: 10px; color: #94a3b8; letter-spacing: 0.05em; font-weight: 600; text-transform: uppercase; }

        /* Notification Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            pointer-events: none;
        }
        .toast {
            background: #065f46;
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            display: flex; align-items: center; gap: 10px;
            pointer-events: auto;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .toast-info { background: #1e1b4b; }
        .toast-error { background: #991b1b; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <?php $__env->stopPush(); ?>

    <div class="matrix-page-root" x-data="{
        showNotification: false,
        notificationMsg: '',
        notificationType: 'success',
        
        showToast(msg, type = 'success') {
            this.notificationMsg = msg;
            this.notificationType = type;
            this.showNotification = true;
            setTimeout(() => { this.showNotification = false; }, 3000);
        },

        toggleRelation(userId, type, value, state) {
            // Arka planda Ajax (Fetch) isteği
            let url = '<?php echo e(route('admin.disiplin.hukuk-matrisi.update', ['user' => ':userId'])); ?>';
            url = url.replace(':userId', userId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({
                    type: type,
                    value: value,
                    status: state
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    this.showToast(data.message || 'Yetki başarıyla güncellendi.', 'success');
                } else {
                    this.showToast(data.message || 'Bir hata oluştu.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showToast('Sunucu ile iletişim kurulamadı.', 'error');
            });
        }
    }">
        <!-- Notification Toast -->
        <div class="toast-container" x-show="showNotification" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4">
            <div class="toast" :class="{'toast-info': notificationType === 'info', 'toast-error': notificationType === 'error'}">
                <svg x-show="notificationType === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <svg x-show="notificationType === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span x-text="notificationMsg"></span>
            </div>
        </div>

        <!-- Bilgi Paneli -->
        <div class="info-bar">
            <div class="info-left">
                <div class="shield">
                    <svg fill="none" stroke="#d97706" stroke-width="2.5" viewBox="0 0 24 24" style="width:20px;height:20px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="info-title">Hukuk yetki matrisi</div>
                    <div class="info-sub">Admin olarak, yetki alanınızdaki personel için <strong>Hukuk Yöneticisi</strong> rolünü ve operasyonel yetkileri yönetebilirsiniz.</div>
                </div>
            </div>
            <div class="stats">
                <div class="stat">
                    <div class="stat-n a"><?php echo e(count($personel)); ?></div>
                    <div class="stat-l">Aday personel</div>
                </div>
                <div class="vdiv"></div>
                <div class="stat">
                    <div class="stat-n i"><?php echo e($personel->filter(fn($p) => $p->hasRole('Hukuk Yöneticisi'))->count()); ?></div>
                    <div class="stat-l">Aktif yönetici</div>
                </div>
            </div>
        </div>

        <!-- Matris Tablosu -->
        <div class="matrix-wrap">
            <div class="matrix-scroll custom-scrollbar">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th class="th-icon col-icon"><div class="accent-bar"></div></th>
                            <th class="th-label col-label">Yetki listesi</th>
                            <?php $__currentLoopData = $personel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="th-person col-p">
                                    <div class="avatar shadow-sm">
                                        <?php if($p->profile_photo_url): ?>
                                            <img src="<?php echo e($p->profile_photo_url); ?>" alt="<?php echo e($p->name); ?>">
                                        <?php else: ?>
                                            <?php echo e(strtoupper(substr($p->name, 0, 1))); ?><?php echo e(strtoupper(substr(strrchr($p->name, ' '), 1, 1))); ?>

                                        <?php endif; ?>
                                        <?php if($p->hasRole('Hukuk Yöneticisi')): ?>
                                            <div class="avatar-badge">HY</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-name" title="<?php echo e($p->name); ?>"><?php echo e($p->name); ?></div>
                                    <div class="p-dept"><?php echo e($p->bolum->ad ?? 'Bölüm Yok'); ?></div>
                                    <button 
                                        @click="toggleRelation('<?php echo e($p->id); ?>', 'role', 'Hukuk Yöneticisi', !<?php echo e($p->hasRole('Hukuk Yöneticisi') ? 'true' : 'false'); ?>)"
                                        class="mgr-btn <?php echo e($p->hasRole('Hukuk Yöneticisi') ? 'on' : 'off'); ?>">
                                        <?php echo e($p->hasRole('Hukuk Yöneticisi') ? 'YÖNETİCİ' : 'YAP'); ?>

                                    </button>
                                </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $groupColors = [
                                'disiplin_ayarlari' => 'amber',
                                'arabuluculuk_tanimlari' => 'blue',
                                'dis_avukatlar' => 'green',
                                'disiplin_sureci' => 'purple',
                                'degerlendirme_savunma' => 'rose',
                                'disiplin_kurulu' => 'teal'
                            ];
                            $colorKeys = array_values($groupColors);
                            $groupCounter = 0;
                        ?>

                        <?php $__currentLoopData = $managedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $groupSlug = Str::slug($groupName, '_');
                                $color = $groupColors[$groupSlug] ?? $colorKeys[$groupCounter % count($colorKeys)];
                                $groupCounter++;
                            ?>

                            <?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="td-icon col-icon bg-<?php echo e($color); ?>">
                                        <div class="group-stripe">
                                            <div class="group-dot dot-<?php echo e($color); ?>"></div>
                                        </div>
                                    </td>
                                    <td class="td-label col-label">
                                        <?php if($loop->first): ?>
                                            <div class="label-group gc-<?php echo e($color); ?>"><?php echo e($groupName); ?></div>
                                        <?php endif; ?>
                                        <div class="label-name"><?php echo e($label); ?></div>
                                    </td>
                                    <?php $__currentLoopData = $personel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="td-switch col-p">
                                            <?php
                                                try {
                                                    $hasThisPerm = $p->hasPermissionTo($slug);
                                                } catch (\Exception $e) {
                                                    $hasThisPerm = false;
                                                }
                                            ?>
                                            <label class="toggle">
                                                <input type="checkbox" 
                                                    <?php if($hasThisPerm): ?> checked <?php endif; ?>
                                                    <?php if(!$p->hasRole('Hukuk Yöneticisi')): ?> disabled <?php endif; ?>
                                                    @change="toggleRelation('<?php echo e($p->id); ?>', 'permission', '<?php echo e($slug); ?>', $event.target.checked)">
                                                <div class="t-track"></div>
                                                <div class="t-thumb"></div>
                                            </label>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Footer / Legend -->
            <div class="footer">
                <div class="legend">
                    <div class="legend-item"><div class="ldot" style="background:#f59e0b"></div>Aktif yetki</div>
                    <div class="legend-item"><div class="ldot" style="background:#e2e8f0"></div>Yetki yok</div>
                    <div class="legend-item"><div class="ldot" style="background:#f8fafc; border:1px solid #e2e8f0"></div>Rol gerekli</div>
                </div>
                <div class="footer-brand">Köksan Portal &copy; 2026 — Hukuk denetim sistemi</div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/hukuk_matrisi.blade.php ENDPATH**/ ?>