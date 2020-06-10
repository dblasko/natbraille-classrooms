CREATE TABLE Users (
    mail VARCHAR(50) PRIMARY KEY,
    name VARCHAR(20),
    firstName VARCHAR(20),
    birthIsoDate DATE,
    pwd VARCHAR(128) NOT NULL,
    isDeleted BOOLEAN DEFAULT FALSE
);

CREATE TABLE Notifications (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isoDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    content VARCHAR(256),
    isSeen BOOLEAN DEFAULT FALSE,
    userMail VARCHAR(50),
    link VARCHAR(128),
    CONSTRAINT fk_notifications_users FOREIGN KEY (userMail) REFERENCES Users(mail)
);

CREATE TABLE Promotions (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    creationIsoDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isClosedPromotion BOOLEAN DEFAULT FALSE,
    inviteLink VARCHAR(256)
);

CREATE TABLE InvitedPerson (
    mail VARCHAR(50) PRIMARY KEY,
    name VARCHAR(20),
    firstName VARCHAR(20)
);

-- "Enum" de rôles, pour n'avoir que des valeurs cohérentes dans l'attribut rôle de PromotionMemberships
-- Y insérer les noms des différents rôles que l'on veut avoir -> pour les tests en appli mettre valeurs en const
CREATE TABLE Roles (roleName VARCHAR(25) PRIMARY KEY);

-- Table d'association entre promotions & invitedPerson -> invitations
CREATE TABLE Invites (
    promotionId INT(6) UNSIGNED,
    invitedPersonMail VARCHAR(50),
    inviteIsoDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_invites PRIMARY KEY (promotionId, invitedPersonMail),
    CONSTRAINT fk_invites_promotionid FOREIGN KEY (promotionId) REFERENCES Promotions(id),
    CONSTRAINT fk_invites_invitedpersonmail FOREIGN KEY (invitedPersonMail) REFERENCES InvitedPerson(mail)
);

-- Table d'association entre user & promotions -> être membre d'une promo avec un rôle
CREATE TABLE PromotionMemberships (
    promotionId INT(6) UNSIGNED,
    memberUserMail VARCHAR(50),
    joinedPromotionIsoDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(25) NOT NULL,
    CONSTRAINT pk_promotionmemberships PRIMARY KEY (promotionId, memberUserMail),
    CONSTRAINT fk_promotionmemberships_promotionid FOREIGN KEY (promotionId) REFERENCES Promotions(id),
    CONSTRAINT fk_promotionmemberships_memberusermail FOREIGN KEY (memberUserMail) REFERENCES Users(mail),
    CONSTRAINT fk_promotionmemberships_role FOREIGN KEY (role) REFERENCES Roles(roleName)
);

CREATE TABLE Exercises (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lastUpdateIsoDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    name VARCHAR(50) NOT NULL,
    exerciseContent VARCHAR(5000),
    exerciseCorrection VARCHAR(5000),
    -- in app split with a | ?
    commentaryContent VARCHAR(5000),
    createdByMail VARCHAR(50),
    idTranscriptionType INT(6),
    CONSTRAINT fk_exercise_idtranscriptiontype FOREIGN KEY (idTranscriptionType) REFERENCES TRANSCRIPTIONTYPE(id),
    CONSTRAINT fk_exercise_createdbymail FOREIGN KEY (createdByMail) REFERENCES Users(mail)
);


-- PARTIE LINA

create table ErrorType
(
   id       int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   coefficient          int,
   explanationTxt       varchar(5000)
);

create table Attempt
(
   id            int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   isoDate                 timestamp,
   idAffectation 					int(6),
   userMail 				varchar(50),
   score      INT(6),
   attemptTxt VARCHAR(5000),
   CONSTRAINT fk_id FOREIGN KEY (idAffectation) REFERENCES EXERCISEAFFECTATION(id),
   CONSTRAINT fk_name FOREIGN KEY (userMail) REFERENCES Users(mail)
);

create table Error
(
   id       int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   errorTxt             varchar(5000),
   correctTxt           varchar(5000),
   idErrorType			int(6),
   idAttempt			int(6),
   CONSTRAINT fk_idError FOREIGN KEY (id) REFERENCES ERRORTYPE(id),
   CONSTRAINT fk_idAttempt FOREIGN KEY (idAttempt) REFERENCES Attempt(id)
);

create table ExerciseAffectation
(
   id   int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   affectationIsoDate   timestamp,
   idExo                int(6),
   idPromotion  		int(6),
   CONSTRAINT fk_idExercise FOREIGN KEY (id) REFERENCES Exercises(id),
   CONSTRAINT fk_idPromotion FOREIGN KEY (id) REFERENCES Promotions(id)
);

create table TranscriptionType
(
   id       int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   name     varchar(25)
);

