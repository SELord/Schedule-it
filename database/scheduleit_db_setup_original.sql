-- Clean up old tables
DROP TABLE IF EXISTS `AdminList`;
DROP TABLE IF EXISTS `Post`;
DROP TABLE IF EXISTS `Reservation`;
DROP TABLE IF EXISTS `Invite`;
DROP TABLE IF EXISTS `Slot`;
DROP TABLE IF EXISTS `Event`;
DROP TABLE IF EXISTS `User`;

-- Create new tables
-- User Table: Hold information about users who are in the system
CREATE TABLE `User`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`onidUID` VARCHAR(255) NOT NULL,
`firstName` VARCHAR(255),
`lastName`  VARCHAR(255),
`email`  VARCHAR(255),
PRIMARY KEY (`id`),
UNIQUE KEY (`onidUID`)
) ENGINE=InnoDB;

-- Event Table: Store information about an event a user has created.
-- An event will have at least one slot.
CREATE TABLE `Event`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`title`  VARCHAR(255) NOT NULL,
`description`  VARCHAR(1000),
`dateStartTime` DATETIME NOT NULL,
`dateEndTime` DATETIME NOT NULL,
`duration` TIME NOT NULL,
`RSVPslotLim` INT(11) DEFAULT '1',
`creatorID` INT(11) NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (`creatorID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Invite Table: Track which users have been invited to an event 
CREATE TABLE `Invite`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`email`  VARCHAR(255) NOT NULL,
`status` ENUM('accepted','declined','no response') DEFAULT 'no response',
`receiverID` INT(11) NOT NULL,
`eventID` INT(11) NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (`receiverID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`eventID`) REFERENCES `Event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Slot Table: Track which time slots were created for which event
CREATE TABLE `Slot`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`startTime` TIME NOT NULL,
`duration` TIME NOT NULL,
`location` VARCHAR(255),
`RSVPlim` INT(11) DEFAULT '1',
`eventID` INT(11) NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (`eventID`) REFERENCES `Event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Reservation Table: Track the many-to-many relationship of invite and slot.
-- Tracks who has a reservation and for what slot
CREATE TABLE `Reservation`(
`inviteID` INT(11) NOT NULL,
`slotID` INT(11) NOT NULL,
PRIMARY KEY (`inviteID`,`slotID`),
FOREIGN KEY (`inviteID`) REFERENCES `Invite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`slotID`) REFERENCES `Slot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Post Table: Track file uploads and messages made by users to slots
CREATE TABLE `Post`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`senderID` INT(11) NOT NULL,
`text` VARCHAR(1000),
`fileName` VARCHAR(255),
`slotID` INT(11) NOT NULL,
`timeStamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
FOREIGN KEY (`senderID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`slotID`) REFERENCES `Slot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- AdminList Table: List of ONID usernames who are approved for administrative privileges 
CREATE TABLE `AdminList`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`onidUID` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY (`onidUID`),
FOREIGN KEY (`onidUID`) REFERENCES `User` (`onidUID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
