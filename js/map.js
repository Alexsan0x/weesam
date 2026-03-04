var map;
var markers = [];
var allPlaces = [];
var activeInfoWindow = null;
var currentPlaceId = null;
var isArabic = false;

function getField(place, field) {
    if (isArabic && place[field + '_ar']) return place[field + '_ar'];
    return place[field] || '';
}

function initMap() {
    var langEl = document.getElementById('siteLang');
    isArabic = (langEl && langEl.value === 'ar');

    map = new google.maps.Map(document.getElementById('googleMap'), {
        center: { lat: 31.5, lng: 36.0 },
        zoom: 8,
        styles: [
            { featureType: 'poi', stylers: [{ visibility: 'simplified' }] },
            { featureType: 'transit', stylers: [{ visibility: 'off' }] }
        ],
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: true
    });

    loadPlaces();
    setupSearch();
    setupCategoryFilters();
}

function loadPlaces() {
    fetch('api/places.php')
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                allPlaces = data.places;
                renderMarkers(allPlaces);
                renderPlacesList(allPlaces);
                checkSelectedPlace();
            }
        })
        .catch(function(err) {
            console.error('Failed to load places:', err);
        });
}

function renderMarkers(places) {
    markers.forEach(function(m) { m.setMap(null); });
    markers = [];

    places.forEach(function(place) {
        var pName = getField(place, 'name');
        var pCity = getField(place, 'city');
        var pCategory = getField(place, 'category');
        var pDesc = getField(place, 'description');

        var marker = new google.maps.Marker({
            position: { lat: place.lat, lng: place.lng },
            map: map,
            title: pName,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(38, 38)
            },
            animation: google.maps.Animation.DROP
        });

        var dirText = isArabic ? 'الاتجاهات &larr;' : 'Get Directions &rarr;';
        var infoContent = '<div style="max-width:250px;font-family:Poppins,sans-serif;' + (isArabic ? 'direction:rtl;text-align:right;' : '') + '">'
            + '<img src="' + place.image + '" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px;" alt="' + pName + '">'  
            + '<h3 style="margin:0 0 4px;font-size:1rem;">' + pName + '</h3>'
            + '<p style="font-size:0.82rem;color:#666;margin:0 0 8px;">' + pCity + ' &bull; ' + pCategory + '</p>'
            + '<p style="font-size:0.82rem;color:#444;line-height:1.4;margin:0 0 8px;">' + pDesc.substring(0, 120) + '...</p>'
            + '<a href="https://www.google.com/maps/dir/?api=1&destination=' + place.lat + ',' + place.lng + '" target="_blank" '
            + 'style="color:#C0392B;font-size:0.85rem;font-weight:600;">' + dirText + '</a>'
            + '</div>';

        var infoWindow = new google.maps.InfoWindow({ content: infoContent });

        marker.addListener('click', function() {
            if (activeInfoWindow) activeInfoWindow.close();
            infoWindow.open(map, marker);
            activeInfoWindow = infoWindow;
            map.panTo(marker.getPosition());
            map.setZoom(13);
            showPlaceDetail(place);
        });

        marker.placeData = place;
        markers.push(marker);
    });
}

function renderPlacesList(places) {
    var list = document.getElementById('placesList');
    if (!list) return;

    if (places.length === 0) {
        var noResultsText = isArabic ? 'لم يتم العثور على أماكن' : 'No places found';
        list.innerHTML = '<div style="text-align:center;padding:40px 20px;color:#888;">'
            + '<i class="fas fa-search" style="font-size:2rem;margin-bottom:10px;display:block;"></i>'
            + noResultsText + '</div>';
        return;
    }

    var html = '';
    places.forEach(function(place) {
        var pName = getField(place, 'name');
        var pDesc = getField(place, 'description');
        var pCategory = getField(place, 'category');
        html += '<div class="place-list-item" data-id="' + place.id + '" onclick="focusPlace(\'' + place.id + '\')">'
            + '<img src="' + place.image + '" alt="' + pName + '">'
            + '<div class="place-list-info">'
            + '<h4>' + pName + '</h4>'
            + '<p>' + pDesc.substring(0, 80) + '...</p>'
            + '<span class="place-category-tag">' + pCategory + '</span>'
            + '</div></div>';
    });
    list.innerHTML = html;
}

function focusPlace(placeId) {
    var place = allPlaces.find(function(p) { return p.id === placeId; });
    if (!place) return;

    map.panTo({ lat: place.lat, lng: place.lng });
    map.setZoom(14);

    var marker = markers.find(function(m) { return m.placeData.id === placeId; });
    if (marker) {
        google.maps.event.trigger(marker, 'click');
    }

    showPlaceDetail(place);
}

function showPlaceDetail(place) {
    currentPlaceId = place.id;
    var detail = document.getElementById('placeDetail');
    document.getElementById('detailName').textContent = getField(place, 'name');
    document.getElementById('detailDesc').textContent = getField(place, 'description');
    document.getElementById('detailDirections').href =
        'https://www.google.com/maps/dir/?api=1&destination=' + place.lat + ',' + place.lng;
    detail.classList.add('show');
}

function checkSelectedPlace() {
    var selectedInput = document.getElementById('selectedPlace');
    if (selectedInput && selectedInput.value) {
        setTimeout(function() {
            focusPlace(selectedInput.value);
        }, 500);
    }
}

function setupSearch() {
    var searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    var debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            var query = searchInput.value.toLowerCase().trim();
            var filtered = allPlaces.filter(function(p) {
                return p.name.toLowerCase().includes(query)
                    || (p.name_ar || '').toLowerCase().includes(query)
                    || p.city.toLowerCase().includes(query)
                    || (p.city_ar || '').toLowerCase().includes(query)
                    || p.category.toLowerCase().includes(query)
                    || p.description.toLowerCase().includes(query)
                    || (p.description_ar || '').includes(query);
            });
            renderMarkers(filtered);
            renderPlacesList(filtered);
        }, 300);
    });
}

function setupCategoryFilters() {
    var filters = document.querySelectorAll('.filter-chip');
    filters.forEach(function(chip) {
        chip.addEventListener('click', function() {
            filters.forEach(function(c) { c.classList.remove('active'); });
            chip.classList.add('active');

            var category = chip.getAttribute('data-category');
            var filtered = category === 'all'
                ? allPlaces
                : allPlaces.filter(function(p) { return p.category === category; });

            renderMarkers(filtered);
            renderPlacesList(filtered);
        });
    });
}

var closeDetailBtn = document.getElementById('closeDetail');
if (closeDetailBtn) {
    closeDetailBtn.addEventListener('click', function() {
        document.getElementById('placeDetail').classList.remove('show');
    });
}

function toggleFavorite() {
    if (!currentPlaceId) return;

    var isLoggedIn = document.getElementById('isLoggedIn');
    if (!isLoggedIn || isLoggedIn.value !== '1') {
        alert(isArabic ? 'يرجى تسجيل الدخول لحفظ المفضلات.' : 'Please log in to save favorites.');
        return;
    }

    var formData = new FormData();
    formData.append('action', 'add');
    formData.append('place_id', currentPlaceId);

    fetch('api/favorites.php', {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        var btn = document.getElementById('detailFavorite');
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check"></i> ' + (isArabic ? 'تم الحفظ!' : 'Saved!');
            btn.style.background = '#27AE60';
        } else {
            btn.innerHTML = '<i class="fas fa-heart"></i> ' + data.message;
        }
        setTimeout(function() {
            btn.innerHTML = '<i class="fas fa-heart"></i> ' + (isArabic ? 'حفظ' : 'Save');
            btn.style.background = '';
        }, 2000);
    })
    .catch(function() {
        alert(isArabic ? 'فشل الحفظ. يرجى المحاولة مرة أخرى.' : 'Failed to save. Please try again.');
    });
}
