import './bootstrap';
import Alpine from 'alpinejs';
import JsBarcode from 'jsbarcode';

window.Alpine = Alpine;
// Bundled (not CDN) so the item form's barcode still renders fully offline —
// same reasoning as self-hosting the Vazirmatn font.
window.JsBarcode = JsBarcode;
Alpine.start();
