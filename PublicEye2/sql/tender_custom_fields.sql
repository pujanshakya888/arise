CREATE TABLE tender_custom_fields (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tender_user_id INT NOT NULL,
  field_label VARCHAR(255) NOT NULL,
  field_value TEXT NULL,
  FOREIGN KEY (tender_user_id) REFERENCES tender_users(id) ON DELETE CASCADE
);
