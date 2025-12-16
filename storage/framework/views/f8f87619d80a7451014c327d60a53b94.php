<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(session('scroll_to_step')): ?>
            setTimeout(() => {
                const stepId = "<?php echo e(session('scroll_to_step')); ?>";
                const element = document.getElementById('step-card-' + stepId);
                
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    element.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                    setTimeout(() => element.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2'), 2000);
                }
            }, 500);
        <?php endif; ?>
    });
</script><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_scripts.blade.php ENDPATH**/ ?>