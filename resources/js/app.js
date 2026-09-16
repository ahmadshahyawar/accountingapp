import './bootstrap';
import Alpine from 'alpinejs';
import JsBarcode from 'jsbarcode';
import Choices from 'choices.js';

window.Alpine = Alpine;
// Bundled (not CDN) so the item form's barcode still renders fully offline —
// same reasoning as self-hosting the Vazirmatn font.
window.JsBarcode = JsBarcode;
Alpine.start();

// Old-app-style searchable dropdown for every <select data-searchable> — the
// real app's combo boxes let you type to filter instead of scrolling a long
// list of accounts/items/persons. Applied on load and re-applied after any
// Alpine-driven DOM swap (x-init on a wrapping element can call this too).
window.initSearchableSelect = function (el) {
    if (!el || el.tagName !== 'SELECT' || el.dataset.choicesInit) return;
    el.dataset.choicesInit = '1';
    try {
        new Choices(el, {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: '',
            position: 'auto',
            searchPlaceholderValue: 'جستجو...',
            noResultsText: 'موردی یافت نشد',
            noChoicesText: 'موردی یافت نشد',
            removeItemButton: false,
            allowHTML: false,
        });
    } catch (e) {
        console.error('Searchable dropdown failed to init:', e);
    }
};
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select[data-searchable]').forEach(window.initSearchableSelect);
});

// Old-app-style quick filter above a grid: typing filters rows by their
// visible text, no page reload (the real app's list screens filter live too).
document.addEventListener('input', (e) => {
    const input = e.target.closest('[data-table-filter]');
    if (!input) return;
    const table = document.getElementById(input.dataset.tableFilter);
    if (!table) return;
    const q = input.value.trim().toLowerCase();
    table.querySelectorAll('tbody tr[data-row]').forEach((row) => {
        row.style.display = !q || row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// Old-app-style grid toolbar: click a row to select it (highlight), which
// enables the ویرایش/حذف buttons in the bottom toolbar to act on that row.
// Mirrors the real app's single-selection list + action-bar pattern instead
// of per-row inline links.
document.addEventListener('click', (e) => {
    const row = e.target.closest('tr[data-row]');
    const table = row?.closest('table');
    if (!table) return;
    if (e.target.closest('a, button, form')) return; // let inner controls behave normally

    table.querySelectorAll('tr.is-selected').forEach((r) => r.classList.remove('is-selected'));
    row.classList.add('is-selected');

    const toolbar = document.querySelector('[data-grid-toolbar]');
    if (!toolbar) return;
    const editBtn = toolbar.querySelector('[data-role="edit"]');
    const deleteBtn = toolbar.querySelector('[data-role="delete"]');
    if (editBtn) {
        if (row.dataset.editUrl) { editBtn.disabled = false; editBtn.dataset.href = row.dataset.editUrl; }
        else { editBtn.disabled = true; delete editBtn.dataset.href; }
    }
    if (deleteBtn) {
        if (row.dataset.deleteForm) { deleteBtn.disabled = false; deleteBtn.dataset.formId = row.dataset.deleteForm; }
        else { deleteBtn.disabled = true; delete deleteBtn.dataset.formId; }
    }
});
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-role="edit"]');
    if (!btn || btn.disabled || !btn.dataset.href) return;
    window.location.href = btn.dataset.href;
});
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-role="delete"]');
    if (!btn || btn.disabled || !btn.dataset.formId) return;
    if (!confirm(btn.dataset.confirm || 'حذف شود؟')) return;
    document.getElementById(btn.dataset.formId)?.requestSubmit();
});
