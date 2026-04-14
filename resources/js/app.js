import './bootstrap';

import Alpine from 'alpinejs';

/**
 * Fotogalerie alba: náhledy v mřížce + lightbox (klik, šipky, Escape).
 *
 * @param {Array<{ large: string, full: string, thumb: string, alt: string, caption: string }>} photos
 */
window.albumGallery = function albumGallery(photos) {
    return {
        photos,
        open: false,
        i: 0,
        openAt(index) {
            this.i = Number(index);
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.open = false;
            document.body.style.overflow = '';
        },
        next() {
            if (this.photos.length === 0) {
                return;
            }
            this.i = (this.i + 1) % this.photos.length;
        },
        prev() {
            if (this.photos.length === 0) {
                return;
            }
            this.i = (this.i - 1 + this.photos.length) % this.photos.length;
        },
        handleKey(e) {
            if (!this.open) {
                return;
            }
            if (e.key === 'Escape') {
                this.close();
            }
            if (e.key === 'ArrowRight') {
                this.next();
            }
            if (e.key === 'ArrowLeft') {
                this.prev();
            }
        },
    };
};

window.Alpine = Alpine;

Alpine.start();
