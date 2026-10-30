<?php
require_once __DIR__ . '/../frontend/db_connect.php';

echo "Seeding additional books...\n";

// 1. Categories
$categories = [
    'Fiction',
    'Non-Fiction',
    'Fantasy',
    'Science Fiction',
    'Mystery & Thriller',
    'Self-Help & Personal Growth',
    'Biography & Memoir',
    'History & Philosophy',
    'Business & Finance',
    'Computer Science & Tech',
    'Classic Literature'
];

$cat_map = [];
foreach ($categories as $cat) {
    $stmt = $conn->prepare("INSERT IGNORE INTO Categories (Category_Name) VALUES (?)");
    $stmt->bind_param("s", $cat);
    $stmt->execute();
    
    $res = $conn->query("SELECT Category_ID FROM Categories WHERE Category_Name = '" . $conn->real_escape_string($cat) . "'");
    $cat_map[$cat] = $res->fetch_assoc()['Category_ID'];
}

// 2. Authors
$authors = [
    'R.K. Narayan',
    'Chetan Bhagat',
    'J.K. Rowling',
    'George Orwell',
    'Harper Lee',
    'F. Scott Fitzgerald',
    'Jane Austen',
    'J.R.R. Tolkien',
    'Agatha Christie',
    'Stephen King',
    'Arthur Conan Doyle',
    'Isaac Asimov',
    'Frank Herbert',
    'Dan Brown',
    'Yuval Noah Harari',
    'Walter Isaacson',
    'Paulo Coelho',
    'Franz Kafka',
    'Leo Tolstoy',
    'Gabriel García Márquez',
    'Dale Carnegie',
    'James Clear',
    'Robert C. Martin',
    'Robert T. Kiyosaki',
    'Morgan Housel',
    'Mark Manson',
    'Mary Shelley',
    'Bram Stoker',
    'Charles Dickens',
    'Sun Tzu',
    'Marcus Aurelius',
    'C.S. Lewis',
    'Khaled Hosseini',
    'Amish Tripathi',
    'Ruskin Bond',
    'Arundhati Roy',
    'A.P.J. Abdul Kalam',
    'Malcolm Gladwell'
];

$author_map = [];
foreach ($authors as $auth) {
    $stmt = $conn->prepare("INSERT IGNORE INTO Authors (Author_Name) VALUES (?)");
    $stmt->bind_param("s", $auth);
    $stmt->execute();

    $res = $conn->query("SELECT Author_ID FROM Authors WHERE Author_Name = '" . $conn->real_escape_string($auth) . "'");
    $author_map[$auth] = $res->fetch_assoc()['Author_ID'];
}

// 3. Books data: Title, ISBN, Author, Category, Price, Stock, Description
$books = [
    [
        'Title' => '1984',
        'ISBN' => '9780451524935',
        'Author' => 'George Orwell',
        'Category' => 'Science Fiction',
        'Price' => 299.00,
        'Stock' => 35,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780451524935-M.jpg',
        'Description' => 'Winston Smith toes the Party line, rewriting history to satisfy the demands of the Ministry of Truth. With every lie he writes, he grows to hate the Party that seeks power for its own sake.'
    ],
    [
        'Title' => 'Animal Farm',
        'ISBN' => '9780451526342',
        'Author' => 'George Orwell',
        'Category' => 'Classic Literature',
        'Price' => 199.00,
        'Stock' => 40,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780451526342-M.jpg',
        'Description' => 'A farm is taken over by its overworked, mistreated animals. With flaming idealism and stirring slogans, they set out to create a paradise of progress, justice, and equality.'
    ],
    [
        'Title' => 'To Kill a Mockingbird',
        'ISBN' => '9780060935467',
        'Author' => 'Harper Lee',
        'Category' => 'Classic Literature',
        'Price' => 349.00,
        'Stock' => 25,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780060935467-M.jpg',
        'Description' => 'The unforgettable novel of a childhood in a sleepy Southern town and the crisis of conscience that rocked it, compassionate and deeply moving.'
    ],
    [
        'Title' => 'The Great Gatsby',
        'ISBN' => '9780743273565',
        'Author' => 'F. Scott Fitzgerald',
        'Category' => 'Classic Literature',
        'Price' => 280.00,
        'Stock' => 30,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780743273565-M.jpg',
        'Description' => 'A true classic of twentieth-century literature and one of the greatest novels of the Jazz Age, telling the story of the fabulously wealthy Jay Gatsby and his love for Daisy Buchanan.'
    ],
    [
        'Title' => 'Pride and Prejudice',
        'ISBN' => '9780141439518',
        'Author' => 'Jane Austen',
        'Category' => 'Classic Literature',
        'Price' => 220.00,
        'Stock' => 45,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780141439518-M.jpg',
        'Description' => 'The romantic clash between the opinionated Elizabeth and her proud beau, Mr. Darcy, is a splendid performance of civilized sparring.'
    ],
    [
        'Title' => 'Sense and Sensibility',
        'ISBN' => '9780141439662',
        'Author' => 'Jane Austen',
        'Category' => 'Classic Literature',
        'Price' => 240.00,
        'Stock' => 20,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780141439662-M.jpg',
        'Description' => 'Follows the lives and loves of the Dashwood sisters Elinor and Marianne as they navigate society, fortune, and heartbreak.'
    ],
    [
        'Title' => 'The Hobbit',
        'ISBN' => '9780547928227',
        'Author' => 'J.R.R. Tolkien',
        'Category' => 'Fantasy',
        'Price' => 450.00,
        'Stock' => 50,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780547928227-M.jpg',
        'Description' => 'Bilbo Baggins is a hobbit who enjoys a comfortable and unambitious life, until Gandalf the wizard and a company of dwarves arrive on his threshold.'
    ],
    [
        'Title' => 'The Fellowship of the Ring',
        'ISBN' => '9780547928210',
        'Author' => 'J.R.R. Tolkien',
        'Category' => 'Fantasy',
        'Price' => 520.00,
        'Stock' => 35,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780547928210-M.jpg',
        'Description' => 'The first volume in The Lord of the Rings, following Frodo Baggins as he embarks on an epic quest across Middle-earth to destroy the One Ring.'
    ],
    [
        'Title' => 'The Two Towers',
        'ISBN' => '9780547928203',
        'Author' => 'J.R.R. Tolkien',
        'Category' => 'Fantasy',
        'Price' => 520.00,
        'Stock' => 28,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780547928203-M.jpg',
        'Description' => 'The Fellowship has been broken. Frodo and Sam continue toward Mordor alone, while Aragorn, Legolas, and Gimli track Merry and Pippin.'
    ],
    [
        'Title' => 'The Return of the King',
        'ISBN' => '9780547928197',
        'Author' => 'J.R.R. Tolkien',
        'Category' => 'Fantasy',
        'Price' => 550.00,
        'Stock' => 22,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780547928197-M.jpg',
        'Description' => 'The culmination of the grand epic: the armies of the Dark Lord gather, and the destiny of Middle-earth rests upon Frodo and Sam approaching Mount Doom.'
    ],
    [
        'Title' => 'And Then There Were None',
        'ISBN' => '9780062073488',
        'Author' => 'Agatha Christie',
        'Category' => 'Mystery & Thriller',
        'Price' => 299.00,
        'Stock' => 30,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780062073488-M.jpg',
        'Description' => 'Ten strangers are invited to an isolated island mansion. One by one, the guests are murdered in accordance with an eerie nursery rhyme.'
    ],
    [
        'Title' => 'Murder on the Orient Express',
        'ISBN' => '9780062693662',
        'Author' => 'Agatha Christie',
        'Category' => 'Mystery & Thriller',
        'Price' => 320.00,
        'Stock' => 25,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780062693662-M.jpg',
        'Description' => 'Just after midnight, a snowdrift stops the Orient Express in its tracks. By morning, an American tycoon lies dead, stabbed a dozen times behind a locked door.'
    ],
    [
        'Title' => 'The ABC Murders',
        'ISBN' => '9780062073587',
        'Author' => 'Agatha Christie',
        'Category' => 'Mystery & Thriller',
        'Price' => 280.00,
        'Stock' => 18,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780062073587-M.jpg',
        'Description' => 'A serial killer is murdering people in alphabetical order, leaving an ABC Railway Guide by each corpse. Hercule Poirot takes the case.'
    ],
    [
        'Title' => 'The Shining',
        'ISBN' => '9780307743657',
        'Author' => 'Stephen King',
        'Category' => 'Mystery & Thriller',
        'Price' => 450.00,
        'Stock' => 20,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780307743657-M.jpg',
        'Description' => 'Jack Torrance accepts a winter caretaker position at the atmospheric Overlook Hotel, but sinister forces begin to manifest as snow cuts them off.'
    ],
    [
        'Title' => 'Misery',
        'ISBN' => '9781501143106',
        'Author' => 'Stephen King',
        'Category' => 'Mystery & Thriller',
        'Price' => 399.00,
        'Stock' => 15,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781501143106-M.jpg',
        'Description' => 'Novelist Paul Sheldon crashes in a blizzard and is rescued by Annie Wilkes, his number one fan, whose devotion turns into a nightmare of captive torture.'
    ],
    [
        'Title' => 'It',
        'ISBN' => '9781501142970',
        'Author' => 'Stephen King',
        'Category' => 'Fiction',
        'Price' => 599.00,
        'Stock' => 14,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781501142970-M.jpg',
        'Description' => 'In Derry, Maine, seven teenagers discovered an unspeakable evil lurking beneath the town sewers. Decades later, they are called back to confront it once more.'
    ],
    [
        'Title' => 'The Adventures of Sherlock Holmes',
        'ISBN' => '9780140437713',
        'Author' => 'Arthur Conan Doyle',
        'Category' => 'Mystery & Thriller',
        'Price' => 260.00,
        'Stock' => 30,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780140437713-M.jpg',
        'Description' => 'Twelve iconic tales featuring the world legendary consulting detective Sherlock Holmes and his steadfast partner Dr. John Watson.'
    ],
    [
        'Title' => 'The Hound of the Baskervilles',
        'ISBN' => '9780141439976',
        'Author' => 'Arthur Conan Doyle',
        'Category' => 'Mystery & Thriller',
        'Price' => 230.00,
        'Stock' => 25,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780141439976-M.jpg',
        'Description' => 'Sherlock Holmes investigates the eerie curse of a gigantic spectral hound haunting the desolate moors of Devonshire.'
    ],
    [
        'Title' => 'Foundation',
        'ISBN' => '9780553293357',
        'Author' => 'Isaac Asimov',
        'Category' => 'Science Fiction',
        'Price' => 380.00,
        'Stock' => 32,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780553293357-M.jpg',
        'Description' => 'Hari Seldon uses psychohistory to foresee the imminent collapse of the Galactic Empire and creates the Foundation to preserve human knowledge.'
    ],
    [
        'Title' => 'I, Robot',
        'ISBN' => '9780553382563',
        'Author' => 'Isaac Asimov',
        'Category' => 'Science Fiction',
        'Price' => 350.00,
        'Stock' => 27,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780553382563-M.jpg',
        'Description' => 'The pioneering collection of stories introducing the Three Laws of Robotics and exploring the future relationship between mankind and machines.'
    ],
    [
        'Title' => 'Dune',
        'ISBN' => '9780441172719',
        'Author' => 'Frank Herbert',
        'Category' => 'Science Fiction',
        'Price' => 499.00,
        'Stock' => 45,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780441172719-M.jpg',
        'Description' => 'Set on the desert planet Arrakis, Dune is the story of the boy Paul Atreides, heir to a noble family tasked with ruling an inhospitable world.'
    ],
    [
        'Title' => 'Dune Messiah',
        'ISBN' => '9780441172696',
        'Author' => 'Frank Herbert',
        'Category' => 'Science Fiction',
        'Price' => 420.00,
        'Stock' => 20,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780441172696-M.jpg',
        'Description' => 'Twelve years after Paul Atreides seized emperor status, religious fanatics run rampant while secret factions plot his overthrow.'
    ],
    [
        'Title' => 'The Da Vinci Code',
        'ISBN' => '9780307474278',
        'Author' => 'Dan Brown',
        'Category' => 'Mystery & Thriller',
        'Price' => 370.00,
        'Stock' => 38,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780307474278-M.jpg',
        'Description' => 'Symbologist Robert Langdon and cryptologist Sophie Neveu unravel a breathless trail of clues embedded in Leonardo da Vinci masterpieces.'
    ],
    [
        'Title' => 'Angels & Demons',
        'ISBN' => '9781416524793',
        'Author' => 'Dan Brown',
        'Category' => 'Mystery & Thriller',
        'Price' => 350.00,
        'Stock' => 30,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781416524793-M.jpg',
        'Description' => 'An ancient secret brotherhood, a devastating antimatter weapon, and a high-stakes hunt across subterranean crypts and cathedrals of Rome.'
    ],
    [
        'Title' => 'Sapiens: A Brief History of Humankind',
        'ISBN' => '9780062316097',
        'Author' => 'Yuval Noah Harari',
        'Category' => 'Non-Fiction',
        'Price' => 499.00,
        'Stock' => 45,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780062316097-M.jpg',
        'Description' => 'Explore how an insignificant ape became the ruler of planet Earth, capable of splitting the atom, flying to the moon, and manipulating genetic code.'
    ],
    [
        'Title' => 'Homo Deus: A Brief History of Tomorrow',
        'ISBN' => '9780062464316',
        'Author' => 'Yuval Noah Harari',
        'Category' => 'Non-Fiction',
        'Price' => 480.00,
        'Stock' => 30,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780062464316-M.jpg',
        'Description' => 'Investigates the projects, dreams, and nightmares that will shape humanity in the twenty-first century, from overcoming death to artificial intelligence.'
    ],
    [
        'Title' => '21 Lessons for the 21st Century',
        'ISBN' => '9780525512172',
        'Author' => 'Yuval Noah Harari',
        'Category' => 'Non-Fiction',
        'Price' => 420.00,
        'Stock' => 25,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780525512172-M.jpg',
        'Description' => 'Probes what it means to be human in an age of bewilderment, tackling pressing questions on technological disruption, truth, and community.'
    ],
    [
        'Title' => 'Steve Jobs',
        'ISBN' => '9781451648539',
        'Author' => 'Walter Isaacson',
        'Category' => 'Biography & Memoir',
        'Price' => 550.00,
        'Stock' => 20,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781451648539-M.jpg',
        'Description' => 'The exclusive biography based on more than forty interviews with Steve Jobs conducted over two years, telling the story of a creative entrepreneur.'
    ],
    [
        'Title' => 'Elon Musk',
        'ISBN' => '9781982181284',
        'Author' => 'Walter Isaacson',
        'Category' => 'Biography & Memoir',
        'Price' => 699.00,
        'Stock' => 25,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781982181284-M.jpg',
        'Description' => 'The astonishingly intimate story of the most fascinating and controversial innovator of our era—a rule-breaking visionary behind Tesla and SpaceX.'
    ],
    [
        'Title' => 'The Alchemist',
        'ISBN' => '9780062315007',
        'Author' => 'Paulo Coelho',
        'Category' => 'Fiction',
        'Price' => 299.00,
        'Stock' => 60,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780062315007-M.jpg',
        'Description' => 'An enchanting tale of Santiago, an Andalusian shepherd boy who yearns to travel in search of a worldly treasure as extravagant as any ever found.'
    ],
    [
        'Title' => 'The Metamorphosis',
        'ISBN' => '9780143105244',
        'Author' => 'Franz Kafka',
        'Category' => 'Classic Literature',
        'Price' => 180.00,
        'Stock' => 22,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780143105244-M.jpg',
        'Description' => 'One morning, Gregor Samsa wakes up from uneasy dreams to find himself transformed in his bed into a monstrous insect.'
    ],
    [
        'Title' => 'War and Peace',
        'ISBN' => '9781400079988',
        'Author' => 'Leo Tolstoy',
        'Category' => 'Classic Literature',
        'Price' => 650.00,
        'Stock' => 15,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781400079988-M.jpg',
        'Description' => 'Tolstoy masterfully weaves together public history and private lives during the Napoleonic wars, creating a literary monument of epic scale.'
    ],
    [
        'Title' => 'One Hundred Years of Solitude',
        'ISBN' => '9780060883287',
        'Author' => 'Gabriel García Márquez',
        'Category' => 'Fiction',
        'Price' => 450.00,
        'Stock' => 28,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780060883287-M.jpg',
        'Description' => 'The brilliant, bestselling, landmark novel that tells the multi-generational story of the Buendía family in the mythical town of Macondo.'
    ],
    [
        'Title' => 'How to Win Friends and Influence People',
        'ISBN' => '9780671027032',
        'Author' => 'Dale Carnegie',
        'Category' => 'Self-Help & Personal Growth',
        'Price' => 250.00,
        'Stock' => 50,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780671027032-M.jpg',
        'Description' => 'Since its release in 1936, this timeless bestseller has helped millions achieve success in their business and personal lives.'
    ],
    [
        'Title' => 'Atomic Habits',
        'ISBN' => '9780735211292',
        'Author' => 'James Clear',
        'Category' => 'Self-Help & Personal Growth',
        'Price' => 499.00,
        'Stock' => 65,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780735211292-M.jpg',
        'Description' => 'An easy and proven way to build good habits and break bad ones. Learn the secret framework that leads to remarkable results through tiny changes.'
    ],
    [
        'Title' => 'Clean Code',
        'ISBN' => '9780132350884',
        'Author' => 'Robert C. Martin',
        'Category' => 'Computer Science & Tech',
        'Price' => 799.00,
        'Stock' => 20,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780132350884-M.jpg',
        'Description' => 'Even bad code can function, but it brings development to its knees. Master craftsman Robert C. Martin presents a revolutionary paradigm for clean software design.'
    ],
    [
        'Title' => 'The Clean Coder',
        'ISBN' => '9780137081073',
        'Author' => 'Robert C. Martin',
        'Category' => 'Computer Science & Tech',
        'Price' => 720.00,
        'Stock' => 16,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780137081073-M.jpg',
        'Description' => 'A Code of Conduct for Professional Programmers, filled with pragmatic advice about craftsmanship, testing, estimation, and attitude.'
    ],
    [
        'Title' => 'Rich Dad Poor Dad',
        'ISBN' => '9781612680194',
        'Author' => 'Robert T. Kiyosaki',
        'Category' => 'Business & Finance',
        'Price' => 350.00,
        'Stock' => 45,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781612680194-M.jpg',
        'Description' => 'Explodes the myth that you need to earn a high income to be rich and explains the difference between working for money and having your money work for you.'
    ],
    [
        'Title' => 'The Psychology of Money',
        'ISBN' => '9789390166268',
        'Author' => 'Morgan Housel',
        'Category' => 'Business & Finance',
        'Price' => 320.00,
        'Stock' => 55,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9789390166268-M.jpg',
        'Description' => 'Doing well with money isn\'t necessarily about what you know. It\'s about how you behave. 19 short stories exploring the strange ways people think about money.'
    ],
    [
        'Title' => 'The Subtle Art of Not Giving a F*ck',
        'ISBN' => '9780062457714',
        'Author' => 'Mark Manson',
        'Category' => 'Self-Help & Personal Growth',
        'Price' => 360.00,
        'Stock' => 35,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780062457714-M.jpg',
        'Description' => 'A generation-defining self-help guide showing how to stop trying to be positive all the time and instead become better, happier people by confronting harsh truths.'
    ],
    [
        'Title' => 'Frankenstein',
        'ISBN' => '9780141439471',
        'Author' => 'Mary Shelley',
        'Category' => 'Classic Literature',
        'Price' => 199.00,
        'Stock' => 25,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780141439471-M.jpg',
        'Description' => 'The classic Gothic tale of Victor Frankenstein, an ambitious scientist whose obsession with bringing inanimate matter to life creates a monstrous creature.'
    ],
    [
        'Title' => 'Dracula',
        'ISBN' => '9780141439846',
        'Author' => 'Bram Stoker',
        'Category' => 'Classic Literature',
        'Price' => 230.00,
        'Stock' => 30,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780141439846-M.jpg',
        'Description' => 'Count Dracula sinister journey from the mountain fastness of Transylvania to Victorian England in search of new blood.'
    ],
    [
        'Title' => 'A Tale of Two Cities',
        'ISBN' => '9780141439600',
        'Author' => 'Charles Dickens',
        'Category' => 'Classic Literature',
        'Price' => 240.00,
        'Stock' => 20,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780141439600-M.jpg',
        'Description' => 'Set in London and Paris before and during the French Revolution, depicting the plight of the French peasantry and the reign of terror.'
    ],
    [
        'Title' => 'Great Expectations',
        'ISBN' => '9780141439563',
        'Author' => 'Charles Dickens',
        'Category' => 'Classic Literature',
        'Price' => 260.00,
        'Stock' => 18,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780141439563-M.jpg',
        'Description' => 'Traces the growth of the orphan Pip from a blacksmith boy to a gentleman of fortune through the help of an unknown benefactor.'
    ],
    [
        'Title' => 'The Art of War',
        'ISBN' => '9781590302255',
        'Author' => 'Sun Tzu',
        'Category' => 'History & Philosophy',
        'Price' => 170.00,
        'Stock' => 40,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781590302255-M.jpg',
        'Description' => 'An ancient Chinese military treatise dating from the Late Spring and Autumn Period, influential across military strategy, business, and leadership.'
    ],
    [
        'Title' => 'Meditations',
        'ISBN' => '9780812968255',
        'Author' => 'Marcus Aurelius',
        'Category' => 'History & Philosophy',
        'Price' => 210.00,
        'Stock' => 35,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780812968255-M.jpg',
        'Description' => 'The private journal entries of the Roman Emperor Marcus Aurelius, offering timeless Stoic wisdom on duty, resilience, mortality, and inner peace.'
    ],
    [
        'Title' => 'The Chronicles of Narnia: The Lion, the Witch and the Wardrobe',
        'ISBN' => '9780064471046',
        'Author' => 'C.S. Lewis',
        'Category' => 'Fantasy',
        'Price' => 340.00,
        'Stock' => 30,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780064471046-M.jpg',
        'Description' => 'Four siblings step through a wardrobe door into the land of Narnia, frozen in eternal winter by the White Witch, awaiting the great lion Aslan.'
    ],
    [
        'Title' => 'The Kite Runner',
        'ISBN' => '9781594631931',
        'Author' => 'Khaled Hosseini',
        'Category' => 'Fiction',
        'Price' => 380.00,
        'Stock' => 32,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781594631931-M.jpg',
        'Description' => 'An epic tale of friendship, redemption, and the devastating power of betrayal, set against the backdrop of Afghanistan from the 1970s to post-9/11.'
    ],
    [
        'Title' => 'A Thousand Splendid Suns',
        'ISBN' => '9781594489502',
        'Author' => 'Khaled Hosseini',
        'Category' => 'Fiction',
        'Price' => 390.00,
        'Stock' => 24,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9781594489502-M.jpg',
        'Description' => 'A breathtaking story of two Afghan women brought together by tragedy and war, forming an unbreakable bond of sisterhood and courage.'
    ],
    [
        'Title' => 'The Immortals of Meluha',
        'ISBN' => '9789380658742',
        'Author' => 'Amish Tripathi',
        'Category' => 'Fantasy',
        'Price' => 299.00,
        'Stock' => 45,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9789380658742-M.jpg',
        'Description' => 'The first book of the Shiva Trilogy. 1900 BC, in the land of Meluha, a Tibetan immigrant named Shiva is hailed as the prophesied saviour Neelkanth.'
    ],
    [
        'Title' => 'Wings of Fire',
        'ISBN' => '9788173711466',
        'Author' => 'A.P.J. Abdul Kalam',
        'Category' => 'Biography & Memoir',
        'Price' => 275.00,
        'Stock' => 50,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9788173711466-M.jpg',
        'Description' => 'The inspiring autobiography of Dr. A.P.J. Abdul Kalam, from a humble childhood in Rameswaram to leading India missile and space launch programmes.'
    ],
    [
        'Title' => 'The God of Small Things',
        'ISBN' => '9780812979657',
        'Author' => 'Arundhati Roy',
        'Category' => 'Fiction',
        'Price' => 350.00,
        'Stock' => 28,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780812979657-M.jpg',
        'Description' => 'The Booker Prize-winning novel chronicling the childhood experiences of fraternal twins whose lives are destroyed by the Love Laws.'
    ],
    [
        'Title' => 'The Blue Umbrella',
        'ISBN' => '9788171673407',
        'Author' => 'Ruskin Bond',
        'Category' => 'Fiction',
        'Price' => 120.00,
        'Stock' => 30,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9788171673407-M.jpg',
        'Description' => 'A heartwarming short story set in the Garhwal Himalayas about Binya and her beautiful blue silk umbrella traded for a leopard-claw pendant.'
    ],
    [
        'Title' => 'Outliers: The Story of Success',
        'ISBN' => '9780316017930',
        'Author' => 'Malcolm Gladwell',
        'Category' => 'Non-Fiction',
        'Price' => 450.00,
        'Stock' => 34,
        'Cover' => 'https://covers.openlibrary.org/b/isbn/9780316017930-M.jpg',
        'Description' => 'Gladwell examines the factors that contribute to high levels of success, asking what makes high-achievers different and exploring the 10,000-hour rule.'
    ]
];

$inserted = 0;
$stmt_check = $conn->prepare("SELECT Book_ID FROM Books WHERE ISBN = ?");
$stmt_insert = $conn->prepare("
    INSERT INTO Books (Title, ISBN, Author_ID, Category_ID, Price, Stock, Cover_Image_URL, Description)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

foreach ($books as $b) {
    $stmt_check->bind_param("s", $b['ISBN']);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows === 0) {
        $author_id = $author_map[$b['Author']];
        $category_id = $cat_map[$b['Category']];
        $stmt_insert->bind_param(
            "ssiiddss",
            $b['Title'],
            $b['ISBN'],
            $author_id,
            $category_id,
            $b['Price'],
            $b['Stock'],
            $b['Cover'],
            $b['Description']
        );
        if ($stmt_insert->execute()) {
            $inserted++;
        } else {
            echo "Error inserting " . $b['Title'] . ": " . $conn->error . "\n";
        }
    }
}

$total_books = $conn->query("SELECT COUNT(*) AS c FROM Books")->fetch_assoc()['c'];
echo "✅ Finished! Inserted $inserted new books. Total books in database: $total_books\n";
