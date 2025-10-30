import './bootstrap';

// BURADAN 'import Alpine' VE 'Alpine.start()' SATIRLARINI SİLDİK.
// ALPINE'I @livewireScripts BAŞLATACAK.


// --- Senin Özel tableManager Kodun (Hata Kontrollü) ---
function tableManager() {
    return {
        selectedIds: [],
        get isAllSelected() {
            // === DÜZELTME ===
            // this.$el (componentin ana elementi) var mı diye kontrol et
            const checkboxes = this.$el?.querySelectorAll('tbody .iaa-checkbox');
            // Eğer checkbox yoksa (başka sayfadaysak) false dön
            if (!checkboxes) return false; 
            return checkboxes.length > 0 && this.selectedIds.length === checkboxes.length;
        },
        toggleAll() {
            // === DÜZELTME ===
            const checkboxes = this.$el?.querySelectorAll('tbody .iaa-checkbox');
            // Checkbox yoksa hiçbir şey yapma
            if (!checkboxes) return; 

            const tableIds = Array.from(checkboxes).map(cb => cb.value);
            if (this.isAllSelected) {
                this.selectedIds = this.selectedIds.filter(id => !tableIds.includes(id));
            } else {
                tableIds.forEach(id => {
                    if (!this.selectedIds.includes(id)) {
                        this.selectedIds.push(id);
                    }
                });
            }
        },
        submitBulkDelete() {
            // === DÜZELTME ===
            // this.$el (componentin ana elementi) var mı diye kontrol et
            if (!this.$el?.dataset?.bulkDeleteUrl) return; 

            if (confirm('Seçili ' + this.selectedIds.length + ' adet öneriyi kalıcı olarak silmek istediğinizden emin misiniz?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = this.$el.dataset.bulkDeleteUrl;
                
                const csrfTokenInput = document.createElement('input');
                csrfTokenInput.type = 'hidden';
                csrfTokenInput.name = '_token';
                csrfTokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfTokenInput);

                this.selectedIds.forEach(id => {
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'iaa_ids[]';
                    idInput.value = id;
                    form.appendChild(idInput);
                });
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}

// Livewire Alpine'ı başlattığında bu event tetiklenecek
document.addEventListener('alpine:init', () => {
    Alpine.data('tableManager', tableManager);
})