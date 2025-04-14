<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Peta Lokasi</h3>

                <!-- Tombol untuk menghapus marker -->
                <button id="clearMarkers" class="px-4 py-2 bg-red-500 text-white rounded mb-4">
                    Hapus Semua Marker
                </button>

                <!-- Tombol untuk menyimpan lokasi -->
                <button id="saveLocation" class="px-4 py-2 bg-red-500 text-white rounded mb-4 ml-2">
                    Simpan Lokasi Terakhir
                </button>

                <div id="map" style="height: 500px;"></div>
            </div>
        </div>
    </div>

    {{-- Tambahkan Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Tambahkan Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var map = L.map('map').setView([-8.3405, 115.0920], 10); // Koordinat awal (Bali)

            // Variabel untuk menyimpan lokasi terakhir yang diklik
            var lastClickedLocation = null;

            // Definisikan berbagai jenis peta
            var standardMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            });

            var satelliteMap = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google Satellite'
            });

            var terrainMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenTopoMap contributors'
            });

            // Set default layer peta ke Standard Map
            standardMap.addTo(map);

            // Tambahkan Layer Control untuk Mode Switch
            var baseMaps = {
                "Standar": standardMap,
                "Satelit": satelliteMap,
                "Terrain": terrainMap
            };
            L.control.layers(baseMaps).addTo(map);

            // Array untuk menyimpan semua marker
            var markers = [];

            // Tambah marker saat peta diklik
            map.on('click', function (e) {
                var lat = e.latlng.lat.toFixed(6); // 6 angka di belakang koma
                var lng = e.latlng.lng.toFixed(6);

                // Reverse Geocoding dari Nominatim
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        var locationName = data.display_name || "Tidak diketahui";

                        var newMarker = L.marker([lat, lng]).addTo(map)
                            .bindPopup(
                                "<b>Koordinat :</b><br>" +
                                "<b>Latitude  :</b> " + lat + "<br>" +
                                "<b>Longitude :</b> " + lng + "<br>" +
                                "<b>Lokasi    :</b> " + locationName
                            )
                            .openPopup();

                        markers.push(newMarker);
                        lastClickedLocation = { lat: lat, lng: lng, name: locationName };
                    })
                    .catch(error => console.log("Terjadi kesalahan:", error));
            });

            // Hapus semua marker
            document.getElementById("clearMarkers").addEventListener("click", function () {
                markers.forEach(marker => {
                    map.removeLayer(marker);
                });
                markers = [];
            });

            // Simpan lokasi terakhir ke database
            document.getElementById("saveLocation").addEventListener("click", function () {
                if (!lastClickedLocation) {
                    alert("Silakan klik peta terlebih dahulu!");
                    return;
                }

                fetch("/save-location", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        location_name: lastClickedLocation.name,
                        latitude: lastClickedLocation.lat,
                        longitude: lastClickedLocation.lng
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message || "Lokasi berhasil disimpan!");
                })
                .catch(error => {
                    console.error("Gagal menyimpan lokasi:", error);
                    alert("Terjadi kesalahan saat menyimpan lokasi.");
                });
            });
        });
    </script>
</x-app-layout>
