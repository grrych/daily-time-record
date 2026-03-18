CREATE DATABASE dtr_db;
USE dtr_db;

CREATE TABLE interns (
    `intern_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` VARCHAR(20) NOT NULL,
    `first_name` VARCHAR(50),
    `middle_name` VARCHAR(50) DEFAULT NULL,
    `last_name` VARCHAR(50),
    `email` VARCHAR(100) UNIQUE,
    `required_hours` INT DEFAULT 500,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE schedules (
    `schedule_id` INT AUTO_INCREMENT PRIMARY KEY,
    `intern_id` INT,
    `day_of_week` ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
    `start_time` TIME,
    `break_start` TIME NULL,
    `break_end` TIME NULL,
    `end_time` TIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (intern_id) REFERENCES interns(intern_id)
);

CREATE TABLE dtr_records (
    `dtr_id` INT AUTO_INCREMENT PRIMARY KEY,
    `intern_id` INT,
    `work_date` DATE,
    `time_in` TIME,
    `time_out` TIME,
    `total_hours` DECIMAL(5,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (intern_id) REFERENCES interns(intern_id)
);

CREATE TABLE users (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `intern_id` INT,
    `password_hash` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (intern_id) REFERENCES interns(intern_id)
);