-- =====================================================
-- 02_ddl.sql
-- DDL: CREATE TABLE statements with constraints
-- Online Bookstore Management System
-- =====================================================

USE bookstore;

-- ---------------------------------------------------
-- 1. USERS
-- ---------------------------------------------------
CREATE TABLE Users (
    User_ID       INT AUTO_INCREMENT PRIMARY KEY,
    Name          VARCHAR(100) NOT NULL,
    Email         VARCHAR(100) NOT NULL UNIQUE,
    Password      VARCHAR(255) NOT NULL,
    Phone         VARCHAR(15)  NOT NULL,
    Role          ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    Created_At    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (Phone REGEXP '^[0-9]{10}$')
);

-- ---------------------------------------------------
-- 2. AUTHORS
-- ---------------------------------------------------
CREATE TABLE Authors (
    Author_ID     INT AUTO_INCREMENT PRIMARY KEY,
    Author_Name   VARCHAR(100) NOT NULL
);

-- ---------------------------------------------------
-- 3. CATEGORIES
-- ---------------------------------------------------
CREATE TABLE Categories (
    Category_ID     INT AUTO_INCREMENT PRIMARY KEY,
    Category_Name   VARCHAR(100) NOT NULL UNIQUE
);

-- ---------------------------------------------------
-- 4. BOOKS
-- ---------------------------------------------------
CREATE TABLE Books (
    Book_ID       INT AUTO_INCREMENT PRIMARY KEY,
    Title         VARCHAR(200) NOT NULL,
    ISBN          VARCHAR(20)  NOT NULL UNIQUE,
    Author_ID     INT NOT NULL,
    Category_ID   INT NOT NULL,
    Price         DECIMAL(10,2) NOT NULL,
    Stock         INT NOT NULL DEFAULT 0,
    Cover_Image_URL VARCHAR(500) DEFAULT '',
    Description   TEXT,
    CHECK (Price >= 0),
    CHECK (Stock >= 0),
    FOREIGN KEY (Author_ID)   REFERENCES Authors(Author_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Category_ID) REFERENCES Categories(Category_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ---------------------------------------------------
-- 5. ORDERS
-- ---------------------------------------------------
CREATE TABLE Orders (
    Order_ID      INT AUTO_INCREMENT PRIMARY KEY,
    User_ID       INT NOT NULL,
    Order_Date    DATETIME DEFAULT CURRENT_TIMESTAMP,
    Total_Amount  DECIMAL(10,2) NOT NULL DEFAULT 0,
    Status        ENUM('Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled')
                  NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ---------------------------------------------------
-- 6. ORDER_DETAILS  (weak entity: composite PK)
-- ---------------------------------------------------
CREATE TABLE Order_Details (
    Order_ID      INT NOT NULL,
    Book_ID       INT NOT NULL,
    Quantity      INT NOT NULL,
    Price         DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (Order_ID, Book_ID),
    CHECK (Quantity > 0),
    FOREIGN KEY (Order_ID) REFERENCES Orders(Order_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Book_ID)  REFERENCES Books(Book_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ---------------------------------------------------
-- 7. PAYMENT
-- ---------------------------------------------------
CREATE TABLE Payment (
    Payment_ID      INT AUTO_INCREMENT PRIMARY KEY,
    Order_ID        INT NOT NULL,
    Payment_Date    DATETIME DEFAULT CURRENT_TIMESTAMP,
    Amount          DECIMAL(10,2) NOT NULL,
    Payment_Method  ENUM('Card', 'UPI', 'NetBanking', 'COD') NOT NULL,
    Payment_Status  ENUM('Pending', 'Success', 'Failed') NOT NULL DEFAULT 'Pending',
    Transaction_Ref VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (Order_ID) REFERENCES Orders(Order_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
);
