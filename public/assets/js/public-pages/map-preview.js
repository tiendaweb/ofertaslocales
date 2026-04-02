(function () {
    const setupHomeMapPreview = (publicPages) => {
        const previewContainer = document.getElementById('home-map-preview');
        const centerMeButton = document.getElementById('home-map-center-me');
        const nearbyButton = document.getElementById('home-map-filter-nearby');
        const feedbackNode = document.getElementById('home-map-feedback');
        const modal = document.getElementById('home-map-offer-modal');
        const modalClose = document.getElementById('home-map-modal-close');
        const modalBusiness = document.getElementById('home-map-modal-business');
        const modalTitle = document.getElementById('home-map-modal-title');
        const modalImage = document.getElementById('home-map-modal-image');
        const modalCategory = document.getElementById('home-map-modal-category');
        const modalDescription = document.getElementById('home-map-modal-description');
        const modalLocation = document.querySelector('#home-map-modal-location span');
        const modalWhatsapp = document.getElementById('home-map-modal-whatsapp');
        const mapOffers = Array.isArray(publicPages.pageData.mapOffers) ? publicPages.pageData.mapOffers.slice(0, 8) : [];

        if (!previewContainer) {
            return;
        }

        const setFeedback = (message, isError = false) => {
            if (!feedbackNode) {
                return;
            }

            feedbackNode.textContent = message;
            feedbackNode.classList.toggle('text-red-600', isError);
            feedbackNode.classList.toggle('text-gray-500', !isError);
        };

        const closeModal = () => {
            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        const openModal = (offer) => {
            if (!modal || !modalBusiness || !modalTitle || !modalImage || !modalCategory || !modalDescription || !modalLocation || !modalWhatsapp) {
                return;
            }

            modalBusiness.textContent = offer.business_name || 'Negocio';
            modalTitle.textContent = offer.title || 'Oferta seleccionada';
            modalImage.src = offer.image_url || '';
            modalImage.alt = offer.title || 'Oferta seleccionada';
            modalCategory.textContent = offer.category || '';
            modalDescription.textContent = offer.description || 'Sin descripción adicional.';
            const extraAddress = [
                offer.between_streets ? `Entre calles: ${offer.between_streets}` : '',
                offer.postal_code ? `CP ${offer.postal_code}` : '',
            ].filter(Boolean).join(' · ');
            modalLocation.textContent = extraAddress
                ? `${offer.location || 'Ubicación no especificada'} · ${extraAddress}`
                : (offer.location || 'Ubicación no especificada');
            modalWhatsapp.href = publicPages.buildWhatsAppLink(offer);
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            if (window.lucide) {
                window.lucide.createIcons();
            }
        };

        modalClose?.addEventListener('click', closeModal);
        modal?.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        if (mapOffers.length === 0) {
            previewContainer.innerHTML = '<div class="h-full flex items-center justify-center text-center px-6 bg-gradient-to-br from-gray-100 to-gray-200"><div><i data-lucide="map-pinned" class="w-10 h-10 text-red-500 mx-auto mb-4"></i><p class="text-lg font-bold text-gray-800">Pronto vas a ver puntos activos en esta zona.</p><p class="text-gray-500 mt-2">Todavía no hay coordenadas suficientes para construir la vista previa.</p></div></div>';
            if (window.lucide) {
                window.lucide.createIcons();
            }
            centerMeButton?.setAttribute('disabled', 'disabled');
            nearbyButton?.setAttribute('disabled', 'disabled');
            setFeedback('No hay ofertas geolocalizadas para mostrar todavía.');
            return;
        }

        if (!window.L) {
            centerMeButton?.setAttribute('disabled', 'disabled');
            nearbyButton?.setAttribute('disabled', 'disabled');
            setFeedback('Vista previa estática activa: Leaflet no se pudo cargar.', true);
            return;
        }

        const validOffers = mapOffers.filter((offer) => Number.isFinite(Number(offer.lat)) && Number.isFinite(Number(offer.lon)));
        if (validOffers.length === 0) {
            setFeedback('No hay coordenadas válidas para construir el mapa.', true);
            return;
        }

        const first = validOffers[0];
        const map = window.L.map(previewContainer).setView([Number(first.lat), Number(first.lon)], 13);
        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        const bounds = [];
        const offerPoints = [];

        validOffers.forEach((offer) => {
            const lat = Number(offer.lat);
            const lon = Number(offer.lon);
            const marker = window.L.marker([lat, lon]).addTo(map);
            marker.bindTooltip(`<div class="w-44 max-w-[11rem]"><img src="${publicPages.escapeHtml(offer.image_url)}" alt="${publicPages.escapeHtml(offer.title)}" class="w-full h-20 object-cover rounded-lg mb-2"><p class="text-xs uppercase tracking-wider text-gray-500 truncate">${publicPages.escapeHtml(offer.category)}</p><p class="font-semibold text-gray-900 truncate">${publicPages.escapeHtml(offer.business_name)}</p><p class="text-red-600 font-semibold text-sm line-clamp-2 leading-5 break-words">${publicPages.escapeHtml(offer.title)}</p></div>`, { direction: 'top', offset: [0, -10] });
            marker.on('click', () => openModal(offer));
            bounds.push([lat, lon]);
            offerPoints.push({ lat, lon });
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [24, 24] });
        }

        const fitToNearby = (originLat, originLon) => {
            const rank = offerPoints
                .map((point) => ({ ...point, distance: Math.hypot(point.lat - originLat, point.lon - originLon) }))
                .sort((a, b) => a.distance - b.distance)
                .slice(0, 4)
                .map((point) => [point.lat, point.lon]);
            rank.push([originLat, originLon]);
            map.fitBounds(rank, { padding: [32, 32], maxZoom: 15 });
            setFeedback('Mostrando ofertas cercanas a tu ubicación.');
        };

        centerMeButton?.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setFeedback('Tu navegador no soporta geolocalización.', true);
                return;
            }
            navigator.geolocation.getCurrentPosition((position) => {
                map.setView([position.coords.latitude, position.coords.longitude], 14, { animate: true });
                setFeedback('Mapa centrado en tu ubicación actual.');
            }, () => setFeedback('No pudimos acceder a tu ubicación.', true), { enableHighAccuracy: true, timeout: 7000 });
        });

        nearbyButton?.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setFeedback('Tu navegador no soporta geolocalización.', true);
                return;
            }
            navigator.geolocation.getCurrentPosition((position) => {
                fitToNearby(position.coords.latitude, position.coords.longitude);
            }, () => setFeedback('No fue posible obtener tu ubicación para filtrar cercanas.', true), { enableHighAccuracy: true, timeout: 7000 });
        });
    };

    window.PublicPagesMapPreview = {
        init(publicPages) {
            setupHomeMapPreview(publicPages);
        },
    };
})();
