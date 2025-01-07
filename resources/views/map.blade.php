@extends('layouts.app')

@section('content')
<div class="flex h-full">
    <!-- Sidebar -->
    <div class="bg-white w-80 h-full overflow-y-auto shadow-lg z-10">
        <div class="p-4">
            <h1 class="text-2xl font-bold mb-4">Équipements Sportifs Angers</h1>

            <!-- Search -->
            <div class="mb-4">
                <input type="text"
                       id="search"
                       placeholder="Rechercher un équipement..."
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Filters -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type d'équipement</label>
                    <select id="typeFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les types</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Activité</label>
                    <select id="activityFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toutes les activités</option>
                    </select>
                </div>
            </div>

            <!-- Results List -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold mb-2">Équipements (<span id="count">0</span>)</h2>
                <div id="results-list" class="space-y-2">
                    <!-- Results will be dynamically populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="flex-1 relative">
        <div id="map" class="absolute inset-0"></div>
    </div>
</div>

<script>
    // Initialize the map centered on Angers
    const map = L.map('map').setView([47.478419, -0.563166], 13);

    // Add Jawg Maps tiles
    L.tileLayer('https://{s}.tile.jawg.io/jawg-light/{z}/{x}/{y}{r}.png?access-token=cJPcOiK2rCU52SCrm0H2UB9YdJqg8u2wADnEdShSbu8E6vtSGE3RFhXaD2RtD7fg', {
        maxZoom: 19,
        attribution: '© Jawg Maps contributors'
    }).addTo(map);

    let markers = [];
    let equipments = [];
    let markerLayer = L.layerGroup().addTo(map);

    // Function to create a marker
    function createMarker(equipment) {
        if (!equipment.geo_point_2d) {
            return null;
        }

        const marker = L.marker([equipment.geo_point_2d.lat, equipment.geo_point_2d.lon])
    .bindPopup(`
        <div class="p-2">
            <h3 class="font-bold">${equipment.nom_equip || equipment.nom || 'Sans nom'}</h3>
            <p><strong>Type:</strong> ${equipment.type_equipement || equipment.equip_2 || 'Type non spécifié'}</p>
            <p><strong>Installation:</strong> ${equipment.nom_instal || 'Non spécifié'}</p>
            ${equipment.activite ? `<p><strong>Activités:</strong> ${equipment.activite}</p>` : ''}
            ${equipment.adresse ? `<p><strong>Adresse:</strong> ${equipment.adresse}</p>` : ''}
            ${equipment.geo_point_2d ? `<a href="https://www.google.com/maps/dir/?api=1&destination=${equipment.geo_point_2d.lat},${equipment.geo_point_2d.lon}" 
            target="_blank" 
            class="text-blue-500 hover:underline">S'y rendre</a>` : ''}
        </div>
    `);
        return marker;
    }

    // Function to update the results list
    function updateResultsList(filteredEquipments) {
        const resultsList = document.getElementById('results-list');
        resultsList.innerHTML = '';
        document.getElementById('count').textContent = filteredEquipments.length;

        filteredEquipments.forEach(equipment => {
            const element = document.createElement('div');
            element.className = 'p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer';
            element.innerHTML = `
                <h3 class="font-semibold">${equipment.nom_equip || equipment.nom || 'Sans nom'}</h3>
                <p class="text-sm text-gray-600">${equipment.type_equipement || equipment.equip_2 || 'Type non spécifié'}</p>
                <p class="text-sm text-gray-500">${equipment.nom_instal || 'Installation non spécifiée'}</p>
                ${equipment.activite ? `<p class="text-sm text-gray-500">${equipment.activite}</p>` : ''}
            `;

            if (equipment.geo_point_2d) {
                element.onclick = () => {
                    map.setView([equipment.geo_point_2d.lat, equipment.geo_point_2d.lon], 16);
                    markers.find(m => m.equipment?.res_fid === equipment.res_fid)?.openPopup();
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

    fetch('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/equipements-sportifs-angers/records?limit=100')
        .then(response => response.json())
        .then(data => {
            equipments = data.results;
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
</script>
@endsection
