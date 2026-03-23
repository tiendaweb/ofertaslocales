(function () {
    const pageDataElement = document.getElementById('page-data');
    const pageData = pageDataElement ? JSON.parse(pageDataElement.textContent || '{}') : {};
    const offers = Array.isArray(pageData.mapOffers) ? pageData.mapOffers : [];
    const mapContainer = document.getElementById('offers-map');

    if (!mapContainer || !window.L || offers.length === 0) {
        return;
    }

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
    const modalDescription = document.getElementById('map-modal-description');
    const modalLocation = document.querySelector('#map-modal-location span');
    const modalExpiration = document.querySelector('#map-modal-expiration span');
    const modalWhatsapp = document.getElementById('map-modal-whatsapp');

    const bounds = [];

    const openModal = (offer) => {
        if (!modal) {
            return;
        }

        modalTitle.textContent = offer.title;
        modalImage.src = offer.image_url;
        modalBusiness.textContent = offer.business_name;
        modalOffer.textContent = offer.title;
        modalDescription.textContent = offer.description;
        modalLocation.textContent = offer.location;
        modalExpiration.textContent = offer.expires_label;
        modalWhatsapp.href = `https://wa.me/${offer.whatsapp}?text=${encodeURIComponent(`Hola! Vi su oferta de '${offer.title}' en el mapa de OfertasCerca. Sigue disponible?`)}`;
        modal.classList.remove('hidden');

        if (window.lucide) {
            window.lucide.createIcons();
        }
    };

    const closeModal = () => {
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    offers.forEach((offer) => {
        if (typeof offer.lat !== 'number' || typeof offer.lon !== 'number') {
            return;
        }

        const marker = window.L.marker([offer.lat, offer.lon]).addTo(map);
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
        bounds.push([offer.lat, offer.lon]);
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [32, 32] });
    }

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
})();
