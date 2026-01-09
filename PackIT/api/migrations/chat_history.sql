
CREATE TABLE `chat_history` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `session_id` varchar(128) NOT NULL,
  `user_id` int DEFAULT NULL,
  `prompt` text NOT NULL,
  `response` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user` (`user_id`),
  KEY `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;