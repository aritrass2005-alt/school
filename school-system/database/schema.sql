-- School System schema (MySQL 8+)
CREATE DATABASE IF NOT EXISTS school_system;
USE school_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    roll INT NOT NULL,
    class INT NOT NULL,
    section VARCHAR(10) NOT NULL,
    dob DATE NOT NULL,
    year INT NOT NULL,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    CONSTRAINT fk_students_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department VARCHAR(120),
    phone VARCHAR(30),
    CONSTRAINT fk_teachers_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent') NOT NULL,
    marked_by INT NOT NULL,
    locked BOOLEAN NOT NULL DEFAULT 0,
    CONSTRAINT fk_attendance_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_attendance_marked_by
        FOREIGN KEY (marked_by) REFERENCES users(id)
        ON DELETE CASCADE,
    UNIQUE KEY uniq_attendance_student_date (student_id, date)
) ENGINE=InnoDB;

CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    file VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notices_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE routines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class INT NOT NULL,
    section VARCHAR(10) NOT NULL,
    file VARCHAR(255) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_routines_uploaded_by
        FOREIGN KEY (uploaded_by) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    exam_name VARCHAR(150) NOT NULL,
    file VARCHAR(255),
    published BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_results_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE google_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_title VARCHAR(150) NOT NULL,
    google_form_link VARCHAR(255) NOT NULL,
    class VARCHAR(20),
    section VARCHAR(10),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_google_forms_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin user (password: 123456)
INSERT INTO users (name, email, password, role, status)
VALUES ('Admin', 'admin@mail.com', '$2y$12$L245.63wg1DA2c5SiQOvE.k1mYxVrvD/d8hBS2UJTn4yOBQ16k48m', 'admin', 'active');
