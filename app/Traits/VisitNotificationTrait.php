<?php

namespace App\Traits;

use App\Models\User;
use App\Models\IaaZiyaretPlani;
use App\Models\Iaa;
use App\Notifications\ZiyaretOnayDurumuBildirimi;
use App\Notifications\ZiyaretRevizyonBildirimi;
use App\Notifications\ZiyaretOnayBekliyorBildirimi;
use App\Notifications\ZiyaretYoneticiBildirimi;
use Illuminate\Support\Facades\Log;

trait VisitNotificationTrait
{
    /**
     * Ziyaret planı BKY veya Direktör tarafından onaylandığında/reddedildiğinde bildirimi merkezden dağıtır.
     *
     * @param Iaa $iaa
     * @param string $actionStatus "Onaylandı", "Direktör Onayı Bekliyor", "Reddedildi", "Revize İsteniyor"
     * @param string $actorName İşlemi yapanın adı
     * @param string|null $reason Açıklama
     */
    protected function dispatchVisitWorkflowNotifications(Iaa $iaa, $actionStatus, $actorName, $reason = null)
    {
        try {
            $ziyaretPlani = $iaa->ziyaretPlani;
            if (!$ziyaretPlani) return;

            $planner = $ziyaretPlani->planner;
            
            // Ziyaretçileri bul
            $visitorIds = [];
            if ($ziyaretPlani->visitors) {
                $visitorsData = is_string($ziyaretPlani->visitors) ? json_decode($ziyaretPlani->visitors, true) : $ziyaretPlani->visitors;
                if (is_array($visitorsData)) {
                    foreach ($visitorsData as $vId) {
                        $visitorIds[] = $vId;
                    }
                }
            } elseif ($ziyaretPlani->visitor_id) {
                $visitorIds[] = $ziyaretPlani->visitor_id;
            }
            
            $visitors = User::whereIn('id', $visitorIds)->get();

            // 1. Çözüm Lideri (Planner) ve Ziyaretçiler
            $recipients = collect();
            if ($planner) $recipients->push($planner);
            foreach ($visitors as $v) $recipients->push($v);
            $recipients = $recipients->unique('id');

            foreach ($recipients as $recipient) {
                if (in_array($actionStatus, ['Onaylandı', 'Direktör Onayı Bekliyor'])) {
                    $recipient->notify(new ZiyaretOnayDurumuBildirimi($iaa, $actorName, $actionStatus));
                } elseif ($actionStatus !== 'Dönüş Tarihi Revizyonu Direktör Onayına Gönderildi') {
                    $recipient->notify(new ZiyaretRevizyonBildirimi($iaa, $actorName, $actionStatus, $reason));
                }
            }

            // 2. Direktör ve Bölüm Lideri (Projenin)
            $projectBolum = $iaa->bolum;
            $projectDirector = $projectBolum ? $projectBolum->director : null;
            $projectLeaders = $projectBolum ? User::role('Bölüm Lideri')->where('bolum_id', $projectBolum->id)->get() : collect();
            
            if ($actionStatus === 'Dönüş Tarihi Revizyonu Direktör Onayına Gönderildi') {
                $requesterNameForRevision = 'Personel';
                if ($ziyaretPlani->return_date_revision_requested_by) {
                    $reqUser = User::find($ziyaretPlani->return_date_revision_requested_by);
                    if ($reqUser) {
                        $requesterNameForRevision = $reqUser->name;
                    }
                } elseif (!empty($ziyaretPlani->visitors)) {
                    $visitorsData = is_string($ziyaretPlani->visitors) ? json_decode($ziyaretPlani->visitors, true) : $ziyaretPlani->visitors;
                    if (is_array($visitorsData) && count($visitorsData) > 0) {
                        $v = User::find($visitorsData[0]);
                        if ($v) $requesterNameForRevision = $v->name;
                    }
                } elseif ($ziyaretPlani->visitor_id) {
                    $v = User::find($ziyaretPlani->visitor_id);
                    if ($v) $requesterNameForRevision = $v->name;
                }
                $this->dispatchDonusTarihiRevizyonNotifications($iaa, $requesterNameForRevision, $reason);
            } else {
                // Direktör Bildirimi
                if ($projectDirector) {
                    if ($actionStatus === 'Direktör Onayı Bekliyor') {
                        // Direktör onayı aktifse onaya bekliyor bildirimi
                        $projectDirector->notify(new ZiyaretOnayBekliyorBildirimi($iaa, $actorName));
                    } elseif (in_array($actionStatus, ['Onaylandı', 'Reddedildi', 'Revize İsteniyor', 'Dönüş Tarihi Revizyonu İptal Edildi'])) {
                        // Diğer durumlarda bilgi bildirimi
                        if ($actionStatus === 'Onaylandı') {
                            $projectDirector->notify(new ZiyaretOnayDurumuBildirimi($iaa, $actorName, $actionStatus));
                        } else {
                            $projectDirector->notify(new ZiyaretRevizyonBildirimi($iaa, $actorName, $actionStatus, $reason));
                        }
                    }
                }
            }

            // Bölüm Lideri Bildirimi (Projenin)
            foreach ($projectLeaders as $leader) {
                if ($actionStatus === 'Onaylandı' || $actionStatus === 'Direktör Onayı Bekliyor') {
                    $leader->notify(new ZiyaretOnayDurumuBildirimi($iaa, $actorName, $actionStatus));
                } elseif ($actionStatus !== 'Dönüş Tarihi Revizyonu Direktör Onayına Gönderildi') {
                    $leader->notify(new ZiyaretRevizyonBildirimi($iaa, $actorName, $actionStatus, $reason));
                }
            }

            // 3. Bölüm Lider Yardımcısı (Projenin) -> bolum.ziyaret.gor yetkisine göre
            $projectViceLeaders = $projectBolum ? User::role('Bölüm Lider Yardımcısı')->where('bolum_id', $projectBolum->id)->get() : collect();
            foreach ($projectViceLeaders as $viceLeader) {
                if ($viceLeader->hasPermissionTo('bolum.ziyaret.gor')) {
                    if ($actionStatus === 'Onaylandı' || $actionStatus === 'Direktör Onayı Bekliyor') {
                        $viceLeader->notify(new ZiyaretOnayDurumuBildirimi($iaa, $actorName, $actionStatus));
                    } elseif ($actionStatus !== 'Dönüş Tarihi Revizyonu Direktör Onayına Gönderildi') {
                        $viceLeader->notify(new ZiyaretRevizyonBildirimi($iaa, $actorName, $actionStatus, $reason));
                    }
                }
            }

            // 3.5. Müşteri Saha Temsilcisi (Projenin Bölümü)
            if ($projectBolum) {
                $sahaTemsilcileri = User::role('Müşteri Saha Temsilcisi')->get();
                foreach ($sahaTemsilcileri as $sahaTemsilcisi) {
                    if (in_array($projectBolum->id, $sahaTemsilcisi->getAllowedBolumIds())) {
                        if ($actionStatus === 'Onaylandı' || $actionStatus === 'Direktör Onayı Bekliyor') {
                            $sahaTemsilcisi->notify(new ZiyaretOnayDurumuBildirimi($iaa, $actorName, $actionStatus));
                        } elseif ($actionStatus !== 'Dönüş Tarihi Revizyonu Direktör Onayına Gönderildi') {
                            $sahaTemsilcisi->notify(new ZiyaretRevizyonBildirimi($iaa, $actorName, $actionStatus, $reason));
                        }
                    }
                }
            }

            // 4. Ziyaretçilerin Kendi Lider ve Direktörleri (Farklı Bölümdeyse)
            if ($projectBolum) {
                foreach ($visitors as $visitor) {
                    if ($visitor->bolum_id && $visitor->bolum_id !== $projectBolum->id) {
                        $visitorBolum = $visitor->bolum;
                        if ($visitorBolum) {
                            $visitorDirector = $visitorBolum->director;
                            $visitorLeaders = User::role('Bölüm Lideri')->where('bolum_id', $visitorBolum->id)->get();
                            
                            $managers = collect();
                            if ($visitorDirector) $managers->push($visitorDirector);
                            foreach ($visitorLeaders as $vl) $managers->push($vl);
                            $managers = $managers->unique('id');

                            $notificationStatus = $actionStatus === 'Direktör Onayı Bekliyor' ? 'Onaylandı' : $actionStatus;
                            foreach ($managers as $manager) {
                                $manager->notify(new ZiyaretYoneticiBildirimi($iaa, $visitor->name, $notificationStatus, $reason));
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Visit Workflow Notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Dönüş Tarihi Revizyonu için özel bildirimleri gönderir.
     */
    protected function dispatchDonusTarihiRevizyonNotifications(Iaa $iaa, $visitorName, $reason)
    {
        try {
            $ziyaretPlani = $iaa->ziyaretPlani;
            if (!$ziyaretPlani) return;

            $requesterId = \Illuminate\Support\Facades\Auth::id();
            $approverUserForNotify = null;
            $approverNameForMsg = 'İlgili Yönetici';

            // 1. Onaylayıcıyı Belirle (Approver)
            if ($ziyaretPlani->return_date_revision_status === 'Direktör Onayı Bekliyor') {
                $projectBolum = $iaa->bolum;
                if ($projectBolum && $projectBolum->director) {
                    $approverUserForNotify = $projectBolum->director;
                    $approverNameForMsg = $projectBolum->director->name . ' (Direktör)';
                }
            } else {
                if ($ziyaretPlani->approved_by) {
                    $approverUserForNotify = User::find($ziyaretPlani->approved_by);
                    if ($approverUserForNotify) {
                        $approverNameForMsg = $approverUserForNotify->name . ' (' . ($approverUserForNotify->unvan ?? 'Yetkili') . ')';
                    }
                } else {
                    $projectBolum = $iaa->bolum;
                    if ($projectBolum && $projectBolum->director) {
                        $approverUserForNotify = $projectBolum->director;
                        $approverNameForMsg = $projectBolum->director->name . ' (Direktör)';
                    }
                }
            }

            // A) Talebi İletene (Requester) Özel Bildirim
            if ($requesterId) {
                $requester = User::find($requesterId);
                if ($requester) {
                    $requester->notify(new \App\Notifications\DonusTarihiRevizyonTalebiBildirimi($iaa, $visitorName, $reason, 'requester', $approverNameForMsg));
                }
            }

            // B) Onaylayıcıya Bildirim (Requester Kendisi Değilse)
            if ($approverUserForNotify && $approverUserForNotify->id !== $requesterId) {
                $approverUserForNotify->notify(new \App\Notifications\DonusTarihiRevizyonTalebiBildirimi($iaa, $visitorName, $reason, 'approver'));
            }

            // C) Çözüm Lideri (Planner) Bildirimi (Requester veya Onaylayıcı değilse)
            $planner = $ziyaretPlani->planner;
            if ($planner && $planner->id !== $requesterId && (!$approverUserForNotify || $planner->id !== $approverUserForNotify->id)) {
                $planner->notify(new \App\Notifications\DonusTarihiRevizyonTalebiBildirimi($iaa, $visitorName, $reason, 'planner'));
            }

            // D) Bölüm Yöneticileri (Direktör, Lider, Lider Yardımcısı)
            $projectBolum = $iaa->bolum;
            if ($projectBolum) {
                $managers = collect();
                
                if ($projectBolum->director) $managers->push($projectBolum->director);
                
                $leaders = User::role('Bölüm Lideri')->where('bolum_id', $projectBolum->id)->get();
                foreach ($leaders as $l) $managers->push($l);
                
                $viceLeaders = User::role('Bölüm Lider Yardımcısı')->where('bolum_id', $projectBolum->id)->get();
                foreach ($viceLeaders as $vl) $managers->push($vl);

                $managers = $managers->unique('id');

                foreach ($managers as $manager) {
                    if ($manager->id === $requesterId) continue;
                    
                    $alreadySentAsApprover = ($approverUserForNotify && $manager->id === $approverUserForNotify->id);
                    $alreadySentAsPlanner = ($planner && $manager->id === $planner->id);
                    
                    if (!$alreadySentAsApprover && !$alreadySentAsPlanner) {
                        $manager->notify(new \App\Notifications\DonusTarihiRevizyonTalebiBildirimi($iaa, $visitorName, $reason, 'manager'));
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Return Date Revision Notification failed: ' . $e->getMessage());
        }
    }
}
