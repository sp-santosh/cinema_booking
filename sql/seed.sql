-- Seed Data for CineBook

-- 1. Roles
INSERT INTO roles (role_id, role_name) VALUES 
(1, 'Admin'),
(2, 'Customer');

-- 2. Initial Users
-- Passwords are hashed versions of 'password123' (bcrypt cost 12)
INSERT INTO users (user_id, role_id, first_name, last_name, email, password_hash, phone) VALUES 
(1, 1, 'System', 'Admin', 'admin@cinebook.com', '$2y$12$R.PcwE/i8nIurAsR/hZ15OXJ6Jd/A2OOT3s.D2h3N5M17WbI.Y4L6', '01234567890'),
(2, 2, 'John', 'Doe', 'john@example.com', '$2y$12$R.PcwE/i8nIurAsR/hZ15OXJ6Jd/A2OOT3s.D2h3N5M17WbI.Y4L6', '07890123456'),
(3, 2, 'Jane', 'Smith', 'jane@example.com', '$2y$12$R.PcwE/i8nIurAsR/hZ15OXJ6Jd/A2OOT3s.D2h3N5M17WbI.Y4L6', '07777777777');

-- 3. Cinemas
INSERT INTO cinemas (cinema_id, name, address_line1, city, postcode) VALUES 
(1, 'CineBook Central Skyline', '123 Entertainment Way', 'London', 'WC2H 7LT'),
(2, 'CineBook Marina', '45 Ocean Drive', 'Brighton', 'BN2 1TW');

-- 4. Halls (Screens)
INSERT INTO halls (hall_id, cinema_id, name) VALUES 
(1, 1, 'Screen 1 - IMAX'),
(2, 1, 'Screen 2 - Standard'),
(3, 2, 'Screen 1 - Standard');

-- 5. Seats (Generating a small grid for 'Screen 1 - Standard' in London, just as an example)
-- We will add Row A (seats 1-5), Row B (seats 1-5), Row C (seats 1-5) for Hall 1
INSERT INTO seats (hall_id, row_label, seat_number) VALUES 
(1, 'A', 1), (1, 'A', 2), (1, 'A', 3), (1, 'A', 4), (1, 'A', 5),
(1, 'B', 1), (1, 'B', 2), (1, 'B', 3), (1, 'B', 4), (1, 'B', 5),
(1, 'C', 1), (1, 'C', 2), (1, 'C', 3), (1, 'C', 4), (1, 'C', 5),
-- Hall 2 (Smaller screen)
(2, 'A', 1), (2, 'A', 2), (2, 'A', 3), 
(2, 'B', 1), (2, 'B', 2), (2, 'B', 3);

-- 6. Movies
INSERT INTO movies (movie_id, title, duration_minutes, age_rating, movie_rating, description) VALUES 
(1, 'Dune: Part Two', 166, '12A', 8.9, 'Paul Atreides unites with Chani and the Fremen while on a warpath of revenge against the conspirators who destroyed his family.'),
(2, 'Oppenheimer', 180, '15', 8.4, 'The story of American scientist, J. Robert Oppenheimer, and his role in the development of the atomic bomb.'),
(3, 'Kung Fu Panda 4', 94, 'PG', 6.7, 'After Po is tapped to become the Spiritual Leader of the Valley of Peace, he needs to find and train a new Dragon Warrior.'),
(4, 'Godzilla x Kong: The New Empire', 115, '12A', 6.6, 'Two ancient titans, Godzilla and Kong, clash in an epic battle as humans unravel their intertwined origins.'),
(5, 'Civil War', 109, '15', 7.6, 'A journey across a dystopian future America, following a team of military-embedded journalists as they race against time.');

-- 7. Screenings (Scheduling some for upcoming dates)
-- NOTE: We use DATE_ADD(NOW(), INTERVAL X DAY) so the seed data is always valid relative to when you run it!
INSERT INTO screenings (screening_id, movie_id, hall_id, start_time, end_time, status) VALUES 
(1, 1, 1, DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL '1 03:00' DAY_MINUTE), 'SCHEDULED'), -- Dune in London IMAX
(2, 2, 2, DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL '1 03:30' DAY_MINUTE), 'SCHEDULED'), -- Oppenheimer in London Standard
(3, 3, 3, DATE_ADD(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL '2 02:00' DAY_MINUTE), 'SCHEDULED'), -- Kung Fu Panda in Brighton
(4, 1, 3, DATE_ADD(NOW(), INTERVAL '2 04:00' DAY_MINUTE), DATE_ADD(NOW(), INTERVAL '2 07:00' DAY_MINUTE), 'SCHEDULED'), -- Dune in Brighton (starts later)
(5, 5, 1, DATE_ADD(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL '3 02:30' DAY_MINUTE), 'SCHEDULED'); -- Civil War in London IMAX

-- 8. Bookings (Example bookings for John Doe on Screening 1)
INSERT INTO bookings (booking_id, customer_id, screening_id, status, total_amount) VALUES 
(1, 2, 1, 'CONFIRMED', 25.00),
(2, 3, 2, 'CONFIRMED', 12.50);

-- 9. Tickets
-- John booked Hall 1, Seats A1 and A2 for Screening 1
-- Note: 'seat_id' matches the auto-increment IDs for Hall 1, Row A Seats 1/2 from above (IDs 1 and 2 assuming an empty table)
INSERT INTO tickets (ticket_id, booking_id, screening_id, seat_id, price) VALUES 
(1, 1, 1, 1, 12.50),
(2, 1, 1, 2, 12.50),
(3, 2, 2, 16, 12.50); -- Jane booked Hall 2, Seat A1 (ID 16 based on insertion order)

-- 10. Payments
INSERT INTO payments (payment_id, booking_id, amount, method, payment_status, paid_at) VALUES 
(1, 1, 25.00, 'CARD', 'PAID', NOW()),
(2, 2, 12.50, 'PAYPAL', 'PAID', NOW());

