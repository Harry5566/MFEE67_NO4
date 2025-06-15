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
('cash'),     -- id = 1
('percent');  -- id = 2

SELECT * FROM  discount_types;

-- 優惠券狀態表
CREATE TABLE coupon_status (
  id TINYINT AUTO_INCREMENT PRIMARY KEY COMMENT '優惠劵狀態編號',
  name VARCHAR(50) NOT NULL COMMENT '範例：0 = inactive、1 = active、2 = expired'
);


INSERT INTO coupon_status (name) VALUES
('inactive'),  -- id = 1
('active');   -- id = 2

SELECT * FROM coupon_status;

-- 使用範圍表
CREATE TABLE usage_scopes (
  id TINYINT AUTO_INCREMENT PRIMARY KEY COMMENT '使用範圍編號',
  name VARCHAR(50) NOT NULL COMMENT '範例：1 = 全站通用、2 = 行程活動、3 = 各式票卷...'
);

INSERT INTO usage_scopes (name) VALUES
('全站通用'),     -- id = 1
('行程活動'),     -- id = 2
('各式票卷');     -- id = 3

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

INSERT INTO coupons (name, discount_code, discount, discount_type_id, quantity, start_date, end_date, status_id, usage_scope_id) VALUES
("越遊啟程券", "X9T3M8R2", 50, 1, 120, "2025-08-13", "2025-08-24", 1, 1),
("初見越南折扣碼", "1T9VWQHE", 50, 1, 158, "2025-06-30", "2025-07-10", 1, 1),
("首購越旅好禮", "NM87DQSD", 100, 1, 179, "2025-05-04", "2025-05-18", 1, 3),
("旅越新客獎勵券", "WKVJVKT3", 250, 2, 106, "2025-07-21", "2025-08-03", 2, 1),
("初越禮包券", "9ARH3G52", 100, 2, 137, "2025-04-17", "2025-04-29", 1, 1),
("越來越划算券", "PK7NM1P6", 100, 1, 97, "2025-05-15", "2025-05-26", 1, 3),
("新人專屬旅遊券", "K9OUXWXH", 50, 2, 155, "2025-06-02", "2025-06-14", 2, 1),
("啟航越南迎新折", "VHJZCVE9", 100, 2, 197, "2025-08-22", "2025-09-02", 1, 1),
("越初體驗折價碼", "7LIWWQ9K", 100, 2, 148, "2025-07-28", "2025-08-08", 1, 1),
("第一次越遊優惠券", "8VGF7OF9", 50, 2, 186, "2025-04-22", "2025-05-04", 2, 3),
("富國島陽光券", "ZHDC9JWQ", 250, 1, 112, "2025-09-10", "2025-09-22", 1, 2),
("峴港 Chill 折券", "9RYESAFM", 150, 2, 195, "2025-06-11", "2025-06-23", 1, 3),
("河內歷史探險券", "IK6NAO7W", 50, 1, 53, "2025-08-05", "2025-08-17", 2, 3),
("巴拿山空中漫遊券", "16GWA6ZJ", 100, 1, 70, "2025-05-27", "2025-06-07", 2, 1),
("下龍灣浪漫遊優惠", "JS344B4H", 50, 2, 158, "2025-07-19", "2025-07-30", 1, 2),
("胡志明夜遊驚喜券", "SKUZVVY8", 250, 2, 92, "2025-04-29", "2025-05-10", 1, 3),
("沙壩避暑優惠碼", "NC89NOPH", 200, 1, 194, "2025-08-16", "2025-08-27", 2, 1),
("美奈沙丘折扣券", "U3IGIVO7", 150, 2, 170, "2025-09-05", "2025-09-16", 1, 2),
("中越文化探索券", "MX9CXYFV", 100, 1, 179, "2025-04-12", "2025-04-24", 2, 1),
("越南經典旅線折價券", "HCCDEY4S", 100, 1, 132, "2025-06-18", "2025-06-29", 2, 3),
("越味小吃券", "TVQYTBW0", 100, 2, 66, "2025-07-15", "2025-07-27", 1, 3),
("河粉天天享折券", "T12PTJ9U", 250, 1, 114, "2025-05-02", "2025-05-14", 2, 3),
("越式咖啡折扣碼", "5AWZ3AH3", 100, 2, 148, "2025-08-27", "2025-09-08", 1, 3),
("越南甜點優惠券", "KAIG70P6", 150, 1, 78, "2025-06-07", "2025-06-18", 1, 2),
("胡志明夜市美食券", "0FCOEOR6", 250, 1, 57, "2025-07-10", "2025-07-21", 2, 2),
("美食走透透折扣碼", "ANTB0RWY", 200, 1, 65, "2025-04-28", "2025-05-10", 2, 3),
("越食派對折扣券", "4JICKQA0", 150, 1, 195, "2025-05-21", "2025-06-01", 1, 1),
("烤肉火鍋吃到折券", "U2EHSMOH", 100, 1, 163, "2025-08-01", "2025-08-12", 2, 3),
("越旅雙11折價碼", "5UOKRKYE", 50, 2, 138, "2025-06-10", "2025-06-20", 2, 3),
("春節富國行旅券", "S7RC3TW0", 150, 2, 81, "2025-07-07", "2025-07-19", 1, 2),
("夏日越南玩樂券", "ZQZXQW40", 50, 2, 137, "2025-04-05", "2025-04-17", 1, 1),
("中秋越旅限時券", "MSJN6UGJ", 50, 1, 67, "2025-09-01", "2025-09-12", 2, 3),
("越南假期閃購券", "FQ4Y9A8U", 150, 1, 153, "2025-06-22", "2025-07-03", 1, 3),
("限時48H越遊碼", "H4BS5WD1", 250, 1, 116, "2025-04-10", "2025-04-22", 1, 1),
("黑五越南特賣券", "29FP7TMO", 200, 1, 171, "2025-07-27", "2025-08-08", 1, 3),
("越玩快閃折扣碼", "NXDSBPOV", 150, 1, 169, "2025-05-23", "2025-06-04", 1, 3),
("限量100張旅越券", "B4DH45JL", 100, 1, 57, "2025-08-05", "2025-08-16", 2, 1),
("越式按摩放鬆券", "YQTGGGBI", 50, 2, 115, "2025-06-29", "2025-07-11", 2, 3),
("水上市場體驗優惠", "LJ5OUDF5", 200, 1, 180, "2025-05-14", "2025-05-26", 1, 1),
("手作DIY折扣券", "3LKT9L7C", 50, 2, 141, "2025-07-23", "2025-08-05", 1, 2),
("越風藝術之旅券", "9E3CG0H8", 150, 2, 147, "2025-04-18", "2025-04-30", 1, 2),
("自然秘境探險券", "E918O73D", 50, 2, 151, "2025-08-06", "2025-08-17", 1, 1),
("古蹟巡禮文化券", "XJU1MTSI", 200, 1, 110, "2025-05-20", "2025-06-01", 2, 2),
("越南宗教巡禮券", "M3I2NY4R", 150, 1, 188, "2025-07-15", "2025-07-26", 1, 3),
("海島放空度假券", "9WJ3WQH7", 100, 2, 143, "2025-06-03", "2025-06-15", 1, 3),
("夜晚遊船折扣碼", "GXVHU0XF", 250, 1, 122, "2025-08-25", "2025-09-06", 1, 1),
("沙灘狂歡折扣券", "B7K9P2ZD", 180, 1, 90, "2025-04-12", "2025-04-25", 1, 2),
("河內風情優惠碼", "Q8XN3GJT", 120, 2, 75, "2025-05-30", "2025-06-10", 2, 3),
("美食饗宴專屬券", "V6MFJYL4", 200, 1, 60, "2025-07-08", "2025-07-18", 1, 1),
("探險樂園特惠券", "D9ZHT1CX", 150, 1, 130, "2025-08-05", "2025-08-20", 1, 2);



SELECT * FROM coupons;
SHOW WARNINGS;

SHOW DATABASES;



SELECT * FROM coupons WHERE id = 2 AND is_valid = 1;
SELECT * FROM coupons WHERE is_valid = 0;

SELECT id, is_valid FROM coupons WHERE id = 2;

SELECT * FROM coupons ORDER BY id DESC LIMIT 10;