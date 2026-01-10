-- Safely recreate user_feedback with FK that matches users.id (signed INT(11))
DROP TABLE IF EXISTS `user_feedback`;

CREATE TABLE `user_feedback` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_agent` VARCHAR(512) DEFAULT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `handled_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `user_feedback`
  ADD CONSTRAINT `FK_user_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_feedback_handledby` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add unread flag and acknowledged timestamp to user_feedback
ALTER TABLE `user_feedback`
  ADD COLUMN `user_unread` TINYINT(1) NOT NULL DEFAULT 0 AFTER `handled_by`,
  ADD COLUMN `acknowledged_at` DATETIME DEFAULT NULL AFTER `user_unread`;