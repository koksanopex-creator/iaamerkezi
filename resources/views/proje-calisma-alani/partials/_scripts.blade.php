<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('scroll_to_step'))
            setTimeout(() => {
                const stepId = "{{ session('scroll_to_step') }}";
                const element = document.getElementById('step-card-' + stepId);
                
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    element.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                    setTimeout(() => element.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2'), 2000);
                }
            }, 500);
        @endif
    });
</script>