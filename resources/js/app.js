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
        el._choicesInstance = new Choices(el, {
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

// Re-syncs a Choices.js dropdown from its underlying native <select> —
// needed after Alpine changes the select's value or option list from
// outside the widget itself (e.g. the جستجوی حساب ها modal picking a row,
// or creating a new person inline and pushing it into the options).
//
// Choices' own clearStore()+setChoices() API was tried first and turned out
// to actively corrupt things: for a select-one it rewrites the underlying
// <select>'s own <option> elements from a snapshot, which orphans Alpine's
// x-for tracking of those same elements (Alpine can no longer find/patch
// the nodes it thinks it owns, so later reactive updates to the options
// array silently stop reaching the DOM). Destroying and fully
// reinitializing Choices avoids that: destroy() hands the untouched native
// <select> — with whatever Alpine has rendered into it — back to Alpine
// intact, then a fresh Choices instance is built as a pure view on top.
window.refreshChoices = function (el) {
    if (!el) return;
    if (el._choicesInstance) {
        el._choicesInstance.destroy();
        delete el._choicesInstance;
    }
    delete el.dataset.choicesInit;
    window.initSearchableSelect(el);
};

// Sets a native <select>'s value and refreshes its Choices.js widget to
// match, retrying briefly if the target <option> hasn't been rendered yet
// (e.g. Alpine's x-for is still patching in a just-created record's option
// on the same tick this runs).
window.setNativeSelectValue = function (el, value, attemptsLeft = 10) {
    if (!el) return;
    const strValue = value === null || value === undefined ? '' : String(value);
    const hasOption = strValue === '' || Array.from(el.options).some((o) => o.value === strValue);
    if (!hasOption && attemptsLeft > 0) {
        requestAnimationFrame(() => window.setNativeSelectValue(el, value, attemptsLeft - 1));
        return;
    }
    el.value = strValue;
    window.refreshChoices(el);
};

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
