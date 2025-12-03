<!-- -----------------------------------------------------------------------------
     Fichier : v_modifier_profil.inc.php
     Rôle    : Vue affichant le formulaire de modification du profil client.
     ----------------------------------------------------------------------------- -->
<link rel="stylesheet" href="<?php echo Chemins::CSS; ?>pages/profil.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="profil-container">
    <div class="profil-header">
        <div class="profil-photo-container">
            <div class="profil-photo">
                <img id="preview-photo" src="<?php echo !empty($client->photo) ? Chemins::IMAGES . 'profil/' . $client->photo : Chemins::IMAGES . 'default-avatar.png'; ?>" alt="Photo de profil">
            </div>
            <div class="photo-upload">
                <label for="photo" class="btn-upload">
                    <span class="material-icons">photo_camera</span>
                    <span class="upload-text">Modifier</span>
                </label>
                <input type="file" id="photo" name="photo" accept="image/*" style="display: none;">
            </div>
        </div>
        <h1>Modifier mon profil</h1>
        <p>Modifiez vos informations personnelles</p>
    </div>

    <form id="form-modification" method="POST" action="index.php?controleur=Client&action=modifierProfil" enctype="multipart/form-data">
        <!-- Champ caché pour la photo -->
        <input type="hidden" id="photo_modifiee" name="photo_modifiee" value="false">
        <div class="profil-sections-container">
            <!-- Colonne gauche -->
            <div>
                <div class="profil-section">
                    <h2>Informations personnelles</h2>
                    <div class="info-group">
                        <span class="material-icons info-icon">person</span>
                        <div class="info-content">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-input" value="<?php echo htmlspecialchars($client->prenom ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="info-group">
                        <span class="material-icons info-icon">badge</span>
                        <div class="info-content">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" class="form-input" value="<?php echo htmlspecialchars($client->nom ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="info-group">
                        <span class="material-icons info-icon">cake</span>
                        <div class="info-content">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance" class="form-input" value="<?php echo isset($client->date_naissance) ? date('Y-m-d', strtotime($client->date_naissance)) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="info-group">
                        <span class="material-icons info-icon">phone</span>
                        <div class="info-content">
                            <label for="tel">Téléphone</label>
                            <input type="tel" id="tel" name="tel" class="form-input" value="<?php echo htmlspecialchars($client->tel ?? ''); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="profil-section">
                    <h2>Sécurité</h2>
                    <div class="info-group">
                        <span class="material-icons info-icon">email</span>
                        <div class="info-content">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($client->email ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="info-group">
                        <span class="material-icons info-icon">lock</span>
                        <div class="info-content">
                            <label for="nouveau_mdp">Nouveau mot de passe (optionnel)</label>
                            <input type="password" id="nouveau_mdp" name="nouveau_mdp" class="form-input" placeholder="Laissez vide pour ne pas modifier">
                        </div>
                    </div>
                    <div class="info-group">
                        <span class="material-icons info-icon">lock_clock</span>
                        <div class="info-content">
                            <label for="confirmer_mdp">Confirmer le mot de passe</label>
                            <input type="password" id="confirmer_mdp" name="confirmer_mdp" class="form-input" placeholder="Confirmez le nouveau mot de passe">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite -->
            <div>
                <div class="profil-section">
                    <h2>Adresse</h2>
                    <div class="info-group">
                        <span class="material-icons info-icon">home</span>
                        <div class="info-content">
                            <label for="rue">Rue</label>
                            <input type="text" id="rue" name="rue" class="form-input" value="<?php echo htmlspecialchars($client->rue ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="info-group">
                        <span class="material-icons info-icon">location_city</span>
                        <div class="info-content">
                            <label for="ville">Ville</label>
                            <input type="text" id="ville" name="ville" class="form-input" value="<?php echo htmlspecialchars($client->ville ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="info-group">
                        <span class="material-icons info-icon">markunread_mailbox</span>
                        <div class="info-content">
                            <label for="codePostal">Code postal</label>
                            <input type="text" id="codePostal" name="codePostal" class="form-input" value="<?php echo htmlspecialchars($client->codePostal ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="button-group">
                <a href="index.php?controleur=Client&action=afficherProfil" class="btn-back">Annuler</a>
                <button type="submit" class="btn-modifier" id="btn-enregistrer">Enregistrer</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal de confirmation -->
<div id="modal-confirmation" class="modal">
    <div class="modal-content">
        <h2>Confirmer les modifications</h2>
        <p>Êtes-vous sûr de vouloir enregistrer ces modifications ?</p>
        <div class="modal-buttons">
            <button id="btn-confirmer" class="btn-modifier">Confirmer</button>
            <button id="btn-annuler" class="btn-back">Annuler</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la photo de profil
    const inputPhoto = document.getElementById('photo');
    const previewPhoto = document.getElementById('preview-photo');
    const photoModifiee = document.getElementById('photo_modifiee');

    inputPhoto.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Vérifier le type de fichier
            if (!file.type.startsWith('image/')) {
                alert('Veuillez sélectionner une image.');
                return;
            }
            // Vérifier la taille du fichier (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 5MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewPhoto.src = e.target.result;
                photoModifiee.value = 'true';
            };
            reader.readAsDataURL(file);
        }
    });

    // Gestion du formulaire et de la modal
    const form = document.getElementById('form-modification');
    const modal = document.getElementById('modal-confirmation');
    const btnConfirmer = document.getElementById('btn-confirmer');
    const btnAnnuler = document.getElementById('btn-annuler');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        modal.style.display = 'flex';
    });

    btnConfirmer.addEventListener('click', function() {
        form.submit();
    });

    btnAnnuler.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Vérification des mots de passe
    const nouveauMdp = document.getElementById('nouveau_mdp');
    const confirmerMdp = document.getElementById('confirmer_mdp');

    function verifierMotsDePasse() {
        if (nouveauMdp.value || confirmerMdp.value) {
            if (nouveauMdp.value !== confirmerMdp.value) {
                confirmerMdp.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                confirmerMdp.setCustomValidity('');
            }
        } else {
            confirmerMdp.setCustomValidity('');
        }
    }

    nouveauMdp.addEventListener('change', verifierMotsDePasse);
    confirmerMdp.addEventListener('change', verifierMotsDePasse);
});
</script> 