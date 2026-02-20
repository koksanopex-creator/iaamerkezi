<?php if(isset($siteLogo) && $siteLogo): ?>
    <img loading="eager" fetchpriority="high" src="<?php echo e(asset('storage/' . $siteLogo)); ?>" alt="Site Logosu" <?php echo e($attributes); ?>>
<?php else: ?>
    <img src="<?php echo e(asset('favicon.png')); ?>" alt="Köksan Logo" <?php echo e($attributes); ?>>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/components/application-logo.blade.php ENDPATH**/ ?>