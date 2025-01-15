-- 用gitbash存檔時，出現warning: in the working copy of 'shopping_store.sql', 
-- lf will be replaced by crlf the next time git touches it
-- 所以用$ git config --global core.autocrlf true

-- drop database Shopping_store;
-- 待檢查，先不執行/建立
-- create database Shopping_store;
-- use shopping_store;



-- Users (我這邊先自己建的user，連user_id 1~10)
-- CREATE TABLE Users (
-- 	   user_id INT primary key,
--     user_name varchar(255) not null,
--     favoirites text
-- );
# 連向 user_id的表:
# orders
# Product_Reviews

-- 1. Categories (商品分類) **
CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    category_tag VARCHAR(20) NOT NULL,
    category_description TEXT,
    parent_id INT,
    created_at DATETIME DEFAULT NOW()  NOT NULL,
    updated_at DATETIME DEFAULT NOW() ON UPDATE CURRENT_TIMESTAMP  NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES Categories(category_id) 
);


-- 2. Products (商品) **
CREATE TABLE Products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    product_description TEXT,
    price INT NOT NULL,
    category_id INT NOT NULL,
    created_at DATETIME DEFAULT NOW() NOT NULL,
    updated_at DATETIME DEFAULT NOW() ON UPDATE CURRENT_TIMESTAMP  NOT NULL,
    product_status VARCHAR(20) DEFAULT '上架',
    image_url VARCHAR(255),
    stock_quantity INT DEFAULT 0  NOT NULL, #庫存
    is_deleted BOOLEAN DEFAULT FALSE NOT NULL, -- 後臺可刪除，但是保留數據(需被訂單商品指向)
    FOREIGN KEY (category_id) REFERENCES Categories(category_id)
);


-- 3. Product_Variants (商品變體) **
CREATE TABLE Product_Variants (
    variant_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variant_name VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    stock_quantity INT DEFAULT 0 NOT NULL,
    variant_status VARCHAR(20) DEFAULT '上架' ,
    image_url VARCHAR(255),
    is_deleted BOOLEAN DEFAULT FALSE, -- 後臺可刪除，但是保留數據(需被訂單商品指向)
    created_at DATETIME DEFAULT NOW() NOT NULL,
    updated_at DATETIME DEFAULT NOW() ON UPDATE CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE
);



-- 4. Shopping_Cart (購物車) **不開表，使用 localstorage 處理
-- CREATE TABLE Shopping_Cart (
--     cart_id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT,
--     product_id INT,
--     variant_id INT,
--     quantity INT DEFAULT 1,
--     added_at DATETIME DEFAULT NOW(),
--     foreign key (user_id) references Users(user_id),
--     FOREIGN KEY (product_id) REFERENCES Products(product_id),
--     FOREIGN KEY (variant_id) REFERENCES Product_Variants(variant_id)
-- );


-- 5. Orders (訂單) **
CREATE TABLE Orders (
    order_id VARCHAR(30) PRIMARY KEY,
    user_id INT NOT NULL,
    total_price INT NOT NULL,
    order_status VARCHAR(15) NOT NULL,
    -- 付款
    payment_method VARCHAR(20) NOT NULL,
    payment_status VARCHAR(20) NOT NULL,
    -- 發票 
    invoice_method VARCHAR(20) NOT NULL, 
    invoice VARCHAR(50) NOT NULL,
    mobile_barcode  VARCHAR(50) , -- 手機載具
	taxID_number  VARCHAR(50) , -- 公司統編
    -- 收件人資料
    recipient_name VARCHAR(50) NOT NULL, 
    recipient_phone INT NOT NULL,
    recipient_email VARCHAR(50) NOT NULL,
    remark VARCHAR(50),
    -- 運送 
    shipping_method VARCHAR(255) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    tracking_number VARCHAR(255),
    shipped_at DATETIME, -- 發貨時間
    -- 訂單時間
    created_at DATETIME DEFAULT NOW() NOT NULL,
    finish_at DATETIME,
	updated_at DATETIME DEFAULT NOW() ON UPDATE CURRENT_TIMESTAMP NOT NULL

    -- 取消訂單 (限未發貨前可取消) **有時間再用
--     cancelled_at DATETIME,
--     cancelled_factor VARCHAR(20),
--     cancelled_reason TEXT,
    
    -- foreign key (user_id) references Users(user_id)
);


-- 6. Order_Items (訂單商品) **
CREATE TABLE Order_Items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(30) NOT NULL,
    product_id INT,
    variant_id INT,
    quantity INT not null,
    price INT NOT NULL,
    return_status VARCHAR(20) , -- 退貨狀態
    returned_quantity INT DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ,
    FOREIGN KEY (variant_id) REFERENCES Product_Variants(variant_id) 
);

-- 7. Product_Reviews (商品評價) **
CREATE TABLE Product_Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    order_item_id INT NOT NULL,
    user_id INT,
	product_id INT,
	variant_id INT,
    rating INT  NOT NULL,
    review_text TEXT,
    created_at DATETIME DEFAULT NOW() NOT NULL,
    FOREIGN KEY (order_item_id) REFERENCES Order_Items(order_item_id)  ON DELETE CASCADE,
    -- foreign key (user_id) references Users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id),
    FOREIGN KEY (variant_id) REFERENCES Product_Variants(variant_id) 

);

-- 9. Return_Order (退貨單) **
CREATE TABLE Return_Order (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(30) NOT NULL,
    return_date DATETIME DEFAULT NOW() NOT NULL,
    return_status VARCHAR(25) NOT NULL,
	-- 部分退貨和全部退貨 (先設定全筆退)
    -- return_type VARCHAR(25) NOT NULL, 
    reason TEXT NOT NULL,
    -- return_processed_date DATETIME,
    -- 退款 --
    refund_amount INT ,
    refund_status VARCHAR(20) NOT NULL,
    refund_processed_at DATETIME ,
    -- 目前只有線上付款選項，故退款方式只有原路退回
    -- payment_method VARCHAR(255) NOT NULL,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE
);

-- 10. Return_Items (退貨商品) **先設定全筆退
-- CREATE TABLE Return_Items (
--     return_item_id INT AUTO_INCREMENT PRIMARY KEY,
--     return_id INT NOT NULL,
--     order_item_id INT NOT NULL,
--     quantity INT NOT NULL,
--     refund_price INT NOT NULL, -- 單件退款價格
--     FOREIGN KEY (return_id) REFERENCES Return_Order(return_id),
--     FOREIGN KEY (order_item_id) REFERENCES Order_Items(order_item_id)
-- );

-- 11. Refunds (退款) **
CREATE TABLE Refunds (
    refund_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(30) NOT NULL,
    -- return_id INT,
    refund_amount INT NOT NULL,
    refund_reason TEXT NOT NULL,
    refund_status VARCHAR(20) NOT NULL,
    processed_at DATETIME NOT NULL,
    payment_method VARCHAR(255) NOT NULL,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id)   ON DELETE CASCADE
    -- FOREIGN KEY (return_id) REFERENCES Return_Order(return_id) 
);

-- 12. Promotions (促銷活動) **
CREATE TABLE Promotions (
    promotion_id INT AUTO_INCREMENT PRIMARY KEY,
    promotion_name VARCHAR(255) NOT NULL,
    promotion_description TEXT,
    start_date DATE NOT NULL,
    end_date DATE,
    discount_percentage INT NOT NULL,
    updated_at  DATETIME DEFAULT NOW() ON UPDATE CURRENT_TIMESTAMP NOT NULL
);

-- 13. Promotion_Products（促銷商品表） **
CREATE TABLE Promotion_Products(
	promotion_product_id INT AUTO_INCREMENT PRIMARY KEY,
    promotion_id INT NOT NULL,
    product_id INT,
    variant_id INT,
    category_id INT,
    FOREIGN KEY (promotion_id) REFERENCES Promotions(promotion_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id),
	FOREIGN KEY (variant_id) REFERENCES Product_Variants(variant_id),
    FOREIGN KEY (category_id) REFERENCES Categories(category_id)
);

-- 14.Favorites（收藏商品）**直接在 user 表開一個欄位用字串紀錄
-- create table Favorites(
-- 	favorite_id int auto_increment primary key,
-- 	user_id int not null,
-- 	product_id int,
-- 	variant_id int,
--     create_at datetime not null,
--     FOREIGN KEY (user_id) REFERENCES Users(user_id),
--     FOREIGN KEY (product_id) REFERENCES Products(product_id),
-- 	FOREIGN KEY (variant_id) REFERENCES Product_Variants(variant_id)
-- );

show tables;
desc users;
desc Categories;
desc Products;
desc Product_Variants ;
-- desc Shopping_Cart;
desc Orders;
desc Order_Items ;
desc Product_Reviews;
desc Return_Order ;
desc Promotions;
desc Promotion_Products;
-- desc Favorites;
-- desc Shipping ;
-- desc Return_Items ;
-- desc Refunds ;

-- select * from Categories;
-- select * from Products;
-- select * from Product_Variants ;
-- select * from Shopping_Cart;
-- select * from Orders;
-- select * from Order_Items ;
-- select * from Product_Reviews;
-- select * from Shipping ;
-- select * from Return_Order ;
-- select * from Promotions;
-- select * from Promotion_Products;

-- ------------------資料變更-----------------------
# 有更新時上面table我也會一起改(drop後可以重新create上面table)

-- 0212先將變體狀態改成可為null
-- ALTER TABLE Product_Variants MODIFY COLUMN variant_status VARCHAR(20) NULL;

-- 2025/01/05--新增promotions欄位
ALTER TABLE promotions
ADD COLUMN updated_at  DATETIME DEFAULT NOW() ON UPDATE CURRENT_TIMESTAMP NOT NULL;



