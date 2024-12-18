CREATE TABLE students (
    nim INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    ukt_paid BOOLEAN DEFAULT FALSE
);

INSERT INTO students (nim, name, ukt_paid) VALUES
(10001, 'Annisa', FALSE),
(10002, 'Budi', FALSE),
(10003, 'Citra', FALSE),
(10004, 'Denny', FALSE),
(10005, 'Eko', FALSE);
