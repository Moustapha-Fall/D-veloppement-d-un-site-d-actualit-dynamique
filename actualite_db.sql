-- ============================================================
-- SCRIPT SQL — Site d'actualite dynamique
-- Base de donnees : actualite_db
-- ESP — Departement Genie Informatique — Pr. I. Fall
-- ============================================================

-- Suppression et recreation de la base
DROP DATABASE IF EXISTS actualite_db;
CREATE DATABASE actualite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE actualite_db;

-- ============================================================
-- TABLE 1 : categories (creee en premier car articles en depend)
-- ============================================================
CREATE TABLE categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at  DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 2 : utilisateurs (creee en deuxieme car articles en depend)
-- ============================================================
CREATE TABLE utilisateurs (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(100) NOT NULL,
    prenom       VARCHAR(100) NOT NULL,
    login        VARCHAR(50)  NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('editeur','admin') NOT NULL DEFAULT 'editeur',
    created_at   DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 3 : articles (creee en dernier — depend des 2 autres)
-- ============================================================
CREATE TABLE articles (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre            VARCHAR(255) NOT NULL,
    description      VARCHAR(500) NOT NULL,
    contenu          LONGTEXT     NOT NULL,
    categorie_id     INT UNSIGNED NOT NULL,
    auteur_id        INT UNSIGNED NOT NULL,
    date_publication DATETIME DEFAULT NOW(),
    image            VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_article_categorie FOREIGN KEY (categorie_id) REFERENCES categories(id),
    CONSTRAINT fk_article_auteur    FOREIGN KEY (auteur_id)    REFERENCES utilisateurs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNEES INITIALES
-- ============================================================

-- 5 categories obligatoires
INSERT INTO categories (nom, description) VALUES
('Technologie', 'Actualites du monde numerique, innovations et high-tech'),
('Sport',        'Resultats, transferts et analyses sportives'),
('Politique',    'Vie politique nationale et internationale'),
('Education',    'Systeme educatif, universites et formation'),
('Culture',      'Arts, cinema, musique et patrimoine');

-- Compte administrateur
-- Mot de passe : Admin2024! (hash bcrypt genere avec password_hash)
INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Diallo', 'Mamadou', 'admin',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Compte editeur de test
-- Mot de passe : Editeur2024!
INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Sow', 'Fatou', 'editeur',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editeur');

-- Articles de test pour peupler la page d'accueil
INSERT INTO articles (titre, description, contenu, categorie_id, auteur_id) VALUES
(
  'L\'intelligence artificielle revolutionne l\'enseignement superieur',
  'Les universites africaines adoptent massivement les outils d\'IA pour personaliser l\'apprentissage.',
  'L\'integration de l\'intelligence artificielle dans les etablissements d\'enseignement superieur africains connait une acceleration sans precedent. Des plateformes adaptatives permettent desormais aux etudiants de progresser a leur propre rythme, tandis que les enseignants disposent d\'outils d\'analyse pour identifier rapidement les difficultes de chaque apprenant.\n\nL\'Ecole Superieure Polytechnique de Dakar figure parmi les pionniers de cette transformation numerique. Ses laboratoires de recherche collaborent avec plusieurs entreprises technologiques pour developper des solutions specifiquement adaptees aux contextes locaux.\n\nCependant, cette revolution souleve des questions importantes sur l\'equite d\'acces et la formation des enseignants. Les experts appellent a une strategie nationale coordonnee pour eviter un nouveau fossé numerique.',
  1, 1
),
(
  'CAN 2025 : Le Senegal en route pour la finale',
  'Apres une victoire eclatante en demi-finale, les Lions de la Teranga visent leur deuxieme titre continental.',
  'La selection nationale senegalaise a livre une prestation majuscule lors de la demi-finale de la Coupe d\'Afrique des Nations. Menee par un collectif soudé et une défense de fer, l\'équipe a dominé son adversaire du soir sur l\'ensemble du match.\n\nL\'entraineur a salue l\'état d\'esprit de ses joueurs, soulignant la capacité du groupe à gérer la pression des grands rendez-vous. Le capitaine s\'est montré particulièrement inspiré, orchestrant le jeu avec une maîtrise remarquable.\n\nLa finale se tiendra dans cinq jours. Le pays entier retient son souffle, espérant revivre les émotions du titre de 2021.',
  2, 2
),
(
  'Nouveau budget : les grandes lignes du projet de loi de finances',
  'Le gouvernement présente un budget ambitieux axé sur les infrastructures et la jeunesse.',
  'Le projet de loi de finances pour l\'exercice prochain a été soumis à l\'Assemblée nationale. Ce texte prévoit une augmentation significative des allocations pour le secteur de l\'éducation et les grands travaux d\'infrastructure.\n\nLes débats parlementaires qui s\'annoncent promettent d\'être intenses. L\'opposition a déjà annoncé qu\'elle déposerait des amendements substantiels, notamment sur les questions de transparence dans la gestion des ressources publiques.\n\nEconomistes et société civile appellent à un débat serein et constructif, rappelant l\'urgence des défis de développement auxquels le pays fait face.',
  3, 1
),
(
  'Réforme du baccalauréat : ce qui change à la rentrée prochaine',
  'Le ministère de l\'Education annonce d\'importantes modifications dans les épreuves du bac.',
  'Une réforme en profondeur du baccalauréat entrera en vigueur dès la prochaine rentrée scolaire. L\'introduction de nouveaux coefficients et l\'ajout d\'épreuves pratiques constituent les changements les plus structurants.\n\nLes associations de parents d\'élèves et les syndicats d\'enseignants ont exprimé des avis contrastés. Si tous saluent la volonté de modernisation, des inquiétudes persistent sur les conditions de mise en œuvre et la formation des correcteurs.\n\nLe ministre a assuré qu\'un accompagnement spécifique serait mis en place pour les lycéens concernés et leurs familles.',
  4, 2
),
(
  'Festival de jazz de Saint-Louis : le programme dévoilé',
  'La 30ème édition du célèbre festival promet une programmation exceptionnelle avec des artistes internationaux.',
  'Le Festival International de Jazz de Saint-Louis fête cette année ses trente ans d\'existence avec un programme d\'une richesse inédite. Plus de cinquante concerts sont prévus sur cinq jours, mêlant grandes figures internationales et talents locaux émergents.\n\nLa ville de Saint-Louis se prépare à accueillir des milliers de visiteurs venus des quatre coins du monde. Les hôtels affichent complet depuis plusieurs semaines, témoignant de l\'attractivité croissante de cet événement culturel majeur.\n\nLes organisateurs ont également prévu un volet pédagogique avec des masterclasses ouvertes aux jeunes musiciens senegalais.',
  5, 1
);

-- ============================================================
-- Verification (commentée pour l'import)
-- SELECT 'Script execute avec succes !' AS message;
-- ============================================================
