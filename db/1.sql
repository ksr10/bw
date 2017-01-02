CREATE TABLE `championship` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `applicant_group` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `created` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `applicant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `championship` int(10) NOT NULL,
  `host_team` VARCHAR(255) NOT NULL,
  `guest_team` VARCHAR(255) NOT NULL,
  `ratio` VARCHAR(255) NOT NULL,
  `bet` VARCHAR(255) NOT NULL,
  `status` ENUM('new', 'wait_select', 'wait_result', 'finished') DEFAULT 'new' NULL,
  `selected` int(10) NOT NULL,
  `group` int(10) NOT NULL,
  `result` VARCHAR(255) NULL,
  `success` int(10) NULL,
  `date` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `applicant` CHANGE `date` `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;