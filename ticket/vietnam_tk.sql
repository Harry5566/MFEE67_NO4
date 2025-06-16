CREATE DATABASE viet_tk;


USE `viet_tk`;

SELECT DATABASE();

CREATE TABLE status(
    id TINYINT PRIMARY KEY,
    name VARCHAR(100)
);

INSERT INTO status (id, name) VALUES
(1, '下架'),
(2, '上架'),
(3, '售完');


CREATE TABLE regions(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

INSERT INTO regions (name)VALUES
('北越'),
('中越'),
('南越');


CREATE TABLE cities(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    region_id INT,
    FOREIGN KEY (region_id) REFERENCES regions(id)
);

INSERT INTO cities (name, region_id)VALUES
('河內',	1),
('沙壩',	1),
('清化',	1),
('下龍市',	1),
('海防市',	1),
('義安',	1),
('順化',	2),
('峴港',	2),
('會安',	2),
('歸仁',	2),
('胡志明市',	3),
('芽莊',	3),
('富國島',	3),
('潘切',	3),
('頭頓',	3),
('大叻',	3);


CREATE TABLE types(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

INSERT INTO types (name)VALUES
('門票'),
('交通');
SELECT * FROM types;


CREATE TABLE acts(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    type_id INT,
    FOREIGN KEY (type_id) REFERENCES types(id)
);

INSERT INTO acts (name, type_id)VALUES
('公園＆花園',	1),
('樂園',	1),
('自然風景',	1),
('展演活動',	1),
('動物園＆水族館',	1),
('博物館＆美術館',	1),
('景點通票',	1),
('歷史景點',	1),
('巴士',	2),
('包車接送',	2),
('機車租借',	2),
('鐵路車票',	2);


CREATE TABLE products(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    price DECIMAL(10,0) DEFAULT 0,
    stock INT DEFAULT 0,
    view_count INT DEFAULT 0,
    status_id TINYINT DEFAULT 1,
    region_id INT,
    city_id INT,
    type_id INT,
    act_id INT,
    is_valid TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    intro VARCHAR(10000),
    FOREIGN KEY (status_id) REFERENCES status(id),
    FOREIGN KEY (region_id) REFERENCES regions(id),
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (type_id) REFERENCES types(id),
    FOREIGN KEY (act_id) REFERENCES acts(id)
);

DESC products;
SELECT * FROM products;
DROP TABLE products;



CREATE TABLE images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    path VARCHAR(200),
    product_id INT,
    FOREIGN KEY (product_id) REFERENCES products(id)
);


