-- Drop and Create Database
DROP DATABASE IF EXISTS Airline;
CREATE DATABASE IF NOT EXISTS Airline;
USE Airline;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Flights Table
CREATE TABLE IF NOT EXISTS flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_no VARCHAR(20) UNIQUE NOT NULL,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_time TIME NOT NULL,
    departure_date DATE NOT NULL,
    total_seats INT DEFAULT 240,
    booked_seats INT DEFAULT 170,
    base_price DECIMAL(10,2) DEFAULT 299.00,
    status ENUM('On Time', 'Delayed', 'Cancelled', 'Boarding') DEFAULT 'On Time'
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    flight_id INT,
    passenger_name VARCHAR(100) NOT NULL,
    passenger_email VARCHAR(100),
    passenger_phone VARCHAR(20),
    ticket_class ENUM('economy', 'business', 'first') DEFAULT 'economy',
    emergency_type ENUM('none', 'pregnant', 'physically_challenged', 'medical') DEFAULT 'none',
    number_of_tickets INT DEFAULT 1,
    total_price DECIMAL(10,2),
    booking_status ENUM('confirmed', 'cancelled', 'pending') DEFAULT 'confirmed',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (flight_id) REFERENCES flights(id) ON DELETE SET NULL
);

-- Hotels Table
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL,
    price_per_night DECIMAL(10,2),
    rating INT DEFAULT 4,
    image_url VARCHAR(500)
);

-- Car Rentals Table
CREATE TABLE IF NOT EXISTS car_rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_name VARCHAR(100) NOT NULL,
    car_type VARCHAR(50),
    price_per_day DECIMAL(10,2),
    available BOOLEAN DEFAULT TRUE
);

-- Support Tickets Table (Emergency)
CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    passenger_name VARCHAR(100),
    emergency_type VARCHAR(50),
    medical_details TEXT,
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
);

-- Insert Pre-defined Flights (5 flights as requested)
INSERT INTO flights (flight_no, origin, destination, departure_time, departure_date, total_seats, booked_seats, base_price, status) VALUES
('NX101', 'Dhaka', 'Tokyo', '19:00:00', CURDATE() + INTERVAL 1 DAY, 240, 170, 599.00, 'On Time'),
('NX102', 'Dhaka', 'New York', '19:00:00', CURDATE() + INTERVAL 1 DAY, 240, 170, 899.00, 'On Time'),
('NX103', 'Dhaka', 'Sweden', '21:00:00', CURDATE() + INTERVAL 1 DAY, 240, 170, 749.00, 'On Time'),
('NX104', 'Germany', 'Netherlands', '17:00:00', CURDATE() + INTERVAL 1 DAY, 240, 170, 199.00, 'On Time'),
('NX105', 'Seoul', 'Okinawa', '19:30:00', CURDATE() + INTERVAL 1 DAY, 240, 170, 449.00, 'On Time');

-- Insert Hotels for destinations
INSERT INTO hotels (name, location, price_per_night, rating) VALUES
('Tokyo Grand Hotel', 'Tokyo, Japan', 299.00, 5),
('The Plaza', 'New York, USA', 459.00, 5),
('Stockholm Royal', 'Stockholm, Sweden', 349.00, 4),
('Amsterdam Marriott', 'Amsterdam, Netherlands', 279.00, 4),
('Okinawa Beach Resort', 'Okinawa, Japan', 389.00, 5),
('Ritz Carlton Tokyo', 'Tokyo, Japan', 599.00, 5),
('Hilton New York', 'New York, USA', 389.00, 4);

-- Insert Car Rentals
INSERT INTO car_rentals (car_name, car_type, price_per_day) VALUES
('Toyota Camry', 'SUV', 89.00),
('Uber Premium', 'Luxury Sedan', 120.00),
('BMW X5', 'Luxury SUV', 199.00),
('Mercedes E-Class', 'Executive', 179.00),
('Tesla Model S', 'Electric Luxury', 249.00),
('Honda CR-V', 'SUV', 79.00);

-- Insert Demo User (user@gmail.com / 1234)
INSERT INTO users (name, email, password, phone) VALUES 
('Demo User', 'user@gmail.com', MD5('1234'), '+1234567890'),
('John Doe', 'john@example.com', MD5('john123'), '+1987654321');

-- Insert Sample Bookings for testing
INSERT INTO bookings (booking_ref, user_id, flight_id, passenger_name, passenger_email, ticket_class, emergency_type, number_of_tickets, total_price, booking_status) VALUES
('NXDEMO001', 1, 1, 'Demo User', 'user@gmail.com', 'economy', 'none', 1, 599.00, 'confirmed'),
('NXDEMO002', 1, 2, 'Demo User', 'user@gmail.com', 'business', 'pregnant', 2, 2697.00, 'confirmed');

-- Add hotel_bookings table (if not exists)
CREATE TABLE IF NOT EXISTS hotel_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    hotel_id INT,
    hotel_name VARCHAR(100),
    location VARCHAR(100),
    check_in_date DATE,
    check_out_date DATE,
    price_per_night DECIMAL(10,2),
    total_price DECIMAL(10,2),
    booking_status VARCHAR(50) DEFAULT 'confirmed',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE SET NULL
);

-- Add car_bookings table (if not exists)
CREATE TABLE IF NOT EXISTS car_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    car_id INT,
    car_name VARCHAR(100),
    car_type VARCHAR(50),
    pickup_date DATE,
    return_date DATE,
    price_per_day DECIMAL(10,2),
    total_price DECIMAL(10,2),
    booking_status VARCHAR(50) DEFAULT 'confirmed',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES car_rentals(id) ON DELETE SET NULL
);