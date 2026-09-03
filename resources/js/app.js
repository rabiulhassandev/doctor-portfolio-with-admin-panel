/**
 * Front-end behaviour for the public site.
 *
 * Everything interactive here is Alpine.js: the mobile menu, the gallery
 * lightbox and the testimonial slider are all declared inline in the Blade
 * views with x-data. This file only wires up Alpine itself and adds the one
 * piece of shared behaviour that would be repetitive to write per element.
 */

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);

window.Alpine = Alpine;
Alpine.start();

/**
 * Scroll-reveal.
 *
 * Add `data-reveal` to any element and it fades and slides into place the first
 * time it scrolls into view. Add a delay in milliseconds to stagger a group:
 *
 *     <div data-reveal></div>
 *     <div data-reveal="150"></div>
 *
 * The matching CSS lives in resources/css/app.css.
 *
 * This is deliberately plain DOM code rather than an Alpine directive. Alpine
 * only walks trees rooted at an `x-data` element, and almost every element we
 * want to reveal sits outside a component — as a directive it would leave most
 * of the site permanently at `opacity: 0`.
 */
const SELECTOR = '[data-reveal]';

const reveal = (el, animate = true) => {
    // Something already off-screen has nothing to animate into, so skip the
    // delay: staggering content the visitor has scrolled past only means they
    // find it still fading in when they scroll back up.
    el.style.transitionDelay = animate ? `${parseInt(el.dataset.reveal, 10) || 0}ms` : '0ms';
    el.classList.add('is-revealed');
};

// True once the element has gone past the top of the window — it can no longer
// scroll into view from below, so it must be shown now or it never will be.
const isAbove = (el) => el.getBoundingClientRect().bottom <= 0;

// IntersectionObserver is in every browser we support, but if it is ever
// missing, show the content rather than leaving it invisible.
const observer =
    typeof IntersectionObserver === 'undefined'
        ? null
        : new IntersectionObserver(
              (entries) => {
                  entries.forEach((entry) => {
                      // `threshold: 0` matters: with a fractional threshold a
                      // section taller than the window has to be scrolled a
                      // long way before enough of it counts as visible, and a
                      // section more than ten windows tall never reveals at all.
                      if (entry.isIntersecting) {
                          reveal(entry.target);
                      } else if (entry.boundingClientRect.bottom <= 0) {
                          reveal(entry.target, false);
                      } else {
                          return;
                      }

                      observer.unobserve(entry.target);
                  });
              },
              // The margin is positive on the bottom, which grows the root box
              // *past* the fold: an element starts revealing shortly before it
              // scrolls into view. With a negative margin — waiting until the
              // element is already 10% up the screen — anyone scrolling quickly
              // arrives at a section that is still fading in, which reads as a
              // page that has not loaded rather than as an animation.
              { rootMargin: '0px 0px 15% 0px', threshold: 0 }
          );

const watch = (el) => {
    if (! observer) return reveal(el, false);

    // Loading straight into the middle of a page — an `#appointment` link, a
    // reload where the browser restores the scroll position, a back
    // navigation — leaves everything above the landing point outside the
    // observer's reach forever. Show those immediately instead.
    if (isAbove(el)) return reveal(el, false);

    observer.observe(el);
};

document.querySelectorAll(SELECTOR).forEach(watch);

// Alpine renders parts of the page after we have run — the gallery grid and the
// testimonial slider both swap their contents. Pick up anything added later.
new MutationObserver((mutations) => {
    mutations.forEach(({ addedNodes }) => {
        addedNodes.forEach((node) => {
            if (node.nodeType !== Node.ELEMENT_NODE) return;

            if (node.matches(SELECTOR)) watch(node);
            node.querySelectorAll(SELECTOR).forEach(watch);
        });
    });
}).observe(document.body, { childList: true, subtree: true });
