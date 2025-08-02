CREATE TABLE tender_project_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tender_user_id INT NOT NULL,
  post_text TEXT NOT NULL,
  post_image VARCHAR(255),
  post_video VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tender_user_id) REFERENCES tender_users(id) ON DELETE CASCADE
);
