USE demo;

CREATE TABLE IF NOT EXISTS demo_table(
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(1024) NOT NULL
);

INSERT INTO demo_table(fullname)
VALUES ("Пепи"), ("Краси"), ("Дидо");