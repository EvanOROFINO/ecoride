/**
 * Validation asynchrone du formulaire d'inscription.
 *
 * Vérifie en temps réel via fetch() si le pseudo et l'email sont disponibles,
 * et affiche un indicateur visuel sans recharger la page.
 *
 * Démontre l'usage de fetch + JSON + debounce + feedback UX progressif.
 */

(function () {
  'use strict';

  const form = document.querySelector('form[action="/register"]');
  if (!form) return;

  const pseudoInput = form.querySelector('input[name="pseudo"]');
  const emailInput  = form.querySelector('input[name="email"]');
  if (!pseudoInput && !emailInput) return;

  const debounceTimers = {};

  /**
   * Crée un indicateur visuel (à côté du label).
   */
  function ensureIndicator(input) {
    let span = input.parentNode.querySelector('.field-availability');
    if (!span) {
      span = document.createElement('span');
      span.className = 'field-availability';
      span.style.cssText = 'margin-left:0.5rem;font-size:0.85rem;font-weight:500';
      input.parentNode.querySelector('label')?.appendChild(span);
    }
    return span;
  }

  /**
   * Met à jour l'indicateur selon l'état (loading / available / taken / error).
   */
  function setStatus(input, state, message) {
    const span = ensureIndicator(input);
    const colors = {
      loading:   '#666',
      available: '#388E3C',
      taken:     '#C62828',
      error:     '#888',
    };
    const icons = { loading: '⏳', available: '✓', taken: '✗', error: '?' };
    span.style.color = colors[state] || '#666';
    span.textContent = `${icons[state] || ''} ${message}`;
  }

  /**
   * Interroge l'API pour vérifier la disponibilité.
   */
  async function checkAvailability(input, field) {
    const value = input.value.trim();
    if (!value || value.length < 3) {
      const span = input.parentNode.querySelector('.field-availability');
      if (span) span.textContent = '';
      return;
    }

    setStatus(input, 'loading', 'Vérification…');

    try {
      const response = await fetch(`/api/auth/check?${field}=${encodeURIComponent(value)}`, {
        headers: { 'Accept': 'application/json' },
      });
      if (!response.ok) throw new Error('HTTP ' + response.status);
      const data = await response.json();
      if (data.available) {
        setStatus(input, 'available', 'Disponible');
      } else {
        setStatus(input, 'taken', 'Déjà utilisé');
      }
    } catch (err) {
      setStatus(input, 'error', 'Vérification impossible');
    }
  }

  if (pseudoInput) {
    pseudoInput.addEventListener('input', () => {
      clearTimeout(debounceTimers.pseudo);
      debounceTimers.pseudo = setTimeout(() => checkAvailability(pseudoInput, 'pseudo'), 400);
    });
  }
  if (emailInput) {
    emailInput.addEventListener('input', () => {
      clearTimeout(debounceTimers.email);
      debounceTimers.email = setTimeout(() => checkAvailability(emailInput, 'email'), 500);
    });
  }
})();
