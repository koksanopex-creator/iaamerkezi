<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Setting;
use Livewire\Component;
use App\Models\Customer;
use App\Models\BulkMailLog;
use App\Models\BulkMailRecipient;
use App\Jobs\SendBulkCustomerMail;

class TopluMusteriMail extends Component
{
    public $subject = '';
    public $messageContent = '';
    public $customers = [];
    public $selectedCustomers = []; // customer_id => true/false
    public $selectAll = true;

    // Tracking active log to show statuses
    public $activeLogId = null;
    public $recipientStatuses = []; // user_id => status

    public function mount()
    {
        $user = auth()->user();
        if (!$user->hasRole('Superadmin') && !$user->hasPermissionTo('toplu_mail_gonder')) {
            abort(403);
        }

        $this->loadAllowedCustomers($user);
    }

    public function loadAllowedCustomers($user)
    {
        // Check restrictions
        $restrictions = Setting::where('key', 'bulk_mail_restrictions')->first();
        $restrictedCustomerIds = [];

        if ($restrictions && $restrictions->value) {
            $data = json_decode($restrictions->value, true);
            $roleRestrictions = $data['roles'] ?? [];
            $userRestrictions = $data['users'] ?? [];

            // Add user specific restrictions
            if (isset($userRestrictions[$user->id])) {
                $restrictedCustomerIds = array_merge($restrictedCustomerIds, $userRestrictions[$user->id]);
            }

            // Add role specific restrictions
            foreach ($user->roles as $role) {
                if (isset($roleRestrictions[$role->id])) {
                    $restrictedCustomerIds = array_merge($restrictedCustomerIds, $roleRestrictions[$role->id]);
                }
            }
        }

        $restrictedCustomerIds = array_unique($restrictedCustomerIds);

        // Load customers that are NOT restricted
        $this->customers = Customer::whereNotIn('id', $restrictedCustomerIds)
            ->with(['users' => function($q) {
                $q->where('is_personnel', false)->whereNull('deleted_at');
            }])
            ->orderBy('name')
            ->get();

        foreach ($this->customers as $customer) {
            $this->selectedCustomers[$customer->id] = true;
        }
    }

    public function toggleSelectAll()
    {
        $this->selectAll = !$this->selectAll;
        foreach ($this->customers as $customer) {
            $this->selectedCustomers[$customer->id] = $this->selectAll;
        }
    }

    public function sendMail()
    {
        $this->validate([
            'subject' => 'required|min:3',
            'messageContent' => 'required|min:10',
        ], [
            'subject.required' => 'E-posta konusu zorunludur.',
            'messageContent.required' => 'Mesaj içeriği zorunludur.',
        ]);

        $selectedCustomerIds = array_keys(array_filter($this->selectedCustomers));
        if (empty($selectedCustomerIds)) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Hata',
                'text' => 'Lütfen en az bir firma seçin.',
            ]);
            return;
        }

        // Get unique users for selected customers
        $targetUsers = User::where('is_personnel', false)
                           ->whereHas('customer', function($q) use ($selectedCustomerIds) {
                               $q->whereIn('id', $selectedCustomerIds);
                           })->get();

        if ($targetUsers->isEmpty()) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Hata',
                'text' => 'Seçilen firmalara ait aktif müşteri temsilcisi bulunamadı.',
            ]);
            return;
        }

        // Create BulkMailLog
        $log = BulkMailLog::create([
            'sender_id' => auth()->id(),
            'subject' => $this->subject,
            'body' => $this->messageContent,
            'total_recipients' => $targetUsers->count(),
        ]);

        $this->activeLogId = $log->id;

        // Queue jobs and create recipients
        foreach ($targetUsers as $targetUser) {
            $recipient = BulkMailRecipient::create([
                'bulk_mail_log_id' => $log->id,
                'user_id' => $targetUser->id,
                'status' => 'queued',
            ]);

            $this->recipientStatuses[$targetUser->id] = 'queued';

            SendBulkCustomerMail::dispatch($recipient->id, clone clone auth()->user(), clone clone $targetUser, clone clone $log);
        }

        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Başarılı',
            'text' => 'E-postalar kuyruğa eklendi ve gönderimi başladı.',
        ]);

        // Formu sıfırla ama log takibi için statusler kalsın (kısmi sıfırlama)
        // $this->subject = '';
        // $this->messageContent = '';
    }

    public function pollStatuses()
    {
        if ($this->activeLogId) {
            $recipients = BulkMailRecipient::where('bulk_mail_log_id', $this->activeLogId)->get();
            foreach ($recipients as $recipient) {
                $this->recipientStatuses[$recipient->user_id] = $recipient->status;
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.toplu-musteri-mail')
            ->layout('layouts.app', ['header' => 'Müşterilere Toplu Mail Gönder']);
    }
}
