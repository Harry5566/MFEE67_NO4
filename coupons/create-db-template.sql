CREATE TABLE imgs (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `msg_id` INT NOT NULL,
  `file` VARCHAR(50) NOT NULL,
  `is_valid` TINYINT NOT NULL DEFAULT '1',
  FOREIGN KEY (`msg_id`) REFERENCES msgs(`id`)
);

DESC imgs;

SELECT * FROM `msgs` ;

SELECT * FROM `msgs` WHERE `id` = 1;

SELECT * FROM `imgs` WHERE `msg_id` = 1;

SELECT
 `msgs`.*,
 GROUP_CONCAT(`imgs`.`file` SEPARATOR ',') AS `imgs`
FROM `msgs` 
LEFT JOIN `imgs`
ON `msgs`.`id` = `imgs`.`msg_id`
WHERE `msgs`.`id` = 1
GROUP BY `msgs`.`id`;

CREATE TABLE `replies`(
  `id` INT AUTO_INCREMENT PRIMARY KEY,
 `text` VARCHAR(200),
  `msg_id` INT
);



DESC `replies`;
DROP TABLE `actives`;

CREATE TABLE `actives`(
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(20) NOT NULL,
  `msg_id` VARCHAR(20) NOT NULL,
  `start_at` DATE NOT NULL,
  `end_at` INT NOT NULL
);
ALTER TABLE `actives`
MODIFY COLUMN start_at DATETIME,
MODIFY COLUMN end_at DATETIME;

DESC `actives`;


SET FOREIGN_KEY_CHECKS =0;

SET FOREIGN_KEY_CHECKS =1;
DROP TABLE `users`;


USE my_db;



CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
  `email` VARCHAR(50) NOT NULL, 
  `password` VARCHAR(100) NOT NULL, 
  `name` VARCHAR(30) NOT NULL,
  `img` VARCHAR(30),
  `create_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `is_valid` TINYINT NOT NULL DEFAULT 1
);  

DESC `users`;

SELECT * FROM `users`;



SELECT  COUNT(*) as count  FROM `users` WHERE `email` = 'ben@ben.com'


SELECT * FROM `users`;

-- CREATE Table `tags`(
--   `id` INT AUTO_INCREMENT PRIMARY KEY,
-- `name` VARCHAR(20) NOT NULL,
-- `is_valid` TINYINT NOT NULL DEFAULT 1
-- );
 
--  SELECT * FROM `tags`;
----------------------------------------------------------
SHOW DATABASES;

CREATE DATABASE IF NOT EXISTS topics;

USE topics;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS usage_scopes;
DROP TABLE IF EXISTS coupon_status;
DROP TABLE IF EXISTS discount_types;




-- 折扣類型表
CREATE TABLE discount_types (
  id TINYINT AUTO_INCREMENT PRIMARY KEY COMMENT '折價類型編號',
  name VARCHAR(50) NOT NULL COMMENT '範例：1 = cash（現金）、2 = percent（百分比）'
);
INSERT INTO discount_types (name) VALUES
('cash'),    
('percent');  

SELECT * FROM  discount_types;

-- 優惠券狀態表
CREATE TABLE coupon_status (
  id TINYINT AUTO_INCREMENT PRIMARY KEY COMMENT '優惠劵狀態編號',
  name VARCHAR(50) NOT NULL COMMENT '範例：0 = inactive、1 = active、2 = expired'
);


INSERT INTO coupon_status (name) VALUES
('inactive'), 
('active'); 

SELECT * FROM coupon_status;

-- 使用範圍表
CREATE TABLE usage_scopes (
  id TINYINT AUTO_INCREMENT PRIMARY KEY COMMENT '使用範圍編號',
  name VARCHAR(50) NOT NULL COMMENT '範例：1 = 全站通用、2 = 行程活動、3 = 各式票卷...'
);

INSERT INTO usage_scopes (name) VALUES
('全站通用'),    
('行程活動'),     
('各式票卷');     

SELECT * FROM usage_scopes;


-- 優惠券主表
CREATE TABLE coupons (
  id INT AUTO_INCREMENT PRIMARY KEY COMMENT '優惠券編號',
  name VARCHAR(100) NOT NULL COMMENT '優惠券名稱',
  discount_code VARCHAR(50) NOT NULL UNIQUE COMMENT '折扣碼代碼',
  discount DECIMAL(8,2) NOT NULL COMMENT '折扣數值（如：100 或 10）',
  discount_type_id TINYINT NOT NULL COMMENT '折扣類型 元 %',
  quantity INT DEFAULT 0 COMMENT '發行數量max',
  start_date DATE NULL COMMENT '開始日期',
  end_date DATE NULL COMMENT '截止日期',
  status_id TINYINT DEFAULT NULL COMMENT '狀態（未啟用、啟用中）',
  usage_scope_id TINYINT DEFAULT NULL COMMENT '使用範圍（依活動類型）',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '最後更新時間',
  is_valid TINYINT DEFAULT 1 COMMENT '軟刪除，刪除資料',
  FOREIGN KEY (discount_type_id) REFERENCES discount_types(id),
  FOREIGN KEY (status_id) REFERENCES coupon_status(id),
  FOREIGN KEY (usage_scope_id) REFERENCES usage_scopes(id)
);

INSERT INTO coupons (
  name, discount_code, discount, discount_type_id, quantity,
  start_date, end_date, status_id, usage_scope_id,
  created_at, updated_at
) VALUES
("越遊啟程券", "X9T3M8R2", 50, 1, 120, "2025-08-13", "2025-08-24", 1, 1, "2025-07-19 14:52:30", "2025-07-19 14:52:30"),
("初見越南折扣碼", "1T9VWQHE", 50, 1, 158, "2025-06-30", "2025-07-10", 1, 1, "2025-06-06 10:44:18", "2025-06-06 10:44:18"),
("首購越旅好禮", "NM87DQSD", 100, 1, 179, "2025-05-04", "2025-05-18", 1, 3, "2025-04-12 08:15:43", "2025-04-12 08:15:43"),
("旅越新客獎勵券", "WKVJVKT3", 250, 2, 106, "2025-07-21", "2025-08-03", 2, 1, "2025-07-02 11:27:01", "2025-07-02 11:27:01"),
("初越禮包券", "9ARH3G52", 100, 2, 137, "2025-04-17", "2025-04-29", 1, 1, "2025-03-28 22:51:12", "2025-03-28 22:51:12"),
("越來越划算券", "PK7NM1P6", 100, 1, 97, "2025-05-15", "2025-05-26", 1, 3, "2025-04-22 17:38:59", "2025-04-22 17:38:59"),
("新人專屬旅遊券", "K9OUXWXH", 50, 2, 155, "2025-06-02", "2025-06-14", 2, 1, "2025-05-09 21:12:44", "2025-05-09 21:12:44"),
("啟航越南迎新折", "VHJZCVE9", 100, 2, 197, "2025-08-22", "2025-09-02", 1, 1, "2025-07-26 09:48:30", "2025-07-26 09:48:30"),
("越初體驗折價碼", "7LIWWQ9K", 100, 2, 148, "2025-07-28", "2025-08-08", 1, 1, "2025-07-01 18:06:55", "2025-07-01 18:06:55"),
("第一次越遊優惠券", "8VGF7OF9", 50, 2, 186, "2025-04-22", "2025-05-04", 2, 3, "2025-03-27 23:21:10", "2025-03-27 23:21:10"),
("富國島陽光券", "ZHDC9JWQ", 250, 1, 112, "2025-09-10", "2025-09-22", 1, 2, "2025-08-27 07:55:41", "2025-08-27 07:55:41"),
("峴港 Chill 折券", "9RYESAFM", 150, 2, 195, "2025-06-11", "2025-06-23", 1, 3, "2025-05-17 14:23:12", "2025-05-17 14:23:12"),
("河內歷史探險券", "IK6NAO7W", 50, 1, 53, "2025-08-05", "2025-08-17", 2, 3, "2025-07-09 03:31:22", "2025-07-09 03:31:22"),
("巴拿山空中漫遊券", "16GWA6ZJ", 100, 1, 70, "2025-05-27", "2025-06-07", 2, 1, "2025-05-04 18:18:53", "2025-05-04 18:18:53"),
("下龍灣浪漫遊優惠", "JS344B4H", 50, 2, 158, "2025-07-19", "2025-07-30", 1, 2, "2025-07-01 05:33:02", "2025-07-01 05:33:02"),
("胡志明夜遊驚喜券", "SKUZVVY8", 250, 2, 92, "2025-04-29", "2025-05-10", 1, 3, "2025-04-10 12:43:36", "2025-04-10 12:43:36"),
("沙壩避暑優惠碼", "NC89NOPH", 200, 1, 194, "2025-08-16", "2025-08-27", 2, 1, "2025-07-21 16:59:44", "2025-07-21 16:59:44"),
("美奈沙丘折扣券", "U3IGIVO7", 150, 2, 170, "2025-09-05", "2025-09-16", 1, 2, "2025-08-19 10:03:11", "2025-08-19 10:03:11"),
("會安古鎮老街券", "N12SB9LO", 100, 1, 126, "2025-06-07", "2025-06-19", 1, 2, "2025-05-14 15:22:19", "2025-05-14 15:22:19"),
("越南文化探索折價券", "F9XYHUKF", 100, 2, 199, "2025-05-10", "2025-05-22", 2, 3, "2025-04-19 07:48:00", "2025-04-19 07:48:00"),
("越遊全境暢行券", "Q93YBZYD", 300, 2, 122, "2025-06-18", "2025-06-30", 2, 3, "2025-05-26 11:59:29", "2025-05-26 11:59:29"),
("限時越南行折扣碼", "FV74SP69", 50, 2, 66, "2025-04-14", "2025-04-25", 1, 1, "2025-03-25 19:33:44", "2025-03-25 19:33:44"),
("爆款越遊專屬券", "NUNV5Y2U", 150, 2, 149, "2025-05-22", "2025-06-03", 1, 3, "2025-04-29 21:03:50", "2025-04-29 21:03:50"),
("越夏優惠專案", "1IK1AKN5", 200, 1, 169, "2025-07-04", "2025-07-15", 2, 2, "2025-06-10 09:27:32", "2025-06-10 09:27:32"),
("探索越南限時折", "7T96VQ5A", 100, 2, 154, "2025-06-25", "2025-07-06", 1, 2, "2025-06-01 06:55:41", "2025-06-01 06:55:41"),
("越旅人氣熱銷券", "3AL8K1V8", 100, 2, 97, "2025-08-01", "2025-08-12", 2, 3, "2025-07-06 12:46:16", "2025-07-06 12:46:16"),
("快閃越南驚喜折", "XAMTI1J9", 150, 1, 141, "2025-09-15", "2025-09-27", 1, 1, "2025-08-30 13:18:33", "2025-08-30 13:18:33"),
("爆品越遊限量券", "Q7UIEFZ6", 100, 2, 110, "2025-07-11", "2025-07-22", 2, 2, "2025-06-16 08:11:29", "2025-06-16 08:11:29"),
("人氣行程折價碼", "SC5SMGVV", 250, 2, 128, "2025-06-20", "2025-07-01", 2, 1, "2025-05-27 11:40:38", "2025-05-27 11:40:38"),
("越南精選行程券", "TAYYOYJI", 100, 1, 81, "2025-05-30", "2025-06-11", 2, 2, "2025-05-07 07:25:56", "2025-05-07 07:25:56"),
("必遊熱點專屬折", "3WGMG55A", 50, 1, 164, "2025-08-10", "2025-08-21", 1, 3, "2025-07-15 17:33:11", "2025-07-15 17:33:11"),
("人氣必去優惠碼", "WD9R5IBB", 200, 1, 75, "2025-07-23", "2025-08-03", 2, 1, "2025-06-28 08:46:02", "2025-06-28 08:46:02"),
("夏季爆款行程券", "V7UFPVMF", 100, 2, 134, "2025-06-13", "2025-06-25", 2, 3, "2025-05-20 21:37:30", "2025-05-20 21:37:30"),
("人氣排行優惠券", "PSMUDKGW", 150, 1, 121, "2025-07-07", "2025-07-18", 1, 2, "2025-06-12 04:18:09", "2025-06-12 04:18:09"),
("行程推薦折扣碼", "QXVPS73K", 300, 2, 186, "2025-05-18", "2025-05-29", 2, 1, "2025-04-25 15:06:50", "2025-04-25 15:06:50"),
("越旅好評折價券", "MDAKOZP5", 100, 1, 133, "2025-06-28", "2025-07-09", 1, 3, "2025-06-04 13:52:37", "2025-06-04 13:52:37"),
("用戶五星推薦券", "0QCTIPKP", 250, 1, 94, "2025-08-07", "2025-08-18", 1, 1, "2025-07-11 20:04:23", "2025-07-11 20:04:23"),
("熱門景點限時折", "YO6NIDNG", 150, 2, 188, "2025-09-01", "2025-09-12", 2, 3, "2025-08-15 18:09:47", "2025-08-15 18:09:47"),
("越旅大賞優惠碼", "U5J9QY4T", 200, 1, 117, "2025-04-25", "2025-05-06", 2, 3, "2025-04-05 06:41:36", "2025-04-05 06:41:36"),
("黑五越南特賣券", "29FP7TMO", 200, 1, 171, "2025-07-27", "2025-08-08", 1, 3, "2025-07-02 00:23:58", "2025-07-02 00:23:58"),
("越玩快閃折扣碼", "NXDSBPOV", 150, 1, 169, "2025-05-23", "2025-06-04", 1, 3, "2025-05-02 21:03:13", "2025-05-02 21:03:13"),
("限量100張旅越券", "B4DH45JL", 100, 1, 57, "2025-08-05", "2025-08-16", 2, 1, "2025-07-13 04:52:34", "2025-07-13 04:52:34"),
("越式按摩放鬆券", "YQTGGGBI", 50, 2, 115, "2025-06-29", "2025-07-11", 2, 3, "2025-06-11 09:38:16", "2025-06-11 09:38:16"),
("水上市場體驗優惠", "LJ5OUDF5", 200, 1, 180, "2025-05-14", "2025-05-26", 1, 1, "2025-04-27 19:25:40", "2025-04-27 19:25:40"),
("手作DIY折扣券", "3LKT9L7C", 50, 2, 141, "2025-07-23", "2025-08-05", 1, 2, "2025-07-04 10:47:08", "2025-07-04 10:47:08"),
("越風藝術之旅券", "9E3CG0H8", 150, 2, 147, "2025-04-18", "2025-04-30", 1, 2, "2025-03-23 06:20:44", "2025-03-23 06:20:44"),
("自然秘境探險券", "E918O73D", 50, 2, 151, "2025-08-06", "2025-08-17", 1, 1, "2025-07-09 17:57:15", "2025-07-09 17:57:15"),
("古蹟巡禮文化券", "XJU1MTSI", 200, 1, 110, "2025-05-20", "2025-06-01", 2, 2, "2025-04-25 15:13:38", "2025-04-25 15:13:38"),
("越南宗教巡禮券", "M3I2NY4R", 150, 1, 188, "2025-07-15", "2025-07-26", 1, 3, "2025-06-28 12:06:10", "2025-06-28 12:06:10"),
("海島放空度假券", "9WJ3WQH7", 100, 2, 143, "2025-06-03", "2025-06-15", 1, 3, "2025-05-07 03:35:29", "2025-05-07 03:35:29"),
("夜晚遊船折扣碼", "GXVHU0XF", 250, 1, 122, "2025-08-25", "2025-09-06", 1, 1, "2025-07-29 18:10:50", "2025-07-29 18:10:50"),
("沙灘狂歡折扣券", "B7K9P2ZD", 180, 1, 90, "2025-04-12", "2025-04-25", 1, 2, "2025-03-17 20:28:22", "2025-03-17 20:28:22"),
("河內風情優惠碼", "Q8XN3GJT", 120, 2, 75, "2025-05-30", "2025-06-10", 2, 3, "2025-05-07 11:45:53", "2025-05-07 11:45:53"),
("美食饗宴專屬券", "V6MFJYL4", 200, 1, 60, "2025-07-08", "2025-07-18", 1, 1, "2025-06-19 13:57:29", "2025-06-19 13:57:29"),
("探險樂園特惠券", "D9ZHT1CX", 150, 1, 130, "2025-08-05", "2025-08-20", 1, 2, "2025-07-10 06:40:58", "2025-07-10 06:40:58");

SELECT id, name, status_id FROM coupons WHERE id = 1;

SELECT * FROM coupons;
SHOW WARNINGS;

SHOW DATABASES;



SELECT * FROM coupons WHERE id = 2 AND is_valid = 1;
SELECT * FROM coupons WHERE is_valid = 0;

SELECT id, is_valid FROM coupons WHERE id = 2;

SELECT * FROM coupons ORDER BY id DESC LIMIT 10;