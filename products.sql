-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 09, 2025 lúc 07:04 PM
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
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` decimal(10,0) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `type` text NOT NULL,
  `ingredients` text NOT NULL,
  `usefor` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `quantity`, `description`, `image`, `type`, `ingredients`, `usefor`) VALUES
(1, 'Premium Green Tea', 32.00, 10, 'This high-quality green tea is carefully harvested from the finest leaves, offering a fresh, grassy flavor with a hint of sweetness. It is rich in antioxidants, which help support overall health and well-being. Ideal for those seeking a refreshing beverage that revitalizes the senses.', 'layout/images/premium-green-tea.jpg', 'Green Tea', 'Pure green tea leaves', 'Perfect for any time of day as a refreshing boost, it can be enjoyed hot or cold and is great for hydration.'),
(2, 'Chamomile Bliss', 22.00, 15, 'A soothing herbal tea made from dried chamomile flowers, Chamomile Bliss promotes relaxation and calmness. Its gentle floral aroma and mild, sweet taste make it an ideal companion for winding down after a hectic day.', 'layout/images/chamomile-bliss.jpg', 'Herbal Tea', 'Dried chamomile flowers', 'Best enjoyed before bedtime to aid sleep and reduce anxiety, making it a comforting ritual for relaxation.'),
(3, 'Herbal Serenity', 20.00, 8, 'This calming blend features a variety of herbs, including lemon balm and lavender, known for their soothing properties. Herbal Serenity offers a fragrant and tranquil experience, making it perfect for moments of peace in a busy life.', 'layout/images/herbal-serenity.jpg', 'Herbal Tea', 'Lemon balm, lavender, and other calming herbs', 'Ideal for unwinding after a long day, it helps ease stress and promote a sense of tranquility and well-being.'),
(4, 'Mystic Oolong', 27.00, 12, 'Mystic Oolong is a semi-oxidized tea that beautifully combines the rich flavors of black tea with the refreshing qualities of green tea. Its complex flavor profile features floral and fruity notes, making it a delightful choice for tea enthusiasts looking for something unique.', 'layout/images/mystic-oolong.jpg', 'Oolong Tea', 'Oolong tea leaves', 'Perfect for adventurous palates, this tea can be enjoyed throughout the day, offering a sophisticated and varied tasting experience.'),
(5, 'Royal Darjeeling', 45.00, 100, 'Often referred to as the \"Champagne of teas,\" Royal Darjeeling is an exquisite black tea from the Darjeeling region of India. It boasts a musky sweetness and delicate floral aroma, making it a luxurious choice for special occasions or quiet moments of indulgence.', 'layout/images/royal-darjeeling.jpg', 'Black Tea', 'Black tea leaves from the Darjeeling region', 'Best enjoyed plain or with a splash of milk, this tea is perfect for elevating afternoon tea or celebratory gatherings.'),
(6, 'Golden Chai Delight', 22.00, 100, 'This traditional spiced tea blend features robust black tea infused with aromatic spices like cinnamon, cardamom, and ginger. Golden Chai Delight is known for its warming qualities and rich flavors, making it a beloved choice during colder months.', 'layout/images/golden-chai-delight.jpg', 'Black Tea (Spiced)', 'Black tea, cinnamon, cardamom, ginger, and milk (optional)', 'Ideal for warming up on chilly days, it can be served with milk and sweetener for a comforting, flavorful experience.'),
(7, 'Elderflower Essence', 30.00, 100, 'Elderflower Essence is a light herbal tea that highlights the delicate flavor of elderflowers, known for their soothing properties. This tea has a subtly sweet and floral profile, making it refreshing and enjoyable at any time.', 'layout/images/elderflower-essence.jpg', 'Herbal Tea', 'Dried elderflowers', 'Great for sipping throughout the day, it provides a gentle lift and is perfect for those seeking a calming beverage.'),
(8, 'Traditional Masala Tea', 22.00, 100, 'A classic Indian brew, Traditional Masala Tea combines robust black tea with a blend of aromatic spices such as cloves, cinnamon, and ginger. This rich and warming drink offers a comforting experience, often enjoyed during family gatherings.', 'layout/images/traditional-masala-tea.jpg', 'Black Tea (Spiced)', 'Black tea, cloves, cinnamon, ginger, and black pepper', 'Enjoyed with milk or on its own, it’s the perfect beverage for cozy evenings or social occasions.'),
(9, 'Jasmine Pearl Elegance', 50.00, 100, 'Jasmine Pearl Elegance features hand-rolled green tea leaves infused with fragrant jasmine blossoms. This tea offers a captivating floral aroma and a smooth, delicate taste, making it a luxurious choice for tea aficionados.', 'layout/images/jasmine-pearl-elegance.jpg', 'Green Tea', 'Green tea leaves, jasmine blossoms', 'Great for special moments or afternoon tea, this elegant tea provides a delightful sensory experience.');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
