// Ajouter jQuery au projet (à inclure dans votre HTML)
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour gérer le clic sur une option de paiement
    function handlePaymentOptionClick(event) {
        // Empêcher le comportement par défaut
        event.preventDefault();
        
        // Obtenir l'élément parent .payment-option
        var option = this.parentNode;
        while (option && !option.classList.contains('payment-option')) {
            option = option.parentNode;
        }
        
        if (!option) return;
        
        // Désactiver toutes les options
        var allOptions = document.querySelectorAll('.payment-option');
        for (var i = 0; i < allOptions.length; i++) {
            allOptions[i].classList.remove('active');
        }
        
        // Activer cette option
        option.classList.add('active');
        
        // Cocher le bouton radio
        var radio = option.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
        }
    }
    
    // Ajouter des gestionnaires d'événements aux en-têtes
    var headers = document.querySelectorAll('.payment-header');
    for (var i = 0; i < headers.length; i++) {
        headers[i].addEventListener('click', handlePaymentOptionClick);
    }
    
    // Activer la première option par défaut
    var firstOption = document.querySelector('.payment-option');
    if (firstOption) {
        firstOption.classList.add('active');
        var firstRadio = firstOption.querySelector('input[type="radio"]');
        if (firstRadio) {
            firstRadio.checked = true;
        }
    }

    // Gestion du code postal et de la ville
    const codePostalInput = document.getElementById('code_postal');
    const villeSelect = document.getElementById('ville');
    
    if (codePostalInput && villeSelect) {
        let timeoutId;
        codePostalInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            const codePostal = this.value;
            const searchInput = this.closest('.search-input');

            // Réinitialiser le select des villes
            villeSelect.innerHTML = '<option value="">Sélectionnez votre ville</option>';
            villeSelect.disabled = true;

            // Vérifier que le code postal a 5 chiffres
            if (codePostal.length === 5 && /^\d+$/.test(codePostal)) {
                timeoutId = setTimeout(() => {
                    // Ajouter la classe loading
                    searchInput.classList.add('loading');

                    // Appel à l'API geo.api.gouv.fr
                    fetch(`https://geo.api.gouv.fr/communes?codePostal=${codePostal}&fields=nom,code,codesPostaux`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur réseau');
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Vérifier si des villes ont été trouvées
                            if (data && data.length > 0) {
                                // Trier les villes par ordre alphabétique
                                data.sort((a, b) => a.nom.localeCompare(b.nom));

                                // Ajouter les villes trouvées
                                data.forEach(ville => {
                                    const option = document.createElement('option');
                                    option.value = ville.nom;
                                    option.textContent = ville.nom;
                                    villeSelect.appendChild(option);
                                });

                                // Activer la liste déroulante
                                villeSelect.disabled = false;

                                // Si une seule ville, la sélectionner automatiquement
                                if (data.length === 1) {
                                    villeSelect.value = data[0].nom;
                                }
                            } else {
                                villeSelect.innerHTML = '<option value="">Aucune ville trouvée pour ce code postal</option>';
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la récupération des villes:', error);
                            villeSelect.innerHTML = '<option value="">Erreur lors de la récupération des villes</option>';
                        })
                        .finally(() => {
                            // Retirer la classe loading
                            searchInput.classList.remove('loading');
                        });
                }, 300);
            }
        });
    }
}); 