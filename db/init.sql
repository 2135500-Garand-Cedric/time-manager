-- init.sql: create tables and a few sample activities
CREATE DATABASE IF NOT EXISTS timemanager;
USE timemanager;


CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME DEFAULT NULL,
    duration_seconds INT DEFAULT NULL,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
);


-- sample activities
INSERT INTO activities (name) VALUES
    ('Work'),('Study'),('Exercise'),('Break');