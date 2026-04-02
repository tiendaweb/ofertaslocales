(function () {
    const pageDataElement = document.getElementById('page-data');
    const pageData = pageDataElement ? JSON.parse(pageDataElement.textContent || '{}') : {};

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

    const buildWhatsAppLink = (offer) => {
        const message = encodeURIComponent(
            `Hola! Vi su oferta de '${offer.title}' en OfertasLocales. Sigue disponible?`
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 xl:gap-7 2xl:gap-8 items-stretch">
                ${offers.map((item) => `
                    <article class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 group flex flex-col">
                        <div class="relative h-48 overflow-hidden bg-gray-200">
                            <a href="/mapa?oferta=${item.id}" class="block h-full w-full" aria-label="Ver oferta en el mapa">
                                <img src="${item.image_url}" alt="${item.business_name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>
                            <div class="absolute top-3 left-3 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1.5 rounded-md shadow-sm">
                                ${item.badge}
                            </div>
                        </div>
                        <div class="p-5 flex-grow flex flex-col">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">${item.category}</div>
                                <a href="/ofertas?negocio=${item.user_id}" class="text-xs font-semibold text-red-600 hover:text-red-700 transition">${item.business_name}</a>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg mb-2 leading-tight">${item.title}</h3>
                            <p class="text-gray-500 text-sm mb-4">${item.description}</p>
                            <div class="mt-auto space-y-3">
                                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 text-sm text-gray-600">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i data-lucide="store" class="w-4 h-4 text-gray-500"></i>
                                        <span class="font-semibold text-gray-900">${item.business_name}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="text-red-500 w-[18px] h-[18px]"></i>
                                        <span class="font-medium text-gray-700">Termina en:</span>
                                        <span data-expiration="${item.expires_at}" class="text-red-600 font-bold ml-auto tabular-nums">${formatRemainingTime(item.expires_at)}</span>
                                    </div>
                                    <div class="mt-2 flex items-start gap-2">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                                        <span class="text-xs text-gray-600 leading-5 line-clamp-2">${item.location}</span>
                                    </div>
                                </div>
                                <a href="${buildWhatsAppLink(item)}" target="_blank" rel="noreferrer" class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm">
                                    <i data-lucide="message-circle" class="w-5 h-5"></i>
                                    Quiero esta oferta
                                </a>
                                <a href="/mapa?oferta=${item.id}" class="w-full border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm">
                                    <i data-lucide="map" class="w-5 h-5"></i>
                                    Ver en el mapa
                                </a>
                            </div>
                        </div>
                    </article>
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
        const offersCountSummary = document.getElementById('offers-count-summary');
        const categorySelect = document.getElementById('inputCategory');

        if (categorySelect && categorySelect.children.length === 0) {
            categorySelect.innerHTML = categories
                .filter((category) => category !== 'Todas')
                .map((category) => `<option value="${category}">${category}</option>`)
                .join('');
        }

        if (!filtersContainer || !offersContainer) {
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
        };

        const renderOffers = () => {
            const filteredOffers = activeCategory === 'Todas'
                ? offers
                : offers.filter((offer) => offer.category === activeCategory);

            if (offersCountSummary) {
                offersCountSummary.textContent = activeCategory === 'Todas'
                    ? `Mostrando ${filteredOffers.length} oferta${filteredOffers.length === 1 ? '' : 's'} activa${filteredOffers.length === 1 ? '' : 's'}.`
                    : `Mostrando ${filteredOffers.length} oferta${filteredOffers.length === 1 ? '' : 's'} en ${activeCategory}.`;
            }

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

    const setupHomeMapPreview = () => {
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
        const mapOffers = Array.isArray(pageData.mapOffers) ? pageData.mapOffers.slice(0, 8) : [];

        if (!previewContainer) {
            return;
        }

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll('\'', '&#039;');

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
            modalLocation.textContent = offer.location || 'Ubicación no especificada';
            modalWhatsapp.href = buildWhatsAppLink(offer);
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

            centerMeButton?.setAttribute('disabled', 'disabled');
            nearbyButton?.setAttribute('disabled', 'disabled');
            setFeedback('No hay ofertas geolocalizadas para mostrar todavía.');
            return;
        }

        if (!window.L) {
            previewContainer.innerHTML = `
                <div class="h-full bg-gradient-to-br from-slate-100 via-white to-red-50 p-6 grid content-center gap-4 md:grid-cols-3">
                    ${mapOffers.slice(0, 3).map((offer) => `
                        <article class="bg-white/90 border border-white shadow-md rounded-2xl p-4 text-left">
                            <div class="flex items-center gap-2 text-xs uppercase tracking-[0.22em] text-red-500 font-semibold mb-3">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                Punto activo
                            </div>
                            <h3 class="font-bold text-gray-900 mb-1">${offer.business_name}</h3>
                            <p class="text-red-600 font-semibold mb-2">${offer.title}</p>
                            <p class="text-sm text-gray-500 mb-3">${offer.location}</p>
                            <a href="/mapa" class="text-sm font-semibold text-gray-800 hover:text-red-600 transition">Ver en mapa completo</a>
                        </article>
                    `).join('')}
                </div>
            `;

            if (window.lucide) {
                window.lucide.createIcons();
            }

            centerMeButton?.setAttribute('disabled', 'disabled');
            nearbyButton?.setAttribute('disabled', 'disabled');
            setFeedback('Vista previa estática activa: Leaflet no se pudo cargar.', true);
            return;
        }

        previewContainer.innerHTML = '';

        const validOffers = mapOffers.filter((offer) => Number.isFinite(Number(offer.lat)) && Number.isFinite(Number(offer.lon)));
        if (validOffers.length === 0) {
            setFeedback('No hay coordenadas válidas para construir el mapa.', true);
            return;
        }

        const first = validOffers[0];
        const map = window.L.map(previewContainer, {
            zoomControl: true,
            scrollWheelZoom: true,
            attributionControl: true,
        }).setView([Number(first.lat), Number(first.lon)], 13);

        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);
        const redMarkerIcon = window.L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41],
        });

        const bounds = [];
        const offerPoints = [];
        validOffers.forEach((offer) => {
            const lat = Number(offer.lat);
            const lon = Number(offer.lon);
            const marker = window.L.marker([lat, lon], { icon: redMarkerIcon }).addTo(map);
            marker.bindTooltip(`
                <div class="w-44 max-w-[11rem]">
                    <img src="${escapeHtml(offer.image_url)}" alt="${escapeHtml(offer.title)}" class="w-full h-20 object-cover rounded-lg mb-2">
                    <p class="text-xs uppercase tracking-wider text-gray-500 truncate">${escapeHtml(offer.category)}</p>
                    <p class="font-semibold text-gray-900 truncate">${escapeHtml(offer.business_name)}</p>
                    <p class="text-red-600 font-semibold text-sm line-clamp-2 leading-5 break-words">${escapeHtml(offer.title)}</p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-4 break-words">${escapeHtml(offer.location)}</p>
                </div>
            `, { direction: 'top', offset: [0, -10] });

            marker.on('click', () => {
                openModal(offer);
            });

            bounds.push([lat, lon]);
            offerPoints.push({ lat, lon, offer });
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [24, 24] });
        }

        const fitToNearby = (originLat, originLon, maxItems = 4) => {
            const rank = offerPoints
                .map((point) => {
                    const distance = Math.hypot(point.lat - originLat, point.lon - originLon);
                    return { ...point, distance };
                })
                .sort((a, b) => a.distance - b.distance)
                .slice(0, Math.max(1, maxItems));

            const nearBounds = rank.map((point) => [point.lat, point.lon]);
            nearBounds.push([originLat, originLon]);
            map.fitBounds(nearBounds, { padding: [32, 32], maxZoom: 15 });
            setFeedback(`Mostrando ${rank.length} ofertas cercanas a tu ubicación.`);
        };

        centerMeButton?.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setFeedback('Tu navegador no soporta geolocalización.', true);
                return;
            }

            setFeedback('Buscando tu ubicación...');
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                map.setView([lat, lon], 14, { animate: true });
                window.L.circleMarker([lat, lon], {
                    radius: 7,
                    color: '#dc2626',
                    weight: 3,
                    fillColor: '#ef4444',
                    fillOpacity: 0.35,
                }).addTo(map);
                setFeedback('Mapa centrado en tu ubicación actual.');
            }, () => {
                setFeedback('No pudimos acceder a tu ubicación. Revisa los permisos del navegador.', true);
            }, { enableHighAccuracy: true, timeout: 7000 });
        });

        nearbyButton?.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setFeedback('Tu navegador no soporta geolocalización.', true);
                return;
            }

            setFeedback('Calculando ofertas cercanas...');
            navigator.geolocation.getCurrentPosition((position) => {
                fitToNearby(position.coords.latitude, position.coords.longitude);
            }, () => {
                setFeedback('No fue posible obtener tu ubicación para filtrar cercanas.', true);
            }, { enableHighAccuracy: true, timeout: 7000 });
        });

        setFeedback('Mapa interactivo activo: podés hacer zoom y tocar un punto para ver la oferta.');
        window.setTimeout(() => map.invalidateSize(), 120);
    };


    const setupInlineEditMode = () => {
        const config = window.inlineEditConfig || {};
        if (!config.isAdmin) {
            return;
        }

        const toggleButton = document.querySelector('[data-inline-edit-toggle]');
        const panel = document.querySelector('[data-inline-edit-panel]');
        const feedbackNode = document.querySelector('[data-inline-edit-feedback]');
        const saveButton = document.querySelector('[data-inline-edit-save]');
        const cancelButton = document.querySelector('[data-inline-edit-cancel]');
        const editableNodes = Array.from(document.querySelectorAll('[data-editable-key]'));

        if (!toggleButton || !panel || editableNodes.length === 0) {
            return;
        }

        const editors = new Map();
        let isEditing = false;

        const setFeedback = (message, tone = 'neutral') => {
            if (!feedbackNode) {
                return;
            }

            feedbackNode.textContent = message;
            feedbackNode.classList.remove('text-indigo-600', 'text-emerald-700', 'text-rose-700');
            if (tone === 'success') {
                feedbackNode.classList.add('text-emerald-700');
                return;
            }

            if (tone === 'error') {
                feedbackNode.classList.add('text-rose-700');
                return;
            }

            feedbackNode.classList.add('text-indigo-600');
        };

        const buildEditor = (node) => {
            const key = node.dataset.editableKey || '';
            const attr = node.dataset.editableAttr || 'text';
            const type = node.dataset.editableType || 'text';
            const initialValue = attr === 'href' ? (node.getAttribute('href') || '') : (node.textContent || '').trim();
            const input = type === 'textarea' ? document.createElement('textarea') : document.createElement('input');

            if (type !== 'textarea') {
                input.setAttribute('type', type === 'url' ? 'url' : 'text');
            }

            input.value = initialValue;
            input.className = 'mt-2 w-full rounded-xl border border-indigo-200 bg-white/90 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/40';
            input.setAttribute('data-inline-editor-input', 'true');
            input.setAttribute('data-inline-editor-key', key);
            input.hidden = true;

            node.insertAdjacentElement('afterend', input);
            editors.set(key, {
                key,
                node,
                input,
                attr,
                originalValue: initialValue,
            });
        };

        const applyValue = (editor, value) => {
            if (editor.attr === 'href') {
                editor.node.setAttribute('href', value);
                return;
            }

            editor.node.textContent = value;
        };

        editableNodes.forEach(buildEditor);

        const setMode = (enabled) => {
            isEditing = enabled;
            panel.classList.toggle('hidden', !enabled);
            toggleButton.textContent = enabled ? 'Desactivar Modo Edición' : 'Activar Modo Edición';

            editors.forEach((editor) => {
                editor.input.hidden = !enabled;
                editor.node.classList.toggle('ring-2', enabled);
                editor.node.classList.toggle('ring-indigo-300', enabled);
                editor.node.classList.toggle('rounded-md', enabled);
                if (enabled) {
                    editor.input.value = editor.attr === 'href'
                        ? (editor.node.getAttribute('href') || '')
                        : (editor.node.textContent || '').trim();
                }
            });

            if (!enabled) {
                setFeedback('');
            } else {
                setFeedback('Editá los campos marcados y guardá para aplicar cambios.');
            }
        };

        const rollback = () => {
            editors.forEach((editor) => {
                applyValue(editor, editor.originalValue);
                editor.input.value = editor.originalValue;
            });
        };

        const saveChanges = async () => {
            const fields = {};

            editors.forEach((editor) => {
                const nextValue = editor.input.value.trim();
                applyValue(editor, nextValue);
                if (nextValue !== editor.originalValue) {
                    fields[editor.key] = nextValue;
                }
            });

            if (Object.keys(fields).length === 0) {
                setFeedback('No hay cambios para guardar.');
                return;
            }

            saveButton?.setAttribute('disabled', 'disabled');
            setFeedback('Guardando cambios...');

            try {
                const response = await fetch(config.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ fields }),
                    credentials: 'same-origin',
                });

                const payload = await response.json();
                if (!response.ok || !payload.ok) {
                    rollback();
                    setFeedback(payload.message || 'No se pudieron guardar los cambios.', 'error');
                    return;
                }

                Object.keys(fields).forEach((key) => {
                    const editor = editors.get(key);
                    if (editor) {
                        editor.originalValue = fields[key];
                    }
                });

                setFeedback(payload.message || 'Cambios guardados correctamente.', 'success');
                setMode(false);
            } catch (error) {
                rollback();
                setFeedback('Error de red al guardar. Se revirtieron los cambios visuales.', 'error');
            } finally {
                saveButton?.removeAttribute('disabled');
            }
        };

        toggleButton.addEventListener('click', () => {
            setMode(!isEditing);
        });

        saveButton?.addEventListener('click', () => {
            void saveChanges();
        });

        cancelButton?.addEventListener('click', () => {
            rollback();
            setMode(false);
        });
    };

    const updateCountdowns = () => {
        document.querySelectorAll('[data-expiration]').forEach((node) => {
            node.textContent = formatRemainingTime(node.getAttribute('data-expiration') || '');
        });
    };

    setupOffersListing();
    setupHomeMapPreview();
    setupInlineEditMode();
    updateCountdowns();
    window.setInterval(updateCountdowns, 1000);
})();
