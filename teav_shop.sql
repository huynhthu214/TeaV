-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 11, 2025 lúc 10:20 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `teav_shop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `CategorypId` varchar(10) NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `Description` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`CategorypId`, `Name`, `Description`) VALUES
('C001', 'Green Tea', 'Fresh and antioxidant-rich green teas'),
('C002', 'Herbal Tea', 'Soothing teas made from herbs'),
('C003', 'Oolong Tea', 'Semi-oxidized teas with complex flavors'),
('C004', 'Black Tea', 'Robust and bold black teas'),
('C005', 'Black Tea (Spiced)', 'Black teas with aromatic spices');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `CustomerId` varchar(10) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` text DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `RegistrationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`CustomerId`, `FirstName`, `LastName`, `Email`, `Password`, `Address`, `RegistrationDate`) VALUES
('CUS001', 'Anna', 'Nguyen', 'anna.nguyen@email.com', 'hashedpassword1', '123 Hanoi St, Hanoi, Vietnam', '2025-04-01'),
('CUS002', 'John', 'Smith', 'john.smith@email.com', 'hashedpassword2', '456 Main St, New York, USA', '2025-04-02'),
('CUS003', 'Linh', 'Tran', 'linh.tran@email.com', 'hashedpassword3', '789 Saigon St, HCMC, Vietnam', '2025-04-03'),
('CUS004', 'Emma', 'Brown', 'emma.brown@email.com', 'hashedpassword4', '321 Sydney Rd, Sydney, Australia', '2025-04-04'),
('CUS005', 'Hiroshi', 'Tanaka', 'hiroshi.tanaka@email.com', 'hashedpassword5', '654 Tokyo Ave, Tokyo, Japan', '2025-04-05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orderdetail`
--

CREATE TABLE `orderdetail` (
  `ProductId` varchar(10) NOT NULL,
  `OrderId` varchar(10) NOT NULL,
  `OrderItemId` varchar(10) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orderdetail`
--

INSERT INTO `orderdetail` (`ProductId`, `OrderId`, `OrderItemId`, `Quantity`) VALUES
('P001', 'ORD001', 'OI001', 2),
('P002', 'ORD001', 'OI002', 1),
('P004', 'ORD002', 'OI003', 1),
('P005', 'ORD002', 'OI004', 1),
('P010', 'ORD003', 'OI005', 1),
('P013', 'ORD003', 'OI006', 1),
('P017', 'ORD004', 'OI007', 1),
('P019', 'ORD004', 'OI008', 1),
('P022', 'ORD004', 'OI009', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `OrderId` varchar(10) NOT NULL,
  `CustomerId` varchar(10) DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `TotalAmount` float DEFAULT NULL,
  `Status` text DEFAULT NULL,
  `ShippingAddress` text DEFAULT NULL,
  `ShippingMethod` varchar(100) DEFAULT NULL,
  `PaymentMethod` varchar(100) DEFAULT NULL,
  `TrackingNumber` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`OrderId`, `CustomerId`, `OrderDate`, `TotalAmount`, `Status`, `ShippingAddress`, `ShippingMethod`, `PaymentMethod`, `TrackingNumber`) VALUES
('ORD001', 'CUS001', '2025-04-12', 86, 'Processing', '123 Hanoi St, Hanoi, Vietnam', 'Standard Shipping', 'Credit Card', 123456),
('ORD002', 'CUS002', '2025-04-13', 83, 'Shipped', '456 Main St, New York, USA', 'Express Shipping', 'PayPal', 654321),
('ORD003', 'CUS003', '2025-04-14', 70, 'Delivered', '789 Saigon St, HCMC, Vietnam', 'Standard Shipping', 'Cash on Delivery', 987654),
('ORD004', 'CUS004', '2025-04-15', 105, 'Processing', '321 Sydney Rd, Sydney, Australia', 'Express Shipping', 'Credit Card', 456789);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product`
--

CREATE TABLE `product` (
  `ProductId` varchar(10) NOT NULL,
  `SupplierId` varchar(10) NOT NULL,
  `CategorypId` varchar(10) DEFAULT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `Description` char(50) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `ImgUrl` text DEFAULT NULL,
  `CreatedAt` date DEFAULT NULL,
  `UpdatedAt` date DEFAULT NULL,
  `Status` text DEFAULT NULL,
  `Price` float DEFAULT NULL,
  `Type` text DEFAULT NULL,
  `Ingredients` text DEFAULT NULL,
  `Usefor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product`
--

INSERT INTO `product` (`ProductId`, `SupplierId`, `CategorypId`, `Name`, `Description`, `Quantity`, `ImgUrl`, `CreatedAt`, `UpdatedAt`, `Status`, `Price`, `Type`, `Ingredients`, `Usefor`) VALUES
('P001', 'S001', 'C001', 'Premium Green Tea', 'This high-quality green tea is carefully harvested', 100, 'layout/images/premium-green-tea.jpg', '2025-04-11', '2025-04-11', 'Available', 32, 'Green Tea', 'Pure green tea leaves', 'Perfect for any time of day as a refreshing boost'),
('P002', 'S001', 'C002', 'Chamomile Bliss', 'A soothing herbal tea made from dried chamomile', 100, 'layout/images/chamomile-bliss.jpg', '2025-04-11', '2025-04-11', 'Available', 22, 'Herbal Tea', 'Dried chamomile flowers', 'Best enjoyed before bedtime to aid sleep'),
('P003', 'S001', 'C002', 'Herbal Serenity', 'This calming blend features a variety of herbs', 100, 'layout/images/herbal-serenity.jpg', '2025-04-11', '2025-04-11', 'Available', 27, 'Herbal Tea', 'Lemon balm, lavender, and other calming herbs', 'Ideal for unwinding after a long day'),
('P004', 'S002', 'C003', 'Mystic Oolong', 'Mystic Oolong is a semi-oxidized tea that combines', 100, 'layout/images/mystic-oolong.jpg', '2025-04-11', '2025-04-11', 'Available', 38, 'Oolong Tea', 'Oolong tea leaves', 'Perfect for adventurous palates'),
('P005', 'S002', 'C004', 'Royal Darjeeling', 'Often referred to as the \"Champagne of teas\"', 100, 'layout/images/royal-darjeeling.jpg', '2025-04-11', '2025-04-11', 'Available', 45, 'Black Tea', 'Black tea leaves from the Darjeeling region', 'Best enjoyed plain or with a splash of milk'),
('P006', 'S001', 'C005', 'Golden Chai Delight', 'This traditional spiced tea blend features robust', 100, 'layout/images/golden-chai-delight.jpg', '2025-04-11', '2025-04-11', 'Available', 22, 'Black Tea (Spiced)', 'Black tea, cinnamon, cardamom, ginger', 'Ideal for warming up on chilly days'),
('P007', 'S002', 'C002', 'Elderflower Essence', 'Elderflower Essence is a light herbal tea', 100, 'layout/images/elderflower-essence.jpg', '2025-04-11', '2025-04-11', 'Available', 30, 'Herbal Tea', 'Dried elderflowers', 'Great for sipping throughout the day'),
('P008', 'S001', 'C005', 'Traditional Masala Tea', 'A classic Indian brew, Traditional Masala Tea', 100, 'layout/images/traditional-masala-tea.jpg', '2025-04-11', '2025-04-11', 'Available', 22, 'Black Tea (Spiced)', 'Black tea, cloves, cinnamon, ginger, black pepper', 'Enjoyed with milk or on its own'),
('P009', 'S002', 'C001', 'Jasmine Pearl Elegance', 'Jasmine Pearl Elegance features hand-rolled green', 100, 'layout/images/jasmine-pearl-elegance.jpg', '2025-04-11', '2025-04-11', 'Available', 55, 'Green Tea', 'Green tea leaves, jasmine blossoms', 'Great for special moments or afternoon tea'),
('P010', 'S003', 'C001', 'Matcha Supreme', 'A vibrant green tea powder with a rich umami taste', 80, 'layout/images/matcha-supreme.jpg', '2025-04-11', '2025-04-11', 'Available', 48, 'Green Tea', 'Matcha green tea powder', 'Perfect for morning energy or ceremonial use'),
('P011', 'S003', 'C001', 'Sencha Delight', 'A classic Japanese green tea with a grassy flavor', 90, 'layout/images/sencha-delight.jpg', '2025-04-11', '2025-04-11', 'Available', 35, 'Green Tea', 'Sencha green tea leaves', 'Great for daily sipping and detox'),
('P012', 'S001', 'C001', 'Dragonwell Bliss', 'A Chinese green tea with a nutty and sweet taste', 85, 'layout/images/dragonwell-bliss.jpg', '2025-04-11', '2025-04-11', 'Available', 40, 'Green Tea', 'Dragonwell green tea leaves', 'Ideal for a refreshing afternoon break'),
('P013', 'S002', 'C002', 'Peppermint Glow', 'A refreshing herbal tea with a cool minty taste', 100, 'layout/images/peppermint-glow.jpg', '2025-04-11', '2025-04-11', 'Available', 20, 'Herbal Tea', 'Dried peppermint leaves', 'Great for digestion and relaxation'),
('P014', 'S003', 'C002', 'Rooibos Harmony', 'A caffeine-free herbal tea with a sweet earthy fla', 95, 'layout/images/rooibos-harmony.jpg', '2025-04-11', '2025-04-11', 'Available', 25, 'Herbal Tea', 'Rooibos leaves', 'Perfect for a caffeine-free evening drink'),
('P015', 'S001', 'C002', 'Lemon Verbena', 'A citrusy herbal tea with a bright and zesty flavo', 90, 'layout/images/lemon-verbena.jpg', '2025-04-11', '2025-04-11', 'Available', 23, 'Herbal Tea', 'Dried lemon verbena leaves', 'Ideal for a refreshing pick-me-up'),
('P016', 'S002', 'C002', 'Hibiscus Dream', 'A tart and floral herbal tea with a vibrant red co', 85, 'layout/images/hibiscus-dream.jpg', '2025-04-11', '2025-04-11', 'Available', 28, 'Herbal Tea', 'Dried hibiscus flowers', 'Great for a cooling summer drink'),
('P017', 'S003', 'C003', 'Tie Guan Yin', 'A floral oolong tea with a smooth and creamy finis', 75, 'layout/images/tie-guan-yin.jpg', '2025-04-11', '2025-04-11', 'Available', 42, 'Oolong Tea', 'Tie Guan Yin oolong leaves', 'Perfect for a sophisticated tea experience'),
('P018', 'S001', 'C003', 'Phoenix Dan Cong', 'A fruity oolong tea with a honey-like sweetness', 70, 'layout/images/phoenix-dan-cong.jpg', '2025-04-11', '2025-04-11', 'Available', 50, 'Oolong Tea', 'Phoenix Dan Cong oolong leaves', 'Ideal for tea connoisseurs seeking depth'),
('P019', 'S002', 'C004', 'Assam Gold', 'A bold black tea with a malty and robust flavor', 90, 'layout/images/assam-gold.jpg', '2025-04-11', '2025-04-11', 'Available', 38, 'Black Tea', 'Assam black tea leaves', 'Great for a strong morning brew'),
('P020', 'S003', 'C004', 'Ceylon Star', 'A bright and brisk black tea from Sri Lanka', 85, 'layout/images/ceylon-star.jpg', '2025-04-11', '2025-04-11', 'Available', 35, 'Black Tea', 'Ceylon black tea leaves', 'Perfect with a slice of lemon'),
('P021', 'S001', 'C004', 'Earl Grey Classic', 'A black tea infused with bergamot for a citrusy no', 80, 'layout/images/earl-grey-classic.jpg', '2025-04-11', '2025-04-11', 'Available', 30, 'Black Tea', 'Black tea, bergamot oil', 'Ideal for a classic afternoon tea'),
('P022', 'S002', 'C005', 'Spiced Apple Chai', 'A cozy black tea with apple and warm spices', 90, 'layout/images/spiced-apple-chai.jpg', '2025-04-11', '2025-04-11', 'Available', 24, 'Black Tea (Spiced)', 'Black tea, apple, cinnamon, cloves', 'Perfect for a comforting autumn drink'),
('P023', 'S003', 'C005', 'Vanilla Spice Blend', 'A black tea with vanilla and spicy notes', 85, 'layout/images/vanilla-spice-blend.jpg', '2025-04-11', '2025-04-11', 'Available', 26, 'Black Tea (Spiced)', 'Black tea, vanilla, cardamom, nutmeg', 'Great for a sweet and spicy treat'),
('P024', 'S001', 'C005', 'Ginger Turmeric Chai', 'A warming black tea with ginger and turmeric', 80, 'layout/images/ginger-turmeric-chai.jpg', '2025-04-11', '2025-04-11', 'Available', 28, 'Black Tea (Spiced)', 'Black tea, ginger, turmeric, black pepper', 'Ideal for a healthy and warming drink');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `ProId` varchar(10) NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `Description` char(50) DEFAULT NULL,
  `DiscountType` varchar(50) DEFAULT NULL,
  `DiscountValue` varchar(50) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Active` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`ProId`, `Name`, `Description`, `DiscountType`, `DiscountValue`, `StartDate`, `EndDate`, `Active`) VALUES
('PRO001', 'Spring Sale', '10% off on all teas', 'Percentage', '10', '2025-04-01', '2025-04-30', 'Yes'),
('PRO002', 'Green Tea Discount', '5% off on green teas', 'Percentage', '5', '2025-04-10', '2025-04-20', 'No'),
('PRO003', 'Herbal Tea Promo', '15% off on herbal teas', 'Percentage', '15', '2025-04-15', '2025-04-25', 'Yes');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `ReviewId` varchar(10) NOT NULL,
  `CustomerId` varchar(10) DEFAULT NULL,
  `ProductId` varchar(10) NOT NULL,
  `Rating` float DEFAULT NULL,
  `Comment` text DEFAULT NULL,
  `ReviewDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`ReviewId`, `CustomerId`, `ProductId`, `Rating`, `Comment`, `ReviewDate`) VALUES
('REV001', 'CUS001', 'P001', 4.5, 'Great taste, very refreshing!', '2025-04-13'),
('REV002', 'CUS002', 'P004', 5, 'Amazing oolong tea, love the flavor.', '2025-04-14'),
('REV003', 'CUS003', 'P002', 4, 'Helps me relax, but a bit pricey.', '2025-04-15'),
('REV004', 'CUS004', 'P010', 4.8, 'Perfect for my morning matcha latte!', '2025-04-16'),
('REV005', 'CUS005', 'P013', 4.2, 'Very soothing, great for digestion.', '2025-04-17'),
('REV006', 'CUS001', 'P019', 4.5, 'Strong and flavorful, my new favorite!', '2025-04-18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sale`
--

CREATE TABLE `sale` (
  `ProId` varchar(10) NOT NULL,
  `ProductId` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sale`
--

INSERT INTO `sale` (`ProId`, `ProductId`) VALUES
('PRO001', 'P001'),
('PRO001', 'P002'),
('PRO001', 'P003'),
('PRO001', 'P004'),
('PRO001', 'P005'),
('PRO001', 'P006'),
('PRO001', 'P007'),
('PRO001', 'P008'),
('PRO001', 'P009'),
('PRO001', 'P010'),
('PRO001', 'P011'),
('PRO001', 'P012'),
('PRO001', 'P013'),
('PRO001', 'P014'),
('PRO001', 'P015'),
('PRO001', 'P016'),
('PRO001', 'P017'),
('PRO001', 'P018'),
('PRO001', 'P019'),
('PRO001', 'P020'),
('PRO001', 'P021'),
('PRO001', 'P022'),
('PRO001', 'P023'),
('PRO001', 'P024'),
('PRO002', 'P001'),
('PRO002', 'P009'),
('PRO002', 'P010'),
('PRO002', 'P011'),
('PRO002', 'P012'),
('PRO003', 'P002'),
('PRO003', 'P003'),
('PRO003', 'P007'),
('PRO003', 'P013'),
('PRO003', 'P014'),
('PRO003', 'P015'),
('PRO003', 'P016');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suppliers`
--

CREATE TABLE `suppliers` (
  `SupplierId` varchar(10) NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `ContactPp` text DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `suppliers`
--

INSERT INTO `suppliers` (`SupplierId`, `Name`, `ContactPp`, `Email`, `Phone`) VALUES
('S001', 'Tea Harmony Co.', 'John Doe', 'contact@teaharmony.com', 1234567890),
('S002', 'Pure Leaf Suppliers', 'Jane Smith', 'info@pureleaf.com', 2147483647),
('S003', 'Global Tea Traders', 'Michael Brown', 'sales@globaltea.com', 2147483647);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategorypId`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`CustomerId`);

--
-- Chỉ mục cho bảng `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD PRIMARY KEY (`ProductId`,`OrderId`),
  ADD KEY `FK_ORDERDET_ORDERDETA_ORDERS` (`OrderId`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderId`),
  ADD KEY `FK_ORDERS_ORDER_CUSTOMER` (`CustomerId`);

--
-- Chỉ mục cho bảng `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductId`),
  ADD KEY `FK_PRODUCT_CATEGORY_CATEGORI` (`CategorypId`),
  ADD KEY `FK_PRODUCT_SUPPLY_SUPPLIER` (`SupplierId`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`ProId`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewId`),
  ADD KEY `FK_REVIEWS_REVIEW_CUSTOMER` (`CustomerId`),
  ADD KEY `FK_REVIEWS_REVIEWED_PRODUCT` (`ProductId`);

--
-- Chỉ mục cho bảng `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`ProId`,`ProductId`),
  ADD KEY `FK_SALE_SALE2_PRODUCT` (`ProductId`);

--
-- Chỉ mục cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`SupplierId`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD CONSTRAINT `FK_ORDERDET_ORDERDETA_ORDERS` FOREIGN KEY (`OrderId`) REFERENCES `orders` (`OrderId`),
  ADD CONSTRAINT `FK_ORDERDET_ORDERDETA_PRODUCT` FOREIGN KEY (`ProductId`) REFERENCES `product` (`ProductId`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_ORDERS_ORDER_CUSTOMER` FOREIGN KEY (`CustomerId`) REFERENCES `customers` (`CustomerId`);

--
-- Các ràng buộc cho bảng `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_PRODUCT_CATEGORY_CATEGORI` FOREIGN KEY (`CategorypId`) REFERENCES `categories` (`CategorypId`),
  ADD CONSTRAINT `FK_PRODUCT_SUPPLY_SUPPLIER` FOREIGN KEY (`SupplierId`) REFERENCES `suppliers` (`SupplierId`);

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `FK_REVIEWS_REVIEWED_PRODUCT` FOREIGN KEY (`ProductId`) REFERENCES `product` (`ProductId`),
  ADD CONSTRAINT `FK_REVIEWS_REVIEW_CUSTOMER` FOREIGN KEY (`CustomerId`) REFERENCES `customers` (`CustomerId`);

--
-- Các ràng buộc cho bảng `sale`
--
ALTER TABLE `sale`
  ADD CONSTRAINT `FK_SALE_SALE2_PRODUCT` FOREIGN KEY (`ProductId`) REFERENCES `product` (`ProductId`),
  ADD CONSTRAINT `FK_SALE_SALE_PROMOTIO` FOREIGN KEY (`ProId`) REFERENCES `promotions` (`ProId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
