
<style>
    /* Force hide any known loader classes */
    .loader-wrapper, .loader, .loading, .loading-overlay, #loader {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        z-index: -1 !important;
    }
</style>
<script>
    // Force override global LOADER function
    window.LOADER = function(status) {
        console.log("Blocked LOADER call");
        return;
    };
    
    // Remove any existing loader elements on load
    $(document).ready(function() {
        $('.loader-wrapper, .loader, .loading, .loading-overlay, #loader').remove();
    });
</script>
