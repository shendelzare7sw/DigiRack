<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<!-- Peta Modal -->
<div x-data="addressModalLogic()" x-show="open" @open-address-modal.window="handleModalOpen($event.detail)" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                @click.away="open = false" 
                class="relative transform sm:rounded-2xl bg-white text-left shadow-xl transition-all w-full h-[100dvh] sm:h-auto sm:my-8 sm:max-w-4xl flex flex-col sm:max-h-[90vh]">
                
                <!-- HEADER -->
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl shrink-0">
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title" x-text="isEdit ? 'Ubah Alamat' : 'Tambah Alamat Baru'"></h3>
                    <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg p-1.5 transition-colors focus:outline-none">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <!-- BODY -->
                <form :action="formAction" method="POST" class="flex flex-col overflow-hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                    <input type="hidden" name="latitude" x-model="form.lat">
                    <input type="hidden" name="longitude" x-model="form.lng">

                    <div class="px-4 sm:px-6 py-5 overflow-y-auto flex-1 custom-scrollbar">
                        <div class="flex flex-col md:flex-row gap-6 md:h-full">
                            
                            <!-- Left Column: Form Info -->
                            <div class="space-y-4 md:flex-1">
                                <h4 class="font-bold text-gray-900 border-b pb-2 mb-4">Detail Kontak</h4>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Label Alamat</label>
                                    <input type="text" name="label" x-model="form.label" required placeholder="Kantor, Rumah, Apartemen" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl py-2 px-3">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Penerima</label>
                                        <input type="text" name="recipient_name" x-model="form.recipient_name" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. HP</label>
                                        <input type="text" name="phone" x-model="form.phone" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl py-2 px-3">
                                    </div>
                                </div>

                                <h4 class="font-bold text-gray-900 border-b pb-2 mb-4 mt-6">Lokasi</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                                        <select name="province_id" x-model="form.province_id" @change="onProvinceChange()" required
                                            class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl py-2 px-3 bg-white">
                                            <option value="">Pilih Provinsi</option>
                                            <template x-for="prov in provincesList" :key="prov.id">
                                                <option :value="prov.id" x-text="prov.name"></option>
                                            </template>
                                        </select>
                                        {{-- Hidden text field auto-filled --}}
                                        <input type="hidden" name="province" :value="selectedProvinceName">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                        <select name="city_id" x-model="form.city_id" @change="onCityChange()" :disabled="citiesList.length === 0" required
                                            class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl py-2 px-3 bg-white disabled:bg-gray-100">
                                            <option value="">Pilih Kota</option>
                                            <template x-for="c in citiesList" :key="c.id">
                                                <option :value="c.id" x-text="c.type + ' ' + c.name"></option>
                                            </template>
                                        </select>
                                        <input type="hidden" name="city" :value="selectedCityName">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Pos</label>
                                        <input type="text" name="postal_code" x-model="form.postal_code" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl py-2 px-3">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                                    <textarea name="full_address" x-model="form.full_address" required rows="3" placeholder="Nama jalan, gedung, blok, RT/RW..." class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl py-2 px-3"></textarea>
                                </div>

                                <div class="pt-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="is_primary" value="1" x-model="form.is_primary" class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue w-4 h-4 mr-2">
                                        Jadikan Alamat Utama
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Right Column: Map -->
                            <div class="space-y-3 flex flex-col md:flex-1 order-first md:order-last border-b border-gray-200 pb-8 md:border-b-0 md:pb-0 mb-4 md:mb-0">
                                <h4 class="font-bold text-gray-900 border-b pb-2 mb-2">Tandai Lokasi Map</h4>
                                <p class="text-xs text-gray-500">Geser pin biru ke lokasi tujuan untuk mempermudah kurir menemukan alamat Anda.</p>
                                
                                <div id="leafletMap" class="w-full rounded-xl border border-gray-300 shadow-inner h-[250px] md:h-auto md:flex-1 z-0 relative"></div>
                                
                                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-800 flex gap-2 items-start mt-2">
                                    <x-icon name="information-circle" class="w-4 h-4 text-brand-blue shrink-0 mt-0.5" />
                                    <div>Koordinat: <span class="font-mono bg-white px-1 ml-1 rounded border border-blue-200" x-text="(form.lat || '-') + ', ' + (form.lng || '-')"></span></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl shrink-0">
                        <button type="button" @click="open = false" class="bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-6 rounded-xl text-sm transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="bg-brand-navy hover:bg-brand-navydark border-2 border-brand-navy text-white font-bold py-2.5 px-8 rounded-xl text-sm transition-colors shadow-sm">
                            Simpan Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet JS & Alpine Logic --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('addressModalLogic', () => ({
            open: false,
            isEdit: false,
            map: null,
            marker: null,
            formAction: '{{ route('profile.addresses.store') }}',
            provincesList: [],
            citiesList: [],
            form: {
                id: null,
                label: '',
                recipient_name: '{{ Auth::user()->name }}',
                phone: '{{ Auth::user()->phone }}',
                province: '',
                province_id: '',
                city: '',
                city_id: '',
                postal_code: '',
                full_address: '',
                is_primary: false,
                lat: '-6.200000',
                lng: '106.816666'
            },

            get selectedProvinceName() {
                const p = this.provincesList.find(x => x.id == this.form.province_id);
                return p ? p.name : this.form.province;
            },

            get selectedCityName() {
                const c = this.citiesList.find(x => x.id == this.form.city_id);
                return c ? (c.type + ' ' + c.name) : this.form.city;
            },

            async loadProvinces() {
                try {
                    const res = await fetch('/api/locations/provinces');
                    this.provincesList = await res.json();
                } catch(e) { console.error('Gagal load provinsi'); }
            },

            async onProvinceChange() {
                this.citiesList = [];
                this.form.city_id = '';
                if (!this.form.province_id) return;
                try {
                    const res = await fetch(`/api/locations/cities/${this.form.province_id}`);
                    this.citiesList = await res.json();
                } catch(e) { console.error('Gagal load kota'); }
            },

            onCityChange() {
                // Auto-fill text fields for backward compat
            },

            async handleModalOpen(detail) {
                await this.loadProvinces();

                this.isEdit = detail.isEdit;
                if (this.isEdit) {
                    this.formAction = `/profile/addresses/${detail.address.id}`;
                    this.form = {
                        id: detail.address.id,
                        label: detail.address.label,
                        recipient_name: detail.address.recipient_name,
                        phone: detail.address.phone,
                        province: detail.address.province,
                        province_id: detail.address.province_id || '',
                        city: detail.address.city,
                        city_id: detail.address.city_id || '',
                        postal_code: detail.address.postal_code,
                        full_address: detail.address.full_address,
                        is_primary: detail.address.is_primary,
                        lat: detail.address.latitude || '-6.200000',
                        lng: detail.address.longitude || '106.816666'
                    };
                    // Load cities for the saved province
                    if (this.form.province_id) {
                        await this.onProvinceChange();
                        // Re-set city_id after cities load
                        this.form.city_id = detail.address.city_id || '';
                    }
                } else {
                    this.formAction = '{{ route('profile.addresses.store') }}';
                    this.form = {
                        id: null,
                        label: '',
                        recipient_name: '{{ Auth::user()->name }}',
                        phone: '{{ Auth::user()->phone }}',
                        province: '',
                        province_id: '',
                        city: '',
                        city_id: '',
                        postal_code: '',
                        full_address: '',
                        is_primary: false,
                        lat: '-6.200000',
                        lng: '106.816666'
                    };
                    this.citiesList = [];
                    
                    if("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition((pos) => {
                            this.form.lat = pos.coords.latitude;
                            this.form.lng = pos.coords.longitude;
                            if(this.map && this.marker) {
                                let newLatLng = new L.LatLng(this.form.lat, this.form.lng);
                                this.map.setView(newLatLng, 15);
                                this.marker.setLatLng(newLatLng);
                            }
                        });
                    }
                }
                
                this.open = true;
                setTimeout(() => { this.initMap(); }, 300);
            },

            initMap() {
                if (this.map) {
                    this.map.remove();
                }

                this.map = L.map('leafletMap').setView([this.form.lat, this.form.lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(this.map);

                this.marker = L.marker([this.form.lat, this.form.lng], {
                    draggable: true
                }).addTo(this.map);

                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.form.lat = pos.lat.toFixed(6);
                    this.form.lng = pos.lng.toFixed(6);
                });

                this.map.on('click', (e) => {
                    const pos = e.latlng;
                    this.marker.setLatLng(pos);
                    this.form.lat = pos.lat.toFixed(6);
                    this.form.lng = pos.lng.toFixed(6);
                });

                this.map.invalidateSize();
            }
        }));
    });
</script>
