-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 06:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecostore`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_users`
--

CREATE TABLE `active_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `active_users`
--

INSERT INTO `active_users` (`id`, `user_id`, `email`, `role`, `login_time`, `is_active`) VALUES
(1, 7, 'ammu@gmail.com', 'Shop', '2025-10-13 02:17:30', 1),
(6, 6, 'liya@gmail.com', 'Shop', '2025-10-13 02:18:23', 1),
(10, 1, 'emanaponnu@gmail.com', 'Shop', '2025-10-12 17:03:36', 1),
(11, 2, 'Fathima@gmail.com', 'Buyer', '2025-10-13 01:44:11', 1),
(24, 14, 'job@gmail.com', 'Shop', '2025-10-13 02:19:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`) VALUES
(101, 'admin@gmail.com', 'admin@123');

-- --------------------------------------------------------

--
-- Table structure for table `admin_categories`
--

CREATE TABLE `admin_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_categories`
--

INSERT INTO `admin_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Recycled Fashion', '2025-10-06 16:54:07'),
(2, 'Upcycled Fashion', '2025-10-06 17:15:13'),
(3, 'sustainable home decor', '2025-10-06 17:15:32'),
(4, 'Recycled Home Products', '2025-10-06 17:16:53'),
(5, 'Upcycled Furniture', '2025-10-06 17:17:11'),
(6, 'Upcycled Kitchenware', '2025-10-06 17:17:26'),
(7, 'ecofriendly kitchenware', '2025-10-06 17:17:37'),
(8, 'Natural and organic skincare', '2025-10-06 17:17:55'),
(9, 'Recycled Stationary', '2025-10-06 17:18:07'),
(10, 'Upcycled stationary', '2025-10-06 17:18:24'),
(11, 'sustainable gardening', '2025-10-06 17:18:39'),
(12, 'Other category', '2025-10-06 17:18:48'),
(13, 'Traditional Kitchenware', '2025-10-10 13:32:43');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(1, 14, 4, 1, '2025-10-10 08:43:28'),
(15, 2, 17, 1, '2025-10-13 01:44:21'),
(16, 2, 14, 1, '2025-10-13 01:44:32');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `cat_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cat_id`, `name`, `image`, `description`, `user_id`, `created_at`) VALUES
(18, 'Upcycled Kitchenware', 'basket.jpg', 'Upcycled kitchenware', 1, '2025-10-09 14:03:31'),
(19, 'Recycled Home Products', 'basket.jpg', 'Recycled Kitchenware', 1, '2025-10-09 14:13:03'),
(20, 'ecofriendly kitchenware', 'glass1.2.jpg', 'Ecofriendly kitchenware', 1, '2025-10-09 15:06:30'),
(21, 'Upcycled stationary', 'pouch1.png', 'Upcycled stationary items', 6, '2025-10-10 07:12:32'),
(22, 'Recycled Stationary', 'ecological-bag-with-cutlery.jpg', 'Recycled Stationary items', 6, '2025-10-10 07:21:03'),
(23, 'Upcycled Fashion', 'Screenshot 2025-08-15 084208.png', 'Upcycled Fashion materials', 7, '2025-10-10 07:34:07'),
(24, 'Recycled Fashion', 'ecological-bag-with-cutlery.jpg', 'Recycled Fashion Products', 7, '2025-10-10 07:37:34'),
(25, 'Natural and organic skincare', 'Screenshot 2025-09-09 181513.png', 'Natural and Organic skincare', 14, '2025-10-10 07:53:20'),
(26, 'sustainable gardening', 'Screenshot 2025-08-16 084905.png', 'Sustainable gardening', 14, '2025-10-10 08:11:38'),
(27, 'Traditional Kitchenware', 'cheenachati1.jpeg', 'Traditional kitchenware for indian kitchen\r\n', 1, '2025-10-10 13:41:49');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'ammu', 'ammu@gmail.com', 'delivery', 'delay', '2025-10-07 00:22:02'),
(2, 'Fathima Hamsa', 'Fathima@gmail.com', 'Delivery', 'Late delivery of item', '2025-10-10 12:49:44');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'pending',
  `order_status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_phone` varchar(15) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_city` varchar(50) DEFAULT NULL,
  `shipping_state` varchar(50) DEFAULT NULL,
  `shipping_pincode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `created_at`, `shipping_name`, `shipping_phone`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_pincode`) VALUES
(1, 1, 1289.00, 'upi', 'paid', 'delivered', '2025-10-10 14:07:59', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 14207.64, 'card', 'failed', 'delivered', '2025-10-10 14:24:50', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 2, 3410.64, 'upi', 'paid', 'delivered', '2025-10-11 16:11:14', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 2, 7918.24, 'upi', 'paid', 'shipped', '2025-10-11 17:19:08', '', '', '', '', '', ''),
(5, 2, 1301.98, 'cod', 'pending', 'pending', '2025-10-12 17:09:29', 'Fathima', '4552875155', 'Karikankudiyil (H),Perumattom,Puthuppadi Po', 'Ernakulam', 'Kerala', '686673');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 19, 1, 1000.00),
(2, 1, 1, 1, 50.00),
(3, 2, 18, 2, 5999.00),
(4, 3, 3, 1, 1999.00),
(5, 3, 1, 1, 50.00),
(6, 3, 10, 1, 799.00),
(7, 4, 18, 1, 5999.00),
(8, 4, 16, 1, 669.00),
(9, 5, 10, 1, 799.00),
(10, 5, 14, 1, 262.00);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rating` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `description`, `price`, `quantity`, `image`, `category_id`, `user_id`, `created_at`, `rating`) VALUES
(1, 'Recycled Glass Tumbler', '\'Beautiful and unusual shaped recycled glass tumbler with gentle curved lip at the top and base. Made from 100% post consumer waste glass.\r\nSize    9cm x 8.5cm \r\napprox  300ml\r\nDishwasher safe.\r\nDue to the nature of hand made blown glass slight variation in size and shape may occur.', 50.00, 19, 'glass1.2.jpg', 19, 1, '2025-10-09 14:16:42', 5),
(2, 'Recycled Glass Bottle Platter', 'Material	   Glass\r\nBrand	           Kavi The Poetry-Art Project\r\nColour	           Clear\r\nSpecial            Feature Eco-Friendly\r\nStyle	           Traditional\r\nShape	           Round\r\nProduct Dimensions 11L x 26W x 11Th Centimeters\r\nNumber of Pieces   1\r\nPattern	           Geometric\r\n\r\n\r\nThis platter is crafted with 100 percent recycled glass bottle, comes with a first-of-its-kind eco-friendly design.\r\nPackage Content and Dimensions : One platter with Cork Stand, Length - 2.3\" ; Height – 3.5\" ; Width – 8\"\r\nThis lovely one of a kind up-cycled vodka bottle serving tray offers a beautiful alternative to the traditional appetizer tray or can even be used as an individual plate or spoon rest. Ideal for home, cafes, restaurants and bars.\r\nThe neck of the bottle is fitted with cork to prevent spillage. Also, the edges have been smoothed and softened keeping safety in mind.\r\nSince all the pieces are recycled, there may be slight scratches.', 500.00, 100, 'glass2.jpg', 19, 1, '2025-10-09 14:33:02', 4.5),
(3, 'Eco-friendly Stainless Steel Kitchen Utensils', 'feature           Eco-Friendly,\r\nStocked material  Metal\r\nutensils type     Utensil Sets\r\nType              Utensils\r\nKeyword           Kitchen tools\r\nMaterial          Dinner/restaurant/hotel/wedding party\r\nPolished          Eco-friendly\r\nDescription\r\n7pcs stainless steel kitchenware set', 1999.00, 399, 'spoon1.png', 20, 1, '2025-10-09 15:11:40', 4),
(4, 'Upcycled Paper Handloom Stationery Zipper Pouch with Plantable Pencils', 'Carry this handcrafted upcycled stationary zipper pouch in style including 10 plantable pencils.\r\n♻️Made from 100% natural dye & paper textile\r\n🤚Handloom by local artisans of Varanasi\r\n✨Stylish, spacious & sustainable fashion\r\n👜Durable metal zipper & cotton inner lining', 90.00, 500, 'pouch1.png', 21, 6, '2025-10-10 07:16:05', 3.5),
(5, 'A5 Ruled Handmad Paper Notebook | Handmade | Sustainable | Tree Free Paper Ruled', 'Soft Bound Design – Lightweight, flexible, and easy to carry\r\nTree-Free Handmade Paper – Made from upcycled waste, not trees\r\n40 Sheets 80 Writing Sides – Plenty of space for your ideas', 40.00, 20, 'note1.jpg', 21, 6, '2025-10-10 07:20:00', 3),
(6, 'Handmade Paper Pen Stand | Eco-Friendly Desk Organizer', 'Hand-woven from newspaper, this upcycled eco-friendly artistic pen stand is a lovely way to organize your stationary. ', 300.00, 80, 'penstand.jpg', 22, 6, '2025-10-10 07:27:33', 4.5),
(7, 'Sustainable  Repurposed Wall Clock', 'Hand-designed Sustainable Earthy Brown Coloured Wall Clock, Symbol of Eternal Time Artistically Arrested in this Repurposed Product...\r\n\r\nAn Unique Eco-friendly Wall Decor Time Piece Crafted with Recycled Old Newspapers Best Expresses that, \"Nothing Really Goes Waste\". Time Moves Forward Eternally Taking in the Old & New...\r\n\r\nA Wonderful Gift for House Warming Parties or to Decorate your Own Home...\r\n\r\nThe Clock is coated with varnish for durability & may easily be kept clean with a paint brush. \r\n\r\nDetails of the Product:-\r\n\r\nItem Code: WCNP-Clock-Brown\r\nPacking: Single\r\nType: Hand-designed Newspaper Wall Clock\r\nColor: Earthy Brown\r\nSize of the Wall Clock Dial: 12\" diameter\r\nOccasion: House Warming Parties & Special Events\r\nMaterial: Old Newspaper\r\nTime Required: Ships in 3-4 days\r\nMade in India', 12999.00, 100, 'clock1.jpg', 22, 6, '2025-10-10 07:31:37', 4),
(8, 'Upcycled Jeans Bag', 'This item is made of upcycled denim. This is a great big tote bag for your everyday needs. This blue denim tote bag is a simple and chic looking fashion accessory you can carry anywhere. Each of my bags is unique because of the different pairs of jeans and combination I use.', 2000.00, 100, 'bag1.jpg', 23, 7, '2025-10-10 07:36:08', 5),
(9, 'Coconut Shell Mini Handbag', 'Closure type      Zipper\r\nStyle             Sling Bag\r\nOccasion type     Casual\r\nNumber of pockets 12\r\nLining            Silk\r\nCountry of Origin India', 2999.00, 6, 'coc2.1.jpg', 24, 7, '2025-10-10 07:40:42', 4.5),
(10, 'Handcrafted Upcycled Denim Necklace', 'Add a touch of sustainable charm to your wardrobe with this upcycled necklace.\r\nHandcrafted with unique upcycled denim\r\n⭐️ Fillings made with cigarette butts\r\n🌿 Sustainable & eco-friendly\r\n✅ Suitable for ethnic & western outfits', 799.00, 10, 'neck1.jpg', 23, 7, '2025-10-10 07:42:53', 4),
(11, 'Women Straw Handbag, Woven Bag', 'Closure type           Zipper\r\nOuter material         Straw\r\nCare instructions      Hand  Wash Only\r\nOccasion type          Beach\r\nNumber of pockets      2', 5000.00, 90, 'bag2.jpg', 24, 7, '2025-10-10 07:46:40', 5),
(12, 'Khadi Natural Lavender Soap | Bathing Bar with Essential Oils | Gently Cleanses, Helps Relieve Stress | For Healthy, Soft Skin | Suitable for All Skin Types | Pack of 3 (125g x 3) | 375g', '\r\nBrand	Khadi Natural\r\nItem Weight	375 Grams\r\nItem dimensions L x W x H	9.3 x 6 x 8.6 Centimeters\r\nScent	Lavender\r\nAge Range (Description)	Adult\r\nSkin Type	All\r\nItem Package Quantity	1\r\nProduct Benefits	Smoothening\r\nItem Form	Bar\r\nMaterial Feature	Natural\r\nSee less\r\nAbout this item\r\nStress Relieving Properties: Lavender oil has proven benefits to relieve stress and anxiety. It relaxes and soothes senses.\r\nAnti-inflammatory properties: It Stimulates circulation, reduces inflammation and energizes body and mind. It leaves skin supple, clean, and fragrant.\r\nAromatic fragrance: It gently exfoliates dead cells, deep cleanses the skin and leaves the skin mildly fragrant.\r\nNatural Made: The soap is made from natural ingredients and is devoid of synthetic preservatives, & artificial colours.\r\nWe LOVE every skin and hair type: The soap is dermatologically tested and can be used on all skin types. We love animals and are against animal cruelty. All our products are ISO, WHO, GMP Certified.', 180.00, 30, 'soap1.1.jpg', 25, 14, '2025-10-10 08:01:06', 3),
(13, 'Juicy Chemistry Aloe, Calendula & Shea - Organic Baby Soap', 'Product Details\r\nAn ideal bathing bar, our certified organic Aloe and Calendula soap has a high percentage of nutrient-rich botanicals and purifying Aloe Vera. It prevents infections and skin rashes while stimulating healthy cell growth and aids in the repair of damaged tissue. Make shower-time fun again for you & your little one!\r\nMade using the traditional cold process method, our soap retains all the vital plant nutrients during the manufacturing process and hence imparts maximum efficacy on application.\r\nKey Benefits\r\nHelps reduce dandruff and itchiness.\r\nMoisturizes dry hair and leaves it · Mild and safe for delicate skin and hair.\r\nAnti-inflammatory aloe soothes the skin and scalp.\r\nIt prevents infections and skin rashes.\r\nPromotes healthy, nourished, and shinier hair.\r\nHelps fade scars over time.\r\nMoisturizes & heals dry skin.\r\nStimulates healthy cell growth.\r\nAids in the repair of damaged tissue.\r\n', 200.00, 50, 'soap1.jpeg', 25, 14, '2025-10-10 08:03:34', 4.5),
(14, 'INDUS VALLEY Bio Organic Multani Mitti Powder For Skin, Hair | Fuller\'s Earth | Bentonite Clay For Glowing Skin, (300g+50g)', '\r\nBrand	INDUS VALLEY\r\nItem Form	Powder\r\nProduct Benefits	Soothing,Brightening,Cleansing,Nourishing\r\nScent	Multani Mitti\r\nMaterial Type Free	Chemical Free\r\nSkin Type	Combination, Dry, Normal, Oily\r\nSpecial Feature	Not Tested On Animals\r\nNet Quantity	250.0 gram\r\nNumber of Items	1\r\nMaterial Feature	Organic', 262.00, 4, 'multani.png', 25, 14, '2025-10-10 08:09:34', 4),
(15, 'ecofynd 4 inches Coir Pots, Biodegradable Garden Nursery Coco Natural Cup for Plants, Seed Germination Kits 100% Eco-Friendly and Indoor Outdoor Seedling Sprouting Transplant (Pack of 10)', '\r\nMaterial	Coir\r\nColour	        Pack of 10\r\nSpecial Feature	Lightweight\r\nStyle	        Garden\r\nPlanter Form	Plant Pot', 443.00, 12, 'sustainable1.png', 26, 14, '2025-10-10 08:14:26', 0),
(16, 'ecofynd 5 inches, Lily Metal Plant Pot with Saucer Plate | Indoor Outdoor Home Decor Item for Garden Plants Flower, Balcony, Patio, Living Room, Garden, Bedroom (Pack of 2, POT029, Color- Multi)', '\r\nMaterial	Metal\r\nColour	        POT029-BF\r\nSpecial Feature	High Quality, Lightweight, Long Lasting,      Rust Resistant, Tested material\r\nStyle	        Modern\r\nPlanter Form	Plant Pot', 669.00, 79, 'sustainable2.png', 26, 14, '2025-10-10 08:16:42', 3),
(17, 'Brass Kadai with Handle 11.50', ' Size:   11.5 inches\r\n Weight: 2 kg\r\n Height: 5 inches\r\n Width:  11.5 inches', 3700.50, 90, 'traditional1.png', 27, 1, '2025-10-10 13:47:12', 4.5),
(18, 'Handcrafted Bronze Cheenachatti - Kansa Kadai: Traditional Indian Cooking Vessel for Healthy Cooking 11 inch', 'Material - Premium quality bronze ,\r\nDia : 11 inch\r\nWeight : 2.9 kg\r\nHandcrafted from high-quality bronze\r\nIt provides even heat distribution and retains heat for longer\r\nAdds elegance to your kitchen with unique texture and patina\r\nMade from Kansa metal for health benefits\r\nWide rim and spacious interior for easy stirring and tossing\r\nIdeal for a variety of cooking styles, from sautéing to deep-frying\r\nBrings the traditional and healthy cooking experience to your home', 5999.00, 37, 'cheenachati1.1.jpeg', 27, 1, '2025-10-10 13:48:24', 5),
(19, 'Earthen Clay Cooking Pot, Traditional Cookware, Handmade Kitchen Essential, Gas stove Safe, Mitti Ke Bartan- 1000 ml', 'Material - Premium quality bronze ,\r\nDia : 11 inch\r\nWeight : 2.9 kg\r\nHandcrafted from high-quality bronze\r\nIt provides even heat distribution and retains heat for longer\r\nAdds elegance to your kitchen with unique texture and patina\r\nMade from Kansa metal for health benefits\r\nWide rim and spacious interior for easy stirring and tossing\r\nIdeal for a variety of cooking styles, from sautéing to deep-frying\r\nBrings the traditional and healthy cooking experience to your home', 1000.00, 50, 'port.jpg', 27, 1, '2025-10-10 13:55:22', 3.5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `f_name` varchar(50) NOT NULL,
  `l_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `role` enum('Shop','Buyer') NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `register_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_restricted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `f_name`, `l_name`, `email`, `password`, `contact`, `role`, `gender`, `register_at`, `is_restricted`) VALUES
(1, 'Emana', 'K A', 'emanaponnu@gmail.com', 'ponnu@2003', '956842986', 'Shop', 'Female', '2025-07-21 10:42:17', 0),
(2, 'Fathima', 'Hamsa', 'Fathima@gmail.com', 'fathima@2002                                                ', '996105200', 'Buyer', 'Female', '2025-07-29 09:46:40', 0),
(6, 'liya', 's', 'liya@gmail.com', 'liya@123', '7567899034', 'Shop', 'female', '2025-09-07 06:05:07', 0),
(7, 'ammu', 'j', 'ammu@gmail.com', 'ammu@123', '8867899034', 'Shop', 'female', '2025-09-07 06:12:54', 0),
(14, 'job', 'g', 'job@gmail.com', 'jobg@123', '7567899034', 'Shop', 'male', '2025-09-23 08:46:35', 0),
(15, 'thara', 'r', 'thara@gmail.com', 'thara@123', '52288965488', 'Buyer', 'female', '2025-10-06 16:08:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `product_id`, `added_at`) VALUES
(1, 14, 3, '2025-10-10 08:43:04'),
(2, 2, 4, '2025-10-10 12:22:43'),
(3, 2, 2, '2025-10-10 12:22:53'),
(4, 2, 3, '2025-10-10 13:10:37'),
(6, 1, 1, '2025-10-10 14:07:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_users`
--
ALTER TABLE `active_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_categories`
--
ALTER TABLE `admin_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `fk_user_category` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `active_users`
--
ALTER TABLE `active_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `admin_categories`
--
ALTER TABLE `admin_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_user_category` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
