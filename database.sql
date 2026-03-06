CREATE DATABASE geeta_enterprises;
USE geeta_enterprises;

CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
INSERT INTO admin (username, password) VALUES ('admin', SHA2('admin123', 256));

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    unit VARCHAR(20) NOT NULL
);
INSERT INTO categories (name, unit) VALUES 
('Glass', 'SQFT'),
('Aluminum', 'PCS'),
('Hardware', 'PCS');

CREATE TABLE stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    min_stock DECIMAL(10,2) DEFAULT 5,
    supplier VARCHAR(100),
    purchase_date DATE,
    purchase_amount DECIMAL(10,2),
    pending_bill DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE workers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(50),
    phone VARCHAR(15),
    aadhar VARCHAR(20),
    pan VARCHAR(20),
    bank_details TEXT,
    document_path VARCHAR(255),
    monthly_salary DECIMAL(10,2) DEFAULT 0,
    joined_date DATE
);

CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    worker_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present','Absent') DEFAULT 'Absent',
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (worker_id, attendance_date)
);

CREATE TABLE advances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    worker_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    advance_date DATE NOT NULL,
    note TEXT,
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
);

CREATE TABLE salary_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    worker_id INT NOT NULL,
    year INT NOT NULL,
    month INT NOT NULL,
    present_days INT,
    absent_days INT,
    gross_salary DECIMAL(10,2),
    advance_deduction DECIMAL(10,2),
    net_paid DECIMAL(10,2),
    payment_date DATE,
    status ENUM('Paid','Pending') DEFAULT 'Pending',
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
);

CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    phone VARCHAR(15),
    email VARCHAR(100),
    gst VARCHAR(50),
    address TEXT
);

CREATE TABLE bills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_no VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    customer_gst VARCHAR(50),
    bill_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2),
    gst_amount DECIMAL(10,2),
    total DECIMAL(10,2),
    qr_code TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

CREATE TABLE bill_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_id INT NOT NULL,
    stock_id INT NOT NULL,
    quantity DECIMAL(10,2),
    price DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    FOREIGN KEY (stock_id) REFERENCES stock(id)
);

CREATE TABLE quotations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quote_no VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    quote_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    valid_till DATE,
    subtotal DECIMAL(10,2),
    gst_amount DECIMAL(10,2),
    total DECIMAL(10,2),
    converted_to_bill INT DEFAULT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (converted_to_bill) REFERENCES bills(id) ON DELETE SET NULL
);

CREATE TABLE quotation_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_id INT NOT NULL,
    stock_id INT NOT NULL,
    quantity DECIMAL(10,2),
    price DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE,
    FOREIGN KEY (stock_id) REFERENCES stock(id)
);

CREATE TABLE sites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    contact_person VARCHAR(100),
    contact_phone VARCHAR(15),
    start_date DATE,
    estimated_amount DECIMAL(10,2),
    balance_amount DECIMAL(10,2),
    status ENUM('Ongoing','Completed','Pending') DEFAULT 'Ongoing'
);

CREATE TABLE site_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    site_id INT NOT NULL,
    amount DECIMAL(10,2),
    payment_date DATE,
    note TEXT,
    FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
);

CREATE TABLE purchases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stock_id INT NOT NULL,
    quantity DECIMAL(10,2),
    purchase_price DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    supplier VARCHAR(100),
    purchase_date DATE,
    payment_status ENUM('Paid','Pending') DEFAULT 'Pending',
    FOREIGN KEY (stock_id) REFERENCES stock(id)
);