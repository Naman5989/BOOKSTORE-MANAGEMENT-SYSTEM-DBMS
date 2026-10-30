-- =====================================================
-- 06_procedure.sql
-- PL/SQL-style Procedure and Function (MySQL syntax)
-- =====================================================

USE bookstore;

-- =====================================================
-- PROCEDURE: sp_calculate_order_total
-- Purpose  : Given an Order_ID, calculate and update the
--            Total_Amount for that order in the Orders table.
-- Params   : IN p_order_id  -> the order to calculate for
--            OUT p_total    -> the calculated total returned to caller
-- =====================================================

DELIMITER //

CREATE PROCEDURE sp_calculate_order_total (
    IN  p_order_id INT,
    OUT p_total    DECIMAL(10,2)
)
BEGIN
    SELECT SUM(Quantity * Price) INTO p_total
    FROM Order_Details
    WHERE Order_ID = p_order_id;

    UPDATE Orders
    SET Total_Amount = p_total
    WHERE Order_ID = p_order_id;
END //

DELIMITER ;

-- ---------------------------------------------------
-- Calling the procedure
-- ---------------------------------------------------
-- CALL sp_calculate_order_total(1, @total);
-- SELECT @total AS Order_Total;


-- =====================================================
-- FUNCTION: fn_books_by_category
-- Purpose  : Given a Category_ID, return the count of
--            books available in that category.
-- Params   : p_category_id -> category to search within
-- Returns  : INT -> number of books in that category
-- =====================================================

DELIMITER //

CREATE FUNCTION fn_books_by_category (p_category_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE book_count INT;

    SELECT COUNT(*) INTO book_count
    FROM Books
    WHERE Category_ID = p_category_id;

    RETURN book_count;
END //

DELIMITER ;

-- ---------------------------------------------------
-- Calling the function
-- ---------------------------------------------------
-- SELECT fn_books_by_category(3) AS Fantasy_Book_Count;
