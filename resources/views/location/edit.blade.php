@extends('home')

@section('content')
    <div class="container">
        <h1>Konum Düzenle</h1>
        <form action="{{ route('location.update', $location->id) }}" method="POST" id="locationForm">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Konum Adı</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $location->name }}" required>
            </div>
            <div class="form-group">
                <label for="color">Renk</label>
                <input type="color" class="form-control" id="color" name="color" value="{{ $location->color }}" required>
            </div>
            <div class="form-group">
                <label for="latitude">Enlem</label>
                <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="{{ $location->latitude }}" readonly>
            </div>
            <div class="form-group">
                <label for="longitude">Boylam</label>
                <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="{{ $location->longitude }}" readonly>
            </div>
            <div id="map" style="height: 500px; margin: 20px 0;"></div>
            <button type="submit" class="btn btn-primary">Güncelle</button>
        </form>
    </div>

    <script>
        const map = L.map('map').setView([{{ $location->latitude }}, {{ $location->longitude }}], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        function createColoredMarkerIcon(color) {
            return L.divIcon({
                className: '',
                html: `<svg xmlns='http://www.w3.org/2000/svg' width='32' height='40' viewBox='0 0 32 40'><path d='M16 0C8.268 0 2 6.268 2 14c0 8.284 12.09 24.36 13.01 25.57a2 2 0 0 0 3.98 0C17.91 38.36 30 22.284 30 14c0-7.732-6.268-14-14-14zm0 20a6 6 0 1 1 0-12 6 6 0 0 1 0 12z' fill='${color}' stroke='#222' stroke-width='2'/><circle cx='16' cy='14' r='4' fill='#fff' opacity='0.7'/></svg>`,
                iconSize: [28, 36],
                iconAnchor: [14, 36],
                popupAnchor: [0, -40]
            });
        }

        let marker = L.marker(
            [{{ $location->latitude }}, {{ $location->longitude }}],
            { icon: createColoredMarkerIcon('{{ $location->color }}'), draggable: true }
        ).addTo(map);

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat;
            document.getElementById('longitude').value = position.lng;
        });

        // Renk değiştiğinde marker rengini güncelle
        document.getElementById('color').addEventListener('change', function(e) {
            const newColor = e.target.value;
            marker.setIcon(createColoredMarkerIcon(newColor));
        });
    </script>
@endsection
