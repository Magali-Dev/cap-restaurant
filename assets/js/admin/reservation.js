document.addEventListener('DOMContentLoaded', function() {

    function updateCounters() {
        const today = new Date().toISOString().split('T')[0];
        let todayCount = 0;
        let pendingCount = 0;
        let confirmedCount = 0;
        let cancelledCount = 0;

        document.querySelectorAll('.reservation-row').forEach(row => {
            const date = row.getAttribute('data-date');
            const status = row.getAttribute('data-status');
            
            if (date === today) todayCount++;
            if (status === 'en attente') pendingCount++;
            if (status === 'confirmée') confirmedCount++;
            if (status === 'annulée') cancelledCount++;
        });

        const todayEl = document.getElementById('today-count');
        const pendingEl = document.getElementById('pending-count');
        const confirmedEl = document.getElementById('confirmed-count');
        const cancelledEl = document.getElementById('cancelled-count');

        if (todayEl) todayEl.textContent = todayCount;
        if (pendingEl) pendingEl.textContent = pendingCount;
        if (confirmedEl) confirmedEl.textContent = confirmedCount;
        if (cancelledEl) cancelledEl.textContent = cancelledCount;
    }

    function setupFilters() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const today = new Date().toISOString().split('T')[0];
                
                document.querySelectorAll('.reservation-row').forEach(row => {
                    const date = row.getAttribute('data-date');
                    const status = row.getAttribute('data-status');
                    
                    let show = false;
                    
                    switch(filter) {
                        case 'all': show = true; break;
                        case 'today': show = date === today; break;
                        case 'pending': show = status === 'en attente'; break;
                        case 'confirmed': show = status === 'confirmée'; break;
                        case 'cancelled': show = status === 'annulée'; break;
                    }
                    
                    row.style.display = show ? '' : 'none';
                });
            });
        });
    }

    // Vérifie qu'il y a au moins une ligne avant de mettre à jour
    if (document.querySelectorAll('.reservation-row').length > 0) {
        updateCounters();
        setupFilters();
    }
});

// Fonctions utilitaires
function toggleMessage(reservationId) {
    const messageContent = document.getElementById('message-' + reservationId);
    if (!messageContent) return;
    const isVisible = messageContent.style.display === 'block';
    messageContent.style.display = isVisible ? 'none' : 'block';
}

function showReservationDetails(reservationId) {
    alert('Détails de la réservation #' + reservationId + '\n\nCette fonctionnalité peut être étendue pour afficher tous les détails.');
}

function callClient(phoneNumber) {
    if (phoneNumber) window.open('tel:' + phoneNumber);
    else alert('Numéro de téléphone non disponible');
}

function emailClient(email) {
    if (email) window.open('mailto:' + email);
    else alert('Adresse email non disponible');
}

function closeModal() {
    const modal = document.getElementById('reservationModal');
    if (modal) modal.style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('reservationModal');
    if (modal && event.target === modal) {
        closeModal();
    }
}
