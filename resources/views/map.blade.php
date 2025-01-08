@extends('layouts.app')

@section('content')
<div class="flex h-full bg-gray-100">
    <!-- Bouton de réinitialisation -->
    <button id="resetViewBtn" 
            class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-full shadow-xl z-50 hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 hover:scale-105">
        <span class="text-xl">🔄</span>
        <span class="font-medium">Vue générale</span>
    </button>

    <!-- Sidebar améliorée -->
    <div class="bg-white w-96 h-full shadow-lg z-10 flex flex-col">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span>🏃</span>
                <span>ParknSport</span>
            </h1>
            
            <div class="mt-6 space-y-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">🔍</span>
                    <input type="text"
                           id="search"
                           placeholder="Rechercher un équipement..."
                           class="w-full pl-10 pr-4 py-2 rounded-full border-2 border-gray-200 focus:border-blue-500 focus:ring-0">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Type</label>
                        <select id="typeFilter" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200">
                            <option value="">🎯 Tous</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Activité</label>
                        <select id="activityFilter" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200">
                            <option value="">🎮 Toutes</option>
                        </select>
                    </div>
                <div>
                    
            </div>
                
                </div>
                <div class="mt-6">
                    <button onclick="location.reload()" 
                            class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition-colors duration-200">
                        Rafraichir la page
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b">
                <h2 class="text-lg font-bold text-gray-700 flex items-center gap-2">
                    <span>📍</span>
                    <span>Équipements</span>
                    <span class="text-sm font-normal text-gray-500">(<span id="count">0</span>)</span>
                </h2>
            </div>
            
            <div id="results-list" class="flex-1 overflow-y-auto p-4 space-y-3"></div>
        </div>
    </div>

    <div class="flex-1 relative">
        <div id="map" class="absolute inset-0"></div>
    </div>
</div>

<script>
    // Initialize map
    const map = L.map('map').setView([47.478419, -0.563166], 13);
    L.tileLayer('https://{s}.tile.jawg.io/jawg-light/{z}/{x}/{y}{r}.png?access-token=cJPcOiK2rCU52SCrm0H2UB9YdJqg8u2wADnEdShSbu8E6vtSGE3RFhXaD2RtD7fg', {
        maxZoom: 19,
        attribution: '© Jawg Maps'
    }).addTo(map);

    // Définir les variables une seule fois
    let markers = [];
    let equipments = [];
    const markerLayer = L.layerGroup().addTo(map);
    const parkingLayer = L.layerGroup().addTo(map);

    // Icons modernisés
    const createIcon = (emoji, color) => L.divIcon({
        className: 'custom-div-icon',
        html: `<div class="flex items-center justify-center w-10 h-10 rounded-full ${color} shadow-lg border-2 border-white">
                <span class="text-xl">${emoji}</span>
               </div>`,
        iconSize: [40, 40],
    }).addTo(map);

    // Styles pour les icônes
    const iconStyles = {
        equipment: L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 shadow-lg border-2 border-white transform transition-transform hover:scale-110">
                    <span class="text-white text-lg">⚽</span>
                   </div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        }),
        car: L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500 shadow-lg border-2 border-white transform transition-transform hover:scale-110">
                    <span class="text-white text-lg">🚗</span>
                   </div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        }),
        bike: L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-500 shadow-lg border-2 border-white transform transition-transform hover:scale-110">
                    <span class="text-white text-lg">🚲</span>
                   </div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        })
    };

    // Ajout de la légende
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function(map) {
        const div = L.DomUtil.create('div', 'bg-white p-3 rounded-lg shadow-lg');
        div.innerHTML = `
            <h4 class="font-bold mb-2">Légende</h4>
            <div class="space-y-2">
                <div class="flex items-center">
                    <span class="text-lg mr-2">⚽</span>
                    <span>Équipement sportif</span>
                </div>
                <div class="flex items-center">
                    <span class="text-lg mr-2">🚗</span>
                    <span>Parking voiture</span>
                </div>
                <div class="flex items-center">
                    <span class="text-lg mr-2">🚲</span>
                    <span>Parking vélo</span>
                </div>
            </div>
        `;
        return div;
    };
    legend.addTo(map);

    // Fonction améliorée pour créer un popup
    function createPopupContent(title, content, button = null) {
        return `
            <div class="p-4 max-w-sm">
                <h3 class="text-xl font-bold mb-2 border-b pb-2">${title}</h3>
                <div class="space-y-2 mb-4">
                    ${content}
                </div>
                ${button ? `
                    <div class="mt-4 text-center">
                        ${button}
                    </div>
                ` : ''}
            </div>
        `;
    }

    // Function to create a marker for equipment
    function createMarker(equipment) {
        if (!equipment.geo_point_2d) {
            return null;
        }

        const marker = L.marker([equipment.geo_point_2d.lat, equipment.geo_point_2d.lon], {
            icon: iconStyles.equipment
        }).bindPopup(createPopupContent(
            equipment.nom_instal || equipment.nom || 'Sans nom',
            `
                <p><strong>Type:</strong> ${equipment.nom_fam_eq || equipment.nom_equip || 'Type non spécifié'}</p>
                ${equipment.activite ? `<p><strong>Activités:</strong> ${equipment.activite}</p>` : ''}
            `,
            `<button onclick="showNearbyParkings(${equipment.geo_point_2d.lat}, ${equipment.geo_point_2d.lon})" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full transition-colors duration-200 transform hover:scale-105">
                🅿️ Voir les parkings à proximité
            </button>`
        ));

        return marker;
    }

    // Function to update the results list
    function updateResultsList(filteredEquipments) {
        const resultsList = document.getElementById('results-list');
        resultsList.innerHTML = '';
        document.getElementById('count').textContent = filteredEquipments.length;

        filteredEquipments.forEach(equipment => {
            const element = document.createElement('div');
            element.className = 'bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer border border-gray-100';
            element.innerHTML = `
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <h3 class="font-semibold text-gray-800">${equipment.nom_instal || equipment.nom || 'Sans nom'}</h3>
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-full">
                            ${equipment.nom_fam_eq || equipment.equip || 'Type non spécifié'}
                        </span>
                    </div>
                    <div class="mt-2 space-y-1">
                        <p class="text-sm text-gray-600">${equipment.categorie || 'Installation non spécifiée'}</p>
                        ${equipment.activite ? `
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <span>🎯</span>
                                <span>${equipment.activite}</span>
                            </p>
                        ` : ''}
                    </div>
                </div>
            `;

            if (equipment.geo_point_2d) {
                element.onclick = () => {
                    map.setView([equipment.geo_point_2d.lat, equipment.geo_point_2d.lon], 16);
                    markers.find(m => m.equipment?.res_fid === equipment.res_fid)?.openPopup();
                    showNearbyParkings(equipment.geo_point_2d.lat, equipment.geo_point_2d.lon);
                };
            }

            resultsList.appendChild(element);
        });
    }

    // Function to filter equipments
    function filterEquipments() {
        const searchText = document.getElementById('search').value.toLowerCase();
        const selectedType = document.getElementById('typeFilter').value;
        const selectedActivity = document.getElementById('activityFilter').value;

        const filtered = equipments.filter(equipment => {
            const searchFields = [
                equipment.nom_equip,
                equipment.nom,
                equipment.nom_instal,
                equipment.activite
            ].filter(Boolean).join(' ').toLowerCase();

            const matchesSearch = searchFields.includes(searchText);
            const matchesType = !selectedType ||
                                (equipment.type_equipement === selectedType) ||
                                (equipment.equip_2 === selectedType);
            const matchesActivity = !selectedActivity ||
                                    (equipment.activite && equipment.activite.toLowerCase().includes(selectedActivity.toLowerCase()));

            return matchesSearch && matchesType && matchesActivity;
        });

        // Clear existing markers and array
        markerLayer.clearLayers();
        markers = [];

        // Add filtered markers
        filtered.forEach(equipment => {
            const marker = createMarker(equipment);
            if (marker) {
                marker.equipment = equipment;
                markerLayer.addLayer(marker);
                markers.push(marker);
            }
        });

        updateResultsList(filtered);
    }

    // Function to populate filters
    function populateFilters(data) {
        // Type filter
        const types = [...new Set(data
            .map(e => e.type_equipement || e.equip_2)
            .filter(Boolean)
        )];

        const typeFilter = document.getElementById('typeFilter');
        types.sort().forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            typeFilter.appendChild(option);
        });

        // Activity filter
        const activities = [...new Set(data
            .flatMap(e => (e.activite || '').split(','))
            .map(a => a.trim())
            .filter(Boolean)
        )];

        const activityFilter = document.getElementById('activityFilter');
        activities.sort().forEach(activity => {
            const option = document.createElement('option');
            option.value = activity;
            option.textContent = activity;
            activityFilter.appendChild(option);
        });
    }

    // Function to calculate distance between two points
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c; // Distance in km
    }

    // Remplacer la fonction showNearbyParkings complètement
    async function showNearbyParkings(equipmentLat, equipmentLon) {
        try {
            markerLayer.clearLayers();
            parkingLayer.clearLayers();

            // Marqueur de l'équipement
            L.marker([equipmentLat, equipmentLon], {
                icon: iconStyles.equipment
            })
            .bindPopup('Équipement sélectionné')
            .addTo(markerLayer);

            // Récupérer les données
            const [parkingsVoituresResp, disponibilitesResp, parkingsVeloResp] = await Promise.all([
                fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/parking-angers/records?limit=100'),
                fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/angers_stationnement/records?limit=100'),
                fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/parking-velo-angers/records?limit=100')
            ]);

            const parkingsVoitures = await parkingsVoituresResp.json();
            const disponibilites = await disponibilitesResp.json();
            const parkingsVelo = await parkingsVeloResp.json();

            const MAX_DISTANCE = 1; // 1km radius
            let nearbyMarkersAdded = false;

            // Traitement des parkings voiture
            parkingsVoitures.results.forEach(parking => {
                // Utiliser ylat et xlong directement
                if (parking.ylat && parking.xlong) {
                    const lat = parseFloat(parking.ylat);
                    const lon = parseFloat(parking.xlong);
                    
                    const distance = calculateDistance(equipmentLat, equipmentLon, lat, lon);
                    console.log(`Distance pour ${parking.nom}: ${distance}km`);
                    
                    if (distance <= MAX_DISTANCE) {
                        nearbyMarkersAdded = true;
                        const dispo = disponibilites.results.find(d => d.nom === parking.nom);
                        console.log(`Disponibilité pour ${parking.nom}:`, dispo);
                        
                        const parkingMarker = L.marker([lat, lon], {
                            icon: iconStyles.car
                        }).bindPopup(createPopupContent(
                            parking.nom,
                            `
                                <div class="space-y-2">
                                    <p><strong>Type:</strong> ${parking.type_ouvrage || 'Non spécifié'}</p>
                                    <p><strong>Distance:</strong> ${(distance * 1000).toFixed(0)}m</p>
                                    <p><strong>Places totales:</strong> ${parking.nb_places}</p>
                                    ${dispo ? `
                                        <div class="mt-2 p-2 ${dispo.disponible > 10 ? 'bg-green-100' : 'bg-red-100'} rounded">
                                            <p class="font-bold">${dispo.disponible} places disponibles</p>
                                        </div>
                                    ` : ''}
                                    <p><strong>Tarif 1h:</strong> ${parking.tarif_1h !== null ? parking.tarif_1h + '€' : 'Gratuit'}</p>
                                    <p class="text-sm text-gray-600">${parking.adresse}</p>
                                </div>
                            `
                        ));
                        parkingLayer.addTo(map); // S'assurer que le layer est ajouté à la carte
                        parkingMarker.addTo(parkingLayer);
                    }
                }
            });

            // Traitement des parkings vélos
            parkingsVelo.results.forEach(parking => {
                if (parking.geo_shape && parking.geo_shape.geometry && parking.geo_shape.geometry.coordinates) {
                    const [lon, lat] = parking.geo_shape.geometry.coordinates;
                    const distance = calculateDistance(equipmentLat, equipmentLon, lat, lon);
                    
                    if (distance <= MAX_DISTANCE) {
                        nearbyMarkersAdded = true;
                        const parkingMarker = L.marker([lat, lon], {
                            icon: iconStyles.bike
                        }).bindPopup(createPopupContent(
                            parking.nom_parkng || 'Parking vélo',
                            `
                                <p><strong>Type:</strong> ${parking.type}</p>
                                <p><strong>Distance:</strong> ${(distance * 1000).toFixed(0)}m</p>
                                <p><strong>Capacité:</strong> ${parking.capacite}</p>
                                <p><strong>Accès:</strong> ${parking.acces}</p>
                                <div class="mt-2 p-2 ${parking.securise === 'OUI' ? 'bg-green-100' : 'bg-yellow-100'} rounded">
                                    <p><strong>Sécurisé:</strong> ${parking.securise}</p>
                                </div>
                            `
                        ));
                        parkingLayer.addLayer(parkingMarker);
                    }
                }
            });

            if (nearbyMarkersAdded) {
                const allMarkers = [...markerLayer.getLayers(), ...parkingLayer.getLayers()];
                const bounds = L.latLngBounds(allMarkers.map(marker => marker.getLatLng()));
                map.fitBounds(bounds, { padding: [50, 50] });
            } else {
                map.setView([equipmentLat, equipmentLon], 16);
                alert('Aucun parking trouvé à moins de 1km');
            }

        } catch (error) {
            console.error('Erreur détaillée:', error);
            alert('Erreur lors du chargement des parkings');
        }
    }

    // Fonction pour réinitialiser la vue
    function resetView() {
        markerLayer.clearLayers();
        filterEquipments();
        map.setView([47.478419, -0.563166], 13);
    }

    fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/equipements-sportifs-angers/records?limit=100')
        .then(response => response.json())
        .then(data => {
            // Vérifier la structure des données et les logger
            console.log('Data structure:', data);
            
            // Utiliser la bonne structure selon l'API
            equipments = data.results || [];  // Utiliser results au lieu de records
            
            // Ajouter des logs pour le débogage
            console.log('Equipments:', equipments);
            
            populateFilters(equipments);
            filterEquipments();
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            alert('Erreur lors du chargement des données');
        });

    // Event listeners
    document.getElementById('search').addEventListener('input', filterEquipments);
    document.getElementById('typeFilter').addEventListener('change', filterEquipments);
    document.getElementById('activityFilter').addEventListener('change', filterEquipments);

    // Ajouter l'event listener pour le bouton de réinitialisation
    document.getElementById('resetViewBtn').addEventListener('click', () => {
        markerLayer.clearLayers();
        filterEquipments();
        map.setView([47.478419, -0.563166], 13);
    });
</script>

<style>
    .custom-div-icon {
        transition: all 0.3s ease;
    }
    .custom-div-icon:hover {
        transform: scale(1.1);
    }
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    /* Ajout de styles pour la liste des résultats */
    #results-list::-webkit-scrollbar {
        width: 6px;
    }

    #results-list::-webkit-scrollbar-track {
        background: transparent;
    }

    #results-list::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 3px;
    }

    #results-list::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0, 0, 0, 0.2);
    }
</style>

@endsection
