CREATE DATABASE IF NOT EXISTS library_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_portal;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS uploads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(255) NOT NULL,
  checksum CHAR(64) NOT NULL,
  uploaded_by INT,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS records (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  title TEXT NOT NULL,
  author VARCHAR(255),
  issn VARCHAR(50),
  subject VARCHAR(255),
  department VARCHAR(255),
  publisher VARCHAR(255),
  type VARCHAR(100),
  link TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_title_author_issn (title(150), author(100), issn(30))
) ENGINE=InnoDB;

-- Sample admin: username 'admin' with password 'ChangeMe123!'
-- Replace hash if you want a different password before importing init.sql
INSERT INTO admins (username, password_hash) VALUES ('admin', '$2y$10$KbQiWqQyE7c1e8Vh3aZ9uOqU1YfP0wM5Q6s2m8Zr4l1t6GqH3y2a');
