CREATE DATABASE dtr_db;
USE dtr_db;

-- =========================
-- INTERNS TABLE
-- =========================
CREATE TABLE interns (
    intern_id INT AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) NOT NULL,
    first_name VARCHAR(50),
    middle_name VARCHAR(50) DEFAULT NULL,
    last_name VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    required_hours INT DEFAULT 500,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- SCHEDULE TEMPLATE (TIME ONLY - REUSABLE)
-- =========================
CREATE TABLE schedule_templates (
    template_id INT AUTO_INCREMENT PRIMARY KEY,
    start_time TIME NOT NULL,
    break_start TIME NULL,
    break_end TIME NULL,
    end_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- INTERN SCHEDULE (DAYS ONLY)
-- =========================
CREATE TABLE intern_schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    intern_id INT NOT NULL,
    template_id INT NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (intern_id) REFERENCES interns(intern_id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES schedule_templates(template_id) ON DELETE CASCADE,

    UNIQUE (intern_id, day_of_week) -- prevent duplicate schedule per day
);

-- =========================
-- DTR RECORDS
-- =========================
CREATE TABLE dtr_records (
    dtr_id INT AUTO_INCREMENT PRIMARY KEY,
    intern_id INT NOT NULL,
    work_date DATE NOT NULL,
    time_in TIME,
    time_out TIME,
    total_hours DECIMAL(5,2),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (intern_id) REFERENCES interns(intern_id) ON DELETE CASCADE
);

-- =========================
-- USERS TABLE
-- =========================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    intern_id INT,
    password_hash VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (intern_id) REFERENCES interns(intern_id) ON DELETE CASCADE
);