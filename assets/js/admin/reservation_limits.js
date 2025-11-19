document.addEventListener('DOMContentLoaded', () => {
    // On cible uniquement le container principal des dates pour éviter d'exécuter le script sur toutes les pages
    const datesContainer = document.querySelector('.limitation-card .dates-section');
    if (!datesContainer) return; // Stop si la page n’a pas de section dates

    // Récupération sécurisée des éléments
    const datePicker = datesContainer.querySelector('#datePicker');
    const addDateBtn = datesContainer.querySelector('#addDateBtn');
    const selectedDates = datesContainer.querySelector('#selectedDates');
    const disabledDatesInput = datesContainer.querySelector('#disabledDatesInput');
    const datesCount = datesContainer.querySelector('#datesCount');

    if (!datePicker || !addDateBtn || !selectedDates || !disabledDatesInput || !datesCount) {
        console.warn('Certains éléments requis sont manquants dans le DOM.');
        return;
    }

    // Création d'un feedback utilisateur (messages)
    const feedback = document.createElement('div');
    feedback.className = 'feedback-message';
    if (selectedDates.parentNode) {
        selectedDates.parentNode.insertBefore(feedback, selectedDates);
    }

    // Initialiser les dates désactivées à partir du champ caché
    let disabledDates = disabledDatesInput.value
        ? disabledDatesInput.value.split(',').filter(date => date.trim() !== '')
        : [];

    // Fonction pour mettre à jour le champ caché, le compteur et l'affichage
    function updateUI() {
        disabledDatesInput.value = disabledDates.join(',');
        datesCount.textContent = `${disabledDates.length} date(s) bloquée(s)`;
        refreshSelectedDates();
    }

    // Rafraîchissement de l'affichage des dates bloquées
    function refreshSelectedDates() {
        selectedDates.innerHTML = '';

        if (disabledDates.length === 0) {
            selectedDates.innerHTML = `
                <div class="empty-state">
                    <span class="empty-icon">📅</span>
                    <span class="empty-text">Aucune date bloquée</span>
                </div>
            `;
            return;
        }

        disabledDates.forEach(date => {
            const dateObj = new Date(date);
            const dateString = dateObj.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            const tag = document.createElement('div');
            tag.className = 'date-tag';
            tag.innerHTML = `
                <span class="date-text">${dateString}</span>
                <button type="button" class="remove-date" data-date="${date}">
                    <span class="remove-icon">×</span>
                </button>
            `;
            selectedDates.appendChild(tag);
        });
    }

    // Ajouter une nouvelle date
    addDateBtn.addEventListener('click', () => {
        const selectedDate = datePicker.value;
        feedback.textContent = ''; // Reset du message

        if (!selectedDate) return;

        if (!disabledDates.includes(selectedDate)) {
            disabledDates.push(selectedDate);
            updateUI();
            datePicker.value = '';
        } else {
            feedback.textContent = 'Cette date est déjà bloquée.';
            feedback.style.color = 'red';
        }
    });

    // Event delegation pour la suppression d'une date
    selectedDates.addEventListener('click', e => {
        const btn = e.target.closest('.remove-date');
        if (!btn) return;

        const dateToRemove = btn.getAttribute('data-date');
        disabledDates = disabledDates.filter(d => d !== dateToRemove);
        updateUI();
    });

    // Initialiser l'affichage dès le chargement
    updateUI();
});
