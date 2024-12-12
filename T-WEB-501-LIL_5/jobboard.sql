-- Création de la base de données
CREATE DATABASE jobboard;

-- Utilisation de la base de données
USE jobboard;

-- Table pour les personnes (recruteurs et candidats)
CREATE TABLE people (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL, 
    token VARCHAR(64) DEFAULT NULL,
    role ENUM('recruiter', 'applicant', 'admin') NOT NULL
);

-- Table pour les entreprises
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(255)
);

-- Table pour les annonces d'emploi
CREATE TABLE advertisements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    short_description TEXT,
    learn_more TEXT,
    wage DECIMAL(10, 2),
    place VARCHAR(255),
    working_time VARCHAR(50),
    company_id INT,
    recruiter_id INT,  -- Ajout de la colonne recruiter_id
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (recruiter_id) REFERENCES people(id)  -- Clé étrangère pour relier l'annonce à un recruteur
);

-- Table pour les candidatures à un emploi
CREATE TABLE job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT,
    advertisement_id INT,
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    email_sent BOOLEAN DEFAULT FALSE,
    email_content TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    notes TEXT,
    recruiter_id INT,
    FOREIGN KEY (applicant_id) REFERENCES people(id),
    FOREIGN KEY (advertisement_id) REFERENCES advertisements(id),
    FOREIGN KEY (recruiter_id) REFERENCES people(id)
);

-- Insérer des entreprises fictives
INSERT INTO companies (name, logo_url) VALUES 
('Tech Innovators', 'https://example.com/logo1.png'),
('Marketing Masters', 'https://example.com/logo2.png'),
('Design Studio', 'https://example.com/logo3.png'),
('Data Insights', 'https://example.com/logo4.png');

-- Insérer un utilisateur admin fictif
INSERT INTO people (first_name, last_name, email, mot_de_passe, role, token)
VALUES (
    'John',                       -- Prénom fictif
    'Doe',                        -- Nom fictif
    'admin@gmail.com',           -- Email fictif
    '$2b$12$a.pMn/aFWAFJGfdg4ETgOO2AEeE1XuK0Ov5UV0ZWzsBMs/rjzwbhe',  -- Mot de passe haché généré (admin123)
    'admin',                     -- Rôle d'admin
    NULL                          -- Pas de token pour l'instant
);
INSERT INTO people (first_name, last_name, email, mot_de_passe, role)
VALUES 
('Alice', 'Smith', 'alice.smith@recruiter.com', 'hashed_password1', 'recruiter'),
('Bob', 'Johnson', 'bob.johnson@recruiter.com', 'hashed_password2', 'recruiter'),
('Carol', 'Williams', 'carol.williams@recruiter.com', 'hashed_password3', 'recruiter'),
('David', 'Brown', 'david.brown@recruiter.com', 'hashed_password4', 'recruiter');

-- Insérer des annonces d'emploi
INSERT INTO advertisements (title, short_description, learn_more, wage, place, working_time, company_id, recruiter_id) VALUES 
('Développeur Web', 'Nous recherchons un développeur web passionné pour rejoindre notre équipe.', 'https://example.com/job1', 35000.00, 'Paris', 'Temps plein', 1, 1),
('Chef de Projet', 'Rejoignez notre équipe en tant que chef de projet pour diriger des projets innovants.', 'https://example.com/job2', 45000.00, 'Lyon', 'Temps plein', 2, 2),
('Graphiste', 'Nous cherchons un graphiste créatif pour travailler sur divers projets.', 'https://example.com/job3', 32000.00, 'Marseille', 'Temps partiel', 3, 3),
('Analyste de Données', 'Aidez-nous à analyser les données pour des prises de décisions éclairées.', 'https://example.com/job4', 40000.00, 'Bordeaux', 'Temps plein', 4, 4),
('Spécialiste SEO', 'Nous recherchons un spécialiste SEO pour améliorer notre visibilité en ligne.', 'https://example.com/job5', 38000.00, 'Nice', 'Temps partiel', 2, 2),
('Développeur Mobile', 'Rejoignez notre équipe en tant que développeur mobile et travaillez sur des applications passionnantes.', 'https://example.com/job6', 42000.00, 'Toulouse', 'Temps plein', 1, 1),
('Responsable Marketing Digital', 'Nous cherchons un responsable marketing digital pour diriger nos campagnes en ligne.', 'https://example.com/job7', 50000.00, 'Lille', 'Temps plein', 2, 2),
('Designer UI/UX', 'Aidez-nous à créer des interfaces utilisateur attrayantes et fonctionnelles.', 'https://example.com/job8', 36000.00, 'Strasbourg', 'Temps plein', 3, 3);


