-- =====================================================
-- 04_dcl.sql
-- DCL: User creation and privilege management
-- =====================================================

USE bookstore;

-- ---------------------------------------------------
-- CREATE DATABASE USERS
-- ---------------------------------------------------

CREATE USER IF NOT EXISTS 'admin'@'localhost'
    IDENTIFIED BY 'admin123';

CREATE USER IF NOT EXISTS 'normaluser'@'localhost'
    IDENTIFIED BY 'user123';

-- ---------------------------------------------------
-- GRANT PRIVILEGES
-- ---------------------------------------------------

-- Admin gets full control over the database
GRANT ALL PRIVILEGES
ON bookstore.*
TO 'admin'@'localhost';

-- Normal user can only read, insert, and update (no delete)
GRANT SELECT, INSERT, UPDATE
ON bookstore.*
TO 'normaluser'@'localhost';

-- Explicitly restrict normal user from deleting records
REVOKE DELETE
ON bookstore.*
FROM 'normaluser'@'localhost';

FLUSH PRIVILEGES;

-- ---------------------------------------------------
-- REVOKE EXAMPLE (demonstration)
-- ---------------------------------------------------

-- If needed, admin privileges can be revoked like this:
-- REVOKE ALL PRIVILEGES ON bookstore.* FROM 'admin'@'localhost';
