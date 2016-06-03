CREATE TABLE `leagues` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,  
  `country` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ratios` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` INT(11) NOT NULL,
  `league_id` INT(11) NOT NULL,
  `host_team` VARCHAR(255) NOT NULL,
  `host_team_odds` DECIMAL(12,4) NOT NULL,
  `draw_odds` DECIMAL(12,4) NOT NULL,
  `guest_team` VARCHAR(255) NOT NULL,
  `guest_team_odds` DECIMAL(12,4) NOT NULL,
  `event_date` timestamp NOT NULL,  
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `rules` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,  
  `description` TEXT NULL,  
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `virtual_users` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,  
  `rule_id` INT(11) NOT NULL,
  `total_amount` DECIMAL(12,4) NOT NULL,
  `total_origin` DECIMAL(12,4) NOT NULL,
  `status` ENUM('active', 'lost') DEFAULT 'active' NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bets` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `rule_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `ratio_id` INT(11) NOT NULL,
  `bet_amount` DECIMAL(12,4) NOT NULL,
  `result` INT(11) NOT NULL,
  `event_data` TEXT,  
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;