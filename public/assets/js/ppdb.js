


            // Data langkah-langkah formulir BARU (5 langkah total) - Tetap diperlukan untuk Progress Bar
    const STEPS = [
        { id: 1, title: 'Data Diri', name: 'data-diri' },
        { id: 2, title: 'Data Sekolah', name: 'data-sekolah' },
        { id: 3, title: 'Pilih Jurusan', name: 'pilih-jurusan' },
        { id: 4, title: 'Upload Persyaratan', name: 'upload-dokumen' },
        { id: 5, title: 'Selesai', name: 'selesai' }
    ];
    let currentStep = 1;
    const totalSteps = STEPS.length;
    // let formData = {}; // <-- DIHAPUS, data sekarang ada di DOM
    // Elemen-elemen DOM
    const stepIndicatorsEl = document.getElementById('stepIndicators');
    const formContentEl = document.getElementById('formContent');
    const progressBarEl = document.getElementById('progressBar');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const ppdbForm = document.getElementById('ppdb-form');
    const notification = document.getElementById('notification');
    const userIdDisplayEl = document.getElementById('user-id-display');
    // Elemen untuk daftar siswa
    const tableContainerEl = document.getElementById('applicants-table-container');
    // Elemen dan Fungsi untuk Active Link Highlighting (Tetap sama)
    const sections = document.querySelectorAll('section[id]');
    const navLinksDesktop = document.querySelectorAll('.nav-link-desktop');
    const navLinksMobile = document.querySelectorAll('.nav-link-mobile');
    /**
     * Memperbarui tautan navigasi agar aktif sesuai dengan section yang sedang terlihat.
     */
    function updateActiveLink() {
        const currentUrl = window.location.href;
        let currentSectionId = 'beranda'; // Default ke Beranda
                
        // --- Jika sedang di halaman route lain (bukan yang pakai #section) ---
        if (!currentUrl.includes('#')) {
            // Cek apakah URL cocok dengan salah satu link di navbar
            navLinksDesktop.forEach(link => {
                link.classList.remove('nav-link-desktop-active');
            
                const linkHref = link.href;
                // Kalau URL sekarang sama dengan link-nya
                if (currentUrl === linkHref || currentUrl.startsWith(linkHref)) {
                    link.classList.add('nav-link-desktop-active');
                }
            });
        
            navLinksMobile.forEach(link => {
                link.classList.remove('nav-link-mobile-active');
            
                const linkHref = link.href;
                if (currentUrl === linkHref || currentUrl.startsWith(linkHref)) {
                    link.classList.add('nav-link-mobile-active');
                }
            });
        
            // Hentikan fungsi di sini (biar nggak lanjut scroll spy)
            return;
        }
    
        // --- Kalau URL pakai anchor (#), jalankan logika scroll spy seperti biasa ---
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (window.scrollY >= sectionTop - 150) {
                currentSectionId = section.getAttribute('id');
            }
        });
    
        // --- Terapkan Kelas Aktif untuk Desktop ---
        navLinksDesktop.forEach(link => {
            link.classList.remove('nav-link-desktop-active');
            if (link.getAttribute('href').includes(currentSectionId)) {
                link.classList.add('nav-link-desktop-active');
            }
        });
    
        // --- Terapkan Kelas Aktif untuk Mobile ---
        navLinksMobile.forEach(link => {
            link.classList.remove('nav-link-mobile-active');
            if (link.getAttribute('href').includes(currentSectionId)) {
                link.classList.add('nav-link-mobile-active');
            }
        });
    }
    // ======================= LOGIKA FORM DAN UI =======================
    /**
     * Fungsi utilitas untuk menampilkan notifikasi kustom.
     */
    function showNotification(message, isError = false) {
        // ... (Fungsi ini tetap sama, tidak perlu diubah) ...
        notification.classList.remove('bg-secondary-green', 'bg-red-500');
        notification.classList.add(isError ? 'bg-red-500' : 'bg-secondary-green');
        const iconHtml = `<i data-lucide="${isError ? 'x-octagon' : 'check-circle'}" class="w-6 h-6"></i>`;
        notification.innerHTML = `${iconHtml}<span id="notification-message">${message}</span>`;
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 5000);
        lucide.createIcons();
    }
    /**
     * Validasi input wajib pada langkah saat ini.
     * FUNGSI INI DIMODIFIKASI untuk membaca dari div step yang aktif.
     */
    function validateStep(stepId) {
        // MODIFIKASI: Cari input hanya di dalam div step yang aktif
        const currentStepEl = document.getElementById('step-' + stepId);
        if (!currentStepEl) return true; // Lewati jika elemen tidak ditemukan
        const requiredInputs = currentStepEl.querySelectorAll('[required]');
        let valid = true;
        let errorMessages = [];
        // Helper function to mark input as invalid
        const markInvalid = (input) => {
            valid = false;
            input.classList.add('border-red-500', 'ring-2', 'ring-red-500/50');
        };
        // Remove previous error markings
        const allInputsInStep = currentStepEl.querySelectorAll('input, select, textarea');
        allInputsInStep.forEach(input => {
            input.classList.remove('border-red-500', 'ring-2', 'ring-red-500/50');
        });
        // 1. Check for empty required fields
        requiredInputs.forEach(input => {
            if (input.type === 'file') {
                // Cek jika input file wajib dan belum ada file yang dipilih
                if (input.files.length === 0) {
                    markInvalid(input);
                    if (!errorMessages.includes('Mohon lengkapi semua kolom bertanda *.')) {
                        errorMessages.push('Mohon lengkapi semua kolom bertanda *.');
                    }
                }
            } else if (input.value.trim() === "" || (input.tagName === 'SELECT' && input.value === "")) {
                markInvalid(input);
                if (!errorMessages.includes('Mohon lengkapi semua kolom bertanda *.')) {
                    errorMessages.push('Mohon lengkapi semua kolom bertanda *.');
                }
            }
        });
        if (!valid) {
            showNotification(errorMessages[0] || `Mohon lengkapi semua kolom bertanda * di langkah ${stepId}.`, true);
            return false;
        }
        // 2. Perform specific validation based on the step (Tetap sama)
        if (stepId === 1) { // Data Diri
            const nisnInput = currentStepEl.querySelector('#nisn');
            const waInput = currentStepEl.querySelector('#kontak-wa');
            if (nisnInput && nisnInput.value.trim().length > 0) {
                const nisnValue = nisnInput.value.trim();
                if (!/^\d{10}$/.test(nisnValue)) {
                    markInvalid(nisnInput);
                    errorMessages.push('NISN harus tepat 10 digit angka.');
                }
            }
            if (waInput && waInput.value.trim().length > 0) {
                const waValue = waInput.value.trim();
                if (!/^\d{10,}$/.test(waValue)) {
                    markInvalid(waInput);
                    errorMessages.push('Nomor WhatsApp tidak valid. Masukkan minimal 10 digit angka.');
                }
            }
        }
        if (errorMessages.length > 0) {
            const specificError = errorMessages.find(msg => msg !== 'Mohon lengkapi semua kolom bertanda *.');
            if (specificError) {
                showNotification(specificError, true);
                return false;
            }
            showNotification(errorMessages[0], true);
            return false;
        }
        return valid;
    }
    /**
     * Merender semua lingkaran indikator langkah pada progress bar.
     */
    function renderStepIndicators() {
        // ... (Fungsi ini tetap sama, tidak perlu diubah) ...
        stepIndicatorsEl.innerHTML = '';
        STEPS.forEach(step => {
            const isCompleted = step.id < currentStep;
            const isActive = step.id === currentStep;
            let circleClasses = 'w-10 h-10 flex items-center justify-center rounded-full text-white font-bold text-sm z-10 step-circle';
            let titleClasses = 'text-xs mt-3 w-max text-center absolute left-1/2 transform -translate-x-1/2 transition duration-300 ease-in-out';
            if (isCompleted) {
                circleClasses += ' bg-secondary-green scale-110';
                titleClasses += ' text-secondary-green font-semibold';
            } else if (isActive) {
                circleClasses += ' bg-primary-blue ring-4 ring-primary-blue/30 scale-110';
                titleClasses += ' text-primary-blue font-bold';
            } else {
                circleClasses += ' bg-gray-400/70 text-gray-100';
                titleClasses += ' text-gray-500';
            }
            const stepEl = document.createElement('div');
            stepEl.className = 'relative flex-1 text-center';
            const circleEl = document.createElement('div');
            circleEl.className = circleClasses + ' cursor-default mx-auto';
            circleEl.innerHTML = isCompleted ? `<i data-lucide="check" class="w-5 h-5"></i>` : step.id;
            const subTitleEl = document.createElement('span');
            subTitleEl.className = titleClasses;
            const stepTitleShort = step.title.split(' ')[0] + ' ' + (step.title.split(' ')[1] || '');
            subTitleEl.textContent = stepTitleShort.trim();
            subTitleEl.style.top = '56px';
            stepEl.appendChild(circleEl);
            stepEl.appendChild(subTitleEl);
            stepIndicatorsEl.appendChild(stepEl);
        });
        lucide.createIcons();
    }
    /**
     * Memperbarui tampilan bilah progres dan KONTEN FORMULIR (Show/Hide).
     * FUNGSI INI DIMODIFIKASI BESAR.
     */
    function updateProgress() {
        // 1. Update Progress Bar (Logika tetap sama)
        const progressSteps = totalSteps - 1;
        const progressPercentage = (currentStep === totalSteps) ? 100 : ((currentStep - 1) / (progressSteps - 1)) * 100;
        progressBarEl.style.width = `${progressPercentage}%`;
        // 2. Update Lingkaran Indikator (Logika tetap sama)
        renderStepIndicators();
        // 3. MODIFIKASI: Tampilkan div langkah yang benar dan sembunyikan yang lain
        // Sembunyikan semua langkah terlebih dahulu
        document.querySelectorAll('.form-step').forEach(stepEl => {
            stepEl.classList.add('hidden');
        });
        // Tampilkan langkah yang aktif
        const activeStepEl = document.getElementById('step-' + currentStep);
        if (activeStepEl) {
            activeStepEl.classList.remove('hidden');
        }
        
        // 4. MODIFIKASI: Jika di langkah terakhir, isi data summary
        if (currentStep === totalSteps) {
            // Ambil data langsung dari input DOM
            const nama = document.getElementById('nama_lengkap')?.value || '-';
            const jurusan = document.getElementById('jurusan')?.value || '-';
            const kontak = document.getElementById('kontak')?.value || '-';
            // Masukkan ke elemen summary
            document.getElementById('summary-nama').textContent = nama;
            document.getElementById('summary-jurusan').textContent = jurusan;
            document.getElementById('summary-kontak').textContent = kontak;
        }
        // 5. Mengelola status tombol Navigasi (Logika tetap sama)
        prevBtn.disabled = currentStep === 1;
        prevBtn.classList.toggle('opacity-50', currentStep === 1);
        if (currentStep === totalSteps) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }
    /**
     * Pindah ke langkah berikutnya.
     */
    window.nextStep = function () {
        if (currentStep < totalSteps) {
            // 1. Validasi langkah saat ini
            if (!validateStep(currentStep)) {
                return;
            }
            // 2. Simpan data (Sudah tidak perlu, data ada di DOM)
            // collectFormData(); // <-- DIHAPUS
            // 3. Pindah langkah dan scroll ke atas form
            currentStep++;
            updateProgress();
            document.getElementById('daftar').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    /**
     * Pindah ke langkah sebelumnya.
     */
    window.prevStep = function () {
        if (currentStep > 1) {
            // collectFormData(); // <-- DIHAPUS
            currentStep--;
            updateProgress();
            document.getElementById('daftar').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    // Logika Submit Formulir (pada Langkah Terakhir)
    document.addEventListener('DOMContentLoaded', function () {
        AOS.init({ once: true, duration: 800 });
        lucide.createIcons();
        // Panggil fungsi active link saat pertama kali dimuat
        updateActiveLink();
        // Tambahkan event listener untuk scroll dan resize
        window.addEventListener('scroll', updateActiveLink);
        window.addEventListener('resize', updateActiveLink);
        // Inisialisasi Tampilan Form
        updateProgress();
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const jalurSelect = document.getElementById('jalur_id');
        const uploadContainer = document.getElementById('syarat-container');
        jalurSelect.addEventListener('change', async function() {
            const jalurId = this.value;
            uploadContainer.innerHTML = '<p class="text-gray-500 text-sm">Memuat syarat...</p>';
        
            if (!jalurId) {
                uploadContainer.innerHTML = '<p class="text-gray-500 text-sm">Pilih jalur pendaftaran terlebih dahulu.</p>';
                return;
            }
        
            try {
                const response = await fetch(`/ppdb/api/syarat-by-jalur/${jalurId}`);
                const data = await response.json();
            
                if (data.length === 0) {
                    uploadContainer.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada syarat aktif untuk jalur ini.</p>';
                    return;
                }
            
                // Tampilkan input upload sesuai jumlah syarat aktif
                uploadContainer.innerHTML = '';
                data.forEach((syarat, index) => {
                    uploadContainer.innerHTML += `
                        <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <label class="block text-gray-700 font-semibold mb-2">
                                ${index + 1}. Unggah ${syarat.syarat} <span class="text-red-500">*</span>
                            </label>
                            <input type="hidden" name="syarat_id[]" value="${syarat.id}">
                            <input type="file" name="syarat_file_${syarat.id}" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-sm border border-gray-300 rounded-lg p-2 bg-gray-50 focus:ring focus:ring-primary-blue focus:border-primary-blue transition" required>
                        </div>
                    `;
                });
            
            } catch (error) {
                console.error('Error memuat syarat:', error);
                uploadContainer.innerHTML = '<p class="text-red-500 text-sm">Terjadi kesalahan saat memuat syarat.</p>';
            }
        });
    });
            
    