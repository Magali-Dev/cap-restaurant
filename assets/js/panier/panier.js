
// PanierSecurise : gestion du panier côté client
// - stockage sécurisé dans localStorage
// - affichage dynamique et mise à jour du panier
// - modification des quantités, suppression d'articles
// - calcul des totaux et compteur
// - intégration Stripe pour le paiement

export class PanierSecurise {
    constructor(urlCreateSession, stripePublicKey) {
        this.urlCreateSession = urlCreateSession;
        this.stripePublicKey = stripePublicKey;
        this.panier = [];
        this.init();
    }

    // Initialisation
   
    init() {
        this.chargerPanier();
        this.afficherPanier();
        this.initValidationStripe(); 
    }
    // Chargement et sauvegarde
 
    chargerPanier() {
        try {
            const panierStorage = localStorage.getItem('panier');
            this.panier = panierStorage ? JSON.parse(panierStorage) : [];
            console.log('📦 Panier chargé:', this.panier.length, 'articles');
        } catch (err) {
            console.error('❌ Erreur chargement panier:', err);
            this.panier = [];
            localStorage.removeItem('panier');
        }
    }

    sauvegarderPanier() {
        try {
            localStorage.setItem('panier', JSON.stringify(this.panier));
        } catch (err) {
            console.error('❌ Erreur sauvegarde panier:', err);
        }
    }

   
    // Sélecteurs simplifiés

    $(sel) { return sel?.trim() ? document.querySelector(sel) : null; }
    $$(sel) { return sel?.trim() ? Array.from(document.querySelectorAll(sel)) : []; }

    
    // Affichage du panier
  
    afficherPanier() {
        const container = this.$('#panier-container');
        if (!container) return;

        if (this.panier.length === 0) {
            container.innerHTML = this.getHTMLPanierVide();
        } else {
            container.innerHTML = this.genererHTMLPanier();
            this.attacherEvenements();
        }

        this.mettreAJourCompteur();
    }

    getHTMLPanierVide() {
        return `
            <div class="empty-panier">
                <i class="fas fa-shopping-basket"></i>
                <h3>Votre panier est vide</h3>
                <p class="text-muted">Ajoutez des pizzas pour commencer !</p>
                <a href="{{ path('pizza_list') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-pizza-slice me-2"></i>Découvrir nos pizzas
                </a>
            </div>
        `;
    }

    genererHTMLPanier() {
        let totalGeneral = 0;
        let html = `<table class="panier-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Qté</th>
                    <th>Suppléments</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>`;

        this.panier.forEach((item, idx) => {
            const prix = this.parsePrix(item.prix);
            const qty = this.parseQuantite(item.qty);

            let totalItem = prix * qty;

            let supplText = 'Aucun';
            if (item.supplements?.length) {
                const totalSup = item.supplements.reduce(
                    (sum, sup) => sum + this.parsePrix(sup.prix) * this.parseQuantite(sup.qty),
                    0
                );
                totalItem += totalSup;

                const validSup = item.supplements.filter(s => s?.nom?.trim());
                if (validSup.length) {
                    supplText = validSup.map(s => `${s.nom} x${s.qty || 1}`).join(', ');
                }
            }

            totalGeneral += totalItem;

            html += `<tr>
                <td>${this.escapeHTML(item.nom || 'Produit')}</td>
                <td>${prix.toFixed(2)} €</td>
                <td>
                    <button class="qty-btn decrement" data-index="${idx}" ${qty <= 1 ? 'disabled' : ''}>-</button>
                    <span>${qty}</span>
                    <button class="qty-btn increment" data-index="${idx}">+</button>
                </td>
                <td>${this.escapeHTML(supplText)}</td>
                <td>${totalItem.toFixed(2)} €</td>
                <td><button class="remove-btn" data-index="${idx}">Supprimer</button></td>
            </tr>`;
        });

        html += `</tbody></table>
            <div class="total-container">
                <strong>Total : ${totalGeneral.toFixed(2)} €</strong>
            </div>`;

        return html;
    }

    // --------------------
    // Gestion des boutons
    // --------------------
    attacherEvenements() {
        this.$$('.increment').forEach(btn => btn.addEventListener('click', () => this.modifierQty(btn.dataset.index, +1)));
        this.$$('.decrement').forEach(btn => btn.addEventListener('click', () => this.modifierQty(btn.dataset.index, -1)));
        this.$$('.remove-btn').forEach(btn => btn.addEventListener('click', () => this.supprimerArticle(btn.dataset.index)));
    }

    modifierQty(idx, delta) {
        const index = this.parseIndex(idx);
        if (!this.estIndexValide(index)) return;
        const nouvelleQty = this.parseQuantite(this.panier[index].qty) + delta;
        if (nouvelleQty >= 1) {
            this.panier[index].qty = nouvelleQty;
            this.sauvegarderPanier();
            this.afficherPanier();
        }
    }

    supprimerArticle(idx) {
        const index = this.parseIndex(idx);
        if (!this.estIndexValide(index)) return;
        if (confirm('Supprimer cet article ?')) {
            this.panier.splice(index, 1);
            this.sauvegarderPanier();
            this.afficherPanier();
        }
    }

    // --------------------
    // Validation Stripe
    // --------------------
    initValidationStripe() {
        const btn = this.$('#valider-panier');
        if (!btn) return;

        btn.addEventListener('click', async e => {
            e.preventDefault();

            if (!this.panier.length) {
                alert('🛒 Panier vide !');
                return;
            }

            const total = this.calculerTotalGeneral();
            if (!confirm(`Payer ${total.toFixed(2)} € ?`)) return;

            if (typeof Stripe === 'undefined') {
                alert('Stripe.js non chargé');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Redirection...';

            try {
                const resp = await fetch(this.urlCreateSession, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ panier: JSON.stringify(this.panier) })
                });

                const data = await resp.json();

                if (data.sessionId) {
                    const stripe = Stripe(this.stripePublicKey);
                    await stripe.redirectToCheckout({ sessionId: data.sessionId });
                } else {
                    throw new Error(data.error || 'Erreur création session Stripe');
                }
            } catch (err) {
                console.error('❌ Erreur paiement:', err);
                alert('❌ Erreur paiement : ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Payer votre commande';
            }
        });
    }

    // --------------------
    // Méthodes utilitaires
    // --------------------
    parsePrix(p) { const n = parseFloat(p); return isNaN(n) || n < 0 ? 0 : n; }
    parseQuantite(q) { const n = parseInt(q); return isNaN(n) || n < 1 ? 1 : n; }
    parseIndex(i) { const n = parseInt(i); return isNaN(n) || n < 0 ? -1 : n; }
    estIndexValide(i) { return i >= 0 && i < this.panier.length; }
    escapeHTML(t) { const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

    calculerTotalGeneral() {
        return this.panier.reduce((total, item) => {
            const prixItem = this.parsePrix(item.prix) * this.parseQuantite(item.qty);
            const prixSupp = item.supplements ? item.supplements.reduce((s, sup) => s + this.parsePrix(sup.prix) * this.parseQuantite(sup.qty), 0) : 0;
            return total + prixItem + prixSupp;
        }, 0);
    }

    // --------------------
    // Compteur dans le header
    // --------------------
    mettreAJourCompteur() {
        const c = this.$('#cart-count');
        if (!c) return;

        const total = this.panier.reduce((s, item) => s + this.parseQuantite(item.qty), 0);
        c.textContent = total > 0 ? total : '';
        if (total > 0) {
            c.classList.add('bump');
            setTimeout(() => c.classList.remove('bump'), 200);
        }
    }
}
