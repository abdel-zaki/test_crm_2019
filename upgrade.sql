
DROP DATABASE IF EXISTS `crm`;
CREATE DATABASE `crm`;

USE `crm`;

-- Sql - table users

DROP TABLE IF EXISTS `addresses`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Sql - table contacts

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(225) NOT NULL,
  `prenom` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Sql - table addresses

CREATE TABLE IF NOT EXISTS `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(4) NOT NULL,
  `street` varchar(225) NOT NULL,
  `postalCode` int(6) NOT NULL,
  `city` varchar(225) NOT NULL,
  `country` varchar(225) NOT NULL,
  `idContact` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idContact` (`idContact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Sql - contraintes

ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`idContact`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

-- Sql - Indexes pour la table `users`

ALTER TABLE users
    ADD UNIQUE index_users_id (id);
ALTER TABLE users
    ADD FULLTEXT index_users_login (login);
ALTER TABLE users
    ADD FULLTEXT index_users_email (email);
ALTER TABLE users
    ADD FULLTEXT index_users_password (password);

-- Sql - Indexes pour la table `contacts`

ALTER TABLE contacts
    ADD UNIQUE index_contacts_id (id);
ALTER TABLE contacts
    ADD FULLTEXT index_contacts_nom (nom);
ALTER TABLE contacts
    ADD FULLTEXT index_contacts_prenom (prenom);
ALTER TABLE contacts
    ADD FULLTEXT index_contacts_nom_prenom (nom, prenom);
ALTER TABLE contacts
    ADD FULLTEXT index_contacts_email (email);
ALTER TABLE contacts
    ADD INDEX index_contacts_userid (userId);

-- Sql - Indexes pour la table `addresses`

ALTER TABLE addresses
    ADD UNIQUE index_addresses_id (id);
ALTER TABLE addresses
    ADD INDEX index_addresses_number (number);
ALTER TABLE addresses
    ADD FULLTEXT index_addresses_street (street);
ALTER TABLE addresses
    ADD INDEX index_addresses_nom_postalCode (postalCode);
ALTER TABLE addresses
    ADD FULLTEXT index_addresses_city (city);
ALTER TABLE addresses
    ADD FULLTEXT index_addresses_country (country);
ALTER TABLE addresses
    ADD INDEX index_addresses_idContact (idContact);

-- Sql - initialisation user de test

INSERT INTO `users` (`id`, `login`, `email`, `password`) VALUES
(1, 'admin', 'lebonoin@test.fr', '21232f297a57a5a743894a0e4a801fc3');
COMMIT;
