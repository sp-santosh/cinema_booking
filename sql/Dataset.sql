-- ROLES
CREATE TABLE roles (
  role_id INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(30) NOT NULL UNIQUE
);

-- USERS
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(30),
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- CINEMAS
CREATE TABLE cinemas (
  cinema_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  address_line1 VARCHAR(200) NOT NULL,
  city VARCHAR(100) NOT NULL,
  postcode VARCHAR(20) NOT NULL
);

-- HALLS
CREATE TABLE halls (
  hall_id INT AUTO_INCREMENT PRIMARY KEY,
  cinema_id INT NOT NULL,
  name VARCHAR(60) NOT NULL,
  FOREIGN KEY (cinema_id) REFERENCES cinemas(cinema_id),
  UNIQUE (cinema_id, name)
);

-- SEATS
CREATE TABLE seats (
  seat_id INT AUTO_INCREMENT PRIMARY KEY,
  hall_id INT NOT NULL,
  row_label VARCHAR(5) NOT NULL,
  seat_number INT NOT NULL,
  FOREIGN KEY (hall_id) REFERENCES halls(hall_id),
  UNIQUE (hall_id, row_label, seat_number)
);

-- MOVIES (UPDATED)
CREATE TABLE movies (
  movie_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  duration_minutes INT NOT NULL,
  age_rating VARCHAR(20) NOT NULL,
  movie_rating DECIMAL(3,1),   -- e.g. 8.4 from API
  description TEXT,
  is_active TINYINT(1) NOT NULL DEFAULT 1
);

-- SCREENINGS
CREATE TABLE screenings (
  screening_id INT AUTO_INCREMENT PRIMARY KEY,
  movie_id INT NOT NULL,
  hall_id INT NOT NULL,
  start_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'SCHEDULED',
  FOREIGN KEY (movie_id) REFERENCES movies(movie_id),
  FOREIGN KEY (hall_id) REFERENCES halls(hall_id),
  UNIQUE (hall_id, start_time)
);

-- BOOKINGS
CREATE TABLE bookings (
  booking_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  screening_id INT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'CONFIRMED',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_amount DECIMAL(10,2),
  FOREIGN KEY (customer_id) REFERENCES users(user_id),
  FOREIGN KEY (screening_id) REFERENCES screenings(screening_id)
);

-- TICKETS (SIMPLIFIED)
CREATE TABLE tickets (
  ticket_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  screening_id INT NOT NULL,
  seat_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
  FOREIGN KEY (screening_id) REFERENCES screenings(screening_id),
  FOREIGN KEY (seat_id) REFERENCES seats(seat_id),
  UNIQUE (screening_id, seat_id)
);

-- PAYMENTS
CREATE TABLE payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  method ENUM('CARD','CASH','PAYPAL') NOT NULL DEFAULT 'CARD',
  payment_status ENUM('PAID','FAILED','REFUNDED') NOT NULL DEFAULT 'PAID',
  paid_at DATETIME,
  FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
);