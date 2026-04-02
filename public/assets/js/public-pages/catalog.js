(function () {
    const setupOffersListing = (pageData) => {
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

        const cardNodes = Array.from(offersContainer.querySelectorAll('[data-offer-card]'));
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
            let visible = 0;
            cardNodes.forEach((card) => {
                const cardCategory = card.getAttribute('data-category') || '';
                const matches = activeCategory === 'Todas' || cardCategory === activeCategory;
                card.classList.toggle('hidden', !matches);
                if (matches) {
                    visible += 1;
                }
            });

            if (offersCountSummary) {
                offersCountSummary.textContent = activeCategory === 'Todas'
                    ? `Mostrando ${visible} oferta${visible === 1 ? '' : 's'} activa${visible === 1 ? '' : 's'}.`
                    : `Mostrando ${visible} oferta${visible === 1 ? '' : 's'} en ${activeCategory}.`;
            }
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

        renderCategories();
        renderOffers();

        if (window.lucide) {
            window.lucide.createIcons();
        }
    };

    const updateCountdowns = (formatRemainingTime) => {
        document.querySelectorAll('[data-countdown]').forEach((node) => {
            const target = node.querySelector('span');
            const expiresAt = node.getAttribute('data-expiration') || '';
            if (target) {
                target.textContent = `Restan ${formatRemainingTime(expiresAt)}`;
            }
        });
    };

    window.PublicPagesCatalog = {
        init(publicPages) {
            setupOffersListing(publicPages.pageData);
            updateCountdowns(publicPages.formatRemainingTime);
            window.setInterval(() => updateCountdowns(publicPages.formatRemainingTime), 1000);
        },
    };
})();
