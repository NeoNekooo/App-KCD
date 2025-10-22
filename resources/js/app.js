import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Sneat Vendor JS - Library dasar
import '~sneat/vendor/libs/jquery/jquery.js';
import '~sneat/vendor/libs/popper/popper.js';
import '~sneat/vendor/js/bootstrap.js';
import '~sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js';

// =================================================================
// ==> HELPER HARUS DI-IMPORT SEBELUM MENU DAN MAIN <==
// File ini mendefinisikan objek 'config' yang dibutuhkan oleh script lain.
import '~sneat/vendor/js/helpers.js';
// =================================================================

// Script yang bergantung pada 'helpers.js'
import '~sneat/vendor/js/menu.js';

// Sneat Core JS - Script utama tema
import '~sneat/js/main.js';

// Page-specific JS - Bisa diletakkan setelah script utama
import '~sneat/vendor/libs/apex-charts/apexcharts.js';
import '~sneat/js/dashboards-analytics.js';
import '~sneat/js/ui-modals.js';
import '~sneat/js/ui-toasts.js';
