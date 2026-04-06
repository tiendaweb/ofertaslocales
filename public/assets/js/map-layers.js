/**
 * Map Layers Control
 * Handles toggling between Offers and Businesses layers on the map
 */

(function () {
  'use strict';

  const MapLayers = {
    /**
     * Initialize map layers control
     * @param {L.Map} map - Leaflet map instance
     * @param {Object} options - Configuration options
     */
    init(map, options = {}) {
      const pageDataElement = document.getElementById('page-data');
      const pageData = pageDataElement ? JSON.parse(pageDataElement.textContent || '{}') : {};

      const offers = Array.isArray(pageData.mapOffers) ? pageData.mapOffers : [];
      const businesses = Array.isArray(pageData.mapBusinesses) ? pageData.mapBusinesses : [];
      const visibleLayers = pageData.layers || ['offers', 'businesses'];

      // Retrieve saved layer preferences from localStorage
      const savedLayers = localStorage.getItem('mapLayers');
      if (savedLayers) {
        try {
          Object.assign(visibleLayers, JSON.parse(savedLayers));
        } catch {
          // Fallback to defaults if corrupted
        }
      }

      this.createLayersControl(map, visibleLayers);
      this.initializeLayerVisibility(visibleLayers, offers, businesses);
      this.setupLayerToggleButtons(visibleLayers);
    },

    /**
     * Create a custom Leaflet control for layer toggles
     * @param {L.Map} map - Leaflet map instance
     * @param {Array} visibleLayers - Current visible layers
     */
    createLayersControl(map, visibleLayers) {
      const LayersControl = window.L.Control.extend({
        onAdd() {
          const container = window.L.DomUtil.create('div', 'leaflet-bar leaflet-control map-layers-control');
          container.style.backgroundColor = '#ffffff';
          container.style.borderRadius = '8px';
          container.style.padding = '4px';
          container.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';

          const offersBtn = this._createButton('offersLayerBtn', 'Ofertas', '📍', container);
          const businessesBtn = this._createButton('businessesLayerBtn', 'Negocios', '🏪', container);

          this._setupButtonStates(offersBtn, businessesBtn, visibleLayers);

          return container;
        },

        _createButton(id, title, emoji, container) {
          const btn = window.L.DomUtil.create('button', 'map-layer-toggle-btn', container);
          btn.id = id;
          btn.type = 'button';
          btn.title = title;
          btn.innerHTML = `<span>${emoji}</span><span class="ml-1 text-xs font-semibold">${title}</span>`;
          btn.style.cssText = `
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            margin: 2px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            transition: all 0.2s;
          `;

          btn.addEventListener('mouseover', () => {
            btn.style.backgroundColor = '#f9fafb';
            btn.style.borderColor = '#d1d5db';
          });

          btn.addEventListener('mouseout', () => {
            if (!btn.classList.contains('active')) {
              btn.style.backgroundColor = 'white';
              btn.style.borderColor = '#e5e7eb';
            }
          });

          return btn;
        },

        _setupButtonStates(offersBtn, businessesBtn, visibleLayers) {
          const updateButtonState = (btn, isActive) => {
            if (isActive) {
              btn.classList.add('active');
              btn.style.backgroundColor = '#fee2e2';
              btn.style.borderColor = '#dc2626';
              btn.style.color = '#991b1b';
            } else {
              btn.classList.remove('active');
              btn.style.backgroundColor = 'white';
              btn.style.borderColor = '#e5e7eb';
              btn.style.color = '#374151';
            }
          };

          // Initial states
          updateButtonState(offersBtn, visibleLayers.includes('offers'));
          updateButtonState(businessesBtn, visibleLayers.includes('businesses'));

          // Toggle handlers
          offersBtn.addEventListener('click', () => {
            const isActive = offersBtn.classList.contains('active');
            if (isActive) {
              visibleLayers.splice(visibleLayers.indexOf('offers'), 1);
            } else {
              visibleLayers.push('offers');
            }
            updateButtonState(offersBtn, !isActive);
            localStorage.setItem('mapLayers', JSON.stringify(visibleLayers));
            this._notifyLayerChange('offers', !isActive);
          });

          businessesBtn.addEventListener('click', () => {
            const isActive = businessesBtn.classList.contains('active');
            if (isActive) {
              visibleLayers.splice(visibleLayers.indexOf('businesses'), 1);
            } else {
              visibleLayers.push('businesses');
            }
            updateButtonState(businessesBtn, !isActive);
            localStorage.setItem('mapLayers', JSON.stringify(visibleLayers));
            this._notifyLayerChange('businesses', !isActive);
          });
        },

        _notifyLayerChange(layer, isVisible) {
          // Dispatch custom event for layer visibility changes
          window.dispatchEvent(
            new CustomEvent('mapLayerToggle', {
              detail: { layer, isVisible },
            })
          );
        },
      });

      map.addControl(new LayersControl({ position: 'topleft' }));
    },

    /**
     * Initialize visibility of layers based on stored preferences
     * @param {Array} visibleLayers - Visible layers
     * @param {Array} offers - Offers data
     * @param {Array} businesses - Businesses data
     */
    initializeLayerVisibility(visibleLayers, offers, businesses) {
      // This would be coordinated with the main map script to show/hide markers
      // For now, we dispatch an event that the main script can listen to
      window.dispatchEvent(
        new CustomEvent('mapLayersReady', {
          detail: {
            visibleLayers,
            offersCount: offers.length,
            businessesCount: businesses.length,
          },
        })
      );
    },

    /**
     * Setup layer toggle button handlers
     * @param {Array} visibleLayers - Visible layers
     */
    setupLayerToggleButtons(visibleLayers) {
      window.addEventListener('mapLayerToggle', (event) => {
        const { layer, isVisible } = event.detail;
        console.log(`Layer "${layer}" toggled: ${isVisible ? 'visible' : 'hidden'}`);
        // Additional logic here if needed
      });
    },
  };

  // Export globally
  window.MapLayers = MapLayers;

  // Initialize when map is ready
  window.addEventListener('load', () => {
    if (window.L && window.L.map) {
      // Delay to ensure main map script has initialized
      setTimeout(() => {
        const mapContainer = document.getElementById('offers-map');
        if (mapContainer && mapContainer._leaflet_map) {
          MapLayers.init(mapContainer._leaflet_map);
        }
      }, 500);
    }
  });
})();
