/**
 * js/validation.js — Validation et interactions des formulaires
 * Projet ActuSenegal — ESP Dakar
 */

'use strict';

/* ============================================================
   UTILITAIRES
   ============================================================ */

const Utils = {
    showError(input, message) {
        input.classList.add('error');
        let span = input.parentElement.querySelector('.field-error');
        if (!span) {
            span = document.createElement('span');
            span.className = 'field-error';
            input.parentElement.appendChild(span);
        }
        span.textContent = message;
    },

    clearError(input) {
        input.classList.remove('error');
        const span = input.parentElement.querySelector('.field-error');
        if (span) span.textContent = '';
    },

    clearAllErrors(form) {
        form.querySelectorAll('.field-error').forEach(s => s.textContent = '');
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    },

    isEmpty(value) {
        return value.trim() === '';
    },

    formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' octets';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' Ko';
        return (bytes / (1024 * 1024)).toFixed(2) + ' Mo';
    }
};

/* ============================================================
   UPLOAD IMAGE AVEC PREVIEW
   ============================================================ */

const ImageUpload = {
    init() {
        document.querySelectorAll('.upload-zone').forEach(zone => {
            const input = zone.querySelector('input[type="file"]');
            const preview = zone.parentElement.querySelector('.upload-preview');
            
            if (!input || !preview) return;

            // Drag & drop
            ['dragenter', 'dragover'].forEach(evt => {
                zone.addEventListener(evt, e => {
                    e.preventDefault();
                    zone.classList.add('dragover');
                });
            });

            ['dragleave', 'drop'].forEach(evt => {
                zone.addEventListener(evt, e => {
                    e.preventDefault();
                    zone.classList.remove('dragover');
                });
            });

            zone.addEventListener('drop', e => {
                const files = e.dataTransfer.files;
                if (files.length) {
                    input.files = files;
                    this.showPreview(input, preview);
                }
            });

            // Selection fichier
            input.addEventListener('change', () => {
                this.showPreview(input, preview);
            });

            // Bouton supprimer
            const removeBtn = preview.querySelector('.upload-preview-remove');
            if (removeBtn) {
                removeBtn.addEventListener('click', e => {
                    e.preventDefault();
                    input.value = '';
                    preview.classList.remove('show');
                });
            }
        });
    },

    showPreview(input, preview) {
        const file = input.files[0];
        if (!file) return;

        // Verifier le type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Format non supporte. Utilisez JPG, PNG, GIF ou WebP.');
            input.value = '';
            return;
        }

        // Verifier la taille (2 Mo max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Image trop lourde (max 2 Mo).');
            input.value = '';
            return;
        }

        // Afficher la preview
        const reader = new FileReader();
        reader.onload = e => {
            const img = preview.querySelector('img');
            const name = preview.querySelector('.upload-preview-name');
            const size = preview.querySelector('.upload-preview-size');
            
            if (img) img.src = e.target.result;
            if (name) name.textContent = file.name;
            if (size) size.textContent = Utils.formatFileSize(file.size);
            
            preview.classList.add('show');
        };
        reader.readAsDataURL(file);
    }
};

/* ============================================================
   FORMULAIRES - VALIDATION
   ============================================================ */

const FormValidation = {
    init() {
        this.initConnexion();
        this.initArticle();
        this.initCategorie();
        this.initUser();
        this.initDeleteConfirm();
        this.initInputClear();
    },

    initConnexion() {
        const form = document.getElementById('formConnexion');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            Utils.clearAllErrors(this);
            let valid = true;

            const login = this.querySelector('#login');
            const mdp = this.querySelector('#mot_de_passe');

            if (Utils.isEmpty(login.value)) {
                Utils.showError(login, 'Le login est obligatoire.');
                valid = false;
            }

            if (Utils.isEmpty(mdp.value)) {
                Utils.showError(mdp, 'Le mot de passe est obligatoire.');
                valid = false;
            } else if (mdp.value.trim().length < 4) {
                Utils.showError(mdp, 'Minimum 4 caracteres.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    },

    initArticle() {
        const form = document.getElementById('formArticle');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            Utils.clearAllErrors(this);
            let valid = true;

            const titre = this.querySelector('#titre');
            const description = this.querySelector('#description');
            const contenu = this.querySelector('#contenu');
            const categorie = this.querySelector('#categorie_id');

            if (Utils.isEmpty(titre.value)) {
                Utils.showError(titre, 'Le titre est obligatoire.');
                valid = false;
            } else if (titre.value.trim().length > 255) {
                Utils.showError(titre, 'Maximum 255 caracteres.');
                valid = false;
            }

            if (Utils.isEmpty(description.value)) {
                Utils.showError(description, 'La description est obligatoire.');
                valid = false;
            } else if (description.value.trim().length > 500) {
                Utils.showError(description, 'Maximum 500 caracteres.');
                valid = false;
            }

            if (Utils.isEmpty(contenu.value)) {
                Utils.showError(contenu, 'Le contenu est obligatoire.');
                valid = false;
            }

            if (!categorie || categorie.value === '' || categorie.value === '0') {
                Utils.showError(categorie, 'Selectionnez une categorie.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });

        // Compteur caracteres
        this.addCharCounter(form.querySelector('#titre'), 255);
        this.addCharCounter(form.querySelector('#description'), 500);
    },

    initCategorie() {
        const form = document.getElementById('formCategorie');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            Utils.clearAllErrors(this);
            let valid = true;

            const nom = this.querySelector('#nom');
            if (Utils.isEmpty(nom.value)) {
                Utils.showError(nom, 'Le nom est obligatoire.');
                valid = false;
            } else if (nom.value.trim().length > 100) {
                Utils.showError(nom, 'Maximum 100 caracteres.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    },

    initUser() {
        const form = document.getElementById('formUser');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            Utils.clearAllErrors(this);
            let valid = true;

            const nom = this.querySelector('#nom');
            const prenom = this.querySelector('#prenom');
            const login = this.querySelector('#login');
            const mdp = this.querySelector('#mot_de_passe');
            const role = this.querySelector('#role');

            if (Utils.isEmpty(nom.value)) {
                Utils.showError(nom, 'Le nom est obligatoire.');
                valid = false;
            }
            if (Utils.isEmpty(prenom.value)) {
                Utils.showError(prenom, 'Le prenom est obligatoire.');
                valid = false;
            }
            if (Utils.isEmpty(login.value)) {
                Utils.showError(login, 'Le login est obligatoire.');
                valid = false;
            } else if (login.value.trim().length < 3) {
                Utils.showError(login, 'Minimum 3 caracteres.');
                valid = false;
            }

            if (mdp && !mdp.disabled) {
                const isCreate = document.querySelector('[name="_mode"]')?.value !== 'modifier';
                if (isCreate && Utils.isEmpty(mdp.value)) {
                    Utils.showError(mdp, 'Le mot de passe est obligatoire.');
                    valid = false;
                } else if (!Utils.isEmpty(mdp.value) && mdp.value.trim().length < 6) {
                    Utils.showError(mdp, 'Minimum 6 caracteres.');
                    valid = false;
                }
            }

            if (!role || role.value === '') {
                Utils.showError(role, 'Selectionnez un role.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    },

    initDeleteConfirm() {
        document.querySelectorAll('.confirm-delete').forEach(el => {
            el.addEventListener('click', function(e) {
                const msg = this.dataset.confirm || 'Supprimer cet element ?';
                if (!confirm(msg)) e.preventDefault();
            });
        });
    },

    initInputClear() {
        document.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', () => Utils.clearError(input));
            input.addEventListener('change', () => Utils.clearError(input));
        });
    },

    addCharCounter(input, max) {
        if (!input) return;
        
        const counter = document.createElement('small');
        counter.className = 'char-counter';
        counter.style.cssText = 'color:#999;float:right;margin-top:.25rem;font-size:.75rem;';
        input.parentElement.appendChild(counter);
        
        const update = () => {
            const len = input.value.length;
            counter.textContent = `${len}/${max}`;
            counter.style.color = len > max ? 'var(--clr-danger)' : '#999';
        };
        
        input.addEventListener('input', update);
        update();
    }
};

/* ============================================================
   INITIALISATION
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    FormValidation.init();
    ImageUpload.init();
});
