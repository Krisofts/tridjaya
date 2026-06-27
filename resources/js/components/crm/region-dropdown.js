// resources/js/components/crm/region-dropdown.js

async function fetchOptions(url, targetSelect, placeholder, selectedValue = null) {
    targetSelect.innerHTML = `<option value="">${placeholder}</option>`;
    targetSelect.disabled  = true;

    try {
        const res  = await fetch(url);
        const data = await res.json();

        data.forEach(item => {
            const opt       = document.createElement('option');
            opt.value       = item.code;
            opt.textContent = item.name;
            if (selectedValue && item.code == selectedValue) {
                opt.selected = true;
            }
            targetSelect.appendChild(opt);
        });

        targetSelect.disabled = false;
    } catch (e) {
        console.error('Gagal memuat data wilayah:', e);
        targetSelect.disabled = false;
    }
}

export function initRegionDropdown() {
    const provinceSelect = document.getElementById('province_code');
    const citySelect     = document.getElementById('city_code');
    const districtSelect = document.getElementById('district_code');

    if (!provinceSelect) return;

    // Nilai awal dari data-* attribute (di-set dari PHP via old() atau $lead->xxx)
    const initialCity     = provinceSelect.dataset.city     || null;
    const initialDistrict = provinceSelect.dataset.district || null;

    // Auto-load saat halaman dibuka (edit atau setelah validasi gagal)
    if (provinceSelect.value) {
        fetchOptions(
            `/region/cities/${provinceSelect.value}`,
            citySelect,
            '-- Pilih Kota --',
            initialCity
        ).then(() => {
            if (initialCity) {
                fetchOptions(
                    `/region/districts/${initialCity}`,
                    districtSelect,
                    '-- Pilih Kecamatan --',
                    initialDistrict
                );
            }
        });
    }

    // Perubahan manual oleh user
    provinceSelect.addEventListener('change', function () {
        citySelect.innerHTML     = '<option value="">-- Pilih Kota --</option>';
        districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        citySelect.disabled      = true;
        districtSelect.disabled  = true;

        if (this.value) {
            fetchOptions(`/region/cities/${this.value}`, citySelect, '-- Pilih Kota --');
        }
    });

    citySelect.addEventListener('change', function () {
        districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        districtSelect.disabled  = true;

        if (this.value) {
            fetchOptions(`/region/districts/${this.value}`, districtSelect, '-- Pilih Kecamatan --');
        }
    });
}