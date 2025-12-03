// Code simple pour gérer le déroulement des options de paiement
window.onload = function() {
    console.log("Script de paiement chargé");
    
    // Activer la première option par défaut
    var firstOption = document.querySelector('.payment-option');
    if (firstOption) {
        firstOption.classList.add('active');
        var firstRadio = firstOption.querySelector('input[type="radio"]');
        if (firstRadio) {
            firstRadio.checked = true;
        }
    }
    
    // Ajouter des gestionnaires d'événements de clic aux en-têtes
    var headers = document.querySelectorAll('.payment-header');
    for (var i = 0; i < headers.length; i++) {
        headers[i].onclick = function() {
            console.log("Clic détecté sur un header");
            
            // Trouver le parent .payment-option
            var option = this.parentNode;
            
            // Désactiver toutes les options
            var allOptions = document.querySelectorAll('.payment-option');
            for (var j = 0; j < allOptions.length; j++) {
                allOptions[j].classList.remove('active');
            }
            
            // Activer cette option
            option.classList.add('active');
            
            // Cocher le bouton radio correspondant
            var radio = option.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
            
            // Empêcher le comportement par défaut
            return false;
        };
    }
}; 