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
        const message = encodeURIComponent(`Hola! Vi su oferta de '${offer.title}' en OfertasLocales. Sigue disponible?`);
        const baseUrl = offer.whatsapp_url || '';

        return `${baseUrl}?text=${message}`;
    };

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll('\'', '&#039;');

    window.PublicPages = {
        pageData,
        formatRemainingTime,
        buildWhatsAppLink,
        escapeHtml,
    };

    const modules = [
        window.PublicPagesCatalog,
        window.PublicPagesMapPreview,
        window.PublicPagesInline,
        window.PublicPagesHome,
    ];

    modules.forEach((module) => {
        if (module && typeof module.init === 'function') {
            module.init(window.PublicPages);
        }
    });
})();
