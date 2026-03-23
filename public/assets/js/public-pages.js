(function () {
    const pageDataElement = document.getElementById('page-data');
    const pageData = pageDataElement ? JSON.parse(pageDataElement.textContent || '{}') : {};

    const formatRemainingTime = (expiresAt) => {
        const expirationDate = new Date(expiresAt);
        const diffInSeconds = Math.max(0, Math.floor((expirationDate.getTime() - Date.now()) / 1000));

        if (diffInSeconds <= 0) {
            return 'Finalizado';
        }

        const hours = Math.floor(diffInSeconds / 3600);
        const minutes = Math.floor((diffInSeconds % 3600) / 60);
        const seconds = diffInSeconds % 60;

        return [hours, minutes, seconds]
            .map((value) => value.toString().padStart(2, '0'))
            .join(':');
    };

    const buildWhatsAppLink = (offer) => {
        const message = encodeURIComponent(
            `Hola! Vi su oferta de '${offer.title}' en OfertasCerca. Sigue disponible?`
        );

        return `https://wa.me/${offer.whatsapp}?text=${message}`;
    };

    const renderOfferCards = (offers, container) => {
        if (!container) {
            return;
        }

        if (offers.length === 0) {
            container.innerHTML = `
                <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                    <i data-lucide="store" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-700">No hay ofertas en esta categoría</h3>
                    <p class="text-gray-500 mt-2">Probá seleccionando otra categoría o explorá el mapa para encontrar más opciones.</p>
                    <button data-reset-category type="button" class="mt-4 text-red-600 font-medium hover:underline">Ver todas las ofertas</button>
                </div>
            `;

            if (window.lucide) {
                window.lucide.createIcons();
            }

            return;
        }

        container.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                ${offers.map((item) => `
                    <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 group flex flex-col">
                        <div class="relative h-48 overflow-hidden bg-gray-200">
                            <img src="${item.image_url}" alt="${item.business_name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1.5 rounded-md shadow-sm">
                                ${item.badge}
                            </div>
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-gray-800 text-xs font-medium px-2 py-1 rounded-md shadow-sm flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3"></i> ${item.location}
                            </div>
                        </div>
                        <div class="p-5 flex-grow flex flex-col">
                            <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">${item.category}</div>
                            <h3 class="font-bold text-gray-900 text-lg mb-2">${item.business_name}</h3>
                            <p class="text-red-600 font-bold text-xl mb-3 leading-tight">${item.title}</p>
                            <p class="text-gray-500 text-sm mb-4">${item.description}</p>
                            <div class="mt-auto">
                                <div class="flex items-center gap-2 mb-4 bg-red-50 p-2.5 rounded-lg border border-red-100">
                                    <i data-lucide="clock" class="text-red-500 w-[18px] h-[18px]"></i>
                                    <span class="text-sm font-medium text-gray-700">Termina en:</span>
                                    <span data-expiration="${item.expires_at}" class="text-red-600 font-bold ml-auto tabular-nums">${formatRemainingTime(item.expires_at)}</span>
                                </div>
                                <a href="${buildWhatsAppLink(item)}" target="_blank" class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm">
                                    <i data-lucide="message-circle" class="w-5 h-5"></i>
                                    Quiero esta oferta
                                </a>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        if (window.lucide) {
            window.lucide.createIcons();
        }
    };

    const setupOffersListing = () => {
        const offers = Array.isArray(pageData.offers) ? pageData.offers : [];
        const categories = Array.isArray(pageData.categories) ? pageData.categories : [];
        const filtersContainer = document.getElementById('category-filters');
        const offersContainer = document.getElementById('offers-container');
        const categorySelect = document.getElementById('inputCategory');

        if (!filtersContainer || !offersContainer || offers.length === 0) {
            return;
        }

        let activeCategory = 'Todas';

        const renderCategories = () => {
            filtersContainer.innerHTML = categories.map((category) => `
                <button
                    type="button"
                    data-category="${category}"
                    class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition-colors ${activeCategory === category ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'}"
                >
                    ${category}
                </button>
            `).join('');

            if (categorySelect && categorySelect.children.length === 0) {
                categorySelect.innerHTML = categories
                    .filter((category) => category !== 'Todas')
                    .map((category) => `<option value="${category}">${category}</option>`)
                    .join('');
            }
        };

        const renderOffers = () => {
            const filteredOffers = activeCategory === 'Todas'
                ? offers
                : offers.filter((offer) => offer.category === activeCategory);

            renderOfferCards(filteredOffers, offersContainer);
        };

        filtersContainer.addEventListener('click', (event) => {
            const button = event.target.closest('[data-category]');
            if (!button) {
                return;
            }

            activeCategory = button.dataset.category || 'Todas';
            renderCategories();
            renderOffers();
        });

        offersContainer.addEventListener('click', (event) => {
            const resetButton = event.target.closest('[data-reset-category]');
            if (!resetButton) {
                return;
            }

            activeCategory = 'Todas';
            renderCategories();
            renderOffers();
        });

        renderCategories();
        renderOffers();
    };

    const setupOfferForm = () => {
        const inputOffer = document.getElementById('inputOffer');
        const charCount = document.getElementById('charCount');
        const inputImage = document.getElementById('inputImage');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const imagePlaceholder = document.getElementById('imagePlaceholder');

        if (inputOffer && charCount) {
            inputOffer.addEventListener('input', (event) => {
                charCount.textContent = `${event.target.value.length}/40`;
            });
        }

        if (inputImage && imagePreviewContainer && imagePreview && imagePlaceholder) {
            inputImage.addEventListener('change', (event) => {
                const [file] = event.target.files || [];
                if (!file) {
                    imagePreviewContainer.classList.add('hidden');
                    imagePlaceholder.classList.remove('hidden');
                    imagePreview.src = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (loadEvent) => {
                    imagePreview.src = String(loadEvent.target?.result || '');
                    imagePreviewContainer.classList.remove('hidden');
                    imagePlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
        }
    };


    const setupHomeMapPreview = () => {
        const previewContainer = document.getElementById('home-map-preview');
        const mapOffers = Array.isArray(pageData.mapOffers) ? pageData.mapOffers.slice(0, 3) : [];

        if (!previewContainer) {
            return;
        }

        if (mapOffers.length === 0) {
            previewContainer.innerHTML = `
                <div class="h-full flex items-center justify-center text-center px-6 bg-gradient-to-br from-gray-100 to-gray-200">
                    <div>
                        <i data-lucide="map-pinned" class="w-10 h-10 text-red-500 mx-auto mb-4"></i>
                        <p class="text-lg font-bold text-gray-800">Pronto vas a ver puntos activos en esta zona.</p>
                        <p class="text-gray-500 mt-2">Todavía no hay coordenadas suficientes para construir la vista previa.</p>
                    </div>
                </div>
            `;

            if (window.lucide) {
                window.lucide.createIcons();
            }

            return;
        }

        previewContainer.innerHTML = `
            <div class="h-full bg-gradient-to-br from-slate-100 via-white to-red-50 p-6 grid content-center gap-4 md:grid-cols-3">
                ${mapOffers.map((offer) => `
                    <article class="bg-white/90 border border-white shadow-md rounded-2xl p-4 text-left">
                        <div class="flex items-center gap-2 text-xs uppercase tracking-[0.22em] text-red-500 font-semibold mb-3">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                            Punto activo
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">${offer.business_name}</h3>
                        <p class="text-red-600 font-semibold mb-2">${offer.title}</p>
                        <p class="text-sm text-gray-500">${offer.location}</p>
                    </article>
                `).join('')}
            </div>
        `;

        if (window.lucide) {
            window.lucide.createIcons();
        }
    };

    const updateCountdowns = () => {
        document.querySelectorAll('[data-expiration]').forEach((node) => {
            node.textContent = formatRemainingTime(node.getAttribute('data-expiration') || '');
        });
    };

    setupOffersListing();
    setupOfferForm();
    setupHomeMapPreview();
    updateCountdowns();
    window.setInterval(updateCountdowns, 1000);
})();
