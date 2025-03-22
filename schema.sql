CREATE DATABASE IF NOT EXISTS myproject;
USE myproject;

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    avatar VARCHAR(255),
    type VARCHAR(50)
);

CREATE TABLE post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uploader VARCHAR(255) NOT NULL,
    content TEXT,
    post_time DATETIME
);

CREATE TABLE upload (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uploader VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    size INT,
    type VARCHAR(225),
    upload_time DATETIME
);

CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    due_date DATE,
    uploader VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL
);

CREATE TABLE assigned_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_username VARCHAR(255) NOT NULL,
    assignment_id INT
);

CREATE TABLE submitted_assignments (
    assignment_id INT,
    uploader VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    upload_time DATETIME
);

CREATE TABLE challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_name VARCHAR(255) NOT NULL,
    hint VARCHAR(255), 
    uploader VARCHAR(255) NOT NULL, 
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    answer VARCHAR(255) NOT NULL,
    content TEXT
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES user(id),
    FOREIGN KEY (receiver_id) REFERENCES user(id)
);

-- hai tài khoản giáo viên và hai tài khoản sinh viên 
-- tài khoản giáo viên: teacher1 / 123456a@A ; teacher2 / 123456a@A
-- tài khoản sinh viên: student1 / 123456a@A ; student2 / 123456a@A).
-- mã hóa mật khẩu thành dạng md5
-- Tài khoản giáo viên
INSERT INTO user (username, name, password, email, phone, type)
VALUES ('teacher1', 'Teacher 1', 'f83e69e4170a786e44e3d32a2479cce9', 'teacher1@example.com', '111','admin'),
       ('teacher2', 'Teacher 2', 'f83e69e4170a786e44e3d32a2479cce9', 'teacher2@example.com', '123','admin');

-- Tài khoản sinh viên
INSERT INTO user (username, name, password, email,phone, type)
VALUES ('student1', 'Student 1', 'f83e69e4170a786e44e3d32a2479cce9', 'student1@example.com','122222', 'user'),
       ('student2', 'Student 2', 'f83e69e4170a786e44e3d32a2479cce9', 'student2@example.com','112222222', 'user');
