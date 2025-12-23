-- Sinematix Database Schema
-- Cinema Ticket Booking System

-- Veritabanını oluştur
CREATE DATABASE IF NOT EXISTS sinematix CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sinematix;

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Filmler tablosu
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    original_title VARCHAR(200),
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    poster VARCHAR(500),
    backdrop VARCHAR(500),
    trailer_url VARCHAR(500),
    director VARCHAR(200),
    cast TEXT,
    genre VARCHAR(200),
    duration INT NOT NULL COMMENT 'Dakika cinsinden',
    rating DECIMAL(3,1) DEFAULT 0,
    release_date DATE,
    language VARCHAR(50) DEFAULT 'Türkçe',
    age_limit VARCHAR(10) DEFAULT 'Genel',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sinemalar tablosu
CREATE TABLE IF NOT EXISTS cinemas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100),
    address TEXT,
    phone VARCHAR(20),
    image VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Salonlar tablosu
CREATE TABLE IF NOT EXISTS halls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cinema_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    hall_type ENUM('2D', '3D', 'IMAX', '4DX', 'VIP') DEFAULT '2D',
    total_rows INT NOT NULL DEFAULT 10,
    seats_per_row INT NOT NULL DEFAULT 12,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (cinema_id) REFERENCES cinemas(id) ON DELETE CASCADE
);

-- Seanslar tablosu
CREATE TABLE IF NOT EXISTS showtimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    hall_id INT NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 150.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE
);

-- Koltuklar tablosu (her salon için sabit koltuklar)
CREATE TABLE IF NOT EXISTS seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hall_id INT NOT NULL,
    row_letter CHAR(1) NOT NULL,
    seat_number INT NOT NULL,
    seat_type ENUM('standard', 'vip', 'wheelchair') DEFAULT 'standard',
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE,
    UNIQUE KEY unique_seat (hall_id, row_letter, seat_number)
);

-- Rezervasyonlar tablosu
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    showtime_id INT NOT NULL,
    reservation_code VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
);

-- Rezervasyon detayları (seçilen koltuklar)
CREATE TABLE IF NOT EXISTS reservation_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    seat_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reservation_seat (reservation_id, seat_id)
);

-- Koltuk durumu görünümü (hangi koltuklar hangi seansta dolu)
CREATE VIEW seat_availability AS
SELECT 
    s.id AS showtime_id,
    st.id AS seat_id,
    st.row_letter,
    st.seat_number,
    CASE 
        WHEN rs.id IS NOT NULL AND r.status = 'confirmed' THEN 'occupied'
        ELSE 'available'
    END AS status
FROM showtimes s
JOIN seats st ON st.hall_id = s.hall_id
LEFT JOIN reservation_seats rs ON rs.seat_id = st.id
LEFT JOIN reservations r ON r.id = rs.reservation_id AND r.showtime_id = s.id;

-- =============================================
-- ÖRNEK VERİLER
-- =============================================

-- Örnek kullanıcılar
INSERT INTO users (name, email, password, phone) VALUES
('Demo Kullanıcı', 'demo@sinematix.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0532 123 4567'),
('Test User', 'test@sinematix.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0533 987 6543');

-- Örnek filmler
INSERT INTO movies (title, original_title, slug, description, poster, backdrop, trailer_url, director, cast, genre, duration, rating, release_date, language, age_limit) VALUES
('Gladyatör 2', 'Gladiator II', 'gladyator-2', 'Efsanevi savaşçı Maximus\'un hikayesinden yıllar sonra, Roma İmparatorluğu\'nun kaderini değiştirecek yeni bir kahraman ortaya çıkıyor. Lucius, imparatorluğun karanlık sırlarını ve kendi geçmişini keşfederken, arenada hayatta kalmak için savaşmak zorunda kalacak.', 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg', 'https://image.tmdb.org/t/p/original/euYIwmwkmz95mnXvufEmbL6ovhZ.jpg', 'https://www.youtube.com/watch?v=4rgYUipGJNo', 'Ridley Scott', 'Paul Mescal, Pedro Pascal, Denzel Washington, Connie Nielsen', 'Aksiyon, Dram, Tarih', 148, 8.2, '2024-11-15', 'Türkçe Dublaj', '16+'),

('Venom: Son Dans', 'Venom: The Last Dance', 'venom-son-dans', 'Eddie Brock ve Venom kaçak hayatlarına devam ederken, onları avlayan güçlü bir düşmanla yüzleşmek zorunda kalırlar. Bu son maceralarında, ikili her şeyi tehlikeye atacak zor kararlar vermek zorunda kalacak.', 'https://image.tmdb.org/t/p/w500/aosm8NMQ3UyoBVpSxyimorCQykC.jpg', 'https://image.tmdb.org/t/p/original/3V4kLQg0kSqPLctI5ziYWabAZYF.jpg', 'https://www.youtube.com/watch?v=HyIyd9joTTc', 'Kelly Marcel', 'Tom Hardy, Chiwetel Ejiofor, Juno Temple', 'Aksiyon, Bilim Kurgu, Macera', 109, 7.1, '2024-10-25', 'Türkçe Dublaj', '13+'),

('Moana 2', 'Moana 2', 'moana-2', 'Moana, atalarından gelen gizemli bir çağrı üzerine tehlikeli denizlere yeniden açılır. Yanında yeni mürettebatı ve eski dostu Maui ile birlikte, kayıp bir adayı ve uzun zamandır unutulmuş sırları keşfetmek için maceraya atılır.', 'https://image.tmdb.org/t/p/w500/4YZpsylmjHbqeWzjKpUEF8gcLNW.jpg', 'https://image.tmdb.org/t/p/original/tElnmtQ6yz1PjN1kePNl8yMSb59.jpg', 'https://www.youtube.com/watch?v=hDZ7y8RP5HE', 'David Derrick Jr., Jason Hand', 'Auliʻi Cravalho, Dwayne Johnson, Alan Tudyk', 'Animasyon, Aile, Macera', 100, 7.8, '2024-11-27', 'Türkçe Dublaj', 'Genel'),

('Wicked', 'Wicked', 'wicked', 'Oz\'un unutulmaz dünyasında geçen bu büyüleyici hikaye, farklı karakterlere sahip iki genç kadının nasıl arkadaş olduğunu ve sonunda düşman haline geldiğini anlatıyor. Elphaba ve Glinda\'nın olağanüstü yolculuğu başlıyor.', 'https://image.tmdb.org/t/p/w500/c5Tqxeo1UpBvnAc3csUm7j3hlQl.jpg', 'https://image.tmdb.org/t/p/original/uKb22E0nlzr914bA9KyA5CVCOlV.jpg', 'https://www.youtube.com/watch?v=6COmYeLsz4c', 'Jon M. Chu', 'Cynthia Erivo, Ariana Grande, Michelle Yeoh, Jeff Goldblum', 'Müzikal, Fantastik, Romantik', 160, 8.5, '2024-11-22', 'Türkçe Altyazılı', 'Genel'),

('Kraven: Avcı', 'Kraven the Hunter', 'kraven-avci', 'Sergei Kravinoff, dünyanın en tehlikeli avcısı olmak için acımasız bir yolculuğa çıkar. Babasının gölgesinden kurtulmak ve kendi kimliğini bulmak için her şeyi göze alacaktır.', 'https://image.tmdb.org/t/p/w500/i47IUSsN126K11JUzqQIOi1Mg1M.jpg', 'https://image.tmdb.org/t/p/original/v9Du2HC3hlknAvGlWhquRbeifwW.jpg', 'https://www.youtube.com/watch?v=gnj_VBRwKzw', 'J.C. Chandor', 'Aaron Taylor-Johnson, Russell Crowe, Ariana DeBose', 'Aksiyon, Gerilim, Suç', 127, 6.8, '2024-12-13', 'Türkçe Dublaj', '18+'),

('Nosferatu', 'Nosferatu', 'nosferatu', 'Klasik vampir efsanesinin yeniden yorumu. Genç bir kadın, kocasını esir alan gizemli ve korkunç Kont Orlok\'un peşine düşer. Karanlık bir aşk hikayesi ve gotik korkunun buluşması.', 'https://image.tmdb.org/t/p/w500/5qGIxdEO841C0tdY8vOdLoRVrr0.jpg', 'https://image.tmdb.org/t/p/original/zOpe0eHsq0A2NvNyBbtT6sj53qV.jpg', 'https://www.youtube.com/watch?v=nulvWqYUM8k', 'Robert Eggers', 'Bill Skarsgård, Lily-Rose Depp, Nicholas Hoult, Willem Dafoe', 'Korku, Fantastik, Gotik', 132, 8.0, '2024-12-25', 'Türkçe Altyazılı', '18+');

-- Örnek sinemalar
INSERT INTO cinemas (name, city, district, address, phone, image) VALUES
('Sinematix Plaza', 'İstanbul', 'Kadıköy', 'Caferağa Mah. Moda Cad. No:42', '0216 123 4567', 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800'),
('Sinematix Forum', 'İstanbul', 'Bayrampaşa', 'Forum İstanbul AVM Kat:3', '0212 987 6543', 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=800'),
('Sinematix Ankara', 'Ankara', 'Çankaya', 'Kızılay Meydanı No:15', '0312 456 7890', 'https://images.unsplash.com/photo-1595769816263-9b910be24d5f?w=800');

-- Örnek salonlar
INSERT INTO halls (cinema_id, name, hall_type, total_rows, seats_per_row) VALUES
(1, 'Salon 1', '2D', 8, 12),
(1, 'Salon 2', '3D', 8, 12),
(1, 'Salon 3 - IMAX', 'IMAX', 10, 16),
(1, 'VIP Salon', 'VIP', 6, 8),
(2, 'Salon 1', '2D', 8, 12),
(2, 'Salon 2', '3D', 8, 12),
(2, 'Salon 3', '4DX', 8, 10),
(3, 'Salon 1', '2D', 8, 12),
(3, 'Salon 2', '3D', 8, 12);

-- Koltukları oluştur (her salon için)
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS generate_seats()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE h_id INT;
    DECLARE h_rows INT;
    DECLARE h_seats INT;
    DECLARE row_num INT;
    DECLARE seat_num INT;
    DECLARE row_char CHAR(1);
    
    DECLARE hall_cursor CURSOR FOR SELECT id, total_rows, seats_per_row FROM halls;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN hall_cursor;
    
    hall_loop: LOOP
        FETCH hall_cursor INTO h_id, h_rows, h_seats;
        IF done THEN
            LEAVE hall_loop;
        END IF;
        
        SET row_num = 0;
        WHILE row_num < h_rows DO
            SET row_char = CHAR(65 + row_num);
            SET seat_num = 1;
            WHILE seat_num <= h_seats DO
                INSERT IGNORE INTO seats (hall_id, row_letter, seat_number, seat_type) 
                VALUES (h_id, row_char, seat_num, 'standard');
                SET seat_num = seat_num + 1;
            END WHILE;
            SET row_num = row_num + 1;
        END WHILE;
    END LOOP;
    
    CLOSE hall_cursor;
END //
DELIMITER ;

CALL generate_seats();
DROP PROCEDURE IF EXISTS generate_seats;

-- Örnek seanslar (bugün ve önümüzdeki 7 gün için)
INSERT INTO showtimes (movie_id, hall_id, show_date, show_time, price) VALUES
-- Gladyatör 2
(1, 1, CURDATE(), '11:00:00', 150.00),
(1, 1, CURDATE(), '14:30:00', 150.00),
(1, 1, CURDATE(), '18:00:00', 175.00),
(1, 1, CURDATE(), '21:30:00', 175.00),
(1, 3, CURDATE(), '13:00:00', 250.00),
(1, 3, CURDATE(), '17:00:00', 250.00),
(1, 3, CURDATE(), '21:00:00', 275.00),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', 150.00),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:30:00', 150.00),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00:00', 175.00),

-- Venom: Son Dans
(2, 2, CURDATE(), '12:00:00', 175.00),
(2, 2, CURDATE(), '15:00:00', 175.00),
(2, 2, CURDATE(), '18:30:00', 200.00),
(2, 2, CURDATE(), '21:00:00', 200.00),
(2, 5, CURDATE(), '14:00:00', 150.00),
(2, 5, CURDATE(), '17:30:00', 150.00),

-- Moana 2
(3, 1, CURDATE(), '10:00:00', 125.00),
(3, 1, CURDATE(), '13:00:00', 125.00),
(3, 2, CURDATE(), '11:30:00', 150.00),
(3, 2, CURDATE(), '14:30:00', 150.00),
(3, 6, CURDATE(), '12:00:00', 150.00),
(3, 6, CURDATE(), '15:00:00', 150.00),

-- Wicked
(4, 4, CURDATE(), '14:00:00', 300.00),
(4, 4, CURDATE(), '18:00:00', 350.00),
(4, 4, CURDATE(), '21:30:00', 350.00),
(4, 8, CURDATE(), '15:00:00', 150.00),
(4, 8, CURDATE(), '19:00:00', 175.00),

-- Kraven
(5, 5, CURDATE(), '16:00:00', 150.00),
(5, 5, CURDATE(), '19:30:00', 175.00),
(5, 5, CURDATE(), '22:00:00', 175.00),
(5, 7, CURDATE(), '17:00:00', 225.00),
(5, 7, CURDATE(), '20:30:00', 250.00),

-- Nosferatu
(6, 1, CURDATE(), '20:00:00', 175.00),
(6, 1, CURDATE(), '23:00:00', 175.00),
(6, 9, CURDATE(), '19:00:00', 175.00),
(6, 9, CURDATE(), '22:00:00', 200.00);

-- Yarın ve sonraki günler için ekstra seanslar
INSERT INTO showtimes (movie_id, hall_id, show_date, show_time, price)
SELECT movie_id, hall_id, DATE_ADD(show_date, INTERVAL 1 DAY), show_time, price
FROM showtimes WHERE show_date = CURDATE();

INSERT INTO showtimes (movie_id, hall_id, show_date, show_time, price)
SELECT movie_id, hall_id, DATE_ADD(show_date, INTERVAL 2 DAY), show_time, price
FROM showtimes WHERE show_date = CURDATE();
