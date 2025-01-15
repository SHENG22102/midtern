use shopping_store;
-- 插入假使用者資訊
INSERT INTO Users (user_id, user_name)
VALUES 
    (1, 'User1'),
    (2, 'User2'),
    (3, 'User3'),
    (4, 'User4'),
    (5, 'User5'),
    (6, 'User6'),
    (7, 'User7'),
	(8, 'User8'),
    (9, 'User9'),
    (10, 'User10');

--  插入商品分類 (Categories)
INSERT INTO Categories (
category_name, 
category_tag, 
category_description, 
parent_id) 
VALUES
('電子產品', 'ELEC', '電子產品及設備', NULL),
('時尚', 'FASH', '服裝及配件', NULL),
('智能手機', 'PHON', '最新智能手機', 1),
('筆記型電腦', 'LAP', '便攜式電腦', 1),
('鞋類', 'SHOE', '各種鞋類', 2);

-- 插入商品 (Products)
INSERT INTO Products (
product_name, 
product_description, 
price, 
category_id, 
product_status, 
image_url, 
stock_quantity)
VALUES
-- 電子產品 (category_id = 1)
('電子產品 1', '電子產品 1 的描述', 100, 1, '上架', 'https://placehold.co/100x100', 50),
('電子產品 2', '電子產品 2 的描述', 150, 1, '上架', 'https://placehold.co/100x100', 30),

-- 時尚 (category_id = 2)
('時尚商品 1', '時尚商品 1 的描述', 80, 2, '上架', 'https://placehold.co/100x100', 70),
('時尚商品 2', '時尚商品 2 的描述', 120, 2, '上架', 'https://placehold.co/100x100', 40),

-- 智能手機 (category_id = 3)
('智能手機 1', '智能手機 1 的描述', 200, 3, '上架', 'https://placehold.co/100x100', 20),
('智能手機 2', '智能手機 2 的描述', 250, 3, '上架', 'https://placehold.co/100x100', 25),

-- 筆記型電腦 (category_id = 4)
('筆記型電腦 1', '筆記型電腦 1 的描述', 500, 4, '上架', 'https://placehold.co/100x100', 10),
('筆記型電腦 2', '筆記型電腦 2 的描述', 550, 4, '上架', 'https://placehold.co/100x100', 15),

-- 鞋類 (category_id = 5)
('鞋子 1', '鞋子 1 的描述', 60, 5, '上架', 'https://placehold.co/100x100', 80),
('鞋子 2', '鞋子 2 的描述', 70, 5, '上架', 'https://placehold.co/100x100', 90);



INSERT INTO Product_Variants (
    product_id, 
    variant_name, 
    price, 
    stock_quantity, 
    image_url
) 
VALUES
-- 電子產品 1 的變體 (product_id = 1)
(1, '電子產品 1 - 變體 A', 110, 20, 'https://placehold.co/100x100'),

-- 電子產品 2 的變體 (product_id = 2)
(2, '電子產品 2 - 變體 A', 130, 25, 'https://placehold.co/100x100'),

-- 時尚商品 1 的變體 (product_id = 3)
(3, '時尚商品 1 - 變體 A', 90, 50, 'https://placehold.co/100x100'),

-- 時尚商品 2 的變體 (product_id = 4)
(4, '時尚商品 2 - 變體 A', 110, 30, 'https://placehold.co/100x100'),

-- 時尚商品 3 的變體 (product_id = 5)
(5, '時尚商品 3 - 變體 A', 130, 20, 'https://placehold.co/100x100'),

-- 時尚商品 4 的變體 (product_id = 6)
(6, '時尚商品 4 - 變體 A', 150, 20, 'https://placehold.co/100x100'),

-- 時尚商品 5 的變體 (product_id = 7)
(7, '時尚商品 5 - 變體 A', 170, 15, 'https://placehold.co/100x100'),

-- 時尚商品 6 的變體 (product_id = 8)
(8, '時尚商品 6 - 變體 A', 190, 30, 'https://placehold.co/100x100'),

-- 時尚商品 7 的變體 (product_id = 9)
(9, '時尚商品 7 - 變體 A', 210, 30, 'https://placehold.co/100x100'),

-- 時尚商品 8 的變體 (product_id = 10)
(10, '時尚商品 8 - 變體 A', 230, 25, 'https://placehold.co/100x100');


INSERT INTO Orders (
    order_id, 
    user_id, 
    total_price, 
    order_status, 
    payment_method, 
    payment_status, 
    invoice_method, 
    invoice, 
    recipient_name, 
    recipient_phone, 
    recipient_email, 
    remark, 
    shipping_method, 
    shipping_address, 
    tracking_number, 
    shipped_at, 
    created_at, 
    finish_at, 
    mobile_barcode, -- 手機載具
    taxID_number -- 統編
) 
VALUES
-- 訂單 1
('ORD00001', 1, 500, '待出貨', '紙本', '已付款', '紙本', 'INV12345', 
 '王小明', '0912345678', 'li.dahua@example.com', '請於上午配送', '宅配', '台北市信義區XX路XX號', 
 'TRACK001', NULL, NOW(), NULL, NULL, '12345678'),

-- 訂單 2
('ORD00002', 2, 300, '已出貨', '載具', '已付款', '載具', 'INV67890', 
 '李大華', '0911223344', 'li.dahua@example.com', '門市取貨後聯繫', '7-11', '7-11門市', 
 'TRACK002', NOW(), NOW(), NULL, '/ABCD123', NULL),

-- 訂單 3
('ORD00003', 3, 1200, '已完成', '統編', '已付款', '統編', 'INV11223', 
 '陳美麗', '0987654321', 'li.dahua@example.com', '感謝服務，請保持聯繫', '全家', '全家門市', 
 'TRACK003', NOW(), NOW(), NOW(), NULL, '87654321'),

-- 訂單 4
('ORD00004', 4, 800, '待出貨', '載具', '已付款', '紙本', 'INV33445', 
 '黃俊傑', '0911223344', 'li.dahua@example.com', '請提前通知配送時間', '宅配', '新竹市東區XX路XX號', 
 'TRACK004', NULL, NOW(), NULL, '/XYZ9876', NULL),

-- 訂單 5
('ORD00005', 5, 450, '已出貨', '紙本', '已付款', '統編', 'INV55667', 
 '林佩真', '0911223344', 'pei.zhen@example.com', '門市領取後無需聯繫', '7-11', '7-11門市', 
 'TRACK005', NOW(), NOW(), NULL, NULL, '56781234');



# 插入假訂單商品
INSERT INTO Order_Items (
    order_id, product_id, variant_id, quantity, price, return_status, returned_quantity
)
VALUES
-- 訂單 1
('ORD00001', 1, 1, 2, 1000, NULL, 0),
('ORD00001', 2, 2, 2, 1500, NULL, 0),
('ORD00001', 3, 3, 1, 500, NULL, 0),
('ORD00001', 4, 4, 1, 1000, NULL, 0),
('ORD00001', 5, 5, 1, 1000, NULL, 0),
-- 訂單 2
('ORD00002', 6, 6, 2, 1200, NULL, 0),
('ORD00002', 7, 7, 1, 800, NULL, 0),
('ORD00002', 8, 8, 1, 1000, NULL, 0),
('ORD00002', 9, 9, 1, 800, NULL, 0),
('ORD00002', 10, 10, 1, 700, NULL, 0),
-- 訂單 3
('ORD00003', 1, 1, 3, 900, NULL, 0),
('ORD00003', 2, 2, 1, 700, NULL, 0),
('ORD00003', 3, 3, 2, 800, NULL, 0),
('ORD00003', 4, 4, 1, 400, NULL, 0),
('ORD00003', 5, 5, 1, 400, NULL, 0),
-- 訂單 4
('ORD00004', 6, 6, 1, 2000, NULL, 0),
('ORD00004', 7, 7, 1, 1500, NULL, 0),
('ORD00004', 8, 8, 2, 1200, NULL, 0),
('ORD00004', 9, 9, 1, 800, NULL, 0),
('ORD00004', 1, 10, 1, 700, NULL, 0),
-- 訂單 5
('ORD00005', 1, 1, 1, 500, NULL, 0),
('ORD00005', 2, 2, 1, 400, NULL, 0),
('ORD00005', 3, 3, 2, 600, NULL, 0),
('ORD00005', 4, 4, 1, 300, NULL, 0),
('ORD00005', 5, 5, 1, 300, NULL, 0);




select * from Categories;
select * from Products;
select * from Product_Variants ;
select * from Orders ;
select * from Order_Items ;

