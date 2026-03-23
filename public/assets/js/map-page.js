(function () {
    const pageDataElement = document.getElementById('page-data');
    const pageData = pageDataElement ? JSON.parse(pageDataElement.textContent || '{}') : {};
    const offers = Array.isArray(pageData.mapOffers) ? pageData.mapOffers : [];
    const mapContainer = document.getElementById('offers-map');

    if (!mapContainer || !window.L || offers.length === 0) {
        return;
    }

    const locationSearchInput = document.getElementById('map-location-search');
    const locationSearchButton = document.getElementById('map-location-search-button');
    const useMyLocationButton = document.getElementById('map-use-my-location');
    const centerUserButton = document.getElementById('map-center-user');
    const locationFeedback = document.getElementById('map-location-feedback');
    const mapOffersList = document.getElementById('map-offers-list');

    const formatRemainingTime = (expiresAt) => {
        const expirationDate = new Date(expiresAt);
        const diffInSeconds = Math.max(0, Math.floor((expirationDate.getTime() - Date.now()) / 1000));

        if (diffInSeconds <= 0) {
            return 'Finalizada';
        }

        const days = Math.floor(diffInSeconds / 86400);
        const hours = Math.floor((diffInSeconds % 86400) / 3600);
        const minutes = Math.floor((diffInSeconds % 3600) / 60);
        const seconds = diffInSeconds % 60;
        const time = [hours, minutes, seconds]
            .map((value) => value.toString().padStart(2, '0'))
            .join(':');

        return days > 0 ? `${days}d ${time}` : time;
    };

    const formatDistance = (kilometers) => {
        if (!Number.isFinite(kilometers)) {
            return '';
        }

        if (kilometers < 1) {
            return `${Math.round(kilometers * 1000)} m`;
        }

        return `${kilometers.toFixed(1)} km`;
    };

    const haversineDistanceKm = (origin, destination) => {
        const radius = 6371;
        const deltaLat = (destination.lat - origin.lat) * (Math.PI / 180);
        const deltaLon = (destination.lon - origin.lon) * (Math.PI / 180);
        const lat1 = origin.lat * (Math.PI / 180);
        const lat2 = destination.lat * (Math.PI / 180);
        const a = Math.sin(deltaLat / 2) ** 2
            + Math.cos(lat1) * Math.cos(lat2) * Math.sin(deltaLon / 2) ** 2;

        return radius * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
    };

    const setFeedback = (message, isError = false) => {
        if (!locationFeedback) {
            return;
        }

        locationFeedback.textContent = message;
        locationFeedback.classList.toggle('text-red-600', isError);
        locationFeedback.classList.toggle('text-emerald-600', !isError);
    };

    const defaultCoordinates = pageData.defaultCenter || [-34.636, -58.536];
    const map = window.L.map(mapContainer).setView(defaultCoordinates, 13);

    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const modal = document.getElementById('map-offer-modal');
    const closeButton = document.getElementById('map-modal-close');
    const modalTitle = document.getElementById('map-modal-title');
    const modalImage = document.getElementById('map-modal-image');
    const modalBusiness = document.getElementById('map-modal-business');
    const modalOffer = document.getElementById('map-modal-offer');
    const modalCategory = document.getElementById('map-modal-category');
    const modalDescription = document.getElementById('map-modal-description');
    const modalLocation = document.querySelector('#map-modal-location span');
    const modalExpiration = document.querySelector('#map-modal-expiration span');
    const modalCountdown = document.querySelector('#map-modal-countdown span');
    const modalWhatsapp = document.getElementById('map-modal-whatsapp');

    let activeOffer = null;
    let userLocation = null;
    let userLocationMarker = null;
    const markersByOfferId = new Map();
    const bounds = [];

    const refreshModalCountdown = () => {
        if (!activeOffer || !modalCountdown) {
            return;
        }

        modalCountdown.textContent = `Restan ${formatRemainingTime(activeOffer.expires_at)}`;
    };

    const refreshSidebarCountdowns = () => {
        document.querySelectorAll('[data-map-countdown]').forEach((countdownNode) => {
            const expiration = countdownNode.getAttribute('data-expiration');
            const label = countdownNode.querySelector('span');

            if (!expiration || !label) {
                return;
            }

            label.textContent = `Restan ${formatRemainingTime(expiration)}`;
        });
    };

    const openModal = (offer) => {
        if (!modal) {
            return;
        }

        activeOffer = offer;
        modalTitle.textContent = offer.title;
        modalImage.src = offer.image_url;
        modalImage.alt = offer.title;
        modalBusiness.textContent = offer.business_name;
        modalOffer.textContent = offer.title;
        modalCategory.textContent = offer.category;
        modalDescription.textContent = offer.description;
        modalLocation.textContent = offer.location;
        modalExpiration.textContent = offer.expires_label;
        refreshModalCountdown();
        modalWhatsapp.href = `https://wa.me/${offer.whatsapp}?text=${encodeURIComponent(`Hola! Vi su oferta de '${offer.title}' en el mapa de OfertasCerca. Sigue disponible?`)}`;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        if (window.lucide) {
            window.lucide.createIcons();
        }
    };

    const closeModal = () => {
        activeOffer = null;

        if (modal) {
            modal.classList.add('hidden');
        }

        document.body.classList.remove('overflow-hidden');
    };

    const updateOffersDistanceUI = () => {
        const triggers = Array.from(document.querySelectorAll('[data-map-offer-trigger]'));

        triggers.forEach((trigger) => {
            const badge = trigger.querySelector('[data-offer-distance-badge]');
            const offerId = Number(trigger.getAttribute('data-map-offer-trigger'));
            const entry = markersByOfferId.get(offerId);

            if (!badge || !entry || !userLocation) {
                if (badge) {
                    badge.classList.add('hidden');
                }

                trigger.dataset.distanceSort = Number.POSITIVE_INFINITY.toString();

                return;
            }

            const distanceKm = haversineDistanceKm(userLocation, {
                lat: entry.offer.lat,
                lon: entry.offer.lon,
            });

            entry.distanceKm = distanceKm;
            trigger.dataset.distanceSort = distanceKm.toString();
            badge.textContent = formatDistance(distanceKm);
            badge.classList.remove('hidden');
        });

        if (!mapOffersList) {
            return;
        }

        triggers
            .sort((left, right) => Number(left.dataset.distanceSort) - Number(right.dataset.distanceSort))
            .forEach((trigger) => mapOffersList.appendChild(trigger));
    };

    const placeUserLocationMarker = (lat, lon) => {
        if (userLocationMarker) {
            map.removeLayer(userLocationMarker);
        }

        userLocationMarker = window.L.marker([lat, lon], {
            title: 'Tu ubicación',
            riseOnHover: true,
        }).addTo(map);
        userLocationMarker.bindTooltip('Tu ubicación', {
            direction: 'top',
            offset: [0, -10],
        });
        centerUserButton?.classList.remove('hidden');
    };

    const setUserLocation = (lat, lon, sourceLabel) => {
        userLocation = { lat, lon };
        placeUserLocationMarker(lat, lon);
        updateOffersDistanceUI();
        setFeedback(`Ubicación detectada por ${sourceLabel}. Distancias actualizadas.`);
        map.setView([lat, lon], 14, { animate: true });
    };

    const geocodeLocation = async (query) => {
        const endpoint = new URL('https://nominatim.openstreetmap.org/search');
        endpoint.searchParams.set('q', query);
        endpoint.searchParams.set('format', 'jsonv2');
        endpoint.searchParams.set('limit', '1');
        endpoint.searchParams.set('addressdetails', '1');
        endpoint.searchParams.set('accept-language', 'es');

        const response = await fetch(endpoint.toString(), {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('No fue posible geocodificar la dirección.');
        }

        const data = await response.json();

        if (!Array.isArray(data) || data.length === 0) {
            throw new Error('No encontramos esa ubicación. Probá con otra referencia.');
        }

        const result = data[0];

        return {
            lat: Number(result.lat),
            lon: Number(result.lon),
            label: result.display_name || query,
        };
    };

    const handleSearchLocation = async () => {
        if (!locationSearchInput) {
            return;
        }

        const query = locationSearchInput.value.trim();

        if (query === '') {
            setFeedback('Escribí una ubicación para iniciar la búsqueda.', true);

            return;
        }

        locationSearchButton?.setAttribute('disabled', 'disabled');
        locationSearchButton?.classList.add('opacity-70', 'cursor-not-allowed');
        setFeedback('Buscando ubicación...');

        try {
            const result = await geocodeLocation(query);
            setUserLocation(result.lat, result.lon, `búsqueda (${result.label})`);
        } catch (error) {
            setFeedback(error instanceof Error ? error.message : 'No pudimos buscar esa ubicación.', true);
        } finally {
            locationSearchButton?.removeAttribute('disabled');
            locationSearchButton?.classList.remove('opacity-70', 'cursor-not-allowed');
        }
    };

    offers.forEach((offer) => {
        const lat = Number(offer.lat);
        const lon = Number(offer.lon);

        if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
            return;
        }

        offer.lat = lat;
        offer.lon = lon;

        const marker = window.L.marker([lat, lon]).addTo(map);
        marker.bindTooltip(`
            <div class="w-48">
                <img src="${offer.image_url}" alt="${offer.title}" class="w-full h-24 object-cover rounded-xl mb-2">
                <p class="font-bold text-gray-900 text-sm">${offer.business_name}</p>
                <p class="text-red-600 font-semibold text-sm">${offer.title}</p>
                <p class="text-gray-500 text-xs mt-1">${offer.location}</p>
            </div>
        `, {
            direction: 'top',
            offset: [0, -10],
            opacity: 0.98,
        });
        marker.on('click', () => openModal(offer));
        markersByOfferId.set(offer.id, { marker, offer, distanceKm: Number.POSITIVE_INFINITY });
        bounds.push([lat, lon]);
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [32, 32] });
    }

    document.querySelectorAll('[data-map-offer-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const offerId = Number(trigger.getAttribute('data-map-offer-trigger'));
            const markerEntry = markersByOfferId.get(offerId);

            if (!markerEntry) {
                return;
            }

            map.setView(markerEntry.marker.getLatLng(), 16, { animate: true });
            markerEntry.marker.openTooltip();
            openModal(markerEntry.offer);
        });
    });

    locationSearchButton?.addEventListener('click', handleSearchLocation);
    locationSearchInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            void handleSearchLocation();
        }
    });

    useMyLocationButton?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            setFeedback('Tu navegador no permite geolocalización.', true);

            return;
        }

        setFeedback('Detectando tu ubicación...');
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                setUserLocation(latitude, longitude, 'GPS del navegador');
            },
            () => {
                setFeedback('No pudimos acceder a tu ubicación. Revisá permisos del navegador.', true);
            },
            {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 0,
            }
        );
    });

    centerUserButton?.addEventListener('click', () => {
        if (!userLocation) {
            return;
        }

        map.setView([userLocation.lat, userLocation.lon], 15, { animate: true });
        userLocationMarker?.openTooltip();
    });

    closeButton?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
    const refreshAllCountdowns = () => {
        refreshModalCountdown();
        refreshSidebarCountdowns();
    };

    refreshSidebarCountdowns();
    map.invalidateSize();
    window.setTimeout(() => map.invalidateSize(), 200);
    window.addEventListener('resize', () => map.invalidateSize());

    window.setInterval(refreshAllCountdowns, 1000);
})();
