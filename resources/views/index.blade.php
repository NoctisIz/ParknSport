@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Carte des équipements sportifs à Angers</h1>
    <div id="map" style="height: 500px;"></div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Initialisation de la carte
    var map = L.map('map').setView([47.4657, -0.5276], 13); // Coordonnées du centre de la carte (Angers)

    // Ajouter un fond de carte Jawg Streets (clé API nécessaire)
    L.tileLayer('https://{s}.tile.jawg.io/jawg-streets/{z}/{x}/{y}.png?access-token=cJPcOiK2rCU52SCrm0H2UB9YdJqg8u2wADnEdShSbu8E6vtSGE3RFhXaD2RtD7fg', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Jawg',
        maxZoom: 19
    }).addTo(map);

    // Fonction pour récupérer les données depuis l'API
    function fetchEquipements(url) {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log('API Response:', data); // Affiche les données pour debug

                var equipements = data.records.map(record => {
                    const geoPoint = record.fields.geo_point_2d;
                    return {
                        name: record.fields.nom_equip,
                        latitude: geoPoint ? geoPoint[1] : null,
                        longitude: geoPoint ? geoPoint[0] : null
                    };
                });

                console.log('Equipements:', equipements); // Affiche les équipements récupérés

                // Ajouter les marqueurs pour chaque équipement
                equipements.forEach(function(equipement) {
                    if (equipement.latitude !== null && equipement.longitude !== null) {
                        L.marker([equipement.latitude, equipement.longitude]).addTo(map)
                            .bindPopup("<strong>" + equipement.name + "</strong>");
                    }
                });

                // Vérifier s'il y a une page suivante (pagination)
                if (data.nhits > data.records.length) {
                    const nextPage = data.links.next; // Récupérer l'URL de la page suivante
                    fetchEquipements(nextPage); // Récupérer les résultats de la page suivante
                }
            })
            .catch(error => console.error('Error fetching data: ', error));
    }

    // Lancer la récupération des données
    const apiUrl = 'https://api.opendatasoft.com/api/records/1.0/search/?dataset=les-equipements-sportifs&q=&rows=100'; // Modifier le nombre de résultats
    fetchEquipements(apiUrl);
</script>
@endpush
@endsection
