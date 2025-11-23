CREATE TABLE Etudiant (
    E_Matricule VARCHAR(6) PRIMARY KEY,
    Nom VARCHAR(150),
    Prenom VARCHAR(150),
    E_mail VARCHAR(150),
    Mot_de_passe VARCHAR(200)
);


CREATE TABLE Responsable_des_equipements (
    RE_Matricule VARCHAR(6) PRIMARY KEY,
    Nom VARCHAR(150),
    Prenom VARCHAR(150),
    E_mail VARCHAR(150),
    Mot_de_passe VARCHAR(200)
);


CREATE TABLE Projet (
    ID_Projet INT PRIMARY KEY AUTO_INCREMENT,
    Nom VARCHAR(150)
);


CREATE TABLE Cours (
    ID_Cours INT PRIMARY KEY AUTO_INCREMENT,
    Nom VARCHAR(150)
);


CREATE TABLE Lier (
    ID_Cours INT,
    ID_Projet INT,
    PRIMARY KEY (ID_Cours, ID_Projet),
    FOREIGN KEY (ID_Cours) REFERENCES Cours(ID_Cours),
    FOREIGN KEY (ID_Projet) REFERENCES Projet(ID_Projet)
);


CREATE TABLE Participer (
    ID_Projet INT,
    E_Matricule VARCHAR(150),
    PRIMARY KEY (ID_Projet, E_Matricule),
    FOREIGN KEY (ID_Projet) REFERENCES Projet(ID_Projet),
    FOREIGN KEY (E_Matricule) REFERENCES Etudiant(E_Matricule)
);


CREATE TABLE Categorie (
    ID_Categorie INT PRIMARY KEY AUTO_INCREMENT,
    Nom VARCHAR(150),
    Valeur_critique INT
);


CREATE TABLE Modele (
    ID_Modele INT PRIMARY KEY AUTO_INCREMENT,
    Reference VARCHAR(200),
    Description TEXT,
    ID_Categorie INT,
    FOREIGN KEY (ID_Categorie) REFERENCES Categorie(ID_Categorie)
);


CREATE TABLE Exemplaire (
    ID_Exemplaire INT PRIMARY KEY AUTO_INCREMENT,
    Etat ENUM ('sad', 'ok', 'happy');, --------------------------------------------------
    Disponibilite VARCHAR(50),-----------------------------------------------------
    ID_Modele INT,
    Date_Retrait DATE,
    RE_Matricule_Retrait VARCHAR(6),
    Date_ajout DATE,
    RE_Matricule_Ajout VARCHAR(6),
    FOREIGN KEY (ID_Modele) REFERENCES Modele(ID_Modele),
    FOREIGN KEY (RE_Matricule_Retrait) REFERENCES Responsable_des_equipements(RE_Matricule),
    FOREIGN KEY (RE_Matricule_Ajout) REFERENCES Responsable_des_equipements(RE_Matricule)
);


CREATE TABLE Emprunt (
    ID_Emprunt INT PRIMARY KEY AUTO_INCREMENT,
    Date_debut DATE,
    Date_fin_prevue DATE,
    Raison_Emprunt TEXT,
    Statut VARCHAR(150), -----------------------------------------
    Date_retour DATE,
    Retard INT, -----------------------------------------------
    Degradation_materiel TEXT, ---------------------------------------------------
    RE_Matricule VARCHAR(6),
    E_Matricule VARCHAR(6),
    ID_Projet INT,
    FOREIGN KEY (RE_Matricule) REFERENCES Responsable_des_equipements(RE_Matricule),
    FOREIGN KEY (E_Matricule) REFERENCES Etudiant(E_Matricule),
    FOREIGN KEY (ID_Projet) REFERENCES Projet(ID_Projet)
);


CREATE TABLE Concerner (
    ID_Emprunt INT,
    ID_Exemplaire INT,
    PRIMARY KEY (ID_Emprunt, ID_Exemplaire),
    FOREIGN KEY (ID_Emprunt) REFERENCES Emprunt(ID_Emprunt),
    FOREIGN KEY (ID_Exemplaire) REFERENCES Exemplaire(ID_Exemplaire)
);


CREATE TABLE Reparer (
    RE_Matricule VARCHAR(6),
    ID_Exemplaire INT,
    Date_Reparation DATE,
    PRIMARY KEY (RE_Matricule, ID_Exemplaire),
    FOREIGN KEY (RE_Matricule) REFERENCES Responsable_des_equipements(RE_Matricule),
    FOREIGN KEY (ID_Exemplaire) REFERENCES Exemplaire(ID_Exemplaire)
);