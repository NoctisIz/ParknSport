@extends('layouts.app')

@section('content')
<!-- Structure principale de la page -->
<div class="flex h-full bg-gray-100">
    <!-- Bouton de réinitialisation de la vue -->
    <button id="resetViewBtn" 
            class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-full shadow-xl z-[9999] hover:bg-blue-600 transition-all duration-200 flex items-center space-x-2 hover:scale-105">
        <span class="text-xl">🔄</span>
        <span class="font-medium">Vue générale</span>
    </button>

    <!-- Panneau de contrôle latéral -->
    <div class="fixed top-20 right-4 bg-white p-4 rounded-lg shadow-xl z-[9999] space-y-4">
        <!-- Bouton pour placer un marqueur -->
        <button id="placeMarkerBtn" 
                class="w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all duration-200 flex items-center justify-center gap-2">
            <span>📍</span>
            <span>Placer un point</span>
        </button>
        <!-- Contrôle du rayon de recherche -->
        <div class="space-y-2">
            <label class="text-sm text-gray-600">Distance de recherche</label>
            <div class="flex items-center gap-2">
                <input type="range" id="searchRadius" min="100" max="5000" step="100" value="1000" 
                    class="w-full">
                <span id="radiusValue" class="text-sm font-medium">1000m</span>
            </div>
        </div>
        <!-- Champ de recherche d'adresse -->
        <div class="space-y-2">
            <label class="text-sm text-gray-600">Rechercher une adresse</label>
            <div class="relative">
                <input type="text" 
                       id="addressSearch" 
                       placeholder="Ex: 1 rue de la Paix..."
                       class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 pr-10">
                <button id="searchAddressBtn"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-1 bg-blue-500 text-white rounded-full hover:bg-blue-600">
                    🔍
                </button>
            </div>
            <div id="addressResults" class="max-h-40 overflow-y-auto hidden bg-white border rounded-lg shadow-lg absolute w-full z-50">
            </div>
        </div>
    </div>

    <!-- Barre latérale avec les filtres et résultats -->
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

    <!-- Conteneur de la carte -->
    <div class="flex-1 relative">
        <!-- Indicateur de chargement -->
        <div id="loadingIndicator" class="hidden absolute bottom-4 left-4 bg-white px-4 py-2 rounded-full shadow-lg z-[9999] flex items-center gap-2">
            <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
            <span class="text-sm font-medium text-gray-700">Chargement des parkings...</span>
        </div>
        <div id="map" class="absolute inset-0"></div>
    </div>
</div>

<script>
    // Initialisation de la carte Leaflet
    const map = L.map('map').setView([47.478419, -0.563166], 13);
    
    // Ajout de la couche de tuiles (fond de carte)
    L.tileLayer('https://{s}.tile.jawg.io/jawg-light/{z}/{x}/{y}{r}.png?access-token=cJPcOiK2rCU52SCrm0H2UB9YdJqg8u2wADnEdShSbu8E6vtSGE3RFhXaD2RtD7fg', {
        maxZoom: 19,
        attribution: '© Jawg Maps'
    }).addTo(map);

    // Déclaration des variables globales
    let markers = [];                  // Tableau des marqueurs d'équipements
    let equipments = [];              // Tableau des équipements
    let maxSearchDistance = 0.5;      // Distance de recherche par défaut (en km)
    let selectedEquipment = null;     // Équipement actuellement sélectionné
    let isPlacingMarker = false;      // Mode placement de marqueur actif/inactif
    let userMarker = null;            // Marqueur utilisateur
    let searchRadius = 1000;          // Rayon de recherche en mètres

    // Création des couches de marqueurs
    const markerLayer = L.layerGroup().addTo(map);    // Couche pour les équipements
    const parkingLayer = L.layerGroup().addTo(map);   // Couche pour les parkings

    // Configuration des icônes personnalisées
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

    // Ajout de la légende sur la carte
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

    // Fonction de création du contenu des popups
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

    // Fonction de création d'un marqueur pour un équipement
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

    // Fonction de mise à jour de la liste des résultats
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

    // Fonction de filtrage des équipements
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
            
            // Amélioration de la correspondance des activités
            const matchesActivity = !selectedActivity || (
                equipment.activite && 
                equipment.activite
                    .split(/,(?![^(]*\))/)
                    .map(a => a.trim())
                    .some(a => a.toLowerCase() === selectedActivity.toLowerCase())
            );

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

    // Fonction de remplissage des filtres
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

        // Activity filter - Amélioration du traitement des activités
        const activities = [...new Set(data
            .flatMap(e => {
                if (!e.activite) return [];
                // Séparation intelligente des activités
                return e.activite
                    .split(/,(?![^(]*\))/) // Sépare par virgule seulement si pas entre parenthèses
                    .map(a => a.trim())
                    .filter(a => a.length > 0);
            })
        )];

        console.log('Activités trouvées:', activities);

        const activityFilter = document.getElementById('activityFilter');
        activities.sort((a, b) => {
            // Retirer les parenthèses pour le tri
            const cleanA = a.replace(/\([^)]*\)/g, '').trim();
            const cleanB = b.replace(/\([^)]*\)/g, '').trim();
            return cleanA.localeCompare(cleanB);
        }).forEach(activity => {
            const option = document.createElement('option');
            option.value = activity;
            option.textContent = activity;
            activityFilter.appendChild(option);
        });
    }

    // Fonction de calcul de distance entre deux points
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

    // Fonction d'affichage des parkings à proximité
    async function showNearbyParkings(equipmentLat, equipmentLon, shouldZoom = true) { // Ajout du paramètre shouldZoom
        const loadingIndicator = document.getElementById('loadingIndicator');
        try {
            loadingIndicator.classList.remove('hidden'); // Afficher le loader

            // Sauvegarder les coordonnées de l'équipement sélectionné
            selectedEquipment = { lat: equipmentLat, lon: equipmentLon };

            markerLayer.clearLayers();
            parkingLayer.clearLayers();

            // Marqueur de l'équipement
            L.marker([equipmentLat, equipmentLon], {
                icon: iconStyles.equipment
            })
            .bindPopup('Équipement sélectionné')
            .addTo(markerLayer);

            const [parkingsData, disponibilitesData, parkingsVeloData] = await Promise.all([
                fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/angers_stationnement/exports/json')
                    .then(res => res.json()),
                fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/parking-angers/exports/json')
                    .then(res => res.json()),
                fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/parking-velo-angers/exports/json')
                    .then(res => res.json())
            ]);

            // Calculer toutes les distances pour trouver le parking le plus proche
            let allParkings = [];
            
            // Ajouter les parkings voiture avec leurs distances
            parkingsData.forEach(parking => {
                if (!parking.geo_point_2d) return;
                const [lat, lon] = parking.geo_point_2d.split(',').map(coord => parseFloat(coord.trim()));
                const distance = calculateDistance(equipmentLat, equipmentLon, lat, lon);
                allParkings.push({ type: 'car', parking, lat, lon, distance });
            });

            // Ajouter les parkings vélos avec leurs distances
            parkingsVeloData?.forEach(parking => {
                if (!parking.geo_shape?.geometry?.coordinates) return;
                const [lon, lat] = parking.geo_shape.geometry.coordinates;
                const distance = calculateDistance(equipmentLat, equipmentLon, lat, lon);
                allParkings.push({ type: 'bike', parking, lat, lon, distance });
            });

            // Trier tous les parkings par distance
            allParkings.sort((a, b) => a.distance - b.distance);

            let nearbyMarkersAdded = false;
            let minDistance = maxSearchDistance;

            // Si aucun parking n'est trouvé dans la distance initiale, 
            // utiliser la distance du parking le plus proche
            if (allParkings.length > 0 && !allParkings.some(p => p.distance <= maxSearchDistance)) {
                minDistance = Math.min(allParkings[0].distance + 0.1, 2); // Maximum 2km
                console.log(`Distance ajustée à ${minDistance}km pour montrer le parking le plus proche`);
            }

            // Ajouter les marqueurs pour les parkings dans la distance ajustée
            allParkings.forEach(({ type, parking, lat, lon, distance }) => {
                if (distance <= minDistance) {
                    nearbyMarkersAdded = true;
                    
                    if (type === 'car') {
                        const disponibilite = disponibilitesData.find(d => 
                            d.nom?.toLowerCase() === parking.id_parking?.toLowerCase()
                        );
                        
                        const parkingMarker = L.marker([lat, lon], {
                            icon: iconStyles.car
                        }).bindPopup(createPopupContent(
                            parking.nom,
                            `
                                <div class="space-y-2">
                                    <p><strong>Distance:</strong> ${(distance * 1000).toFixed(0)}m</p>
                                    ${disponibilite ? `
                                        <div class="mt-2 p-2 ${disponibilite.disponible > 10 ? 'bg-green-100' : 'bg-red-100'} rounded">
                                            <p class="font-bold text-${disponibilite.disponible > 10 ? 'green' : 'red'}-700">
                                                ${disponibilite.disponible} places disponibles
                                            </p>
                                        </div>
                                    ` : ''}
                                    <p><strong>Total places:</strong> ${parking.nb_places}</p>
                                    <p><strong>Tarif:</strong> ${parking.tarif_1h ? parking.tarif_1h + '€/h' : 'Gratuit'}</p>
                                    <p class="text-sm text-gray-600">${parking.adresse}</p>
                                </div>
                            `
                        ));
                        parkingLayer.addLayer(parkingMarker);
                    } else {
                        const parkingMarker = L.marker([lat, lon], {
                            icon: iconStyles.bike
                        }).bindPopup(createPopupContent(
                            parking.nom_parkng || 'Parking vélo',
                            `
                                <div class="space-y-2">
                                    <p><strong>Type:</strong> ${parking.type || 'Non spécifié'}</p>
                                    <p><strong>Distance:</strong> ${(distance * 1000).toFixed(0)}m</p>
                                    <p><strong>Capacité:</strong> ${parking.capacite || 'Non spécifiée'} places</p>
                                </div>
                            `
                        ));
                        parkingLayer.addLayer(parkingMarker);
                    }
                }
            });

            if (nearbyMarkersAdded) {
                if (shouldZoom) { // Ne faire le zoom que si shouldZoom est true
                    const allMarkers = [...markerLayer.getLayers(), ...parkingLayer.getLayers()];
                    const bounds = L.latLngBounds(allMarkers.map(marker => marker.getLatLng()));
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            } else {
                if (shouldZoom) {
                    map.setView([equipmentLat, equipmentLon], 16);
                }
                alert('Aucun parking trouvé à proximité');
            }

        } catch (error) {
            console.error('Erreur détaillée:', error);
            alert('Erreur lors du chargement des parkings');
        } finally {
            loadingIndicator.classList.add('hidden'); // Cacher le loader dans tous les cas
        }
    }

    // Fonction de réinitialisation de la vue
    function resetView() {
        // Nettoyer tous les layers
        markerLayer.clearLayers();
        parkingLayer.clearLayers();
        
        // Supprimer le marqueur utilisateur s'il existe
        if (userMarker) {
            userMarker.remove();
            userMarker = null;
        }
        
        // Réinitialiser l'équipement sélectionné
        selectedEquipment = null;
        
        // Vider le champ de recherche d'adresse
        document.getElementById('addressSearch').value = '';
        document.getElementById('addressResults').innerHTML = '';
        document.getElementById('addressResults').classList.add('hidden');
        
        // Recharger les équipements
        filterEquipments();
        
        // Recentrer la carte
        map.setView([47.478419, -0.563166], 13);
    }

    // Fonction de basculement du mode placement de marqueur
    function toggleMarkerPlacement() {
        isPlacingMarker = !isPlacingMarker;
        const btn = document.getElementById('placeMarkerBtn');
        
        if (isPlacingMarker) {
            btn.classList.add('bg-red-500', 'hover:bg-red-600');
            btn.classList.remove('bg-green-500', 'hover:bg-green-600');
            btn.innerHTML = '<span>❌</span><span>Annuler</span>';
            map.getContainer().style.cursor = 'crosshair';
        } else {
            btn.classList.remove('bg-red-500', 'hover:bg-red-600');
            btn.classList.add('bg-green-500', 'hover:bg-green-600');
            btn.innerHTML = '<span>📍</span><span>Placer un point</span>';
            map.getContainer().style.cursor = '';
        }
    }

    // Fonction de recherche des équipements à proximité
    function findNearbyEquipments(lat, lon, radius) {
        markerLayer.clearLayers();
        
        const nearbyEquipments = equipments.filter(equipment => {
            if (!equipment.geo_point_2d) return false;
            const distance = calculateDistance(lat, lon, equipment.geo_point_2d.lat, equipment.geo_point_2d.lon);
            return distance * 1000 <= radius; // Convertir km en mètres
        });

        nearbyEquipments.forEach(equipment => {
            const marker = createMarker(equipment);
            if (marker) {
                marker.equipment = equipment;
                markerLayer.addLayer(marker);
            }
        });

        // Mettre à jour la liste des résultats
        updateResultsList(nearbyEquipments);

        // Ajuster la vue pour montrer tous les marqueurs
        if (nearbyEquipments.length > 0) {
            const allMarkers = [...markerLayer.getLayers()];
            if (userMarker) allMarkers.push(userMarker);
            const bounds = L.latLngBounds(allMarkers.map(marker => marker.getLatLng()));
            map.fitBounds(bounds, { padding: [50, 50] });
        }

        return nearbyEquipments.length;
    }

    // Fonction de recherche d'adresse
    async function searchAddress(query) {
        try {
            // Nettoyer la requête et encoder correctement
            const cleanQuery = query.replace(/[^a-zA-Z0-9\s]/g, ''); // Enlève les caractères spéciaux
            const encodedQuery = encodeURIComponent(cleanQuery.toUpperCase());
            
            // Utiliser la syntaxe correcte pour LIKE avec des guillemets simples
            const response = await fetch(
                `https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/ref_loc_adresse/records?limit=5&where=libelle_complet_adresse like '${encodedQuery}%'`
            );
            
            // ...reste du code de la fonction inchangé...
            const data = await response.json();
                
            const resultsDiv = document.getElementById('addressResults');
            resultsDiv.innerHTML = '';
            
            if (data.results && data.results.length > 0) {
                resultsDiv.classList.remove('hidden');
                data.results.forEach(address => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b last:border-b-0';
                    div.innerHTML = `
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">📍</span>
                            <span>${address.libelle_complet_adresse}</span>
                        </div>
                        <div class="text-sm text-gray-500 ml-7">
                            ${address.libelle_quartier_angers || 'Quartier non spécifié'}
                        </div>
                    `;
                    div.onclick = () => {
                        if (address.coordo_geo) {
                            placeMarkerAtAddress({
                                adresse: address.libelle_complet_adresse,
                                geo_point_2d: {
                                    lat: address.coordo_geo.lat,
                                    lon: address.coordo_geo.lon
                                }
                            });
                            resultsDiv.classList.add('hidden');
                            document.getElementById('addressSearch').value = address.libelle_complet_adresse;
                        }
                    };
                    resultsDiv.appendChild(div);
                });
            } else {
                resultsDiv.innerHTML = `
                    <div class="p-3 text-gray-500 text-center">
                        Aucune adresse trouvée
                    </div>
                `;
                resultsDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Erreur lors de la recherche d\'adresse:', error);
            const resultsDiv = document.getElementById('addressResults');
            resultsDiv.innerHTML = `
                <div class="p-3 text-red-500 text-center">
                    Erreur lors de la recherche
                </div>
            `;
            resultsDiv.classList.remove('hidden');
        }
    }

    // Fonction de placement d'un marqueur à une adresse
    function placeMarkerAtAddress(address) {
        if (!address.geo_point_2d) return;
        
        // Supprimer le marqueur précédent s'il existe
        if (userMarker) {
            userMarker.remove();
        }

        // Créer le nouveau marqueur
        userMarker = L.marker([address.geo_point_2d.lat, address.geo_point_2d.lon], {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-500 shadow-lg border-2 border-white transform transition-transform hover:scale-110">
                        <span class="text-white text-lg">📍</span>
                    </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            })
        }).addTo(map);

        // Rechercher les équipements à proximité
        findNearbyEquipments(address.geo_point_2d.lat, address.geo_point_2d.lon, searchRadius);
    }

    // Chargement initial des données des équipements
    fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/equipements-sportifs-angers/exports/json')
        .then(response => response.json())
        .then(data => {
            // Vérifier la structure des données et les logger
            console.log('Data structure:', data);
            
            // Utiliser la bonne structure selon l'API
            equipments = data;  // Les données sont directement un tableau avec /export
            
            // Ajouter des logs pour le débogage
            console.log('Nombre total d\'équipements:', data.length);
            console.log('Equipments:', equipments);
            
            populateFilters(equipments);
            filterEquipments();
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            alert('Erreur lors du chargement des données');
        });

    // Écouteurs d'événements
    document.getElementById('search').addEventListener('input', filterEquipments);
    document.getElementById('typeFilter').addEventListener('change', filterEquipments);
    document.getElementById('activityFilter').addEventListener('change', filterEquipments);
    document.getElementById('resetViewBtn').addEventListener('click', resetView);
    document.getElementById('placeMarkerBtn').addEventListener('click', toggleMarkerPlacement);
    document.getElementById('searchRadius').addEventListener('input', function(e) {
        const value = e.target.value;
        document.getElementById('radiusValue').textContent = value + 'm';
        searchRadius = parseInt(value);
        
        if (userMarker) {
            const pos = userMarker.getLatLng();
            findNearbyEquipments(pos.lat, pos.lng, searchRadius);
        }
    });

    // Gestion des événements de la carte
    map.on('click', function(e) {
        if (!isPlacingMarker) return;
        
        // Supprimer le marqueur précédent s'il existe
        if (userMarker) {
            userMarker.remove();
        }

        // Créer le nouveau marqueur
        userMarker = L.marker(e.latlng, {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-500 shadow-lg border-2 border-white transform transition-transform hover:scale-110">
                        <span class="text-white text-lg">📍</span>
                    </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            })
        }).addTo(map);

        // Rechercher les équipements à proximité
        findNearbyEquipments(e.latlng.lat, e.latlng.lng, searchRadius);
        
        // Désactiver le mode placement de marqueur
        toggleMarkerPlacement();
    });

    // Remplacer l'écouteur d'événement de zoom existant ou l'ajouter après l'initialisation de la carte
    map.on('zoomend', function() {
        const currentZoom = map.getZoom();
        // Ajuster la distance en fonction du niveau de zoom avec plus de paliers
        if (currentZoom <= 11) {
            maxSearchDistance = 10; // 5km pour un zoom très large
        } else if (currentZoom <= 12) {
            maxSearchDistance = 10; // 4km
        } else if (currentZoom <= 13) {
            maxSearchDistance = 7; // 3km
        } else if (currentZoom <= 14) {
            maxSearchDistance = 5; // 2km
        } else if (currentZoom <= 15) {
            maxSearchDistance = 2; // 1km
        } else if (currentZoom <= 16) {
            maxSearchDistance = 1; // 750m
        } else {
            maxSearchDistance = 0.5; // 500m pour un zoom très rapproché
        }
        
        console.log(`Zoom level: ${currentZoom}, Search distance: ${maxSearchDistance}km`);
        
        // Si des parkings sont affichés et qu'un équipement est sélectionné, mettre à jour l'affichage
        if (parkingLayer.getLayers().length > 0 && selectedEquipment) {
            showNearbyParkings(selectedEquipment.lat, selectedEquipment.lon, false);
        }
    });

    // Gestion de la recherche d'adresse
    let searchTimeout;
    document.getElementById('addressSearch').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 3) {
            document.getElementById('addressResults').classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchAddress(query);
        }, 300); // Délai réduit pour une meilleure réactivité
    });

    // Autres écouteurs d'événements pour la recherche d'adresse
    document.getElementById('searchAddressBtn').addEventListener('click', () => {
        const query = document.getElementById('addressSearch').value.trim();
        if (query.length >= 3) {
            searchAddress(query);
        }
    });

    // Pour fermer la liste des résultats quand on clique ailleurs
    document.addEventListener('click', (e) => {
        const addressResults = document.getElementById('addressResults');
        const addressSearch = document.getElementById('addressSearch');
        if (!addressResults.contains(e.target) && !addressSearch.contains(e.target)) {
            addressResults.classList.add('hidden');
        }
    });

    document.getElementById('addressSearch').addEventListener('focus', (e) => {
        const query = e.target.value.trim();
        if (query.length >= 3) {
            searchAddress(query);
        }
    });
</script>

<!-- Styles CSS personnalisés -->
<style>
    /* Styles pour les icônes personnalisées */
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

    #loadingOverlay {
        backdrop-filter: blur(2px);
    }

    .animate-spin {
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    #loadingIndicator {
        transition: all 0.3s ease;
        opacity: 1;
    }

    #loadingIndicator.hidden {
        display: none;
        opacity: 0;
    }
</style>

@endsection
