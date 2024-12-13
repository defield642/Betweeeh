CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    phonenumber VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    otp INT
);