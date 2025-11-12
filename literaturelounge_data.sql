-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2023 at 08:40 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `literaturelounge_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `admin_id` int(11) NOT NULL,
  `admin_loginname` varchar(50) NOT NULL,
  `admin_password` varchar(50) NOT NULL,
  `admin_email` varchar(30) NOT NULL,
  `admin_fullname` varchar(30) NOT NULL,
  `admin_address` varchar(255) NOT NULL,
  `admin_phone` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`admin_id`, `admin_loginname`, `admin_password`, `admin_email`, `admin_fullname`, `admin_address`, `admin_phone`) VALUES
(1, 'admin', '202cb962ac59075b964b07152d234b70', 'nguyenvana@gmail.com', 'Nguyễn Văn A', 'Hà Nội', '0987654321');

-- --------------------------------------------------------

--
-- Table structure for table `tblcart`
--

CREATE TABLE `tblcart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcart`
--

INSERT INTO `tblcart` (`cart_id`, `user_id`) VALUES
(1, 1),
(10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `tblcart_details`
--

CREATE TABLE `tblcart_details` (
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`category_id`, `category_name`) VALUES
(1, 'Novels'),
(2, 'History Books'),
(3, 'Science Books');

-- --------------------------------------------------------

--
-- Table structure for table `tblcomment`
--

CREATE TABLE `tblcomment` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `comment_content` varchar(255) NOT NULL,
  `comment_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcomment`
--

INSERT INTO `tblcomment` (`comment_id`, `user_id`, `product_id`, `comment_content`, `comment_time`) VALUES
(2, 1, 2, 'Hay', '2023-08-08 08:38:42'),
(3, 1, 3, 'Hay', '2023-08-20 21:15:49'),
(14, 1, 9, 'Hay', '2023-10-15 16:43:07');

-- --------------------------------------------------------

--
-- Table structure for table `tblmomo`
--

CREATE TABLE `tblmomo` (
  `momo_id` int(11) NOT NULL,
  `PartnerCode` varchar(50) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `Amount` varchar(50) NOT NULL,
  `OrderInfo` varchar(100) NOT NULL,
  `OrderType` varchar(50) NOT NULL,
  `TransId` int(11) NOT NULL,
  `payType` varchar(50) NOT NULL,
  `order_code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblmomo`
--

INSERT INTO `tblmomo` (`momo_id`, `PartnerCode`, `OrderId`, `Amount`, `OrderInfo`, `OrderType`, `TransId`, `payType`, `order_code`) VALUES
(10, 'MOMOBKUN20180529', 1696686623, '35600', 'Thanh toán qua MoMo ATM', 'momo_wallet', 2147483647, 'napas', 5238);

-- --------------------------------------------------------

--
-- Table structure for table `tblorder`
--

CREATE TABLE `tblorder` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_created_time` datetime NOT NULL,
  `order_address` varchar(30) NOT NULL,
  `order_notes` varchar(255) NOT NULL,
  `order_value` float NOT NULL,
  `order_phone` varchar(12) NOT NULL,
  `order_status` tinyint(1) NOT NULL,
  `order_receiver` varchar(50) DEFAULT NULL,
  `order_payment` varchar(20) DEFAULT NULL,
  `order_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorder`
--

INSERT INTO `tblorder` (`order_id`, `user_id`, `order_created_time`, `order_address`, `order_notes`, `order_value`, `order_phone`, `order_status`, `order_receiver`, `order_payment`, `order_code`) VALUES
(16, 1, '2023-10-07 20:28:26', 'Hà Nội', '', 18000, '0362046866', 1, 'Nguyễn Văn A', 'vnpay', 6956),
(17, 1, '2023-10-07 20:51:36', 'Hà Nội', '', 35600, '0987654321', 1, 'Nguyễn Văn A', 'momo', 5238),
(18, 1, '2023-10-13 21:43:31', 'Hà Nội', '', 18000, '0123456789', 1, 'Nguyễn Văn A', 'cod', 9802),
(19, 1, '2023-10-15 16:06:28', 'Hà Nội', '', 18000, '0123456789', 1, 'Nguyễn Văn A', 'vnpay', 1591),
(21, 10, '2023-10-15 20:18:09', 'Hà Nội', '', 351000, '0123456789', 1, 'Nguyễn Văn B', 'cod', 1699),
(22, 10, '2023-10-15 20:18:49', 'Hà Nội', '', 171000, '0123456789', 1, 'Nguyễn Văn B', 'vnpay', 5369),
(23, 10, '2023-10-15 20:40:56', 'Hà Nội', '', 741000, '0123456789', 1, 'Nguyễn Văn B', 'cod', 5001);

-- --------------------------------------------------------

--
-- Table structure for table `tblorder_details`
--

CREATE TABLE `tblorder_details` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_code` int(11) DEFAULT NULL,
  `purchase_price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorder_details`
--

INSERT INTO `tblorder_details` (`order_id`, `product_id`, `quantity`, `order_code`, `purchase_price`) VALUES
(16, 8, 1, 6956, 18000),
(17, 7, 1, 5238, 17600),
(17, 8, 1, 5238, 18000),
(18, 8, 1, 9802, 18000),
(19, 8, 1, 1591, 18000),
(21, 8, 1, 1699, 180000),
(21, 9, 1, 1699, 171000),
(22, 13, 1, 5369, 171000),
(23, 12, 2, 5001, 285000),
(23, 13, 1, 5001, 171000);

-- --------------------------------------------------------

--
-- Table structure for table `tblproduct`
--

CREATE TABLE `tblproduct` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_title` varchar(100) NOT NULL,
  `product_description` text NOT NULL,
  `product_price` float NOT NULL,
  `product_quantity` float NOT NULL,
  `product_image` varchar(50) NOT NULL,
  `product_discount` float NOT NULL,
  `product_author` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblproduct`
--

INSERT INTO `tblproduct` (`product_id`, `category_id`, `product_title`, `product_description`, `product_price`, `product_quantity`, `product_image`, `product_discount`, `product_author`) VALUES
(1, 1, 'Brave New World', 'One of the giants of the dystopian genre. Having already shaken up the literary world when it was first published, Brave New World is relevant even today as it urges readers to ask questions about autonomy, hedonism, and our definition of “utopia.”', 50000, 99, 'bravenewworld.jpg', 5, 'Aldous Huxley'),
(2, 1, 'The Book Thief', 'This book has an unmistakably distinct narrator — Death. Set in Nazi Germany, it follows Liesel, a young girl in her new foster care home. As the world around her begins to crumble, Liesel must find solace in books and the power of words.', 35000, 100, 'thebookthief.jpg', 5, 'Markus Zusak'),
(3, 1, 'As I Lay Dying', 'As William Faulkner attested: “I set out deliberately to write a tour-de-force. Before I ever put pen to paper and set down the first word I knew what the last word would be and almost where the last period would fall.” This is the grueling story of the Bundren family’s slow, tortuous journey to bury Addie, their wife and mother, in her hometown of Mississippi...', 36000, 100, 'asilaydying.jpg', 10, 'William Faulkner'),
(4, 1, 'Anna Karenina', 'If you like lengthy books in which to immerse yourself, then this is a real treat. This epic novel tells the parallel stories of Anna Karenina and Konstantin Levin over a span of 800+ pages — dealing with social change, politics, theology, and philosophy in nineteenth-century Russia all the while.', 180000, 99, 'annakarenina.jpg', 10, 'Leo Tolstoy'),
(5, 1, 'Animal Farm', 'When Old Major the boar dies on Manor Farm, two young pigs named Snowball and Napoleon rise to create new leadership in this allegorical book that is supposed to mirror the Russian Revolution of 1917 — and the ensuing Stalinist Soviet Union. Animal Farm is a stunning achievement, and not just because Orwell proved that a story about pigs can be terrifying.', 180000, 100, 'animalfarm.jpg', 10, 'George Orwell'),
(6, 1, 'The Aleph and Other Stories', 'Jorge Luis Borges’ keen insight and philosophical wisdom is on full display in this acclaimed short story collection. From “The Immortal” to “The House of Asterion,” the stories within are glittering, haunting examples of worlds created by a master of magic realism.', 20000, 100, 'thealephandotherstories.jpg', 10, 'Jorge Luis Borges'),
(7, 1, 'The Alchemist', 'Written in only two weeks, The Alchemist has sold more than two million copies worldwide — and the magical story of Santiago’s journey to the pyramids of Egypt continues to enchant readers worldwide. A dreamy triumph.', 200000, 99, 'thealchemist.jpg', 12, 'Paulo Coelho'),
(8, 1, 'The Adventures of Sherlock Holmes', 'In 1891, Sir Arthur Conan Doyle published “A Scandal in Bohemia,” the first short story to feature Sherlock Holmes. Sharp and engrossing, this collection shows how exactly Sherlock Holmes became a cultural phenomenon and the most recognizable detective of all time.', 200000, 96, 'theadvanturesofsherlockholmes.jpg', 10, 'Arthur Conan Doyle'),
(9, 1, 'Adventures of Huckleberry Finn', 'A young boy and a slave in 19th-century Louisiana must find their way home — with only the Mississippi River for a guide. This slender book by Mark Twain’s is so well-regarded that it’s said by many to be The Great American Novel.', 180000, 94, 'adventuresofHuckleberryFinn.jpg', 5, 'Mark Twain'),
(12, 1, 'The Lord of the Rings', 'No author casts a greater shadow over one genre quite like J.R.R. Tolkien and epic fantasy. Start here with the trilogy that launched it all: The Lord of the Rings and Frodo’s quest to rid Middle-Earth of Sauron once and for all', 300000, 98, 'thelordoftherings.jpg', 5, 'Rings by J.R.R. Tolkien'),
(13, 3, 'The Origin of Species', 'In The Origin of Species (1859) Darwin challenged many of the most deeply-held beliefs of the Western world. Arguing for a material, not divine, origin of species, he showed that new species are achieved by \"natural selection.\"', 180000, 98, 'theoriginofspecies.jpg', 5, ' Charles Darwin');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `user_id` int(11) NOT NULL,
  `user_loginname` varchar(50) NOT NULL,
  `user_password` varchar(50) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_fullname` varchar(50) NOT NULL,
  `user_address` varchar(50) NOT NULL,
  `user_phone` varchar(12) NOT NULL,
  `user_created_date` date NOT NULL,
  `user_enabled` tinyint(1) DEFAULT 1,
  `user_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`user_id`, `user_loginname`, `user_password`, `user_email`, `user_fullname`, `user_address`, `user_phone`, `user_created_date`, `user_enabled`, `user_deleted`) VALUES
(1, 'a', '202cb962ac59075b964b07152d234b70', 'nguyenvana@gmail.com', 'Nguyễn Văn A', 'Hà Nội', '0123456789', '2023-07-18', 1, 0),
(10, 'b', '202cb962ac59075b964b07152d234b70', 'nguyenvanb@gmail.com', 'Nguyễn Văn B', 'Hà Nội', '0123456789', '2023-10-15', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblvnpay`
--

CREATE TABLE `tblvnpay` (
  `vnpay_id` int(11) NOT NULL,
  `Amount` varchar(50) NOT NULL,
  `BankCode` varchar(50) NOT NULL,
  `BankTranNo` varchar(50) NOT NULL,
  `CardType` varchar(50) NOT NULL,
  `OrderInfo` varchar(100) NOT NULL,
  `PayDate` varchar(50) NOT NULL,
  `TmnCode` varchar(50) NOT NULL,
  `TransactionNo` varchar(50) NOT NULL,
  `order_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblvnpay`
--

INSERT INTO `tblvnpay` (`vnpay_id`, `Amount`, `BankCode`, `BankTranNo`, `CardType`, `OrderInfo`, `PayDate`, `TmnCode`, `TransactionNo`, `order_code`) VALUES
(13, '1800000', 'NCB', 'VNP14135450', 'ATM', 'Thanh toan GD:6956', '20231007203035', '6448J9KM', '14135450', 6956);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `tblcart`
--
ALTER TABLE `tblcart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tblcart_details`
--
ALTER TABLE `tblcart_details`
  ADD PRIMARY KEY (`cart_id`,`product_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `tblcomment`
--
ALTER TABLE `tblcomment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tblmomo`
--
ALTER TABLE `tblmomo`
  ADD PRIMARY KEY (`momo_id`);

--
-- Indexes for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tblorder_details`
--
ALTER TABLE `tblorder_details`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tblproduct`
--
ALTER TABLE `tblproduct`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tblvnpay`
--
ALTER TABLE `tblvnpay`
  ADD PRIMARY KEY (`vnpay_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcart`
--
ALTER TABLE `tblcart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tblcomment`
--
ALTER TABLE `tblcomment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tblmomo`
--
ALTER TABLE `tblmomo`
  MODIFY `momo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblorder`
--
ALTER TABLE `tblorder`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tblproduct`
--
ALTER TABLE `tblproduct`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblvnpay`
--
ALTER TABLE `tblvnpay`
  MODIFY `vnpay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblcart`
--
ALTER TABLE `tblcart`
  ADD CONSTRAINT `tblcart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`user_id`);

--
-- Constraints for table `tblcart_details`
--
ALTER TABLE `tblcart_details`
  ADD CONSTRAINT `tblcart_details_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `tblcart` (`cart_id`),
  ADD CONSTRAINT `tblcart_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblproduct` (`product_id`);

--
-- Constraints for table `tblcomment`
--
ALTER TABLE `tblcomment`
  ADD CONSTRAINT `tblcomment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`user_id`),
  ADD CONSTRAINT `tblcomment_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblproduct` (`product_id`);

--
-- Constraints for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD CONSTRAINT `tblorder_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`user_id`);

--
-- Constraints for table `tblorder_details`
--
ALTER TABLE `tblorder_details`
  ADD CONSTRAINT `tblorder_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tblorder` (`order_id`),
  ADD CONSTRAINT `tblorder_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblproduct` (`product_id`);

--
-- Constraints for table `tblproduct`
--
ALTER TABLE `tblproduct`
  ADD CONSTRAINT `tblproduct_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `tblcategory` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
