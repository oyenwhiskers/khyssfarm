-- KHYSS Farm Database Backup
-- Created: 2025-10-08 21:53:53
-- Laravel Version: 12.32.5

SET FOREIGN_KEY_CHECKS = 0;

-- Table: harvests
TRUNCATE TABLE `harvests`;
INSERT INTO `harvests` (`id`, `harvest_date`, `quantity_kg`, `variety`, `notes`, `field_location`, `created_at`, `updated_at`) VALUES
('1', '2025-08-09', '0.50', 'Bara', 'First Harvest', 'Merpati', '2025-10-08 20:42:21', '2025-10-08 20:42:30'),
('2', '2025-08-16', '1.20', 'Bara', 'Second Harvest', 'Merpati', '2025-10-08 20:42:52', '2025-10-08 20:42:52'),
('3', '2025-08-23', '2.00', 'Bara', 'Third Harvest', 'Merpati', '2025-10-08 20:43:20', '2025-10-08 20:43:20'),
('4', '2025-09-09', '3.94', 'Bara', 'Fourth Harvest', 'Merpati', '2025-10-08 20:43:51', '2025-10-08 20:43:51'),
('5', '2025-09-17', '5.42', 'Bara', 'Fifth Harvest', 'Merpati', '2025-10-08 20:44:11', '2025-10-08 20:44:31'),
('6', '2025-09-26', '9.00', 'Bara', 'Sixth Harvest', 'Merpati', '2025-10-08 20:45:01', '2025-10-08 20:45:01'),
('7', '2025-10-08', '23.15', 'Bara', 'Seventh Harvest', 'Merpati', '2025-10-08 20:45:37', '2025-10-08 20:45:37');

-- Table: sales
TRUNCATE TABLE `sales`;
INSERT INTO `sales` (`id`, `customer_id`, `sale_date`, `quantity_kg`, `price_per_kg`, `total_amount`, `variety`, `payment_status`, `notes`, `created_at`, `updated_at`) VALUES
('1', '5', '2025-09-09', '3.94', '27.00', '106.38', 'Bara', 'paid', 'Third harvest sales', '2025-10-08 20:48:20', '2025-10-08 20:48:20'),
('2', '10', '2025-09-17', '2.00', '25.00', '50.00', 'Bara', 'paid', 'Fourth harvest sales', '2025-10-08 20:49:46', '2025-10-08 20:49:46'),
('3', '3', '2025-09-17', '3.42', '27.00', '92.34', 'Bara', 'paid', 'Fourth harvest sales', '2025-10-08 20:50:41', '2025-10-08 20:51:04'),
('4', '5', '2025-09-26', '9.00', '20.00', '180.00', 'Bara', 'paid', 'Sixth Harvest Sales', '2025-10-08 20:51:55', '2025-10-08 20:52:51'),
('5', '12', '2025-09-30', '2.00', '27.00', '54.00', 'Bara', 'paid', 'Seventh Harvest Sales', '2025-10-08 20:52:42', '2025-10-08 20:52:42'),
('6', '10', '2025-09-30', '2.00', '25.00', '50.00', 'Bara', 'paid', 'Seventh Harvest Sales', '2025-10-08 20:53:21', '2025-10-08 20:53:21'),
('7', '11', '2025-10-03', '1.40', '25.00', '35.00', 'Bara', 'paid', 'Seventh Harvest Sales', '2025-10-08 20:54:04', '2025-10-08 20:54:29'),
('8', '8', '2025-10-04', '3.00', '17.00', '51.00', 'Bara', 'paid', 'Seventh Harvest Sales', '2025-10-08 20:55:10', '2025-10-08 20:55:10'),
('9', '6', '2025-10-04', '2.00', '25.00', '50.00', 'Bara', 'paid', 'Seventh Harvest Sales', '2025-10-08 20:55:45', '2025-10-08 20:55:45'),
('10', '7', '2025-10-05', '5.00', '17.00', '85.00', 'Bara', 'paid', 'Seventh Harvest Sales', '2025-10-08 20:56:18', '2025-10-08 20:56:18'),
('11', '5', '2025-10-05', '7.74', '20.00', '154.80', 'Bara', 'paid', 'Seventh Harvest Sales', '2025-10-08 20:57:16', '2025-10-08 20:57:16'),
('12', '13', '2025-10-09', '1.00', '25.00', '25.00', 'Bara', 'pending', 'Eighth Harvest Sales', '2025-10-08 21:46:12', '2025-10-08 21:46:12'),
('13', '14', '2025-10-09', '1.00', '27.00', '27.00', 'Bara', 'pending', 'Eighth Harvest Sales', '2025-10-08 21:46:49', '2025-10-08 21:46:49');

-- Table: customers
TRUNCATE TABLE `customers`;
INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `location`, `customer_type`, `source`, `notes`, `created_at`, `updated_at`) VALUES
('1', 'Seaman Cafe', '+60138686502', NULL, 'Lot 1 Blok B6 Bandar Labuk Jaya Batu 7', 'Sandakan', 'retailer', 'whatsapp', 'Restaurant', '2025-10-08 20:22:56', '2025-10-08 20:27:59'),
('2', 'Pak Ali Restaurant', '+60138686502', NULL, 'Jalan Dataran BU 3, 90000 Sandakan, Sabah', 'Sandakan', 'retailer', 'whatsapp', NULL, '2025-10-08 20:23:23', '2025-10-08 20:23:23'),
('3', 'Harimau Menangis', '+60138686502', NULL, 'Lot 1, Blok A1, IJM Batu 6, 90000 Sandakan,', 'Sandakan', 'retailer', 'whatsapp', NULL, '2025-10-08 20:23:39', '2025-10-08 20:23:39'),
('5', 'Thai Gin Yao Mookata', '+601126182919', NULL, 'Prima Square, Lot 208, Block 21, Ground Floor, Lor Prima 8, Batu 4, Jalan Utara, 90000 Sandakan, Sabah', 'Sandakan', 'retailer', 'facebook', NULL, '2025-10-08 20:25:46', '2025-10-08 20:31:18'),
('6', 'Kak Mala', '+60128021085', NULL, 'No Rumah 477 - 476, Lorong Mawar 7, Batu 6, Taman Mawar', 'Sandakan', 'individual', 'recommendation', NULL, '2025-10-08 20:26:04', '2025-10-08 20:31:58'),
('7', 'Thiam Hao Yun', '+60128011157', NULL, 'Lrg Ave 4, IJM, 90000', 'Sandakan', 'retailer', 'recommendation', NULL, '2025-10-08 20:27:49', '2025-10-08 20:27:49'),
('8', 'New King City', '+60172226763', NULL, 'Lorong Avenue 7, Taman Utama, IJM', 'Sandakan', 'retailer', 'recommendation', NULL, '2025-10-08 20:29:01', '2025-10-08 20:29:01'),
('10', 'Dapur Seri Wijaya', '+60133953769', NULL, 'Tingkat Bawah, Lot G-A-43A, Blok A, Sejati Walk, Batu 7, Sandakan, Malaysia', 'Sandakan', 'retailer', 'whatsapp', NULL, '2025-10-08 20:36:17', '2025-10-08 20:38:24'),
('11', 'Darma Dammang', '+60195851875', NULL, 'Taman Kenari', 'Sandakan', 'individual', 'recommendation', NULL, '2025-10-08 20:38:17', '2025-10-08 20:38:17'),
('12', 'Julkiflee Martinis', '+60178935929', NULL, 'Taman Mawar', 'Sandakan', 'individual', 'recommendation', NULL, '2025-10-08 20:38:47', '2025-10-08 20:38:47'),
('13', 'Jaurah Ning', '+60198123205', NULL, 'Taman Nuri Batu 7', 'Sandakan', 'individual', 'facebook', NULL, '2025-10-08 20:39:04', '2025-10-08 20:39:04'),
('14', 'Siti Reehana', '+60142004805', NULL, 'Sungai Batang Batu 10', 'Sandakan', 'individual', 'facebook', NULL, '2025-10-08 20:39:19', '2025-10-08 20:39:19'),
('15', 'Soleh', '+60195227678', NULL, 'Lorong Kayu', 'Sandakan', 'wholesaler', 'facebook', NULL, '2025-10-08 20:40:28', '2025-10-08 20:40:28'),
('16', 'Karimusslam', '+601112364794', NULL, 'Pasar Bandar Sandakan', 'Sandakan', 'wholesaler', 'facebook', NULL, '2025-10-08 20:41:34', '2025-10-08 20:41:34');

-- Table: costs
TRUNCATE TABLE `costs`;
INSERT INTO `costs` (`id`, `date`, `category`, `description`, `amount`, `supplier`, `notes`, `created_at`, `updated_at`) VALUES
('1', '2025-09-09', 'labor', 'Upah Petik @ 3.984KG', '26.70', 'Mr Karim', 'Rate at RM 5 per KG', '2025-10-08 20:59:34', '2025-10-08 21:07:55'),
('2', '2025-09-18', 'labor', 'Upah Petik @ 5.42KG', '40.00', 'Mr Karim', 'Rate RM 5 per KG', '2025-10-08 21:00:06', '2025-10-08 21:07:42'),
('3', '2025-09-29', 'labor', 'Upah Petik @ 9.00KG', '45.00', 'Mr Karim', 'Rate RM 5 per KG', '2025-10-08 21:00:36', '2025-10-08 21:07:15'),
('4', '2025-09-30', 'labor', 'Upah Petik @ 4.00KG', '20.00', 'Mr Karim', 'Rate RM 5 per KG', '2025-10-08 21:01:25', '2025-10-08 21:06:53'),
('5', '2025-09-30', 'loan', 'Chilli Farm Loan', '308.40', 'Mr Shamrim', 'Loan Payment for October', '2025-10-08 21:02:07', '2025-10-08 21:02:22'),
('6', '2025-09-30', 'bills', 'Electric & Water Bill', '70.00', 'Mr Raimay', 'Bill Payment for September 2025', '2025-10-08 21:02:59', '2025-10-08 21:02:59'),
('7', '2025-10-04', 'other', 'Facebook Ads', '14.31', 'Facebook', NULL, '2025-10-08 21:03:39', '2025-10-08 21:03:39'),
('8', '2025-10-05', 'labor', 'Upah Petik Cili @ 2.71KG', '14.00', 'Mr Ken', 'Rate RM 5 per KG', '2025-10-08 21:05:12', '2025-10-08 21:06:24'),
('9', '2025-10-05', 'labor', 'Upah Petik Cili @ 16.44KG', '82.00', 'Mr Karim', 'Rate RM 5 per KG', '2025-10-08 21:05:57', '2025-10-08 21:06:39'),
('10', '2025-10-08', 'other', 'Facebook Ads Marketing (5 Days)', '26.50', 'Facebook', NULL, '2025-10-08 21:39:46', '2025-10-08 21:39:46');

-- Table: marketings
TRUNCATE TABLE `marketings`;
INSERT INTO `marketings` (`id`, `campaign_name`, `campaign_type`, `marketing_channel`, `budget_spent`, `start_date`, `end_date`, `description`, `leads_generated`, `impressions`, `sales_revenue`, `customers_retained`, `product_units_sold`, `clicks`, `conversions`, `notes`, `status`, `created_at`, `updated_at`) VALUES
('5', 'Marketplace Boosting', 'lead_generation', 'facebook', '40.81', '2025-10-08', '2025-10-13', 'Increase leads from facebook', '4', NULL, '52.00', NULL, NULL, '229', '2', NULL, 'active', '2025-10-08 21:11:09', '2025-10-08 21:43:47');

SET FOREIGN_KEY_CHECKS = 1;
