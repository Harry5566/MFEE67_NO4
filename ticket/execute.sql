SHOW DATABASES;

CREATE DATABASE `my_test_db`;

USE `my_test_db`;

SELECT DATABASE();

DESC `status`;
SELECT * FROM `trips`;
DROP TABLE `trips`;

-- TABLE
-- products  regions  cities  types  acts  status  images


SET FOREIGN_KEY_CHECKS=0;
SET FOREIGN_KEY_CHECKS=1;

--修改欄位
ALTER TABLE `products` CHANGE `created_at` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- 修改資料表內容
UPDATE `products`  set status_id = 1 WHERE id = 7;


