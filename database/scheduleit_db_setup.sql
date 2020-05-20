-- Clean up old tables
DROP TABLE IF EXISTS `scheduleit_AdminList`;
DROP TABLE IF EXISTS `scheduleit_Post`;
DROP TABLE IF EXISTS `scheduleit_Reservation`;
DROP TABLE IF EXISTS `scheduleit_Invite`;
DROP TABLE IF EXISTS `scheduleit_Slot`;
DROP TABLE IF EXISTS `scheduleit_Event`;
DROP TABLE IF EXISTS `scheduleit_User`;

-- Create new tables
-- scheduleit_User Table: Hold information about users who are in the system
CREATE TABLE `scheduleit_User`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`onidID` VARCHAR(255) NOT NULL,
`firstName` VARCHAR(255),
`lastName`  VARCHAR(255),
`email`  VARCHAR(255),
PRIMARY KEY (`id`),
UNIQUE KEY (`onidID`)
) ENGINE=InnoDB;

-- scheduleit_Event Table: Store information about an event a user has created.
-- An event will have at least one slot.
CREATE TABLE `scheduleit_Event`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`title`  VARCHAR(255) NOT NULL,
`description`  VARCHAR(1000),
`location` VARCHAR(255),
`dateStart` DATE NOT NULL,
`dateEnd` DATE NOT NULL,
`RSVPslotLim` INT(11) DEFAULT '1',
`creatorID` INT(11) NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (`creatorID`) REFERENCES `scheduleit_User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- scheduleit_Invite Table: Track which users have been invited to an event 
CREATE TABLE `scheduleit_Invite`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`status` ENUM('accepted','declined','no response') DEFAULT 'no response',
`receiverID` INT(11) NOT NULL,
`eventID` INT(11) NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (`receiverID`) REFERENCES `scheduleit_User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`eventID`) REFERENCES `scheduleit_Event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- scheduleit_Slot Table: Track which time slots were created for which event
CREATE TABLE `scheduleit_Slot`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`startDateTime` DATETIME NOT NULL,
`endDateTime` DATETIME NOT NULL,
`location` VARCHAR(255),
`RSVPlim` INT(11) DEFAULT '1',
`eventID` INT(11) NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (`eventID`) REFERENCES `scheduleit_Event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- scheduleit_Reservation Table: Track the many-to-many relationship of scheduleit_Invite and slot.
-- Tracks who has a reservation and for what slot
CREATE TABLE `scheduleit_Reservation`(
`inviteID` INT(11) NOT NULL,
`slotID` INT(11) NOT NULL,
PRIMARY KEY (`inviteID`,`slotID`),
FOREIGN KEY (`inviteID`) REFERENCES `scheduleit_Invite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`slotID`) REFERENCES `scheduleit_Slot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- scheduleit_Post Table: Track file uploads and messages made by users to slots
CREATE TABLE `scheduleit_Post`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`senderID` INT(11) NOT NULL,
`text` VARCHAR(1000),
`fileName` VARCHAR(255),
`slotID` INT(11) NOT NULL,
`timeStamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
FOREIGN KEY (`senderID`) REFERENCES `scheduleit_User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`slotID`) REFERENCES `scheduleit_Slot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- scheduleit_AdminList Table: List of ONID usernames who are approved for administrative privileges 
CREATE TABLE `scheduleit_AdminList`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`onidID` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY (`onidID`),
FOREIGN KEY (`onidID`) REFERENCES `scheduleit_User` (`onidID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
