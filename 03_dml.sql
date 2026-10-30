-- =====================================================
-- 03_dml.sql
-- DML: INSERT, UPDATE, DELETE, SEARCH demonstrations
-- Online Bookstore Management System (50+ Books & Payments)
-- =====================================================

USE bookstore;

-- ---------------------------------------------------
-- 1. USERS
-- ---------------------------------------------------
INSERT INTO Users (Name, Email, Password, Phone, Role) VALUES
('Admin User', 'admin@bookstore.com', '$2y$10$ykyXAC4phXff9JoTtDvZjeYxdrtZp17cX76mvc15n498rnfANa97a', '9876543210', 'admin'),
('Naman Sharma', 'naman@example.com', '$2y$10$8ge3Th0fXSguVcTKr.OU/eUCaTLPV4rSyYi6z86zopLwLeaA9CF06', '9000011111', 'customer'),
('Riya Verma', 'riya@example.com', '$2y$10$FzGz6vW4SU7DtofGW6x8IOt5hodjAykx13ZJ65BUMGoU7fO6mzNga', '9988776655', 'customer'),
('Naman Verma', 'vnaman5989@gmail.com', '$2y$10$dU42j9pEeVxndu9X3B9t5uAedpD04j8wyM9qReUHbNguTSUX3oiBW', '2732894384', 'customer');

-- ---------------------------------------------------
-- 2. AUTHORS
-- ---------------------------------------------------
INSERT INTO Authors (Author_ID, Author_Name) VALUES
(1, 'R.K. Narayan'),
(2, 'Chetan Bhagat'),
(3, 'J.K. Rowling'),
(4, 'R.K. Narayan'),
(5, 'Chetan Bhagat'),
(6, 'J.K. Rowling'),
(7, 'George Orwell'),
(8, 'Harper Lee'),
(9, 'F. Scott Fitzgerald'),
(10, 'Jane Austen'),
(11, 'J.R.R. Tolkien'),
(12, 'Agatha Christie'),
(13, 'Stephen King'),
(14, 'Arthur Conan Doyle'),
(15, 'Isaac Asimov'),
(16, 'Frank Herbert'),
(17, 'Dan Brown'),
(18, 'Yuval Noah Harari'),
(19, 'Walter Isaacson'),
(20, 'Paulo Coelho'),
(21, 'Franz Kafka'),
(22, 'Leo Tolstoy'),
(23, 'Gabriel García Márquez'),
(24, 'Dale Carnegie'),
(25, 'James Clear'),
(26, 'Robert C. Martin'),
(27, 'Robert T. Kiyosaki'),
(28, 'Morgan Housel'),
(29, 'Mark Manson'),
(30, 'Mary Shelley'),
(31, 'Bram Stoker'),
(32, 'Charles Dickens'),
(33, 'Sun Tzu'),
(34, 'Marcus Aurelius'),
(35, 'C.S. Lewis'),
(36, 'Khaled Hosseini'),
(37, 'Amish Tripathi'),
(38, 'Ruskin Bond'),
(39, 'Arundhati Roy'),
(40, 'A.P.J. Abdul Kalam'),
(41, 'Malcolm Gladwell');

-- ---------------------------------------------------
-- 3. CATEGORIES
-- ---------------------------------------------------
INSERT INTO Categories (Category_ID, Category_Name) VALUES
(1, 'Fiction'),
(3, 'Fantasy'),
(5, 'Non-Fiction'),
(7, 'Science Fiction'),
(8, 'Mystery & Thriller'),
(9, 'Self-Help & Personal Growth'),
(10, 'Biography & Memoir'),
(11, 'History & Philosophy'),
(12, 'Business & Finance'),
(13, 'Computer Science & Tech'),
(14, 'Classic Literature');

-- ---------------------------------------------------
-- 4. BOOKS (50+ curated titles with covers & descriptions)
-- ---------------------------------------------------
INSERT INTO Books (Title, ISBN, Author_ID, Category_ID, Price, Stock, Cover_Image_URL, Description) VALUES
('Malgudi Days', '9780140056509', 1, 1, 275.00, 20, '', ''),
('Five Point Someone', '9788129135728', 2, 1, 199.00, 29, '', ''),
('Harry Potter and the Stone', '9780747532699', 3, 3, 499.00, 15, '', ''),
('1984', '9780451524935', 7, 7, 299.00, 34, 'https://covers.openlibrary.org/b/isbn/9780451524935-M.jpg', 'Winston Smith toes the Party line, rewriting history to satisfy the demands of the Ministry of Truth. With every lie he writes, he grows to hate the Party that seeks power for its own sake.'),
('Animal Farm', '9780451526342', 7, 14, 199.00, 40, 'https://covers.openlibrary.org/b/isbn/9780451526342-M.jpg', 'A farm is taken over by its overworked, mistreated animals. With flaming idealism and stirring slogans, they set out to create a paradise of progress, justice, and equality.'),
('To Kill a Mockingbird', '9780060935467', 8, 14, 349.00, 25, 'https://covers.openlibrary.org/b/isbn/9780060935467-M.jpg', 'The unforgettable novel of a childhood in a sleepy Southern town and the crisis of conscience that rocked it, compassionate and deeply moving.'),
('The Great Gatsby', '9780743273565', 9, 14, 280.00, 30, 'https://covers.openlibrary.org/b/isbn/9780743273565-M.jpg', 'A true classic of twentieth-century literature and one of the greatest novels of the Jazz Age, telling the story of the fabulously wealthy Jay Gatsby and his love for Daisy Buchanan.'),
('Pride and Prejudice', '9780141439518', 10, 14, 220.00, 44, 'https://covers.openlibrary.org/b/isbn/9780141439518-M.jpg', 'The romantic clash between the opinionated Elizabeth and her proud beau, Mr. Darcy, is a splendid performance of civilized sparring.'),
('Sense and Sensibility', '9780141439662', 10, 14, 240.00, 20, 'https://covers.openlibrary.org/b/isbn/9780141439662-M.jpg', 'Follows the lives and loves of the Dashwood sisters Elinor and Marianne as they navigate society, fortune, and heartbreak.'),
('The Hobbit', '9780547928227', 11, 3, 450.00, 49, 'https://covers.openlibrary.org/b/isbn/9780547928227-M.jpg', 'Bilbo Baggins is a hobbit who enjoys a comfortable and unambitious life, until Gandalf the wizard and a company of dwarves arrive on his threshold.'),
('The Fellowship of the Ring', '9780547928210', 11, 3, 520.00, 35, 'https://covers.openlibrary.org/b/isbn/9780547928210-M.jpg', 'The first volume in The Lord of the Rings, following Frodo Baggins as he embarks on an epic quest across Middle-earth to destroy the One Ring.'),
('The Two Towers', '9780547928203', 11, 3, 520.00, 28, 'https://covers.openlibrary.org/b/isbn/9780547928203-M.jpg', 'The Fellowship has been broken. Frodo and Sam continue toward Mordor alone, while Aragorn, Legolas, and Gimli track Merry and Pippin.'),
('The Return of the King', '9780547928197', 11, 3, 550.00, 22, 'https://covers.openlibrary.org/b/isbn/9780547928197-M.jpg', 'The culmination of the grand epic: the armies of the Dark Lord gather, and the destiny of Middle-earth rests upon Frodo and Sam approaching Mount Doom.'),
('And Then There Were None', '9780062073488', 12, 8, 299.00, 30, 'https://covers.openlibrary.org/b/isbn/9780062073488-M.jpg', 'Ten strangers are invited to an isolated island mansion. One by one, the guests are murdered in accordance with an eerie nursery rhyme.'),
('Murder on the Orient Express', '9780062693662', 12, 8, 320.00, 25, 'https://covers.openlibrary.org/b/isbn/9780062693662-M.jpg', 'Just after midnight, a snowdrift stops the Orient Express in its tracks. By morning, an American tycoon lies dead, stabbed a dozen times behind a locked door.'),
('The ABC Murders', '9780062073587', 12, 8, 280.00, 18, 'https://covers.openlibrary.org/b/isbn/9780062073587-M.jpg', 'A serial killer is murdering people in alphabetical order, leaving an ABC Railway Guide by each corpse. Hercule Poirot takes the case.'),
('The Shining', '9780307743657', 13, 8, 450.00, 20, 'https://covers.openlibrary.org/b/isbn/9780307743657-M.jpg', 'Jack Torrance accepts a winter caretaker position at the atmospheric Overlook Hotel, but sinister forces begin to manifest as snow cuts them off.'),
('Misery', '9781501143106', 13, 8, 399.00, 15, 'https://covers.openlibrary.org/b/isbn/9781501143106-M.jpg', 'Novelist Paul Sheldon crashes in a blizzard and is rescued by Annie Wilkes, his number one fan, whose devotion turns into a nightmare of captive torture.'),
('It', '9781501142970', 13, 1, 599.00, 14, 'https://covers.openlibrary.org/b/isbn/9781501142970-M.jpg', 'In Derry, Maine, seven teenagers discovered an unspeakable evil lurking beneath the town sewers. Decades later, they are called back to confront it once more.'),
('The Adventures of Sherlock Holmes', '9780140437713', 14, 8, 260.00, 30, 'https://covers.openlibrary.org/b/isbn/9780140437713-M.jpg', 'Twelve iconic tales featuring the world legendary consulting detective Sherlock Holmes and his steadfast partner Dr. John Watson.'),
('The Hound of the Baskervilles', '9780141439976', 14, 8, 230.00, 25, 'https://covers.openlibrary.org/b/isbn/9780141439976-M.jpg', 'Sherlock Holmes investigates the eerie curse of a gigantic spectral hound haunting the desolate moors of Devonshire.'),
('Foundation', '9780553293357', 15, 7, 380.00, 32, 'https://covers.openlibrary.org/b/isbn/9780553293357-M.jpg', 'Hari Seldon uses psychohistory to foresee the imminent collapse of the Galactic Empire and creates the Foundation to preserve human knowledge.'),
('I, Robot', '9780553382563', 15, 7, 350.00, 27, 'https://covers.openlibrary.org/b/isbn/9780553382563-M.jpg', 'The pioneering collection of stories introducing the Three Laws of Robotics and exploring the future relationship between mankind and machines.'),
('Dune', '9780441172719', 16, 7, 499.00, 45, 'https://covers.openlibrary.org/b/isbn/9780441172719-M.jpg', 'Set on the desert planet Arrakis, Dune is the story of the boy Paul Atreides, heir to a noble family tasked with ruling an inhospitable world.'),
('Dune Messiah', '9780441172696', 16, 7, 420.00, 20, 'https://covers.openlibrary.org/b/isbn/9780441172696-M.jpg', 'Twelve years after Paul Atreides seized emperor status, religious fanatics run rampant while secret factions plot his overthrow.'),
('The Da Vinci Code', '9780307474278', 17, 8, 370.00, 38, 'https://covers.openlibrary.org/b/isbn/9780307474278-M.jpg', 'Symbologist Robert Langdon and cryptologist Sophie Neveu unravel a breathless trail of clues embedded in Leonardo da Vinci masterpieces.'),
('Angels & Demons', '9781416524793', 17, 8, 350.00, 30, 'https://covers.openlibrary.org/b/isbn/9781416524793-M.jpg', 'An ancient secret brotherhood, a devastating antimatter weapon, and a high-stakes hunt across subterranean crypts and cathedrals of Rome.'),
('Sapiens: A Brief History of Humankind', '9780062316097', 18, 5, 499.00, 45, 'https://covers.openlibrary.org/b/isbn/9780062316097-M.jpg', 'Explore how an insignificant ape became the ruler of planet Earth, capable of splitting the atom, flying to the moon, and manipulating genetic code.'),
('Homo Deus: A Brief History of Tomorrow', '9780062464316', 18, 5, 480.00, 30, 'https://covers.openlibrary.org/b/isbn/9780062464316-M.jpg', 'Investigates the projects, dreams, and nightmares that will shape humanity in the twenty-first century, from overcoming death to artificial intelligence.'),
('21 Lessons for the 21st Century', '9780525512172', 18, 5, 420.00, 25, 'https://covers.openlibrary.org/b/isbn/9780525512172-M.jpg', 'Probes what it means to be human in an age of bewilderment, tackling pressing questions on technological disruption, truth, and community.'),
('Steve Jobs', '9781451648539', 19, 10, 550.00, 20, 'https://covers.openlibrary.org/b/isbn/9781451648539-M.jpg', 'The exclusive biography based on more than forty interviews with Steve Jobs conducted over two years, telling the story of a creative entrepreneur.'),
('Elon Musk', '9781982181284', 19, 10, 699.00, 25, 'https://covers.openlibrary.org/b/isbn/9781982181284-M.jpg', 'The astonishingly intimate story of the most fascinating and controversial innovator of our era—a rule-breaking visionary behind Tesla and SpaceX.'),
('The Alchemist', '9780062315007', 20, 1, 299.00, 59, 'https://covers.openlibrary.org/b/isbn/9780062315007-M.jpg', 'An enchanting tale of Santiago, an Andalusian shepherd boy who yearns to travel in search of a worldly treasure as extravagant as any ever found.'),
('The Metamorphosis', '9780143105244', 21, 14, 180.00, 22, 'https://covers.openlibrary.org/b/isbn/9780143105244-M.jpg', 'One morning, Gregor Samsa wakes up from uneasy dreams to find himself transformed in his bed into a monstrous insect.'),
('War and Peace', '9781400079988', 22, 14, 650.00, 15, 'https://covers.openlibrary.org/b/isbn/9781400079988-M.jpg', 'Tolstoy masterfully weaves together public history and private lives during the Napoleonic wars, creating a literary monument of epic scale.'),
('One Hundred Years of Solitude', '9780060883287', 23, 1, 450.00, 28, 'https://covers.openlibrary.org/b/isbn/9780060883287-M.jpg', 'The brilliant, bestselling, landmark novel that tells the multi-generational story of the Buendía family in the mythical town of Macondo.'),
('How to Win Friends and Influence People', '9780671027032', 24, 9, 250.00, 50, 'https://covers.openlibrary.org/b/isbn/9780671027032-M.jpg', 'Since its release in 1936, this timeless bestseller has helped millions achieve success in their business and personal lives.'),
('Atomic Habits', '9780735211292', 25, 9, 499.00, 64, 'https://covers.openlibrary.org/b/isbn/9780735211292-M.jpg', 'An easy and proven way to build good habits and break bad ones. Learn the secret framework that leads to remarkable results through tiny changes.'),
('Clean Code', '9780132350884', 26, 13, 799.00, 20, 'https://covers.openlibrary.org/b/isbn/9780132350884-M.jpg', 'Even bad code can function, but it brings development to its knees. Master craftsman Robert C. Martin presents a revolutionary paradigm for clean software design.'),
('The Clean Coder', '9780137081073', 26, 13, 720.00, 16, 'https://covers.openlibrary.org/b/isbn/9780137081073-M.jpg', 'A Code of Conduct for Professional Programmers, filled with pragmatic advice about craftsmanship, testing, estimation, and attitude.'),
('Rich Dad Poor Dad', '9781612680194', 27, 12, 350.00, 45, 'https://covers.openlibrary.org/b/isbn/9781612680194-M.jpg', 'Explodes the myth that you need to earn a high income to be rich and explains the difference between working for money and having your money work for you.'),
('The Psychology of Money', '9789390166268', 28, 12, 320.00, 55, 'https://covers.openlibrary.org/b/isbn/9789390166268-M.jpg', 'Doing well with money isn\'t necessarily about what you know. It\'s about how you behave. 19 short stories exploring the strange ways people think about money.'),
('The Subtle Art of Not Giving a F*ck', '9780062457714', 29, 9, 360.00, 35, 'https://covers.openlibrary.org/b/isbn/9780062457714-M.jpg', 'A generation-defining self-help guide showing how to stop trying to be positive all the time and instead become better, happier people by confronting harsh truths.'),
('Frankenstein', '9780141439471', 30, 14, 199.00, 25, 'https://covers.openlibrary.org/b/isbn/9780141439471-M.jpg', 'The classic Gothic tale of Victor Frankenstein, an ambitious scientist whose obsession with bringing inanimate matter to life creates a monstrous creature.'),
('Dracula', '9780141439846', 31, 14, 230.00, 30, 'https://covers.openlibrary.org/b/isbn/9780141439846-M.jpg', 'Count Dracula sinister journey from the mountain fastness of Transylvania to Victorian England in search of new blood.'),
('A Tale of Two Cities', '9780141439600', 32, 14, 240.00, 20, 'https://covers.openlibrary.org/b/isbn/9780141439600-M.jpg', 'Set in London and Paris before and during the French Revolution, depicting the plight of the French peasantry and the reign of terror.'),
('Great Expectations', '9780141439563', 32, 14, 260.00, 18, 'https://covers.openlibrary.org/b/isbn/9780141439563-M.jpg', 'Traces the growth of the orphan Pip from a blacksmith boy to a gentleman of fortune through the help of an unknown benefactor.'),
('The Art of War', '9781590302255', 33, 11, 170.00, 40, 'https://covers.openlibrary.org/b/isbn/9781590302255-M.jpg', 'An ancient Chinese military treatise dating from the Late Spring and Autumn Period, influential across military strategy, business, and leadership.'),
('Meditations', '9780812968255', 34, 11, 210.00, 35, 'https://covers.openlibrary.org/b/isbn/9780812968255-M.jpg', 'The private journal entries of the Roman Emperor Marcus Aurelius, offering timeless Stoic wisdom on duty, resilience, mortality, and inner peace.'),
('The Chronicles of Narnia: The Lion, the Witch and the Wardrobe', '9780064471046', 35, 3, 340.00, 30, 'https://covers.openlibrary.org/b/isbn/9780064471046-M.jpg', 'Four siblings step through a wardrobe door into the land of Narnia, frozen in eternal winter by the White Witch, awaiting the great lion Aslan.'),
('The Kite Runner', '9781594631931', 36, 1, 380.00, 32, 'https://covers.openlibrary.org/b/isbn/9781594631931-M.jpg', 'An epic tale of friendship, redemption, and the devastating power of betrayal, set against the backdrop of Afghanistan from the 1970s to post-9/11.'),
('A Thousand Splendid Suns', '9781594489502', 36, 1, 390.00, 24, 'https://covers.openlibrary.org/b/isbn/9781594489502-M.jpg', 'A breathtaking story of two Afghan women brought together by tragedy and war, forming an unbreakable bond of sisterhood and courage.'),
('The Immortals of Meluha', '9789380658742', 37, 3, 299.00, 45, 'https://covers.openlibrary.org/b/isbn/9789380658742-M.jpg', 'The first book of the Shiva Trilogy. 1900 BC, in the land of Meluha, a Tibetan immigrant named Shiva is hailed as the prophesied saviour Neelkanth.'),
('Wings of Fire', '9788173711466', 40, 10, 275.00, 50, 'https://covers.openlibrary.org/b/isbn/9788173711466-M.jpg', 'The inspiring autobiography of Dr. A.P.J. Abdul Kalam, from a humble childhood in Rameswaram to leading India missile and space launch programmes.'),
('The God of Small Things', '9780812979657', 39, 1, 350.00, 28, 'https://covers.openlibrary.org/b/isbn/9780812979657-M.jpg', 'The Booker Prize-winning novel chronicling the childhood experiences of fraternal twins whose lives are destroyed by the Love Laws.'),
('The Blue Umbrella', '9788171673407', 38, 1, 120.00, 30, 'https://covers.openlibrary.org/b/isbn/9788171673407-M.jpg', 'A heartwarming short story set in the Garhwal Himalayas about Binya and her beautiful blue silk umbrella traded for a leopard-claw pendant.'),
('Outliers: The Story of Success', '9780316017930', 41, 5, 450.00, 34, 'https://covers.openlibrary.org/b/isbn/9780316017930-M.jpg', 'Gladwell examines the factors that contribute to high levels of success, asking what makes high-achievers different and exploring the 10,000-hour rule.');

-- ---------------------------------------------------
-- 5. SAMPLE ORDERS & PAYMENTS (Admin verification demo)
-- ---------------------------------------------------
INSERT INTO Orders (Order_ID, User_ID, Order_Date, Total_Amount, Status) VALUES
(1, 2, NOW() - INTERVAL 2 HOUR, 798.00, 'Pending'),
(2, 3, NOW() - INTERVAL 1 HOUR, 670.00, 'Pending'),
(3, 2, NOW() - INTERVAL 1 DAY,  199.00, 'Confirmed'),
(4, 3, NOW() - INTERVAL 30 MINUTE, 299.00, 'Pending');

INSERT INTO Payment (Payment_ID, Order_ID, Payment_Date, Amount, Payment_Method, Payment_Status, Transaction_Ref) VALUES
(1, 1, NOW() - INTERVAL 2 HOUR, 798.00, 'UPI',        'Pending', 'UPI-9876543210@paytm'),
(2, 2, NOW() - INTERVAL 1 HOUR, 670.00, 'Card',       'Pending', 'AUTH-CARD-4421'),
(3, 3, NOW() - INTERVAL 1 DAY,  199.00, 'NetBanking', 'Success', 'HDFC-TXN-881239'),
(4, 4, NOW() - INTERVAL 30 MINUTE, 299.00, 'COD',     'Pending', NULL);

-- ---------------------------------------------------
-- 6. UPDATION DEMONSTRATIONS
-- ---------------------------------------------------
-- Update price of a book
UPDATE Books SET Price = 275.00 WHERE Book_ID = 1;

-- Update user phone number
UPDATE Users SET Phone = '9000011111' WHERE User_ID = 2;

-- Admin verifies and marks customer payment as completed
UPDATE Payment SET Payment_Status = 'Success', Transaction_Ref = 'UPI-VERIFIED-7711' WHERE Payment_ID = 1;
UPDATE Orders SET Status = 'Confirmed' WHERE Order_ID = 1;

-- ---------------------------------------------------
-- 7. DELETION DEMONSTRATIONS
-- ---------------------------------------------------
-- Delete a book by Book_ID (safe demo if no orders)
-- DELETE FROM Books WHERE Book_ID = 999;

-- ---------------------------------------------------
-- 8. SEARCHING & JOIN DEMONSTRATIONS
-- ---------------------------------------------------
-- Search books by title keyword
SELECT * FROM Books WHERE Title LIKE '%Harry%';

-- Search books priced under 300
SELECT Title, Price, Stock FROM Books WHERE Price < 300;

-- Search books with author and category names (JOIN)
SELECT B.Title, A.Author_Name, C.Category_Name, B.Price, B.Stock
FROM Books B
JOIN Authors A ON B.Author_ID = A.Author_ID
JOIN Categories C ON B.Category_ID = C.Category_ID;

-- Search all customer payments pending admin verification
SELECT P.Payment_ID, O.Order_ID, U.Name, P.Amount, P.Payment_Method, P.Payment_Status
FROM Payment P
JOIN Orders O ON P.Order_ID = O.Order_ID
JOIN Users U ON O.User_ID = U.User_ID
WHERE P.Payment_Status = 'Pending';
