import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Vendor
import '~sneat/vendor/libs/jquery/jquery.js';
import '~sneat/vendor/libs/popper/popper.js';
import '~sneat/vendor/js/bootstrap.js';
import '~sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js';

// Helpers
import '~sneat/vendor/js/config.js';
import '~sneat/vendor/js/helpers.js';
import '~sneat/vendor/js/menu.js';

// Core
import '~sneat/js/main.js';

// Page specific
import '~sneat/vendor/libs/apex-charts/apexcharts.js';
import '~sneat/js/dashboards-analytics.js';
import '~sneat/js/ui-modals.js';
import '~sneat/js/ui-toasts.js';
