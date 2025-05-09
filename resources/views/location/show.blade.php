@extends('home')

@section('content')
    <div class="container">
            <div id="map" style="height: 500px; margin: 20px 0;"></div>
            <br>
            <div class="flex flex-row gap-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Konum Bilgileri</h5>
                    <p class="card-text">
                        <strong>Konum Adı:</strong> {{ $location->name }}
                    </p>
                    <p class="card-text">
                        <strong>Renk:</strong> 
                        <span style="display: inline-flex; align-items: center;">
                            {{ $location->color }}
                            <span style="display: inline-block; width: 20px; height: 20px; margin-left: 10px; border: 1px solid #ccc; background-color: {{ $location->color }};"></span>
                        </span>
                    </p>
                    <p class="card-text">
                        <strong>Enlem:</strong> {{ $location->latitude }}
                    </p>
                    <p class="card-text">
                        <strong>Boylam:</strong> {{ $location->longitude }}
                    </p>
                </div>
            </div>
            </div>
        </div>
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
            { icon: createColoredMarkerIcon('{{ $location->color }}') }
        ).addTo(map);

    </script>
@endsection
