document.addEventListener('DOMContentLoaded', () => {
    //  Menu burger 
    const burger = document.getElementById('burgerMenu');
    const menu = document.getElementById('mobileMenu');
    const closeMenu = document.getElementById('closeMenu');
    const overlay = document.getElementById('menuOverlay');

    const openMenu = () => {
        menu.classList.add('active');
        overlay.classList.add('active');
        burger.classList.add('active');
        burger.setAttribute('aria-expanded', 'true');
    };
    const closeMenuFunc = () => {
        menu.classList.remove('active');
        overlay.classList.remove('active');
        burger.classList.remove('active');
        burger.setAttribute('aria-expanded', 'false');
    };
    if (burger && menu && overlay && closeMenu) {
        burger.addEventListener('click', openMenu);
        closeMenu.addEventListener('click', closeMenuFunc);
        overlay.addEventListener('click', closeMenuFunc);
    }

    //  scroll 
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', e => {
            const href = anchor.getAttribute('href');
            
           
            if (!href || href === '#' || href === '') {
                e.preventDefault();
                return;
            }
            
            const target = document.querySelector(href);
            if (!target) return;
            
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        });
    });

    //  Badge panier 
    window.updateCartCount = (animated = false) => {
        const cartCount = document.getElementById('cart-count');
        if (!cartCount) return;
        
        let panier = [];
        try {
            panier = JSON.parse(localStorage.getItem('panier')) || [];
        } catch (error) {
            console.error('Erreur lecture panier:', error);
            panier = [];
        }
        
        const total = panier.reduce((sum, item) => {
            if (!item || typeof item.qty === 'undefined') {
                return sum;
            }
            return sum + (parseInt(item.qty) || 0);
        }, 0);
        
        cartCount.textContent = total > 0 ? total : '';
        if (animated) {
            cartCount.classList.add('bump');
            setTimeout(() => cartCount.classList.remove('bump'), 200);
        }
    };
    updateCartCount();

    //  Mini panier 
    const cartLink = document.getElementById('cart-link');
    const miniCart = document.getElementById('mini-cart');
    const miniCartItems = document.getElementById('mini-cart-items');
    const miniCartTotal = document.getElementById('mini-cart-total');

    if (cartLink && miniCart) {
        cartLink.addEventListener('click', (e) => {
            e.preventDefault();
            miniCart.style.display = miniCart.style.display === 'block' ? 'none' : 'block';
            renderMiniCart();
        });
    }

    function renderMiniCart() {
        if (!miniCartItems || !miniCartTotal) return;
        
        let panier = [];
        try {
            panier = JSON.parse(localStorage.getItem('panier')) || [];
        } catch (error) {
            console.error('Erreur lecture panier:', error);
            panier = [];
        }

        miniCartItems.innerHTML = '';
        let totalGeneral = 0;

        if (panier.length === 0) {
            miniCartItems.innerHTML = '<p class="text-muted">Votre panier est vide.</p>';
            miniCartTotal.textContent = '0 €';
            return;
        }

        panier.forEach(item => {
            if (!item || typeof item.prix === 'undefined') return;
            
            const prixBase = parseFloat(item.prix) || 0;
            const quantite = parseInt(item.qty) || 1;
            let totalItem = prixBase * quantite;

            if (item.supplements && Array.isArray(item.supplements)) {
                totalItem += item.supplements.reduce((sum, sup) => {
                    return sum + ((parseFloat(sup?.prix) || 0) * (parseInt(sup?.qty) || 1));
                }, 0);
            }

            totalGeneral += totalItem;

            const supText = item.supplements?.filter(sup => sup?.nom)
                .map(s => `${s.nom} x${s.qty || 1}`).join(', ') || '';

            miniCartItems.innerHTML += `
                <div class="mini-cart-item">
                    <div>
                        ${item.nom || 'Produit'} x${quantite}
                        ${supText ? `<div class="mini-cart-item-sup">${supText}</div>` : ''}
                    </div>
                    <div>${totalItem.toFixed(2)} €</div>
                </div>
            `;
        });

        miniCartTotal.textContent = totalGeneral.toFixed(2) + ' €';
    }

    document.addEventListener('click', (e) => {
        if (miniCart && cartLink && !cartLink.contains(e.target) && !miniCart.contains(e.target)) {
            miniCart.style.display = 'none';
        }
    });
});

