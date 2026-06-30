<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Takim;
use App\Models\Iaa;
use Illuminate\Support\Facades\DB;

class SyncTeamScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'puan:senkronize-et';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takımların ve personellerin puanlarını mevcut tamamlanmış projelere göre yeniden hesaplar ve senkronize eder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Puan senkronizasyonu başlatılıyor...');

        // 1. TAKIM PUANLARINI SENKRONİZE ET
        $this->info('Takım puanları hesaplanıyor...');

        $takimlar = Takim::all();
        $bar = $this->output->createProgressBar(count($takimlar));
        $bar->start();

        foreach ($takimlar as $takim) {
            // Sadece bu takıma atanmış ve TAMAMLANMIŞ projelerin puanlarını topla
            $gercekPuan = Iaa::where('atanan_takim_id', $takim->id)
                ->where('durum', 'Tamamlandı')
                ->sum('puan');

            if ($takim->toplam_puan != $gercekPuan) {
                // $this->line(" Düzeltme: {$takim->ad} ({$takim->toplam_puan} -> {$gercekPuan})");
                $takim->toplam_puan = $gercekPuan;
                $takim->save();
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info('Takım puanları senkronize edildi.');

        // 2. KULLANICI PUANLARINI SENKRONİZE ET
        $this->info('Kullanıcı puanları hesaplanıyor...');
        $puanService = new \App\Services\Dashboard\KullaniciPuanService();

        $users = \App\Models\User::where('is_personnel', true)->get();
        $barUser = $this->output->createProgressBar(count($users));
        $barUser->start();

        foreach ($users as $user) {
            $gercekUserPuan = $puanService->calculateTotalScore($user);

            if ($user->toplam_puan != $gercekUserPuan) {
                $user->toplam_puan = $gercekUserPuan;
                $user->save();
            }
            $barUser->advance();
        }
        $barUser->finish();
        $this->newLine();
        $this->info('Tüm puanlar başarıyla senkronize edildi.');
    }
}
