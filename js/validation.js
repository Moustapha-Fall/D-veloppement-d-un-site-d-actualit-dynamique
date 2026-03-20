/**
 * js/validation.js — Validation côté client de tous les formulaires
 * Projet Backend PHP/MySQL — ESP Dakar
 * Chaque formulaire est identifié par son attribut id unique.
 */

'use strict';

/* ---- Utilitaires ----------------------------------------- */
function showError(input, message) {
    input.classList.add('error');
    let span = input.parentElement.querySelector('.field-error');
    if (!span) {
        span = document.createElement('span');
        span.className = 'field-error';
        input.parentElement.appendChild(span);
    }
    span.textContent = message;
}

function clearError(input) {
    input.classList.remove('error');
    const span = input.parentElement.querySelector('.field-error');
    if (span) span.textContent = '';
}

function clearAllErrors(form) {
    form.querySelectorAll('.field-error').forEach(s => s.textContent = '');
    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
}

function isEmpty(value) {
    return value.trim() === '';
}

/* ---- Formulaire Connexion --------------------------------- */
const formConnexion = document.getElementById('formConnexion');
if (formConnexion) {
    formConnexion.addEventListener('submit', function (e) {
        clearAllErrors(this);
        let valid = true;

        const login = this.querySelector('#login');
        const mdp   = this.querySelector('#mot_de_passe');

        if (isEmpty(login.value)) {
            showError(login, 'Le login est obligatoire.');
            valid = false;
        }

        if (isEmpty(mdp.value)) {
            showError(mdp, 'Le mot de passe est obligatoire.');
            valid = false;
        } else if (mdp.value.trim().length < 4) {
            showError(mdp, 'Le mot de passe doit contenir au moins 4 caractères.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

/* ---- Formulaire Article (Ajouter / Modifier) -------------- */
const formArticle = document.getElementById('formArticle');
if (formArticle) {
    formArticle.addEventListener('submit', function (e) {
        clearAllErrors(this);
        let valid = true;

        const titre       = this.querySelector('#titre');
        const description = this.querySelector('#description');
        const contenu     = this.querySelector('#contenu');
        const categorie   = this.querySelector('#categorie_id');

        if (isEmpty(titre.value)) {
            showError(titre, 'Le titre est obligatoire.');
            valid = false;
        } else if (titre.value.trim().length > 255) {
            showError(titre, 'Le titre ne peut pas dépasser 255 caractères.');
            valid = false;
        }

        if (isEmpty(description.value)) {
            showError(description, 'La description courte est obligatoire.');
            valid = false;
        } else if (description.value.trim().length > 500) {
            showError(description, 'La description ne peut pas dépasser 500 caractères.');
            valid = false;
        }

        if (isEmpty(contenu.value)) {
            showError(contenu, 'Le contenu est obligatoire.');
            valid = false;
        }

        if (!categorie || categorie.value === '' || categorie.value === '0') {
            showError(categorie, 'Veuillez sélectionner une catégorie.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    // Compteur de caractères pour le titre
    const titreInput = formArticle.querySelector('#titre');
    if (titreInput) {
        const counter = document.createElement('small');
        counter.style.cssText = 'color:#999;float:right;margin-top:.2rem;';
        titreInput.parentElement.appendChild(counter);
        const update = () => counter.textContent = `${titreInput.value.length}/255`;
        titreInput.addEventListener('input', update);
        update();
    }
}

/* ---- Formulaire Catégorie --------------------------------- */
const formCategorie = document.getElementById('formCategorie');
if (formCategorie) {
    formCategorie.addEventListener('submit', function (e) {
        clearAllErrors(this);
        let valid = true;

        const nom = this.querySelector('#nom');
        if (isEmpty(nom.value)) {
            showError(nom, 'Le nom de la catégorie est obligatoire.');
            valid = false;
        } else if (nom.value.trim().length > 100) {
            showError(nom, 'Le nom ne peut pas dépasser 100 caractères.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

/* ---- Formulaire Utilisateur ------------------------------- */
const formUser = document.getElementById('formUser');
if (formUser) {
    formUser.addEventListener('submit', function (e) {
        clearAllErrors(this);
        let valid = true;

        const nom    = this.querySelector('#nom');
        const prenom = this.querySelector('#prenom');
        const login  = this.querySelector('#login');
        const mdp    = this.querySelector('#mot_de_passe');
        const role   = this.querySelector('#role');

        if (isEmpty(nom.value)) {
            showError(nom, 'Le nom est obligatoire.'); valid = false;
        }
        if (isEmpty(prenom.value)) {
            showError(prenom, 'Le prénom est obligatoire.'); valid = false;
        }
        if (isEmpty(login.value)) {
            showError(login, 'Le login est obligatoire.'); valid = false;
        } else if (login.value.trim().length < 3) {
            showError(login, 'Le login doit contenir au moins 3 caractères.'); valid = false;
        }

        // Mot de passe : obligatoire seulement à la création (input non disabled)
        if (mdp && !mdp.disabled) {
            const isCreate = document.querySelector('[name="_mode"]')?.value !== 'modifier';
            if (isCreate && isEmpty(mdp.value)) {
                showError(mdp, 'Le mot de passe est obligatoire.'); valid = false;
            } else if (!isEmpty(mdp.value) && mdp.value.trim().length < 6) {
                showError(mdp, 'Le mot de passe doit contenir au moins 6 caractères.'); valid = false;
            }
        }

        if (!role || role.value === '') {
            showError(role, 'Veuillez sélectionner un rôle.'); valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

/* ---- Confirmation suppression ----------------------------- */
document.querySelectorAll('.confirm-delete').forEach(el => {
    el.addEventListener('click', function (e) {
        const msg = this.dataset.confirm || 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.';
        if (!confirm(msg)) e.preventDefault();
    });
});

/* ---- Effacement des erreurs à la saisie ------------------- */
document.querySelectorAll('input, select, textarea').forEach(input => {
    input.addEventListener('input', () => clearError(input));
    input.addEventListener('change', () => clearError(input));
});
