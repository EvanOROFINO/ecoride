/**
 * Recherche live des covoiturages avec fetch().
 *
 * Sur la page d'accueil et la page /covoiturages, propose une recherche
 * asynchrone : dès que l'utilisateur a renseigné départ + arrivée + date,
 * on interroge l'API /api/covoiturages et on affiche les premiers résultats
 * en aperçu sans recharger la page.
 *
 * Démontre l'usage de fetch + async/await + AbortController (annulation de
 * requête si l'utilisateur retape avant la fin) + debounce.
 */

(function () {
  'use strict';

  const form = document.querySelector('form.search-bar, form[data-search-live]');
  if (!form) return;

  // Conteneur d'aperçu (créé si absent)
  let preview = document.getElementById('search-live-preview');
  if (!preview) {
    preview = document.createElement('div');
    preview.id = 'search-live-preview';
    preview.className = 'search-live-preview';
    preview.setAttribute('aria-live', 'polite');
    form.parentNode.insertBefore(preview, form.nextSibling);
  }

  let debounceTimer = null;
  let currentController = null;

  /**
   * Lance la recherche asynchrone. Utilise AbortController pour annuler
   * les requêtes obsolètes si l'utilisateur retape rapidement.
   */
  async function searchLive() {
    const depart = form.querySelector('[name="depart"]')?.value.trim();
    const arrivee = form.querySelector('[name="arrivee"]')?.value.trim();
    const date = form.querySelector('[name="date"]')?.value;

    if (!depart || !arrivee || !date) {
      preview.innerHTML = '';
      preview.style.display = 'none';
      return;
    }

    // Annule la requête précédente si en cours
    if (currentController) currentController.abort();
    currentController = new AbortController();

    preview.innerHTML = '<p class="search-loading">Recherche en cours…</p>';
    preview.style.display = 'block';

    try {
      const params = new URLSearchParams({ depart, arrivee, date, limit: 3 });
      const response = await fetch(`/api/covoiturages?${params.toString()}`, {
        signal: currentController.signal,
        headers: { 'Accept': 'application/json' },
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const data = await response.json();
      renderPreview(data);
    } catch (err) {
      if (err.name === 'AbortError') return; // requête annulée volontairement
      preview.innerHTML = `<p class="search-error">Erreur : ${err.message}</p>`;
    }
  }

  /**
   * Affiche un aperçu HTML des 3 premiers covoiturages trouvés.
   */
  function renderPreview(data) {
    if (!data.results || data.results.length === 0) {
      preview.innerHTML = '<p class="search-empty">Aucun trajet trouvé. Essayez une autre date.</p>';
      return;
    }
    const items = data.results.slice(0, 3).map(c => `
      <a href="/covoiturages/${c.id}" class="search-result">
        <strong>${escapeHtml(c.chauffeur_pseudo)}</strong>
        — ${escapeHtml(c.heure_depart)} → ${escapeHtml(c.heure_arrivee)}
        ${c.energie === 'electrique' ? '<span class="badge-eco">🌿 Éco</span>' : ''}
        <span class="search-price">${Math.round(c.prix_personne)} cr.</span>
      </a>
    `).join('');
    preview.innerHTML = `
      <div class="search-results-header">
        ${data.total} trajet(s) trouvé(s) — aperçu :
      </div>
      ${items}
      <a href="/covoiturages?depart=${encodeURIComponent(data.depart)}&arrivee=${encodeURIComponent(data.arrivee)}&date=${data.date}" class="search-see-all">
        Voir tous les résultats →
      </a>
    `;
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
  }

  // Debounce de 350 ms pour éviter de spammer l'API
  ['input', 'change'].forEach(evt => {
    form.querySelectorAll('input').forEach(input => {
      input.addEventListener(evt, () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(searchLive, 350);
      });
    });
  });
})();
