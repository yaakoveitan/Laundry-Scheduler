CREATE DATABASE IF NOT EXISTS Laundry;
USE Laundry;

DROP TABLE IF EXISTS slots;
DROP TABLE IF EXISTS users;

CREATE TABLE users 
(
	userid VARCHAR(45),
    password VARCHAR(80) NOT NULL,
	firstName VARCHAR(45) NOT NULL,
    lastName VARCHAR(45) NOT NULL,
    aptNumber TINYINT(10) NOT NULL,
    email VARCHAR(45),
    phone VARCHAR(20),
    PRIMARY KEY (userid),
    UNIQUE (aptNumber)
);

CREATE TABLE slots
(
	start DATETIME(0),
    userid VARCHAR(45),
    PRIMARY KEY (start),
    FOREIGN KEY (userid)
		REFERENCES users(userid)
        ON DELETE CASCADE
);