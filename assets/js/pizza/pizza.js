document.addEventListener('DOMContentLoaded', () => {

    function updateCartCount(animated = false) {
        const cartCount = document.getElementById('cart-count');
        if (!cartCount) return;

        const panier = JSON.parse(localStorage.getItem('panier')) || [];
        const total = panier.reduce((sum, item) => sum + item.qty, 0);

        cartCount.textContent = total > 0 ? total : '';

        if (animated) {
            cartCount.classList.add('bump');
            setTimeout(() => cartCount.classList.remove('bump'), 200);
        }
    }

    //  Boutons Ajouter au panier 
    document.querySelectorAll('.btn-ajouter-panier').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const nom = button.dataset.nom;
            const prix = parseFloat(button.dataset.prix);

            // Récupérer le panier
            let panier = JSON.parse(localStorage.getItem('panier')) || [];

            // Vérifier si l'item est déjà dans le panier
            const existing = panier.find(item => item.id == id);
            if (existing) {
                existing.qty += 1;
            } else {
                panier.push({
                    id: id,
                    nom: nom,
                    prix: prix,
                    qty: 1,
                    suppléments: []
                });
            }

            // Sauvegarder
            localStorage.setItem('panier', JSON.stringify(panier));

            // Mise à jour du badge
            updateCartCount(true);

            // Optionnel : alert ou toast
            alert(`🍕 ${nom} ajouté au panier !`);
        });
    });

    // Initialiser le badge
    updateCartCount();
});
