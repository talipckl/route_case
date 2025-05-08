@extends('home')

@section('content')
    <div id="map" style="height: 500px;"></div>
    @if ($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #ef4444; padding: 12px; border-radius: 8px; margin: 20px auto; max-width: 500px;">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li style="color: #991b1b; margin-bottom: 4px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('location.store') }}" method="post">
        @csrf
        <div style="display: flex; justify-content: center; margin-top: 24px;">
            <div
            style="background: #fff; box-shadow: 0 2px 8px #0001; border-radius: 12px; padding: 24px 32px; display: flex; flex-direction: column; gap: 16px; align-items: center; min-width: 320px;">
            <div style="display: flex; gap: 12px; width: 100%;">
                <div style="flex: 1;">
                    <label for="latitude" style="display: block; margin-bottom: 4px; font-size: 15px; color: #333;">Enlem (Latitude)</label>
                    <input type="text" id="latitude" name="latitude" placeholder="Enlem (Latitude)"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px;" readonly>
                </div>
                <div style="flex: 1;">
                    <label for="longitude" style="display: block; margin-bottom: 4px; font-size: 15px; color: #333;">Boylam (Longitude)</label>
                    <input type="text" id="longitude" name="longitude" placeholder="Boylam (Longitude)"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px;"
                        readonly>
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center; width: 100%;">
                <input type="text" required id="locationName" name="name" placeholder="Konum Adı"
                    style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px;">
            </div>
            <div style="display: flex; gap: 12px; align-items: center; width: 100%;">
                <label for="markerColor" style="font-size: 15px; color: #333;">Marker Rengi:</label>
                <input type="color" id="markerColor" name="color" value="#f53003"
                    style="width: 36px; height: 36px; border: none; background: none; cursor: pointer;">
            </div>
            <button type="submit" style="background: #007bff; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                Konum Ekle
            </button>
        </div>
    </form>
    <script>
        const map = L.map('map').setView([20, 0], 2); // Dünya haritası, global başlangıç

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker;

        // Dinamik renkli marker ikonu oluşturucu
        function createColoredMarkerIcon(color) {
            // Klasik lokasyon pini SVG'si
            return L.divIcon({
                className: '',
                html: `<svg xmlns='http://www.w3.org/2000/svg' width='32' height='40' viewBox='0 0 32 40'><path d='M16 0C8.268 0 2 6.268 2 14c0 8.284 12.09 24.36 13.01 25.57a2 2 0 0 0 3.98 0C17.91 38.36 30 22.284 30 14c0-7.732-6.268-14-14-14zm0 20a6 6 0 1 1 0-12 6 6 0 0 1 0 12z' fill='${color}' stroke='#222' stroke-width='2'/><circle cx='16' cy='14' r='4' fill='#fff' opacity='0.7'/></svg>`,
                iconSize: [28, 36],
                iconAnchor: [14, 36],
                popupAnchor: [0, -40]
            });
        }

        // Haritaya tıklayınca marker ekle
        map.on('click', function(e) {
            const {
                lat,
                lng
            } = e.latlng;
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            const color = document.getElementById('markerColor').value;
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng], {
                icon: createColoredMarkerIcon(color)
            }).addTo(map);
        });
    </script>
@endsection
