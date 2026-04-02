(function () {
    window.PublicPagesHome = {
        init() {
            const homeRoot = document.getElementById('home-map-preview');
            if (!homeRoot) {
                return;
            }

            homeRoot.setAttribute('data-home-module-ready', 'true');
        },
    };
})();
