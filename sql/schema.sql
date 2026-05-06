CREATE TABLE page (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT,
    content TEXT
);

INSERT INTO page (title, content)
VALUES ('Page 1', 'Hello from Docker Secrets');