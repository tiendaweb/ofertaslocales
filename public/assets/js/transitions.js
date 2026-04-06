/**
 * Global Transitions System
 * Handles page transitions with Fade + Slide effects
 */

(function () {
  'use strict';

  const TransitionSystem = {
    /**
     * Initialize the transition system
     */
    init() {
      // Check for prefers-reduced-motion
      const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      if (!prefersReducedMotion) {
        // Enable transitions after page load
        document.body.classList.add('transitions-enabled');
        document.body.classList.add('page-transition');

        // Add page transition root class to main
        const main = document.querySelector('main');
        if (main) {
          main.classList.add('page-transition-root');
        }
      }

      // Handle navigation clicks for smooth transitions
      this.setupNavigationTransitions();

      // Handle back/forward button
      this.setupHistoryTransitions();

      // Initialize all modules
      this.initializeModules();
    },

    /**
     * Setup transitions for navigation links
     */
    setupNavigationTransitions() {
      document.addEventListener('click', (event) => {
        const link = event.target.closest('a');

        if (!link || !this.shouldTransition(link)) {
          return;
        }

        const href = link.getAttribute('href');

        // Skip if it's the current page or external link
        if (href === window.location.pathname || href.startsWith('http') || href.startsWith('#')) {
          return;
        }

        // Prevent default and do transition
        event.preventDefault();

        this.performTransition(href);
      });
    },

    /**
     * Check if a link should trigger transition
     */
    shouldTransition(link) {
      // Skip external links
      if (link.target === '_blank' || link.href.includes('#')) {
        return false;
      }

      // Skip if has download attribute
      if (link.hasAttribute('download')) {
        return false;
      }

      // Only transition same-origin URLs
      return new URL(link.href).origin === window.location.origin;
    },

    /**
     * Perform page transition
     */
    performTransition(url) {
      const main = document.querySelector('main');

      if (!main) {
        window.location.href = url;
        return;
      }

      // Add exit animation
      document.body.classList.add('page-exit');
      main.classList.remove('page-transition');

      // Wait for animation then navigate
      setTimeout(() => {
        window.location.href = url;
      }, 250); // Match the fadeSlideOutLeft animation duration
    },

    /**
     * Setup history transition (back/forward buttons)
     */
    setupHistoryTransitions() {
      window.addEventListener('pageshow', (event) => {
        const main = document.querySelector('main');
        if (main && event.persisted) {
          document.body.classList.remove('page-exit');
          main.classList.add('page-transition');
        }
      });

      window.addEventListener('pagehide', () => {
        const main = document.querySelector('main');
        if (main) {
          main.classList.remove('page-transition');
        }
      });
    },

    /**
     * Initialize staggered card animations
     */
    initializeModules() {
      // Add card-list-item class to cards for staggered animation
      const offerCards = document.querySelectorAll('[data-offer-card]');
      offerCards.forEach((card) => {
        card.classList.add('card-list-item');
      });

      // Initialize tab transitions
      this.setupTabTransitions();
    },

    /**
     * Setup transitions for tab content
     */
    setupTabTransitions() {
      const tabButtons = document.querySelectorAll('[role="tab"]');

      tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
          const tabId = button.getAttribute('aria-controls');
          const tabPanel = tabId ? document.getElementById(tabId) : null;

          if (tabPanel) {
            // Fade out old content
            const activePanel = button.parentElement
              ?.querySelector('[role="tabpanel"][aria-hidden="false"]');

            if (activePanel && activePanel !== tabPanel) {
              activePanel.classList.add('hidden');
            }

            // Fade in new content
            tabPanel.classList.remove('hidden');
            tabPanel.classList.add('tab-content');
          }
        });
      });
    },

    /**
     * Trigger staggered animation for a container
     */
    animateStaggered(selector) {
      const container = document.querySelector(selector);
      if (!container) return;

      container.classList.add('stagger-children');
    },

    /**
     * Manually trigger modal animation
     */
    showModal(modalElement) {
      if (!modalElement) return;

      modalElement.classList.remove('modal-exit');
      modalElement.classList.add('modal');

      // Ensure reflow for animation to trigger
      void modalElement.offsetWidth;
    },

    /**
     * Manually trigger modal exit animation
     */
    hideModal(modalElement) {
      if (!modalElement) return;

      modalElement.classList.add('modal-exit');

      setTimeout(() => {
        modalElement.classList.add('hidden');
        modalElement.classList.remove('modal', 'modal-exit');
      }, 200); // Match fadeScaleOut duration
    },
  };

  // Export globally
  window.TransitionSystem = TransitionSystem;

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      TransitionSystem.init();
    });
  } else {
    TransitionSystem.init();
  }
})();
