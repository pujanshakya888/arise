CREATE TABLE tender_form_data (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tender_user_id INT NOT NULL,
  field_label VARCHAR(255) NOT NULL,
  field_value TEXT,
  FOREIGN KEY (tender_user_id) REFERENCES tender_users(id) ON DELETE CASCADE
);
