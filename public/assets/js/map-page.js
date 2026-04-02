(function () {
    const pageDataElement = document.getElementById('page-data');
    const pageData = pageDataElement ? JSON.parse(pageDataElement.textContent || '{}') : {};
    const offers = Array.isArray(pageData.mapOffers) ? pageData.mapOffers : [];
    const selectedOfferId = Number(pageData.selectedOfferId || 0);
    const mapContainer = document.getElementById('offers-map');

    if (!mapContainer || !window.L || offers.length === 0) {
        return;
    }

    const locationSearchInput = document.getElementById('map-location-search');
    const locationSearchButton = document.getElementById('map-location-search-button');
    const useMyLocationButton = document.getElementById('map-use-my-location');
    const centerUserButton = document.getElementById('map-center-user');
    const clearSearchButton = document.getElementById('map-clear-search');
    const locationFeedback = document.getElementById('map-location-feedback');
    const mapOffersList = document.getElementById('map-offers-list');
    const mobileSheet = document.getElementById('map-mobile-sheet');
    const mobileOffersToggle = document.getElementById('mobile-offers-toggle');
    const offerTriggers = Array.from(document.querySelectorAll('[data-map-offer-trigger]'));
    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll('\'', '&#039;');

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

    const feedbackMessages = {
        searchPending: 'Buscando ubicación...',
        searchEmpty: 'Escribí una ubicación para iniciar la búsqueda.',
        searchSuccess: 'Ubicación encontrada. Distancias actualizadas.',
        searchError: 'No pudimos completar la búsqueda. Probá con otra referencia.',
        gpsPending: 'Detectando tu ubicación...',
        gpsSuccess: 'Ubicación detectada. Distancias actualizadas.',
        gpsUnsupported: 'Tu navegador no permite geolocalización.',
        gpsPermissionDenied: 'No diste permiso de ubicación. Revisá la configuración del navegador.',
        gpsUnavailable: 'No pudimos acceder a tu ubicación. Intentá nuevamente.',
        gpsTimeout: 'La detección demoró demasiado. Volvé a intentarlo.',
        resetDone: 'Vista restablecida. Mostramos las ofertas según el orden inicial.',
    };

    const setFeedback = (message, isError = false) => {
        if (!locationFeedback) {
            return;
        }

        locationFeedback.textContent = message;
        locationFeedback.classList.toggle('text-red-600', isError);
        locationFeedback.classList.toggle('text-emerald-600', !isError);
    };

    const setFeedbackByKey = (key, isError = false, suffix = '') => {
        const baseMessage = feedbackMessages[key] || feedbackMessages.searchError;
        setFeedback(`${baseMessage}${suffix}`, isError);
    };

    const defaultCoordinates = pageData.defaultCenter || [-34.636, -58.536];
    const map = window.L.map(mapContainer).setView(defaultCoordinates, 13);
    map.getContainer().setAttribute('tabindex', '-1');
    const redMarkerIcon = window.L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41],
    });

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
    let isSearchingLocation = false;
    let isLocatingUser = false;
    const markersByOfferId = new Map();
    const bounds = [];
    let mobileSheetState = 'collapsed';

    const setMobileSheetState = (nextState) => {
        if (!mobileSheet) {
            return;
        }

        mobileSheetState = nextState === 'expanded' ? 'expanded' : 'collapsed';
        mobileSheet.classList.toggle('map-mobile-sheet--expanded', mobileSheetState === 'expanded');
        mobileSheet.classList.toggle('map-mobile-sheet--collapsed', mobileSheetState === 'collapsed');
        mobileSheet.setAttribute('aria-expanded', mobileSheetState === 'expanded' ? 'true' : 'false');

        if (mobileOffersToggle) {
            mobileOffersToggle.setAttribute('aria-expanded', mobileSheetState === 'expanded' ? 'true' : 'false');
        }

        if (mobileSheetState === 'collapsed') {
            map.getContainer().focus({ preventScroll: true });
        }
    };

    const focusOfferOnMap = (offerId, openDetails = true) => {
        const markerEntry = markersByOfferId.get(Number(offerId));

        if (!markerEntry) {
            return;
        }

        map.setView(markerEntry.marker.getLatLng(), 16, { animate: true });
        markerEntry.marker.openTooltip();

        if (openDetails) {
            openModal(markerEntry.offer);
        }
    };

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
        const extraAddress = [
            offer.between_streets ? `Entre calles: ${offer.between_streets}` : '',
            offer.postal_code ? `CP ${offer.postal_code}` : '',
        ].filter(Boolean).join(' · ');
        modalLocation.textContent = extraAddress ? `${offer.location} · ${extraAddress}` : offer.location;
        modalExpiration.textContent = offer.expires_label;
        refreshModalCountdown();
        modalWhatsapp.href = `${offer.whatsapp_url}?text=${encodeURIComponent(`Hola! Vi su oferta de '${offer.title}' en el mapa de OfertasLocales. Sigue disponible?`)}`;
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

    const reorderOffersList = (compareFn) => {
        if (!mapOffersList) {
            return;
        }

        offerTriggers
            .sort(compareFn)
            .forEach((trigger) => mapOffersList.appendChild(trigger));
    };

    const updateOffersDistanceUI = () => {
        offerTriggers.forEach((trigger) => {
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

        reorderOffersList((left, right) => {
            if (!userLocation) {
                return Number(left.dataset.originalOrder) - Number(right.dataset.originalOrder);
            }

            return Number(left.dataset.distanceSort) - Number(right.dataset.distanceSort);
        });
    };

    const setButtonBusyState = (button, isBusy) => {
        if (!button) {
            return;
        }

        const label = button.querySelector('[data-button-label]');
        const defaultLabel = label?.getAttribute('data-label-default') ?? label?.textContent ?? '';
        const busyLabel = label?.getAttribute('data-label-busy') ?? defaultLabel;

        button.classList.toggle('opacity-70', isBusy);
        button.classList.toggle('cursor-not-allowed', isBusy);
        button.classList.toggle('ring-2', isBusy);
        button.classList.toggle('ring-red-200', isBusy);

        if (label) {
            label.textContent = isBusy ? busyLabel : defaultLabel;
        }
    };

    const updateActionStates = () => {
        const hasPendingOperation = isSearchingLocation || isLocatingUser;
        const canCenter = Boolean(userLocation) && !hasPendingOperation;

        if (locationSearchButton) {
            locationSearchButton.disabled = hasPendingOperation;
            setButtonBusyState(locationSearchButton, isSearchingLocation);
        }

        if (useMyLocationButton) {
            useMyLocationButton.disabled = hasPendingOperation;
            setButtonBusyState(useMyLocationButton, isLocatingUser);
        }

        if (locationSearchInput) {
            locationSearchInput.disabled = hasPendingOperation;
        }

        if (centerUserButton) {
            centerUserButton.disabled = !canCenter;
            centerUserButton.classList.toggle('opacity-60', !canCenter);
            centerUserButton.classList.toggle('cursor-not-allowed', !canCenter);
        }

        if (clearSearchButton) {
            clearSearchButton.disabled = hasPendingOperation;
            clearSearchButton.classList.toggle('opacity-60', hasPendingOperation);
            clearSearchButton.classList.toggle('cursor-not-allowed', hasPendingOperation);
        }
    };

    const placeUserLocationMarker = (lat, lon) => {
        if (userLocationMarker) {
            map.removeLayer(userLocationMarker);
        }

        userLocationMarker = window.L.marker([lat, lon], {
            title: 'Tu ubicación',
            riseOnHover: true,
            icon: redMarkerIcon,
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
        setFeedbackByKey(sourceLabel === 'GPS del navegador' ? 'gpsSuccess' : 'searchSuccess');
        map.setView([lat, lon], 14, { animate: true });
        updateActionStates();
    };

    const geocodeLocation = async (query) => {
        const endpoint = new URL('https://nominatim.openstreetmap.org/search');
        const normalizedQuery = /argentina/i.test(query) ? query : `${query}, Argentina`;
        endpoint.searchParams.set('q', normalizedQuery);
        endpoint.searchParams.set('format', 'jsonv2');
        endpoint.searchParams.set('limit', '1');
        endpoint.searchParams.set('addressdetails', '1');
        endpoint.searchParams.set('accept-language', 'es');
        endpoint.searchParams.set('countrycodes', 'ar');

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
            setFeedbackByKey('searchEmpty', true);

            return;
        }

        isSearchingLocation = true;
        updateActionStates();
        setFeedbackByKey('searchPending');

        try {
            const result = await geocodeLocation(query);
            setUserLocation(result.lat, result.lon, `búsqueda (${result.label})`);
        } catch (error) {
            setFeedback(error instanceof Error ? error.message : feedbackMessages.searchError, true);
        } finally {
            isSearchingLocation = false;
            updateActionStates();
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

        const marker = window.L.marker([lat, lon], { icon: redMarkerIcon, riseOnHover: true }).addTo(map);
        marker.bindTooltip(`
            <div class="map-offer-tooltip">
                <img src="${escapeHtml(offer.image_url)}" alt="${escapeHtml(offer.title)}" class="map-offer-tooltip__image">
                <p class="map-offer-tooltip__business">${escapeHtml(offer.business_name)}</p>
                <p class="map-offer-tooltip__title">${escapeHtml(offer.title)}</p>
                <p class="map-offer-tooltip__location">${escapeHtml(offer.location)}</p>
            </div>
        `, {
            direction: 'top',
            offset: [0, -10],
            opacity: 0.98,
            className: 'map-leaflet-tooltip',
        });
        marker.on('click', () => openModal(offer));
        markersByOfferId.set(offer.id, { marker, offer, distanceKm: Number.POSITIVE_INFINITY });
        bounds.push([lat, lon]);
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [32, 32] });
    }

    offerTriggers.forEach((trigger, index) => {
        trigger.dataset.originalOrder = index.toString();
        trigger.addEventListener('click', () => {
            const offerId = Number(trigger.getAttribute('data-map-offer-trigger'));
            focusOfferOnMap(offerId, true);
            if (window.innerWidth < 768) {
                setMobileSheetState('collapsed');
            }
        });
        trigger.addEventListener('mouseenter', () => {
            const offerId = Number(trigger.getAttribute('data-map-offer-trigger'));
            const markerEntry = markersByOfferId.get(offerId);
            markerEntry?.marker.openTooltip();
        });
        trigger.addEventListener('mouseleave', () => {
            const offerId = Number(trigger.getAttribute('data-map-offer-trigger'));
            const markerEntry = markersByOfferId.get(offerId);
            markerEntry?.marker.closeTooltip();
        });
        trigger.addEventListener('focus', () => {
            const offerId = Number(trigger.getAttribute('data-map-offer-trigger'));
            const markerEntry = markersByOfferId.get(offerId);
            markerEntry?.marker.openTooltip();
        });
        trigger.addEventListener('blur', () => {
            const offerId = Number(trigger.getAttribute('data-map-offer-trigger'));
            const markerEntry = markersByOfferId.get(offerId);
            markerEntry?.marker.closeTooltip();
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
            setFeedbackByKey('gpsUnsupported', true);

            return;
        }

        isLocatingUser = true;
        updateActionStates();
        setFeedbackByKey('gpsPending');
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                setUserLocation(latitude, longitude, 'GPS del navegador');
                isLocatingUser = false;
                updateActionStates();
            },
            (error) => {
                const permissionDenied = error.code === error.PERMISSION_DENIED;
                const timeoutReached = error.code === error.TIMEOUT;
                const feedbackKey = permissionDenied
                    ? 'gpsPermissionDenied'
                    : timeoutReached
                        ? 'gpsTimeout'
                        : 'gpsUnavailable';

                setFeedbackByKey(feedbackKey, true);
                isLocatingUser = false;
                updateActionStates();
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

    clearSearchButton?.addEventListener('click', () => {
        if (locationSearchInput) {
            locationSearchInput.value = '';
        }

        userLocation = null;

        if (userLocationMarker) {
            map.removeLayer(userLocationMarker);
            userLocationMarker = null;
        }

        centerUserButton?.classList.add('hidden');
        updateOffersDistanceUI();
        map.setView(defaultCoordinates, 13, { animate: true });
        setFeedbackByKey('resetDone');
        updateActionStates();
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
    updateActionStates();
    setMobileSheetState(window.innerWidth >= 768 ? 'expanded' : 'collapsed');
    map.invalidateSize();
    window.setTimeout(() => map.invalidateSize(), 200);
    if (selectedOfferId > 0) {
        window.setTimeout(() => focusOfferOnMap(selectedOfferId, true), 260);
    }
    window.addEventListener('resize', () => map.invalidateSize());
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            setMobileSheetState('expanded');
        } else if (mobileSheetState !== 'collapsed') {
            setMobileSheetState('collapsed');
        }
    });

    mobileOffersToggle?.addEventListener('click', () => {
        setMobileSheetState(mobileSheetState === 'expanded' ? 'collapsed' : 'expanded');
    });

    window.setInterval(refreshAllCountdowns, 1000);
})();
