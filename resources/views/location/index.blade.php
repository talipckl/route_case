@extends('home')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h1>Konumlar</h1>
    <div id="map" style="height: 500px;"></div>
    <div id="loading-indicator" class="fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded shadow hidden">
        Rota hesaplanıyor...
    </div>

    <script>
        let selectedMarker = null;
        let routeLayer = null;
        let isProcessing = false;
        const map = L.map('map').setView([20, 0], 2);
        const loadingIndicator = document.getElementById('loading-indicator');

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Dinamik renkli marker ikonu oluşturucu
        function createColoredMarkerIcon(color) {
            return L.divIcon({
                className: '',
                html: `<svg xmlns='http://www.w3.org/2000/svg' width='32' height='40' viewBox='0 0 32 40'><path d='M16 0C8.268 0 2 6.268 2 14c0 8.284 12.09 24.36 13.01 25.57a2 2 0 0 0 3.98 0C17.91 38.36 30 22.284 30 14c0-7.732-6.268-14-14-14zm0 20a6 6 0 1 1 0-12 6 6 0 0 1 0 12z' fill='${color}' stroke='#222' stroke-width='2'/><circle cx='16' cy='14' r='4' fill='#fff' opacity='0.7'/></svg>`,
                iconSize: [28, 36],
                iconAnchor: [14, 36],
                popupAnchor: [0, -40]
            });
        }

        const locations = @json($locations);

        if (locations.length > 0) {
            locations.forEach(location => {
                const marker = L.marker(
                    [parseFloat(location.latitude), parseFloat(location.longitude)], {
                        icon: createColoredMarkerIcon(location.color)
                    }
                ).addTo(map);

                const popupContent = `
                <div class="min-w-[200px] bg-white rounded-md shadow">
                    <div class="p-2">
                        <div class="mb-2">
                            <span class="text-sm font-medium text-gray-800">${location.name}</span>
                        </div>
                        <div class="flex justify-end gap-1 mt-2">
                            <a href="/location/${location.id}/edit" class="px-2 py-1 bg-blue-500 text-white text-xs font-medium rounded hover:bg-blue-600 transition-colors duration-200">
                                Düzenle
                            </a>
                            <a href="/location/${location.id}/show" class="px-2 py-1 bg-blue-500 text-white text-xs font-medium rounded hover:bg-blue-600 transition-colors duration-200">
                                Görüntüle
                            </a>
                            <form action="/location/${location.id}/destroy" method="POST" class="inline" onsubmit="return confirm('Bu konumu silmek istediğinize emin misiniz?')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="px-2 py-1 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors duration-200">
                                    Sil
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;

                marker.bindPopup(popupContent, {
                    maxWidth: 250,
                    className: 'rounded-md overflow-hidden'
                });
            });
        }

        // Tüm markerları görecek şekilde haritayı ortala
        if (locations.length > 0) {
            const bounds = L.latLngBounds(locations.map(loc => [parseFloat(loc.latitude), parseFloat(loc.longitude)]));
            map.fitBounds(bounds, {
                padding: [50, 50]
            });
        }

        async function calculateAndDrawRoute(lat, lng) {
            if (isProcessing) return;
            
            isProcessing = true;
            loadingIndicator.classList.remove('hidden');

            try {
                // Önceki rotayı temizle
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                }

                const response = await fetch(`{{ route('location.calculateRoute') }}?latitude=${lat}&longitude=${lng}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.status === 429) {
                    const data = await response.json();
                    const retryAfterSeconds = Math.ceil(data.retryAfter);
                    alert(`Çok fazla istek gönderdiniz. Lütfen 60 saniye sonra tekrar deneyin.`);
                    isProcessing = false;
                    loadingIndicator.classList.add('hidden');
                    return;
                }

                const data = await response.json();
                
                const coordinates = data.route.map(point => [
                    parseFloat(point.latitude), 
                    parseFloat(point.longitude)
                ]);

                let currentIndex = 0;
                routeLayer = L.featureGroup().addTo(map);

                // Her bir nokta çifti arasında çizgi çiz
                function drawNextSegment() {
                    if (currentIndex < coordinates.length - 1) {
                        const segment = L.polyline(
                            [coordinates[currentIndex], coordinates[currentIndex + 1]],
                            {
                                color: 'blue',
                                weight: 3,
                                opacity: 0.7
                            }
                        ).addTo(routeLayer);

                        // Sıra numarası ekle
                        L.circleMarker(coordinates[currentIndex], {
                            radius: 10,
                            fillColor: '#fff',
                            color: '#000',
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).bindPopup(`Durak ${currentIndex + 1}`).addTo(routeLayer);

                        currentIndex++;
                        setTimeout(drawNextSegment, 200);
                    } else {
                        // Son noktayı işaretle
                        L.circleMarker(coordinates[currentIndex], {
                            radius: 10,
                            fillColor: '#fff',
                            color: '#000',
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).bindPopup(`Durak ${currentIndex + 1}`).addTo(routeLayer);

                        // Tüm rotayı görüntüle
                        map.fitBounds(routeLayer.getBounds(), {
                            padding: [50, 50]
                        });

                        // İşlem tamamlandı
                        isProcessing = false;
                        loadingIndicator.classList.add('hidden');
                    }
                }

                // Animasyonu başlat
                drawNextSegment();

            } catch (error) {
                alert('Rota hesaplanırken bir hata oluştu!');
                isProcessing = false;
                loadingIndicator.classList.add('hidden');
            }
        }

        // Haritaya tıklama olayı
        map.on('click', function(e) {
            if (isProcessing) {
                alert('Lütfen mevcut rota hesaplaması bitene kadar bekleyin.');
                return;
            }

            if (selectedMarker) {
                map.removeLayer(selectedMarker);
            }
            
            // Yeni marker oluştur
            selectedMarker = L.marker(e.latlng, {
                icon: createColoredMarkerIcon('#000000')
            }).addTo(map);

            // Rota hesaplamayı başlat
            calculateAndDrawRoute(e.latlng.lat, e.latlng.lng);
        });
    </script>
@endsection
