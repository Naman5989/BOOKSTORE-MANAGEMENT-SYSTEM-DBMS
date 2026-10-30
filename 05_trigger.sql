-- =====================================================
-- 05_trigger.sql
-- Trigger: Automatically reduce book stock after an order
-- =====================================================

USE bookstore;

DELIMITER //

-- ---------------------------------------------------
-- Trigger Name : trg_reduce_stock_after_order
-- Purpose      : Whenever a new row is inserted into
--                Order_Details, reduce the Stock of the
--                corresponding book by the quantity ordered.
-- Fires        : AFTER INSERT ON Order_Details
-- ---------------------------------------------------

CREATE TRIGGER trg_reduce_stock_after_order
AFTER INSERT ON Order_Details
FOR EACH ROW
BEGIN
    UPDATE Books
    SET Stock = Stock - NEW.Quantity
    WHERE Book_ID = NEW.Book_ID;
END //

DELIMITER ;

-- ---------------------------------------------------
-- HOW IT WORKS / DEMONSTRATION
-- ---------------------------------------------------
-- Before: Book_ID = 3 ('Harry Potter and the Stone') has Stock = 15
--
-- SELECT Stock FROM Books WHERE Book_ID = 3;   -- 15
--
-- INSERT INTO Orders (User_ID, Total_Amount, Status)
-- VALUES (2, 998.00, 'Pending');
--
-- INSERT INTO Order_Details (Order_ID, Book_ID, Quantity, Price)
-- VALUES (LAST_INSERT_ID(), 3, 2, 499.00);
--
-- -- Trigger fires automatically here
--
-- SELECT Stock FROM Books WHERE Book_ID = 3;   -- 13 (18 - 2)
