@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 fw-bold">Pengaturan Lokasi Kantor</h3>

    <form action="{{ route('lokasi.update') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-geo-alt me-2"></i>Koordinat Kantor</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info small">
                            Geser <strong>Pin Merah</strong> di peta untuk menentukan titik pusat kantor.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Latitude</label>
                            <input type="text" name="latitude" id="latitude" class="form-control bg-light" 
                                   value="{{ $lokasi->latitude }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Longitude</label>
                            <input type="text" name="longitude" id="longitude" class="form-control bg-light" 
                                   value="{{ $lokasi->longitude }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Radius Presensi (Meter)</label>
                            <div class="input-group">
                                <input type="number" name="radius_meter" id="radius" class="form-control" 
                                       value="{{ $lokasi->radius_meter }}">
                                <span class="input-group-text">Meter</span>
                            </div>
                            <div class="form-text text-muted">Jarak maksimal pegawai bisa absen.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Alamat (Opsional)</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ $lokasi->alamat }}</textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Lokasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-2">
                        <div id="map" style="height: 500px; width: 100%; border-radius: 10px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initMap" async defer></script>

<script>
    let map;
    let marker;
    let circle;

    function initMap() {
        // 1. Ambil Data Awal dari PHP
        const initialLat = {{ $lokasi->latitude }};
        const initialLng = {{ $lokasi->longitude }};
        const initialRadius = {{ $lokasi->radius_meter }};
        const pusatKantor = { lat: initialLat, lng: initialLng };

        // 2. Inisialisasi Peta
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 19, // Zoom dekat agar terlihat gedung
            center: pusatKantor,
            mapTypeId: google.maps.MapTypeId.HYBRID, // Mode Satelit + Jalan (Persis Flutter)
            streetViewControl: false,
            mapTypeControl: false,
        });

        // 3. Pasang Marker (Pin) yang bisa digeser
        marker = new google.maps.Marker({
            position: pusatKantor,
            map: map,
            draggable: true, // Bisa digeser
            title: "Geser Saya!",
            animation: google.maps.Animation.DROP
        });

        // 4. Pasang Lingkaran Radius
        circle = new google.maps.Circle({
            strokeColor: "#FF0000",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#FF0000",
            fillOpacity: 0.35,
            map: map,
            center: pusatKantor,
            radius: initialRadius, // Sesuai database
        });

        // === EVENT LISTENERS (Interaksi) ===

        // A. Saat Marker Digeser (Drag End)
        marker.addListener("dragend", function (event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();

            // Update Form Input
            document.getElementById("latitude").value = lat.toFixed(8);
            document.getElementById("longitude").value = lng.toFixed(8);

            // Pindahkan Lingkaran ikut Marker
            circle.setCenter(event.latLng);
            
            // Geser kamera peta
            map.panTo(event.latLng);
        });

        // B. Saat Marker Sedang Digeser (Drag) -> Lingkaran ikut bergerak real-time
        marker.addListener("drag", function (event) {
            circle.setCenter(event.latLng);
        });

        // C. Saat Input Radius Diubah -> Update Ukuran Lingkaran
        document.getElementById("radius").addEventListener("input", function () {
            const newRadius = parseFloat(this.value);
            if (!isNaN(newRadius)) {
                circle.setRadius(newRadius);
            }
        });
    }
</script>
@endsection