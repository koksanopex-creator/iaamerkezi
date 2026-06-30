<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; // Eklendi
use App\Services\Ai\GeminiService;
use App\Services\Ai\AiTools;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GlobalChatBot extends Component
{
    public $isOpen = false;
    public $messages = []; // [['role' => 'user|ai', 'content' => '...']]
    public $userMessage = '';
    public $isTyping = false;
    public $currentUrl = '';

    // protected $listeners = ['updateCurrentUrl' => 'setUrl']; // Kaldırıldı

    #[On('updateCurrentUrl')]
    public function setUrl($url) // Direkt parametre ismiyle eşleşir
    {
        $this->currentUrl = $url;
    }

    public function mount()
    {
        // Session'dan geçmişi yükle (Basit kalıcılık)
        if (session()->has('chat_history')) {
            $this->messages = session('chat_history');
        } else {
            // İlk karşılama mesajı
            $this->messages[] = [
                'role' => 'ai',
                'content' => 'Merhaba! Ben Köksan Portal Asistanınız. Size nasıl yardımcı olabilirim? (Örn: "Bu sayfa nedir?", "Bekleyen işlerim neler?")'
            ];
        }
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function sendMessage(GeminiService $gemini)
    {
        if (empty(trim($this->userMessage)))
            return;

        // 1. Kullanıcı mesajını ekle
        $this->messages[] = ['role' => 'user', 'content' => $this->userMessage];
        $this->userMessage = '';
        $this->isTyping = true;

        // Session güncelle
        session(['chat_history' => $this->messages]);
    }

    // Bu method frontend'de "wire:poll" veya "wire:loading" bittikten sonra tetiklenebilir
    // Ancak Livewire'da UI donmadan uzun işlem yapmak zordur.
    // Şimdilik senkron çalışacak ama UI'da loading spinner göstereceğiz.
    public function generateResponse(GeminiService $gemini)
    {
        // Son mesaj AI ise işlem yapma (tekrarlamayı önle)
        if (empty($this->messages) || end($this->messages)['role'] === 'ai') {
            $this->isTyping = false;
            return;
        }

        // 2. Gemini'ye Gönder (Tools ile beraber)
        // System Prompt'u history'ye 'user' gibi ekleyerek (veya context olarak) verebiliriz.
        // Kimlik bilgisini en başa ekleyelim (Context Injection)
        $context = AiTools::getDefinitions()['get_auth_user_info']['description']; // Basit referans
        // Asıl context'i ilk mesajda inject etmek daha iyi ama her request'te hatırlatmak gerekebilir.

        // Basit Chat Geçmişi Hazırla
        $historyForApi = $this->messages;

        // System Instruction Injection (Her zaman en güncel context)
        $user = Auth::user();
        // Sayfa bağlamını al
        $pageContext = $this->getPageContext($this->currentUrl);

        $systemMsg = "Sen kurumsal bir asistansın. Kullanıcı: {$user->name}, Bölüm: " . ($user->bolum->ad ?? 'Yok') . ". " .
            "Şu anki Sayfa: {$this->currentUrl}. " .
            "Sayfa Amacı / Bağlamı: {$pageContext} " . // <-- EKLENDİ
            "Rolün: Yetkili olunan verileri, tanımlı araçları (tools) kullanarak getirmek ve yönlendirme yapmak. " .
            "Asla yetki dışı bilgi uydurma. Veri tabanı ID'lerini kullanıcıya gösterme. " .
            "Sayfa hakkında soru sorulursa şu anki URL'ye göre ('$pageContext' bilgisi ışığında) cevap ver. " .
            "Bir sayfaya gitmek istenirse markdown link ver (örn: [Başlık](/url)). " .
            "Bilemediğin veya hata aldığın durumlarda ASLA 'İK ile görüşün' gibi genel şeyler söyleme. " .
            "Şu metni kullan: 'Sistem tasarımcısı Opex Biriminden Celal Karaman ile iletişime geçebilirsiniz. İletişim; Mail: celal.karaman@koksan.com Telefon: 0549 678 76 91' ";

        // API'ye gönderilecek history'nin başına system instruction ekleyelim (Gemini Flash bunu sever)
        array_unshift($historyForApi, ['role' => 'user', 'content' => "SYSTEM: " . $systemMsg]);

        // Tool Tanımlarını Al
        $toolsDef = AiTools::getDefinitions();

        // --- API ÇAĞRISI 1 ---
        $response = $gemini->chat($historyForApi, $toolsDef);

        if (isset($response['error'])) {
            $this->messages[] = ['role' => 'ai', 'content' => "Üzgünüm, bir hata oluştu: " . $response['error']];
            $this->isTyping = false;
            return;
        }

        // 3. Yanıt Analizi (Tool Call var mı?)
        $cadidate = $response['candidates'][0]['content'] ?? null;
        if (!$cadidate) {
            $this->messages[] = ['role' => 'ai', 'content' => "Üzgünüm, boş yanıt aldım."];
            $this->isTyping = false;
            return;
        }
        ;

        $parts = $cadidate['parts']; // Birden fazla parça olabilir (Text + FunctionCall)

        // Text yanıt varsa ekle
        foreach ($parts as $part) {
            if (isset($part['text'])) {
                // Sadece text ise direkt göster
                // $this->messages[] = ['role' => 'ai', 'content' => $part['text']];
                // Hemen ekleme, belki function call da vardır, hepsini birleştirelim veya function result'ı bekleyelim.
                // Basitlik için: Eğer function call varsa text'i görmezden gel veya "İşliyorum..." de.
            }

            if (isset($part['functionCall'])) {
                $fnName = $part['functionCall']['name'];
                $fnArgs = $part['functionCall']['args'] ?? [];

                // --- TOOL ÇALIŞTIRMA ---
                $toolClass = new AiTools();
                if (method_exists($toolClass, $fnName)) {
                    // Kullanıcıya bilgi ver
                    // $this->messages[] = ['role' => 'ai', 'content' => "🔄 $fnName çalıştırılıyor..."];

                    try {
                        $result = $toolClass->$fnName($fnArgs);
                    } catch (\Exception $e) {
                        $result = "Hata: " . $e->getMessage();
                    }

                    // --- API ÇAĞRISI 2 (Sonucu modele geri besle) ---
                    // Modelin function call çıktısını history'ye eklemeliyiz (Mock)
                    $historyForApi[] = ['role' => 'ai', 'content' => "TOOL_CALL: $fnName(" . json_encode($fnArgs) . ")"];
                    $historyForApi[] = ['role' => 'user', 'content' => "TOOL_RESULT: " . $result];

                    // Tekrar sor
                    $finalResponse = $gemini->chat($historyForApi); // Tools göndermeye gerek yok artık veya yeni tool chain için gönderebiliriz.

                    // Final yanıtı al
                    $finalText = $finalResponse['candidates'][0]['content']['parts'][0]['text'] ?? "Sonuç işlendi ancak yanıt üretilemedi.";

                    $this->messages[] = ['role' => 'ai', 'content' => $finalText];

                } else {
                    $this->messages[] = ['role' => 'ai', 'content' => "Hata: İstenen fonksiyon ($fnName) sistemde bulunamadı."];
                }

                $this->isTyping = false;
                session(['chat_history' => $this->messages]);
                return; // Döngüden çık
            }
        }

        // Eğer function call yoksa, sadece text ise
        if (isset($parts[0]['text'])) {
            $this->messages[] = ['role' => 'ai', 'content' => $parts[0]['text']];
        }

        $this->isTyping = false;
        session(['chat_history' => $this->messages]);
    }

    public function render()
    {
        return view('livewire.global-chat-bot');
    }

    // Chat geçmişini temizle
    public function clearHistory()
    {
        session()->forget('chat_history');
        $this->messages = [
            ['role' => 'ai', 'content' => 'Sohbet temizlendi. Nasıl yardımcı olabilirim?']
        ];
    }
    // URL'ye göre sayfa açıklamasını döndürür
    private function getPageContext($url)
    {
        $path = parse_url($url, PHP_URL_PATH);

        $definitions = [
            '/dashboard' => 'ANA SAYFA: Kişisel özetinizi, bekleyen işlerinizi ve performans puanlarınızı gördüğünüz merkezi ekran.',
            '/user/profile' => 'PROFİL SAYFASI: Kişisel bilgilerinizi, yetkinliklerinizi ve hesap ayarlarınızı yönettiğiniz alan.',
            '/admin/iaa-yonetim' => 'İAA (İYİLEŞTİRME) YÖNETİM PANELİ: Bu sayfada; Havuza düşen yeni fikirler onaylanır, "Talep" aşamasındaki projeler (Kalite/Yönetim onayı) incelenir ve projelere yönetici tarafından müdahale edilir.',
            '/admin/sikayetler' => 'MÜŞTERİ ŞİKAYETLERİ YÖNETİMİ: Müşterilerden gelen şikayetlerin listelendiği, takım atamalarının yapıldığı ve şikayet durumlarının değiştirildiği ana ekran.',
            '/admin/musteriler' => 'MÜŞTERİ TANIMLAMA EKRANI: Sisteme yeni müşteri firmalarının eklendiği veya mevcut firmaların düzenlendiği sayfa.',
            '/admin/disiplin' => 'DİSİPLİN KURULU PANELİ: Personel disiplin süreçlerinin, savunma ve ceza işlemlerinin yürütüldüğü yetkili ekranı.',
            '/takimlar' => 'TAKIMLAR VE LİDERLİK: Sistemdeki tüm aktif takımların, üyelerin ve takım puanlarının görüntülendiği sayfa.',
            '/puan-durumu' => 'PUAN VE SIRALAMA: Genel, bölüm bazlı veya takım bazlı performans sıralamalarının detaylı incelendiği ekran.'
        ];

        // Tam eşleşme kontrolü
        if (isset($definitions[$path])) {
            return $definitions[$path];
        }

        // Regex / Pattern kontrolü (Örn: /proje-calisma-alani/123)
        if (preg_match('#/proje-calisma-alani/\d+#', $path)) {
            return "PROJE ÇALIŞMA ALANI: Seçilen projenin detaylarının görüldüğü, adımların tamamlandığı ve yorumların yapıldığı aktif çalışma ekranı.";
        }
        if (preg_match('#/admin/disiplin/\d+/edit#', $path)) {
            return "DİSİPLİN DOSYASI DETAYI: İlgili disiplin tutanağının detaylarının incelendiği ve karar/düzenleme işlemlerinin yapıldığı ekran.";
        }
        if (preg_match('#/sikayet-detay/\d+#', $path)) {
            return "ŞİKAYET DETAY SAYFASI: Tek bir müşteri şikayetinin tüm tarihçesinin, dosyalarının ve aksiyonlarının görüntülendiği detay ekranı.";
        }

        return "Genel bir sistem sayfası. Sayfa içeriğine göre kullanıcıya yardımcı ol.";
    }
}
