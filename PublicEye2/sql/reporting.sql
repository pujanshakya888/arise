CREATE TABLE reporting (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  report_text TEXT NOT NULL,
  report_image VARCHAR(255) NULL,
  report_video VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
