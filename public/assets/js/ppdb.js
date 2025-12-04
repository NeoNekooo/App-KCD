// ======================= Inisialisasi =======================
const formElement = document.getElementById('ppdb-form');
const tingkatAktif = parseInt(formElement?.dataset.tingkat || 0, 10);

// Tentukan langkah-langkah berdasarkan tingkat
let STEPS = [];

if (tingkatAktif === 10) {
    STEPS = [
        { id: 1, title: 'Data Diri' },
        { id: 2, title: 'Data Sekolah' },
        { id: 3, title: 'Pilih Jurusan' },
        { id: 4, title: 'Upload Persyaratan' },
        { id: 5, title: 'Selesai' },
    ];
} else {
    STEPS = [
        { id: 1, title: 'Data Diri' },
        { id: 2, title: 'Data Sekolah' },
        { id: 3, title: 'Upload Persyaratan' },
        { id: 4, title: 'Selesai' },
    ];
}

// ======================= Variabel DOM =======================
let currentStep = 1;
const totalSteps = STEPS.length;

const stepIndicatorsEl = document.getElementById('stepIndicators');
const formContentEl = document.getElementById('formContent');
const progressBarEl = document.getElementById('progressBar');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const submitBtn = document.getElementById('submitBtn');
const notification = document.getElementById('notification');

function markInvalid(input) {
    if (!input) return;
    input.classList.add('border-red-500', 'ring-2', 'ring-red-500/50');
}

// ======================= Fungsi Utilitas =======================
function showNotification(message, isError = false) {
    notification.classList.remove('bg-secondary-green', 'bg-red-500');
    notification.classList.add(isError ? 'bg-red-500' : 'bg-secondary-green');
    const iconHtml = `<i data-lucide="${isError ? 'x-octagon' : 'check-circle'}" class="w-6 h-6"></i>`;
    notification.innerHTML = `${iconHtml}<span id="notification-message" class="ml-2">${message}</span>`;
    notification.classList.add('show');
    setTimeout(() => {
        notification.classList.remove('show');
    }, 5000);
    lucide.createIcons();
}

// Validasi step saat ini
function validateStep(stepId) {
    const currentStepEl = document.getElementById('step-' + stepId);
    if (!currentStepEl) return true;
    const requiredInputs = currentStepEl.querySelectorAll('[required]');
    let valid = true;

    currentStepEl.querySelectorAll('input, select, textarea').forEach(input => {
        input.classList.remove('border-red-500', 'ring-2', 'ring-red-500/50');
    });

    requiredInputs.forEach(input => {
        if (input.type === 'file') {
            if (input.files.length === 0) markInvalid(input);
        } else if (input.value.trim() === "" || (input.tagName === 'SELECT' && input.value === "")) {
            markInvalid(input);
        }
    });

    if (!valid) {
        showNotification('Mohon lengkapi semua kolom bertanda *.', true);
        return false;
    }

    // Validasi khusus step 1
    if (stepId === 1) {
        const nisnInput = currentStepEl.querySelector('#nisn');
        if (nisnInput && !/^\d{10}$/.test(nisnInput.value.trim())) {
            markInvalid(nisnInput);
            showNotification('NISN harus tepat 10 digit angka.', true);
            return false;
        }
        const waInput = currentStepEl.querySelector('#kontak');
        if (waInput && !/^\d{10,}$/.test(waInput.value.trim())) {
            markInvalid(waInput);
            showNotification('Nomor WhatsApp tidak valid (minimal 10 digit).', true);
            return false;
        }
    }

    return true;
}

// Render indikator step
function renderStepIndicators() {
    stepIndicatorsEl.innerHTML = '';
    STEPS.forEach(step => {
        const isCompleted = step.id < currentStep;
        const isActive = step.id === currentStep;

        const circleEl = document.createElement('div');
        circleEl.className = `w-10 h-10 flex items-center justify-center rounded-full text-white font-bold text-sm z-10 mx-auto cursor-default step-circle`;
        if (isCompleted) circleEl.classList.add('bg-secondary-green', 'scale-110');
        else if (isActive) circleEl.classList.add('bg-primary-blue', 'ring-4', 'ring-primary-blue/30', 'scale-110');
        else circleEl.classList.add('bg-gray-400/70', 'text-gray-100');

        circleEl.innerHTML = isCompleted ? `<i data-lucide="check" class="w-5 h-5"></i>` : step.id;

        const titleEl = document.createElement('span');
        titleEl.textContent = step.title.split(' ')[0] + ' ' + (step.title.split(' ')[1] || '');
        titleEl.className = 'text-xs mt-3 w-max text-center absolute left-1/2 transform -translate-x-1/2 transition duration-300 ease-in-out';
        titleEl.style.top = '56px';
        if (isCompleted) titleEl.classList.add('text-secondary-green', 'font-semibold');
        else if (isActive) titleEl.classList.add('text-primary-blue', 'font-bold');
        else titleEl.classList.add('text-gray-500');

        const wrapper = document.createElement('div');
        wrapper.className = 'relative flex-1 text-center';
        wrapper.appendChild(circleEl);
        wrapper.appendChild(titleEl);

        stepIndicatorsEl.appendChild(wrapper);
    });
    lucide.createIcons();
}

// Update progress bar & konten
function updateProgress() {
    const progressSteps = totalSteps - 1; // step terakhir adalah Selesai
    const progressPercentage = ((currentStep - 1) / progressSteps) * 100;
    progressBarEl.style.width = `${progressPercentage}%`;

    renderStepIndicators();

    // sembunyikan semua step
    document.querySelectorAll('.form-step').forEach(el => el.classList.add('hidden'));
    // tampilkan step aktif
    const activeStepEl = document.getElementById('step-' + currentStep);
    if (activeStepEl) activeStepEl.classList.remove('hidden');

    // update summary di step terakhir
    if (currentStep === totalSteps) {
        document.getElementById('summary-nama').textContent = document.getElementById('nama_lengkap')?.value || '-';
        if (tingkatAktif === 10) document.getElementById('summary-jurusan').textContent = document.getElementById('jurusan')?.value || '-';
        document.getElementById('summary-kontak').textContent = document.getElementById('kontak')?.value || '-';
    }

    prevBtn.disabled = currentStep === 1;
    prevBtn.classList.toggle('opacity-50', currentStep === 1);

    // ===== perbaikan tombol next/submit =====
    if (currentStep === totalSteps) {
        nextBtn.classList.add('hidden');
        submitBtn.classList.remove('hidden');
    } else {
        nextBtn.classList.remove('hidden');
        submitBtn.classList.add('hidden');
    }
}

// ======================= Navigasi Step =======================
window.nextStep = async function () {

    if (currentStep < totalSteps) {

        if (!validateStep(currentStep)) return;

        // ===== CEK NISN DULU kalau masih di step 1 =====
        if (currentStep === 1) {
            const nisInput = document.getElementById('nisn');
            if (nisInput) {
                const nisn = nisInput.value.trim();

                try {
                    const res = await fetch(`/ppdb/check-nisn?nisn=${nisn}`);
                    const data = await res.json();

                    if (data.exists) {
                        const nisInput = document.getElementById('nisn');
                        markInvalid(nisInput);
                        showNotification('NISN sudah terdaftar. Silakan gunakan yang lain.', true);
                        return;
                    }
                } catch (err) {
                    showNotification('Gagal memeriksa NISN.', true);
                    return;
                }
            }
        }

        // ===== lanjut kalau aman =====
        currentStep++;
        updateProgress();
        document.getElementById('daftar').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};


window.prevStep = function () {
    if (currentStep > 1) {
        currentStep--;
        updateProgress();
        document.getElementById('daftar').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// ======================= Syarat File Dinamis =======================
document.addEventListener('DOMContentLoaded', function() {
    const jalurSelect = document.getElementById('jalur_id');
    const uploadContainer = document.getElementById('syarat-container');
    if (!jalurSelect) return;

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

            if (!data.length) {
                uploadContainer.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada syarat aktif untuk jalur ini.</p>';
                return;
            }

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

// ======================= Inisialisasi Form =======================
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({ once: true, duration: 800 });
    lucide.createIcons();
    updateProgress();
});
