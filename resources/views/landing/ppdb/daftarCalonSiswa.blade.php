@extends('layouts.ppdb')

@section('content-ppdb')
<section id="daftar-siswa-list" class="py-20 bg-neutral-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4" data-aos="fade-up">
            Daftar Calon Siswa PPDB
        </h2>
        <p class="text-xl text-gray-600 mb-12 max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Data ini menampilkan pendaftar yang telah berhasil mengirimkan formulir dan diverifikasi. Daftar diperbarui secara real-time.
        </p>

        <div class="mb-6 max-w-xl mx-auto" data-aos="fade-up" data-aos-delay="150">
            <div class="relative">
                <input type="text" id="search-input" placeholder="Cari No. Pendaftaran, Nama, NISN, atau Sekolah..."
                       class="w-full py-3 pl-12 pr-4 text-gray-700 bg-white border border-gray-300 rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-blue focus:border-primary-blue transition duration-200">
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2"></i>
            </div>
        </div>

        <div id="applicants-table-container" class="mt-8 relative" data-aos="fade-up" data-aos-delay="200">
            <div id="loading" class="text-center py-10 bg-white rounded-xl shadow-md transition-opacity duration-300">
                <i data-lucide="loader-circle" class="w-8 h-8 text-primary-blue mx-auto mb-3 animate-spin"></i>
                <p class="text-gray-700">Memuat data pendaftar...</p>
            </div>
            
            <div id="table-content" class="opacity-0 transition-opacity duration-500 ease-in-out">
                </div>
        </div>
        
        <div id="pagination-controls" class="mt-8 flex justify-center space-x-2 opacity-0 transition-opacity duration-300 ease-in-out">
            </div>

        <div class="mt-8">
            <p class="text-xs text-gray-400 mt-1">ID Sesi Pengguna: <span id="user-id-display">Memuat...</span></p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('applicants-table-container');
    const tableContent = document.getElementById('table-content');
    const loading = document.getElementById('loading');
    const searchInput = document.getElementById('search-input');
    const paginationControls = document.getElementById('pagination-controls');
    let allApplicants = [];
    const pageSize = 10; // Jumlah data per halaman
    let currentPage = 1;

    // --- 1. Fungsi Fetch Data Utama ---
    function fetchAllApplicants() {
        loading.style.display = 'block'; // Tampilkan loading saat fetch data awal
        
        // Asumsi endpoint mengembalikan SEMUA data, bukan hanya data per halaman
        fetch("{{ route('ppdb.daftarCalonSiswa') }}", { 
            headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        })
        .then(res => res.json())
        .then(data => {
            allApplicants = data.applicants || [];
            loading.style.display = 'none';
            renderTableAndPagination(allApplicants, currentPage);

            // Mulai interval update
            setInterval(fetchApplicantsUpdates, 10000); 
        })
        .catch(err => {
            console.error(err);
            loading.innerHTML = `
                <div class="text-center py-10 bg-white rounded-xl shadow-md border-b-4 border-red-500">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500 mx-auto mb-3"></i>
                    <p class="text-xl font-semibold text-gray-700">Gagal memuat data!</p>
                    <p class="text-gray-500">Periksa koneksi internet atau hubungi admin.</p>
                </div>
            `;
            loading.style.display = 'block';
        });
    }

    // --- 2. Fungsi Fetch Update (untuk real-time) ---
    // Fungsi ini hanya me-refresh data dan me-render ulang tabel pada halaman saat ini
    function fetchApplicantsUpdates() {
        fetch("{{ route('ppdb.daftarCalonSiswa') }}", { 
            headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        })
        .then(res => res.json())
        .then(data => {
            // Cek apakah jumlah data berubah, jika tidak, tidak perlu re-render
            if (data.applicants && data.applicants.length !== allApplicants.length) {
                 allApplicants = data.applicants;
                 const filteredApplicants = filterApplicants(allApplicants, searchInput.value);
                 renderTableAndPagination(filteredApplicants, currentPage);
            }
        })
        .catch(err => console.error("Update failed:", err));
    }


    // --- 3. Fungsi Filter Search ---
    function filterApplicants(applicants, searchTerm) {
        if (!searchTerm) return applicants;
        const lowerSearchTerm = searchTerm.toLowerCase();
        return applicants.filter(applicant => 
            (applicant.registration_number && applicant.registration_number.toLowerCase().includes(lowerSearchTerm)) ||
            (applicant.nama_lengkap && applicant.nama_lengkap.toLowerCase().includes(lowerSearchTerm)) ||
            (applicant.nisn && applicant.nisn.toLowerCase().includes(lowerSearchTerm)) ||
            (applicant.asal_sekolah && applicant.asal_sekolah.toLowerCase().includes(lowerSearchTerm)) ||
            (applicant.jurusan && applicant.jurusan.toLowerCase().includes(lowerSearchTerm))
        );
    }

    // --- 4. Fungsi Render Table & Pagination (Inti Perubahan) ---
    function renderTableAndPagination(data, page) {
        // Efek animasi: sembunyikan konten sebelum render
        tableContent.classList.remove('opacity-100');
        tableContent.classList.add('opacity-0');
        paginationControls.classList.remove('opacity-100');
        paginationControls.classList.add('opacity-0');

        // Tunggu sebentar (sesuai durasi transisi opacity) sebelum merender konten baru
        setTimeout(() => {
            const totalItems = data.length;
            const totalPages = Math.ceil(totalItems / pageSize);
            
            // Batasi halaman agar tidak melewati batas
            currentPage = Math.min(Math.max(1, page), totalPages || 1); 

            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const paginatedData = data.slice(start, end);

            // Render Table
            if (!paginatedData || paginatedData.length === 0) {
                 tableContent.innerHTML = `
                    <div class="text-center py-10 bg-white rounded-xl shadow-md border-b-4 border-secondary-green">
                        <i data-lucide="info" class="w-8 h-8 text-primary-blue mx-auto mb-3"></i>
                        <p class="text-xl font-semibold text-gray-700">${searchInput.value ? 'Pendaftar tidak ditemukan.' : 'Belum ada pendaftar.'}</p>
                        <p class="text-gray-500">${searchInput.value ? 'Coba kata kunci lain.' : 'Ayo segera daftar!'}</p>
                    </div>
                `;
            } else {
                let rows = '';
                paginatedData.forEach((applicant, index) => {
                    // Nomor Urut berdasarkan urutan global (start + index + 1)
                    const globalIndex = start + index + 1; 
                    const rowClass = globalIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                    rows += `
                        <tr class="${rowClass} hover:bg-yellow-50/50 transition duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${globalIndex}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-primary-blue">
                                ${applicant.registration_number}    
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${applicant.nisn || '-'}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">${applicant.nama_lengkap}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${applicant.asal_sekolah || '-'}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-secondary-green">${applicant.jurusan || '-'}</td>
                        </tr>
                    `;
                });

                tableContent.innerHTML = `
                    <div class="overflow-x-auto bg-white rounded-xl shadow-2xl shadow-primary-blue/20 border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-primary-blue text-white text-center sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">No. Urut</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">No. Pendaftaran</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">NISN</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Asal Sekolah</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Jurusan Dipilih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${rows}
                            </tbody>
                        </table>
                    </div>
                    <p class="text-sm text-gray-500 mt-6 text-center">
                        Total Pendaftar Saat Ini: <span class="font-bold text-lg text-primary-blue">${totalItems}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1 text-center">
                        Data diperbarui secara real-time dari database.
                    </p>
                `;
            }

            // Render Pagination
            renderPagination(totalPages, currentPage, data);

            // Efek animasi: tampilkan konten setelah render
            tableContent.classList.remove('opacity-0');
            tableContent.classList.add('opacity-100');
            paginationControls.classList.remove('opacity-0');
            paginationControls.classList.add('opacity-100');

            // Re-initialize lucide icons for the new content
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

        }, 500); // Durasi tunggu = Durasi transisi opacity (0.5 detik)
    }

    // --- 5. Fungsi Render Tombol Pagination ---
    function renderPagination(totalPages, currentPage, data) {
        if (totalPages <= 1 || data.length === 0) {
            paginationControls.innerHTML = '';
            return;
        }

        let buttons = '';

        // Tombol Sebelumnya
        buttons += `<button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-full hover:bg-gray-100 transition duration-150 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:border-primary-blue'}" 
                           data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
                        <i data-lucide="chevron-left" class="w-4 h-4 inline mr-1"></i> Prev
                    </button>`;

        // Tombol Halaman (Maksimal 5 tombol di tengah)
        const maxPagesToShow = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }
        
        // Halaman Awal
        if (startPage > 1) {
            buttons += `<button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-full hover:bg-gray-100 transition duration-150" data-page="1">1</button>`;
            if (startPage > 2) {
                buttons += `<span class="px-2 py-2 text-sm text-gray-500">...</span>`;
            }
        }


        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === currentPage 
                ? 'bg-primary-blue text-white border-primary-blue shadow-md' 
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100 hover:border-primary-blue';
            buttons += `<button class="pagination-btn px-4 py-2 text-sm font-medium rounded-full transition duration-150 ${activeClass}" 
                               data-page="${i}">${i}</button>`;
        }
        
        // Halaman Akhir
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                buttons += `<span class="px-2 py-2 text-sm text-gray-500">...</span>`;
            }
            buttons += `<button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-full hover:bg-gray-100 transition duration-150" data-page="${totalPages}">${totalPages}</button>`;
        }


        // Tombol Selanjutnya
        buttons += `<button class="pagination-btn px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-full hover:bg-gray-100 transition duration-150 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:border-primary-blue'}" 
                           data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
                        Next <i data-lucide="chevron-right" class="w-4 h-4 inline ml-1"></i>
                    </button>`;

        paginationControls.innerHTML = buttons;
    }

    // --- 6. Event Listeners ---

    // Event Listener untuk Search Input
    let searchTimeout;
    searchInput.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const filtered = filterApplicants(allApplicants, this.value);
            currentPage = 1; // Reset ke halaman 1 saat search
            renderTableAndPagination(filtered, currentPage);
        }, 300); // Debounce 300ms untuk performa
    });

    // Event Listener untuk Pagination
    paginationControls.addEventListener('click', function(e) {
        const button = e.target.closest('.pagination-btn');
        if (button && !button.disabled) {
            const newPage = parseInt(button.dataset.page);
            if (newPage !== currentPage) {
                currentPage = newPage;
                const filtered = filterApplicants(allApplicants, searchInput.value);
                renderTableAndPagination(filtered, currentPage);
                // Scroll ke atas tabel
                tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });

    // --- Inisialisasi ---
    fetchAllApplicants();
});
</script>
@endsection