// app.js
import { PanierSecurise } from './js/panier/panier.js';
import './js/header.js';
import './js/admin/reservation_limits.js';
import './js/admin/reservation.js';
import './js/pizza/pizza.js';

import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import flatpickr from 'flatpickr';

console.log('✅ app.js est chargé !');

document.addEventListener('DOMContentLoaded', () => {
    // Vérifie si on est sur la page panier
    const jsVars = document.getElementById('js-variables');
    if (!jsVars) return; 

    // Récupération des données depuis le DOM
    const urlCreateSession = jsVars.dataset.urlCreateSession;
    const stripePublicKey = jsVars.dataset.stripePublicKey;

    // Initialisation sécurisée du panier
    try {
        new PanierSecurise(urlCreateSession, stripePublicKey);
        console.log('🛒 PanierSecurise initialisé avec succès.');
    } catch (err) {
        console.error('❌ Erreur d’initialisation du panier :', err);
    }
});
