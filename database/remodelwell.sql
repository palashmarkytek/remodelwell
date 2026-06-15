-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2026 at 06:41 PM
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
-- Database: `remodelwell`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_activity_log`
--

DROP TABLE IF EXISTS `ci_activity_log`;
CREATE TABLE `ci_activity_log` (
  `id` int(11) NOT NULL,
  `activity_id` tinyint(4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ci_activity_log`
--

INSERT INTO `ci_activity_log` (`id`, `activity_id`, `user_id`, `admin_id`, `created_at`) VALUES
(1, 4, 0, 1, '2026-02-14 01:35:23'),
(2, 4, 0, 1, '2026-04-22 00:59:56'),
(3, 5, 0, 1, '2026-04-22 01:29:18'),
(4, 4, 0, 1, '2026-04-22 02:12:18'),
(5, 4, 0, 1, '2026-04-22 04:36:28'),
(6, 4, 0, 1, '2026-04-22 04:36:53'),
(7, 5, 0, 1, '2026-04-22 04:47:48'),
(8, 4, 0, 1, '2026-04-22 18:27:27'),
(9, 5, 0, 1, '2026-04-22 20:06:58'),
(10, 5, 0, 1, '2026-04-22 20:07:32'),
(11, 5, 0, 1, '2026-04-22 20:10:13'),
(12, 5, 0, 1, '2026-04-22 20:10:21'),
(13, 5, 0, 1, '2026-04-22 20:10:28'),
(14, 5, 0, 1, '2026-04-22 20:12:45'),
(15, 5, 0, 1, '2026-04-22 20:31:30'),
(16, 5, 0, 1, '2026-04-22 20:31:33'),
(17, 5, 0, 1, '2026-04-22 20:31:42'),
(18, 4, 0, 1, '2026-04-22 21:09:16'),
(19, 5, 0, 1, '2026-04-22 21:10:16'),
(20, 5, 0, 1, '2026-04-23 22:15:30'),
(21, 5, 0, 1, '2026-04-23 22:22:03'),
(22, 5, 0, 1, '2026-04-23 22:24:40'),
(23, 5, 0, 1, '2026-04-23 23:37:00'),
(24, 5, 0, 1, '2026-04-27 20:03:13'),
(25, 5, 0, 1, '2026-05-02 20:39:18'),
(26, 4, 0, 1, '2026-05-02 20:41:57'),
(27, 4, 0, 1, '2026-05-06 19:42:31'),
(28, 4, 0, 1, '2026-05-07 13:45:45'),
(29, 4, 0, 1, '2026-05-07 19:25:35'),
(30, 6, 0, 1, '2026-05-10 18:26:20'),
(31, 4, 0, 1, '2026-05-12 20:17:15'),
(32, 4, 0, 1, '2026-05-15 01:10:29'),
(33, 4, 0, 1, '2026-05-16 00:26:58'),
(34, 4, 0, 1, '2026-05-19 00:53:13'),
(35, 4, 0, 1, '2026-05-26 00:58:53'),
(36, 5, 0, 1, '2026-06-11 02:09:33'),
(37, 5, 0, 1, '2026-06-11 02:42:03'),
(38, 5, 0, 1, '2026-06-15 18:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `ci_activity_status`
--

DROP TABLE IF EXISTS `ci_activity_status`;
CREATE TABLE `ci_activity_status` (
  `id` int(11) NOT NULL,
  `description` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ci_activity_status`
--

INSERT INTO `ci_activity_status` (`id`, `description`) VALUES
(1, 'User Created'),
(2, 'User Edited'),
(3, 'User Deleted'),
(4, 'Admin Created'),
(5, 'Admin Edited'),
(6, 'Admin Deleted'),
(10, 'Edit Project'),
(11, 'Add Project'),
(12, 'Edit Document'),
(13, 'Add Document'),
(14, 'Edit Template'),
(15, 'Add Template'),
(16, 'Edit Group'),
(17, 'Add Group'),
(18, 'Add Send'),
(19, 'Edit Leave'),
(20, 'Add Leave'),
(21, 'Delete Project'),
(22, 'Delete Leave'),
(23, 'Delete Document'),
(24, 'Delete Template');

-- --------------------------------------------------------

--
-- Table structure for table `ci_admin`
--

DROP TABLE IF EXISTS `ci_admin`;
CREATE TABLE `ci_admin` (
  `admin_id` int(11) NOT NULL,
  `display_id` varchar(100) NOT NULL,
  `admin_role_id` int(11) NOT NULL,
  `name` varchar(511) NOT NULL,
  `doj` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `mobile_no` varchar(255) NOT NULL,
  `additional_mobile_no` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime NOT NULL,
  `is_verify` tinyint(4) NOT NULL DEFAULT 1,
  `is_admin` tinyint(4) NOT NULL DEFAULT 1,
  `is_active` tinyint(4) NOT NULL DEFAULT 0,
  `is_supper` tinyint(4) NOT NULL DEFAULT 0,
  `token` varchar(255) NOT NULL,
  `token_date_time` datetime DEFAULT NULL,
  `password_reset_code` varchar(255) NOT NULL,
  `leaves_no` int(10) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `gst` varchar(50) DEFAULT NULL,
  `pan` varchar(10) DEFAULT NULL,
  `aadhaar` varchar(12) DEFAULT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `company` varchar(255) NOT NULL,
  `source_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ci_admin`
--

INSERT INTO `ci_admin` (`admin_id`, `display_id`, `admin_role_id`, `name`, `doj`, `email`, `address`, `mobile_no`, `additional_mobile_no`, `password`, `last_login`, `is_verify`, `is_admin`, `is_active`, `is_supper`, `token`, `token_date_time`, `password_reset_code`, `leaves_no`, `created_at`, `updated_at`, `deleted_at`, `gst`, `pan`, `aadhaar`, `company_address`, `company`, `source_id`) VALUES
(1, 'Super Admin', 1, 'Rohan Kakkar', NULL, 'info@markytek.com', 'A-26, Amarabati, Sodepur, Khardaha, West Bengal 700110', '8296022833', NULL, '$2y$10$cOs/k4ik5fKdzBWbXb9KlO2j5oZ1PTGREWVixclRYGFeWbIp4RJYW', '2024-09-07 12:58:39', 1, 1, 1, 1, '', NULL, '', 0, '2024-09-07 12:58:39', '2026-04-22 23:10:29', NULL, NULL, NULL, NULL, NULL, 'Markytek', NULL),
(7, 'RWELL000001', 2, 'Palash Roy', '2026-06-15', 'palash@markytek.com', 'States: AL,CO,HI,ID; ZIP Codes: 12345,12345', '8296022833', '', '$2y$10$cOs/k4ik5fKdzBWbXb9KlO2j5oZ1PTGREWVixclRYGFeWbIp4RJYW', '0000-00-00 00:00:00', 1, 1, 1, 0, '', NULL, '', 11, '2026-06-15 17:40:49', '2026-06-15 17:40:49', NULL, NULL, NULL, NULL, 'States: AL,CO,HI,ID; ZIP Codes: 12345,12345', 'Palash COmpany', 'palashco388'),
(9, 'RWELL000007', 2, 'Rohan Kakkar', '2026-06-15', 'rohan@markytek.com', 'States: GA,HI,IL; ZIP Codes: 12345', '9432165869', '', '$2y$10$cOs/k4ik5fKdzBWbXb9KlO2j5oZ1PTGREWVixclRYGFeWbIp4RJYW', '0000-00-00 00:00:00', 1, 1, 1, 0, '', NULL, '', 11, '2026-06-15 17:49:23', '2026-06-15 17:49:23', NULL, NULL, NULL, NULL, 'States: GA,HI,IL; ZIP Codes: 12345', 'Rohan Company', 'rohancom805'),
(10, 'RWELL000009', 2, 'Kunal Ghosh', '2026-06-15', 'kunal@markytek.com', 'States: CO,HI,IA; ZIP Codes: 23232', '8967452390', '', '$2y$10$cOs/k4ik5fKdzBWbXb9KlO2j5oZ1PTGREWVixclRYGFeWbIp4RJYW', '0000-00-00 00:00:00', 1, 1, 1, 0, '', NULL, '', 11, '2026-06-15 17:53:53', '2026-06-15 18:03:55', NULL, NULL, NULL, NULL, 'States: CO,HI,IA; ZIP Codes: 23232', 'Kunal Company', 'kunalcom494');

-- --------------------------------------------------------

--
-- Table structure for table `ci_admin_roles`
--

DROP TABLE IF EXISTS `ci_admin_roles`;
CREATE TABLE `ci_admin_roles` (
  `admin_role_id` int(11) NOT NULL,
  `admin_role_title` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `admin_role_status` int(11) NOT NULL,
  `admin_role_created_by` int(1) NOT NULL,
  `admin_role_created_on` datetime NOT NULL,
  `admin_role_modified_by` int(11) NOT NULL,
  `admin_role_modified_on` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ci_admin_roles`
--

INSERT INTO `ci_admin_roles` (`admin_role_id`, `admin_role_title`, `admin_role_status`, `admin_role_created_by`, `admin_role_created_on`, `admin_role_modified_by`, `admin_role_modified_on`, `deleted_at`) VALUES
(1, 'Super Admin', 1, 0, '2018-03-15 12:48:04', 0, '2018-03-17 12:53:16', NULL),
(2, 'Advertiser', 1, 0, '2022-01-08 10:19:09', 0, '2026-06-11 01:01:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ci_advertisers`
--

DROP TABLE IF EXISTS `ci_advertisers`;
CREATE TABLE `ci_advertisers` (
  `advertiser_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT 'Linked ci_admin.admin_id created from /auth/register',
  `currently_buying` varchar(10) NOT NULL,
  `monthly_budget` varchar(100) NOT NULL,
  `agreement_accept` varchar(10) NOT NULL,
  `advertiserID` int(11) DEFAULT NULL COMMENT 'Leadspedia advertiserID returned by advertisers/create.do',
  `contactID` int(11) DEFAULT NULL COMMENT 'Leadspedia contactID returned by advertisersContacts/create.do',
  `leadspedia_request` longtext DEFAULT NULL,
  `leadspedia_response` longtext DEFAULT NULL,
  `leadspedia_http_code` int(11) DEFAULT NULL,
  `leadspedia_status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ci_advertisers`
--

INSERT INTO `ci_advertisers` (`advertiser_id`, `admin_id`, `currently_buying`, `monthly_budget`, `agreement_accept`, `advertiserID`, `contactID`, `leadspedia_request`, `leadspedia_response`, `leadspedia_http_code`, `leadspedia_status`, `created_at`, `updated_at`) VALUES
(1, 7, 'Yes', '1234', 'Yes', 280, 253, '{\"create\":{\"endpoint\":\"advertisers\\/create.do\",\"payload\":{\"advertiserName\":\"\",\"accountManagerID\":8604,\"status\":\"Active\"}},\"advertiser_create\":{\"endpoint\":\"advertisers\\/create.do\",\"payload\":{\"accountManagerID\":8604,\"advertiserName\":\"PalashCompany\",\"status\":\"Active\"}},\"advertiser_update_info\":{\"endpoint\":\"advertisers\\/updateInfo.do\",\"payload\":{\"advertiserName\":\"Palash Roy\",\"externalCRMID\":\"RWELL000001\",\"source\":\"Contractor Portal\",\"advertiserID\":280}},\"advertiser_contact_create\":{\"endpoint\":\"advertisersContacts\\/create.do\",\"payload\":{\"phoneNumber\":\"8296022833\",\"advertiserID\":280,\"emailAddress\":\"palash@markytek.com\",\"firstName\":\"Palash\",\"lastName\":\"Roy\",\"password\":\"PalashRoy_8296022833\"}}}', '{\"create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":false,\\\"message\\\":\\\"Advertiser Name is Required\\\"}\"},\"update_info\":{\"skipped\":true,\"message\":\"Update Info was not called because Create did not return a valid advertiserID.\"},\"advertiser_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Advertiser has been created\\\",\\\"data\\\":{\\\"advertiserID\\\":280}}\"},\"advertiser_update_info\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Advertiser has been updated\\\"}\"},\"advertiser_contact_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Contact has been created\\\",\\\"data\\\":{\\\"contactID\\\":253}}\"}}', 200, 'success', '2026-06-15 17:40:49', '2026-06-15 19:14:05'),
(2, 9, 'Yes', '900', 'Yes', 281, 254, '{\"advertiser_create\":{\"endpoint\":\"advertisers\\/create.do\",\"payload\":{\"accountManagerID\":8604,\"advertiserName\":\"RohanCompany\",\"status\":\"Active\"}},\"advertiser_update_info\":{\"endpoint\":\"advertisers\\/updateInfo.do\",\"payload\":{\"advertiserName\":\"Rohan Kakkar\",\"externalCRMID\":\"RWELL000007\",\"source\":\"Contractor Portal\",\"advertiserID\":281}},\"advertiser_contact_create\":{\"endpoint\":\"advertisersContacts\\/create.do\",\"payload\":{\"phoneNumber\":\"9432165869\",\"advertiserID\":281,\"emailAddress\":\"rohan@markytek.com\",\"firstName\":\"Rohan\",\"lastName\":\"Kakkar\",\"password\":\"RohanKakkar_9432165869\"}}}', '{\"advertiser_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Advertiser has been created\\\",\\\"data\\\":{\\\"advertiserID\\\":281}}\"},\"advertiser_update_info\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Advertiser has been updated\\\"}\"},\"advertiser_contact_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Contact has been created\\\",\\\"data\\\":{\\\"contactID\\\":254}}\"}}', 200, 'success', '2026-06-15 17:49:23', '2026-06-15 19:25:16'),
(3, 10, 'Yes', '2332', 'Yes', 279, 252, '{\"advertiser_create\":{\"endpoint\":\"advertisers\\/create.do\",\"payload\":{\"accountManagerID\":8604,\"advertiserName\":\"KunalCompany\",\"status\":\"Active\"}},\"advertiser_update_info\":{\"endpoint\":\"advertisers\\/updateInfo.do\",\"payload\":{\"advertiserName\":\"Kunal Ghosh\",\"externalCRMID\":\"RWELL000009\",\"source\":\"Contractor Portal\",\"advertiserID\":279}},\"advertiser_contact_create\":{\"endpoint\":\"advertisersContacts\\/create.do\",\"payload\":{\"phoneNumber\":\"8967452390\",\"advertiserID\":279,\"emailAddress\":\"kunal@markytek.com\",\"firstName\":\"Kunal\",\"lastName\":\"Ghosh\",\"password\":\"KunalGhosh_8967452390\"}}}', '{\"advertiser_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Advertiser has been created\\\",\\\"data\\\":{\\\"advertiserID\\\":279}}\"},\"advertiser_update_info\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Advertiser has been updated\\\"}\"},\"advertiser_contact_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Contact has been created\\\",\\\"data\\\":{\\\"contactID\\\":252}}\"}}', 200, 'success', '2026-06-15 17:53:53', '2026-06-15 19:13:43');

-- --------------------------------------------------------

--
-- Table structure for table `ci_advertiser_vertical_map`
--

DROP TABLE IF EXISTS `ci_advertiser_vertical_map`;
CREATE TABLE `ci_advertiser_vertical_map` (
  `advertiser_vertical_map_id` int(11) NOT NULL,
  `advertiser_id` int(11) NOT NULL,
  `vertical_id` int(11) NOT NULL COMMENT 'References ci_verticals.id',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_advertiser_vertical_map`
--

INSERT INTO `ci_advertiser_vertical_map` (`advertiser_vertical_map_id`, `advertiser_id`, `vertical_id`, `is_active`, `created_at`) VALUES
(4, 1, 4, 1, '2026-06-11 02:42:03'),
(5, 1, 3, 1, '2026-06-11 02:42:03'),
(13, 1, 2, 1, '2026-06-15 17:40:49'),
(15, 2, 1, 1, '2026-06-15 17:49:23'),
(17, 3, 7, 1, '2026-06-15 18:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `ci_general_settings`
--

DROP TABLE IF EXISTS `ci_general_settings`;
CREATE TABLE `ci_general_settings` (
  `id` int(11) NOT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `application_name` varchar(255) DEFAULT NULL,
  `defult_leaves_no` int(5) NOT NULL DEFAULT 10,
  `timezone` varchar(255) DEFAULT NULL,
  `contact_number` varchar(100) DEFAULT NULL,
  `default_language` int(11) NOT NULL,
  `copyright` tinytext DEFAULT NULL,
  `email_from` varchar(100) NOT NULL,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT NULL,
  `smtp_user` varchar(50) DEFAULT NULL,
  `smtp_pass` varchar(50) DEFAULT NULL,
  `leadspedia_account_manager_id` varchar(100) DEFAULT NULL,
  `leadspedia_api_key` varchar(255) DEFAULT NULL,
  `leadspedia_api_secret` varchar(255) DEFAULT NULL,
  `signup_agreement` longtext DEFAULT NULL,
  `acknowledgement_letter` text NOT NULL,
  `acknowledgement_text` longtext NOT NULL,
  `regards` text NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ci_general_settings`
--

INSERT INTO `ci_general_settings` (`id`, `favicon`, `logo`, `application_name`, `defult_leaves_no`, `timezone`, `contact_number`, `default_language`, `copyright`, `email_from`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `leadspedia_account_manager_id`, `leadspedia_api_key`, `leadspedia_api_secret`, `signup_agreement`, `acknowledgement_letter`, `acknowledgement_text`, `regards`, `created_date`, `updated_date`) VALUES
(1, 'assets/img/e271253e1ca361b54b9a2ea0060eceec.png', 'assets/img/77f7be52f7eb872ae650f4a0fa3940a5.png', 'RemodelWell', 11, 'Asia/Kolkata', '+91 98368 52847', 2, '© 2026 All Rights Reserved.', 'info@remodelwell.com', 'smtp.hostinger.com', 465, 'info@remodelwell.com', 'nF[9[Rv:&3', '8604', '83a477f26c532826b5f46fd9a6c595e6', '1b540dfd22a39cc8d477055351e1b1dc', 'By completing signup, you confirm that the information supplied is correct and agree to be contacted for lead delivery setup.', '<p>Dear {{customer_name}},</p><p>Greetings from B Singha Roy & Associates!</p><p>Please find attached the Engagement Letter for the assignment of {{for}}. The letter outlines the scope of our work, responsibilities, terms, and other related details.</p><p>We request you to kindly review the contents and, if found in order, sign and return a scanned copy of the same for our records. Should you have any questions or require any clarifications, please feel free to reach out to us at +91<strong>8981892768</strong>.</p><p> </p>', '<p><strong>1. Transparency</strong></p><ul><li>Clearly state the scope of the financial solution or service being offered.</li><li>Provide a breakdown of fees or charges, ensuring no hidden costs.</li><li>Include all terms and conditions related to the service in the acknowledgment.</li></ul><p><strong>2. Personalization</strong></p><ul><li>Address the client by name to make the communication more personalized.</li><li>Mention specific details about the financial service requested or being provided.</li></ul><p><strong>3. Confirmation of Details</strong></p><ul><li>Clearly list the following in the acknowledgment:<ul><li>Service requested (e.g., tax consultancy, financial planning, loan assistance).</li><li>Price or estimated fees for the service.</li><li>Any required documents or next steps.</li></ul></li></ul><p><strong>4. Commitment to Timelines</strong></p><ul><li>Specify the timeline for service completion or important milestones.</li><li>Acknowledge the expected timeframe for follow-ups or deliverables.</li></ul><p><strong>5. Professional Tone</strong></p><ul><li>Use a professional and courteous tone to reinforce trust.</li><li>Avoid jargon unless the client is well-versed in financial terminology.</li></ul><p><strong>6. Contact Information</strong></p><ul><li>Always provide a point of contact for questions or concerns.</li><li>Include your organization’s phone number, email, and office address for ease of communication.</li></ul><p><strong>7. Compliance with Regulations</strong></p><ul><li>Include a disclaimer or note about adherence to relevant financial laws or regulations.</li><li>Provide transparency about any regulatory bodies you are affiliated with.</li></ul><p><strong>8. Express Gratitude</strong></p><ul><li>Thank the client for choosing your organization and express commitment to delivering value.</li></ul><p><strong>9. Confidentiality Assurance</strong></p><ul><li>Reassure the client that their financial information will be handled with the utmost confidentiality and in compliance with privacy laws.</li></ul><p><strong>10. Next Steps</strong></p><ul><li>Outline what the client can expect next (e.g., a follow-up call, submission of documents, scheduled meetings).</li><li>Provide a timeline for when they can expect updates or results</li></ul>', '<p>We look forward to your continued cooperation.</p><p>Warm regards,<br><strong>Biplab Singha Roy</strong><br>Proprietor<br><strong>B Singha Roy & Associates</strong><br>Chartered Accountants<br>FRN: 327983E<br>M. No.: 303756<br>cabsroy@gmail.com | +91<strong>8981892768</strong> | <br>A-26, Amarabati, Sodepur, West Bengal, 700110</p>', '2026-06-15 17:18:51', '2026-06-15 17:18:51');

-- --------------------------------------------------------

--
-- Table structure for table `ci_language`
--

DROP TABLE IF EXISTS `ci_language`;
CREATE TABLE `ci_language` (
  `id` int(11) NOT NULL,
  `name` varchar(225) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(15) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ci_language`
--

INSERT INTO `ci_language` (`id`, `name`, `short_name`, `status`, `created_at`) VALUES
(2, 'English', 'en', 1, '2019-09-16 01:13:17');

-- --------------------------------------------------------

--
-- Table structure for table `ci_usa_states`
--

DROP TABLE IF EXISTS `ci_usa_states`;
CREATE TABLE `ci_usa_states` (
  `state_id` int(10) UNSIGNED NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `state_abbreviation` char(2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ci_usa_states`
--

INSERT INTO `ci_usa_states` (`state_id`, `state_name`, `state_abbreviation`, `is_active`) VALUES
(1, 'Alabama', 'AL', 1),
(2, 'Alaska', 'AK', 1),
(3, 'Arizona', 'AZ', 1),
(4, 'Arkansas', 'AR', 1),
(5, 'California', 'CA', 1),
(6, 'Colorado', 'CO', 1),
(7, 'Connecticut', 'CT', 1),
(8, 'Delaware', 'DE', 1),
(9, 'Florida', 'FL', 1),
(10, 'Georgia', 'GA', 1),
(11, 'Hawaii', 'HI', 1),
(12, 'Idaho', 'ID', 1),
(13, 'Illinois', 'IL', 1),
(14, 'Indiana', 'IN', 1),
(15, 'Iowa', 'IA', 1),
(16, 'Kansas', 'KS', 1),
(17, 'Kentucky', 'KY', 1),
(18, 'Louisiana', 'LA', 1),
(19, 'Maine', 'ME', 1),
(20, 'Maryland', 'MD', 1),
(21, 'Massachusetts', 'MA', 1),
(22, 'Michigan', 'MI', 1),
(23, 'Minnesota', 'MN', 1),
(24, 'Mississippi', 'MS', 1),
(25, 'Missouri', 'MO', 1),
(26, 'Montana', 'MT', 1),
(27, 'Nebraska', 'NE', 1),
(28, 'Nevada', 'NV', 1),
(29, 'New Hampshire', 'NH', 1),
(30, 'New Jersey', 'NJ', 1),
(31, 'New Mexico', 'NM', 1),
(32, 'New York', 'NY', 1),
(33, 'North Carolina', 'NC', 1),
(34, 'North Dakota', 'ND', 1),
(35, 'Ohio', 'OH', 1),
(36, 'Oklahoma', 'OK', 1),
(37, 'Oregon', 'OR', 1),
(38, 'Pennsylvania', 'PA', 1),
(39, 'Rhode Island', 'RI', 1),
(40, 'South Carolina', 'SC', 1),
(41, 'South Dakota', 'SD', 1),
(42, 'Tennessee', 'TN', 1),
(43, 'Texas', 'TX', 1),
(44, 'Utah', 'UT', 1),
(45, 'Vermont', 'VT', 1),
(46, 'Virginia', 'VA', 1),
(47, 'Washington', 'WA', 1),
(48, 'West Virginia', 'WV', 1),
(49, 'Wisconsin', 'WI', 1),
(50, 'Wyoming', 'WY', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ci_verticals`
--

DROP TABLE IF EXISTS `ci_verticals`;
CREATE TABLE `ci_verticals` (
  `id` int(11) NOT NULL,
  `vertical_id` varchar(100) NOT NULL,
  `vertical_name` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `leadspedia_created_on` datetime DEFAULT NULL,
  `total_offers` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `raw_data` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_verticals`
--

INSERT INTO `ci_verticals` (`id`, `vertical_id`, `vertical_name`, `price`, `status`, `group_id`, `group_name`, `leadspedia_created_on`, `total_offers`, `is_active`, `raw_data`, `created_at`) VALUES
(1, '41', 'Appliance Repair', 123.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:41:37', 6, 1, '{\"verticalID\":41,\"verticalName\":\"Appliance Repair\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:41:37\",\"groupName\":\"Home Improvement\",\"totalOffers\":6}', '2026-06-12 23:39:37'),
(2, '14', 'Auto Insurance', 0.00, 'Active', 5, 'Insurance', '2023-06-02 11:40:33', 12, 1, '{\"verticalID\":14,\"verticalName\":\"Auto Insurance\",\"status\":\"Active\",\"groupID\":5,\"createdOn\":\"2023-06-02 11:40:33\",\"groupName\":\"Insurance\",\"totalOffers\":12}', '2026-06-12 23:39:37'),
(3, '29', 'Awnings', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 16:51:47', 1, 1, '{\"verticalID\":29,\"verticalName\":\"Awnings\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 16:51:47\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(4, '30', 'Basements', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 16:54:03', 119, 1, '{\"verticalID\":30,\"verticalName\":\"Basements\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 16:54:03\",\"groupName\":\"Home Improvement\",\"totalOffers\":119}', '2026-06-12 23:39:37'),
(5, '7', 'Bathroom Remodel', 0.00, 'Active', 1, 'Home Improvement', '2022-06-14 16:37:52', 392, 1, '{\"verticalID\":7,\"verticalName\":\"Bathroom Remodel\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2022-06-14 16:37:52\",\"groupName\":\"Home Improvement\",\"totalOffers\":392}', '2026-06-12 23:39:37'),
(6, '27', 'Biohazard', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 16:40:11', 0, 1, '{\"verticalID\":27,\"verticalName\":\"Biohazard\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 16:40:11\",\"groupName\":\"Home Improvement\",\"totalOffers\":0}', '2026-06-12 23:39:37'),
(7, '47', 'Blinds', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:46:06', 1, 1, '{\"verticalID\":47,\"verticalName\":\"Blinds\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:46:06\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(8, '48', 'Cabinet Refacing', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:47:51', 2, 1, '{\"verticalID\":48,\"verticalName\":\"Cabinet Refacing\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:47:51\",\"groupName\":\"Home Improvement\",\"totalOffers\":2}', '2026-06-12 23:39:37'),
(9, '31', 'Cabinets', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 16:56:36', 3, 1, '{\"verticalID\":31,\"verticalName\":\"Cabinets\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 16:56:36\",\"groupName\":\"Home Improvement\",\"totalOffers\":3}', '2026-06-12 23:39:37'),
(10, '28', 'Carpet', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 16:47:11', 1, 1, '{\"verticalID\":28,\"verticalName\":\"Carpet\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 16:47:11\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(11, '33', 'Closets', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 17:21:43', 1, 1, '{\"verticalID\":33,\"verticalName\":\"Closets\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 17:21:43\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(12, '49', 'Deck', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:48:13', 3, 1, '{\"verticalID\":49,\"verticalName\":\"Deck\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:48:13\",\"groupName\":\"Home Improvement\",\"totalOffers\":3}', '2026-06-12 23:39:37'),
(13, '50', 'Dental', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:48:51', 0, 1, '{\"verticalID\":50,\"verticalName\":\"Dental\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:48:51\",\"groupName\":\"Home Improvement\",\"totalOffers\":0}', '2026-06-12 23:39:37'),
(14, '35', 'Electrical', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 18:32:10', 10, 1, '{\"verticalID\":35,\"verticalName\":\"Electrical\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 18:32:10\",\"groupName\":\"Home Improvement\",\"totalOffers\":10}', '2026-06-12 23:39:37'),
(15, '51', 'Fences', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:49:20', 4, 1, '{\"verticalID\":51,\"verticalName\":\"Fences\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:49:20\",\"groupName\":\"Home Improvement\",\"totalOffers\":4}', '2026-06-12 23:39:37'),
(16, '20', 'Final Expense', 0.00, 'Active', 5, 'Insurance', '2023-06-02 11:47:29', 6, 1, '{\"verticalID\":20,\"verticalName\":\"Final Expense\",\"status\":\"Active\",\"groupID\":5,\"createdOn\":\"2023-06-02 11:47:29\",\"groupName\":\"Insurance\",\"totalOffers\":6}', '2026-06-12 23:39:37'),
(17, '25', 'Fire Damage', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 16:29:31', 0, 1, '{\"verticalID\":25,\"verticalName\":\"Fire Damage\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 16:29:31\",\"groupName\":\"Home Improvement\",\"totalOffers\":0}', '2026-06-12 23:39:37'),
(18, '21', 'Flooring', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 05:23:02', 124, 1, '{\"verticalID\":21,\"verticalName\":\"Flooring\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 05:23:02\",\"groupName\":\"Home Improvement\",\"totalOffers\":124}', '2026-06-12 23:39:37'),
(19, '43', 'Foundation Repair', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:43:30', 16, 1, '{\"verticalID\":43,\"verticalName\":\"Foundation Repair\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:43:30\",\"groupName\":\"Home Improvement\",\"totalOffers\":16}', '2026-06-12 23:39:37'),
(20, '42', 'Garage Door', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:42:39', 4, 1, '{\"verticalID\":42,\"verticalName\":\"Garage Door\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:42:39\",\"groupName\":\"Home Improvement\",\"totalOffers\":4}', '2026-06-12 23:39:37'),
(21, '59', 'Gutter Protection', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:53:18', 3, 1, '{\"verticalID\":59,\"verticalName\":\"Gutter Protection\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:53:18\",\"groupName\":\"Home Improvement\",\"totalOffers\":3}', '2026-06-12 23:39:37'),
(22, '12', 'Gutters', 0.00, 'Active', 1, 'Home Improvement', '2022-10-21 10:33:40', 246, 1, '{\"verticalID\":12,\"verticalName\":\"Gutters\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2022-10-21 10:33:40\",\"groupName\":\"Home Improvement\",\"totalOffers\":246}', '2026-06-12 23:39:37'),
(23, '46', 'Handyman', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:45:37', 2, 1, '{\"verticalID\":46,\"verticalName\":\"Handyman\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:45:37\",\"groupName\":\"Home Improvement\",\"totalOffers\":2}', '2026-06-12 23:39:37'),
(24, '16', 'Health Insurance', 0.00, 'Active', 5, 'Insurance', '2023-06-02 11:44:49', 5, 1, '{\"verticalID\":16,\"verticalName\":\"Health Insurance\",\"status\":\"Active\",\"groupID\":5,\"createdOn\":\"2023-06-02 11:44:49\",\"groupName\":\"Insurance\",\"totalOffers\":5}', '2026-06-12 23:39:37'),
(25, '4', 'Home Improvement', 0.00, 'Active', 1, 'Home Improvement', '2021-07-27 22:21:49', 77, 1, '{\"verticalID\":4,\"verticalName\":\"Home Improvement\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2021-07-27 22:21:49\",\"groupName\":\"Home Improvement\",\"totalOffers\":77}', '2026-06-12 23:39:37'),
(26, '17', 'Home Insurance', 0.00, 'Active', 5, 'Insurance', '2023-06-02 11:45:18', 7, 1, '{\"verticalID\":17,\"verticalName\":\"Home Insurance\",\"status\":\"Active\",\"groupID\":5,\"createdOn\":\"2023-06-02 11:45:18\",\"groupName\":\"Insurance\",\"totalOffers\":7}', '2026-06-12 23:39:37'),
(27, '15', 'Home Security', 0.00, 'Active', 1, 'Home Improvement', '2023-06-02 11:42:43', 25, 1, '{\"verticalID\":15,\"verticalName\":\"Home Security\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2023-06-02 11:42:43\",\"groupName\":\"Home Improvement\",\"totalOffers\":25}', '2026-06-12 23:39:37'),
(28, '10', 'Home Warranty', 0.00, 'Active', 1, 'Home Improvement', '2022-08-16 00:16:25', 22, 1, '{\"verticalID\":10,\"verticalName\":\"Home Warranty\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2022-08-16 00:16:25\",\"groupName\":\"Home Improvement\",\"totalOffers\":22}', '2026-06-12 23:39:37'),
(29, '52', 'House Cleaning', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:50:03', 1, 1, '{\"verticalID\":52,\"verticalName\":\"House Cleaning\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:50:03\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(30, '11', 'HVAC', 0.00, 'Active', 1, 'Home Improvement', '2022-10-12 04:51:42', 213, 1, '{\"verticalID\":11,\"verticalName\":\"HVAC\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2022-10-12 04:51:42\",\"groupName\":\"Home Improvement\",\"totalOffers\":213}', '2026-06-12 23:39:37'),
(31, '53', 'Insulation', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:50:29', 1, 1, '{\"verticalID\":53,\"verticalName\":\"Insulation\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:50:29\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(32, '54', 'Junk Removal', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:50:58', 1, 1, '{\"verticalID\":54,\"verticalName\":\"Junk Removal\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:50:58\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(33, '34', 'Kitchen Remodeling', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 17:39:23', 20, 1, '{\"verticalID\":34,\"verticalName\":\"Kitchen Remodeling\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 17:39:23\",\"groupName\":\"Home Improvement\",\"totalOffers\":20}', '2026-06-12 23:39:37'),
(34, '40', 'Lawn Care', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:40:56', 1, 1, '{\"verticalID\":40,\"verticalName\":\"Lawn Care\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:40:56\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(35, '18', 'Life Insurance', 0.00, 'Active', 5, 'Insurance', '2023-06-02 11:45:48', 5, 1, '{\"verticalID\":18,\"verticalName\":\"Life Insurance\",\"status\":\"Active\",\"groupID\":5,\"createdOn\":\"2023-06-02 11:45:48\",\"groupName\":\"Insurance\",\"totalOffers\":5}', '2026-06-12 23:39:37'),
(36, '60', 'Long Distance Mover', 0.00, 'Active', 1, 'Home Improvement', '2024-06-19 20:12:55', 1, 1, '{\"verticalID\":60,\"verticalName\":\"Long Distance Mover\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-06-19 20:12:55\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(37, '19', 'Medicare', 0.00, 'Active', 5, 'Insurance', '2023-06-02 11:46:58', 2, 1, '{\"verticalID\":19,\"verticalName\":\"Medicare\",\"status\":\"Active\",\"groupID\":5,\"createdOn\":\"2023-06-02 11:46:58\",\"groupName\":\"Insurance\",\"totalOffers\":2}', '2026-06-12 23:39:37'),
(38, '24', 'Mold Removal', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 15:27:50', 1, 1, '{\"verticalID\":24,\"verticalName\":\"Mold Removal\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 15:27:50\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(39, '55', 'Movers', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:51:23', 6, 1, '{\"verticalID\":55,\"verticalName\":\"Movers\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:51:23\",\"groupName\":\"Home Improvement\",\"totalOffers\":6}', '2026-06-12 23:39:37'),
(40, '5', 'MVA', 0.00, 'Active', 2, 'Legal', '2022-03-14 15:03:52', 2, 1, '{\"verticalID\":5,\"verticalName\":\"MVA\",\"status\":\"Active\",\"groupID\":2,\"createdOn\":\"2022-03-14 15:03:52\",\"groupName\":\"Legal\",\"totalOffers\":2}', '2026-06-12 23:39:37'),
(41, '26', 'Painting', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 16:38:36', 8, 1, '{\"verticalID\":26,\"verticalName\":\"Painting\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 16:38:36\",\"groupName\":\"Home Improvement\",\"totalOffers\":8}', '2026-06-12 23:39:37'),
(42, '39', 'Pest Control', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:19:58', 29, 1, '{\"verticalID\":39,\"verticalName\":\"Pest Control\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:19:58\",\"groupName\":\"Home Improvement\",\"totalOffers\":29}', '2026-06-12 23:39:37'),
(43, '32', 'Plumbing', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 17:16:16', 40, 1, '{\"verticalID\":32,\"verticalName\":\"Plumbing\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 17:16:16\",\"groupName\":\"Home Improvement\",\"totalOffers\":40}', '2026-06-12 23:39:37'),
(44, '56', 'Pressure Washing', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:51:53', 2, 1, '{\"verticalID\":56,\"verticalName\":\"Pressure Washing\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:51:53\",\"groupName\":\"Home Improvement\",\"totalOffers\":2}', '2026-06-12 23:39:37'),
(45, '6', 'Remodeling', 0.00, 'Active', 1, 'Home Improvement', '2022-06-13 15:42:54', 2, 1, '{\"verticalID\":6,\"verticalName\":\"Remodeling\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2022-06-13 15:42:54\",\"groupName\":\"Home Improvement\",\"totalOffers\":2}', '2026-06-12 23:39:37'),
(46, '2', 'Roofing', 0.00, 'Active', 1, 'Home Improvement', '2021-04-02 14:51:47', 465, 1, '{\"verticalID\":2,\"verticalName\":\"Roofing\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2021-04-02 14:51:47\",\"groupName\":\"Home Improvement\",\"totalOffers\":465}', '2026-06-12 23:39:37'),
(47, '13', 'Siding', 0.00, 'Active', 1, 'Home Improvement', '2022-10-21 10:35:18', 172, 1, '{\"verticalID\":13,\"verticalName\":\"Siding\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2022-10-21 10:35:18\",\"groupName\":\"Home Improvement\",\"totalOffers\":172}', '2026-06-12 23:39:37'),
(48, '1', 'Solar', 0.00, 'Active', 1, 'Home Improvement', '2021-04-01 18:06:11', 299, 1, '{\"verticalID\":1,\"verticalName\":\"Solar\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2021-04-01 18:06:11\",\"groupName\":\"Home Improvement\",\"totalOffers\":299}', '2026-06-12 23:39:37'),
(49, '57', 'Solar Panels Installation', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:52:31', 1, 1, '{\"verticalID\":57,\"verticalName\":\"Solar Panels Installation\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:52:31\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(50, '8', 'SSDI', 0.00, 'Active', 3, 'SSDI', '2022-07-28 10:28:27', 1, 1, '{\"verticalID\":8,\"verticalName\":\"SSDI\",\"status\":\"Active\",\"groupID\":3,\"createdOn\":\"2022-07-28 10:28:27\",\"groupName\":\"SSDI\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(51, '36', 'Stairlifts', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:08:58', 6, 1, '{\"verticalID\":36,\"verticalName\":\"Stairlifts\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:08:58\",\"groupName\":\"Home Improvement\",\"totalOffers\":6}', '2026-06-12 23:39:37'),
(52, '37', 'Sunrooms', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:10:34', 1, 1, '{\"verticalID\":37,\"verticalName\":\"Sunrooms\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:10:34\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(53, '45', 'Tree', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:44:52', 1, 1, '{\"verticalID\":45,\"verticalName\":\"Tree\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:44:52\",\"groupName\":\"Home Improvement\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(54, '38', 'Walk In Tubs', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:11:23', 135, 1, '{\"verticalID\":38,\"verticalName\":\"Walk In Tubs\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:11:23\",\"groupName\":\"Home Improvement\",\"totalOffers\":135}', '2026-06-12 23:39:37'),
(55, '23', 'Water Damage', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 15:10:24', 8, 1, '{\"verticalID\":23,\"verticalName\":\"Water Damage\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 15:10:24\",\"groupName\":\"Home Improvement\",\"totalOffers\":8}', '2026-06-12 23:39:37'),
(56, '44', 'Water Proofing', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:44:23', 4, 1, '{\"verticalID\":44,\"verticalName\":\"Water Proofing\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:44:23\",\"groupName\":\"Home Improvement\",\"totalOffers\":4}', '2026-06-12 23:39:37'),
(57, '9', 'WC', 0.00, 'Active', 4, 'WC', '2022-07-28 10:28:42', 1, 1, '{\"verticalID\":9,\"verticalName\":\"WC\",\"status\":\"Active\",\"groupID\":4,\"createdOn\":\"2022-07-28 10:28:42\",\"groupName\":\"WC\",\"totalOffers\":1}', '2026-06-12 23:39:37'),
(58, '58', 'Window Cleaning', 0.00, 'Active', 1, 'Home Improvement', '2024-02-02 21:52:54', 2, 1, '{\"verticalID\":58,\"verticalName\":\"Window Cleaning\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2024-02-02 21:52:54\",\"groupName\":\"Home Improvement\",\"totalOffers\":2}', '2026-06-12 23:39:37'),
(59, '3', 'Windows', 0.00, 'Active', 1, 'Home Improvement', '2021-04-22 18:15:00', 435, 1, '{\"verticalID\":3,\"verticalName\":\"Windows\",\"status\":\"Active\",\"groupID\":1,\"createdOn\":\"2021-04-22 18:15:00\",\"groupName\":\"Home Improvement\",\"totalOffers\":435}', '2026-06-12 23:39:37');

-- --------------------------------------------------------

--
-- Table structure for table `ci_vertical_contract_map`
--

DROP TABLE IF EXISTS `ci_vertical_contract_map`;
CREATE TABLE `ci_vertical_contract_map` (
  `advertiser_vertical_map_id` int(11) NOT NULL,
  `contractID` int(11) DEFAULT NULL COMMENT 'Leadspedia contractID returned by leadDistributionContracts/create.do',
  `leads_per_week` int(11) NOT NULL DEFAULT 0,
  `zip_codes` text DEFAULT NULL,
  `state_abbreviations` text DEFAULT NULL COMMENT 'Comma-separated state abbreviations',
  `lead_delivery_method` varchar(100) NOT NULL,
  `lead_delivery_sms` varchar(50) DEFAULT NULL,
  `lead_delivery_email` varchar(255) DEFAULT NULL,
  `delivery_days` varchar(255) DEFAULT NULL COMMENT 'Comma-separated weekday names',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `leadspedia_request` longtext DEFAULT NULL,
  `leadspedia_response` longtext DEFAULT NULL,
  `leadspedia_http_code` int(11) DEFAULT NULL,
  `leadspedia_status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ci_vertical_contract_map`
--

INSERT INTO `ci_vertical_contract_map` (`advertiser_vertical_map_id`, `contractID`, `leads_per_week`, `zip_codes`, `state_abbreviations`, `lead_delivery_method`, `lead_delivery_sms`, `lead_delivery_email`, `delivery_days`, `start_time`, `end_time`, `leadspedia_request`, `leadspedia_response`, `leadspedia_http_code`, `leadspedia_status`, `created_at`, `updated_at`) VALUES
(13, 2087, 200, '12345,12345', 'AL,CO,HI,ID', 'Email', '', 'palash@markytek.com', 'Monday,Wednesday,Friday', '06:36:00', '18:36:00', '{\"contract_create\":{\"endpoint\":\"leadDistributionContracts\\/create.do\",\"payload\":{\"advertiserID\":280,\"verticalID\":14,\"contractName\":\"RWELL000001_Auto Insurance\",\"defaultPrice\":1,\"revenueModel\":\"Fixed\"}},\"zip_filter\":{\"endpoint\":\"leadDistributionContracts\\/addFilter.do\",\"payload\":{\"date\":\"2026-06-15\",\"value\":\"12345,12345\",\"contractID\":2087,\"fieldID\":1369,\"operator\":\"Equals\"}},\"state_filter\":{\"endpoint\":\"leadDistributionContracts\\/addFilter.do\",\"payload\":{\"date\":\"2026-06-15\",\"value\":\"AL,CO,HI,ID\",\"contractID\":2087,\"fieldID\":1368,\"operator\":\"Equals\"}},\"contract_schedule\":{\"endpoint\":\"leadDistributionContracts\\/createSchedule.do\",\"payload\":{\"Friday\":\"Yes\",\"Monday\":\"Yes\",\"Saturday\":\"No\",\"Sunday\":\"No\",\"Thursday\":\"No\",\"Tuesday\":\"No\",\"Wednesday\":\"Yes\",\"cap\":200,\"contractID\":2087,\"endTime\":\"18:36:00\",\"startTime\":\"06:36:00\",\"price\":0,\"revenueCap\":1234,\"type\":\"Exclusive\"}}}', '{\"contract_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Contract has been created\\\",\\\"data\\\":{\\\"contractID\\\":2087}}\"},\"zip_filter\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Filter has been created\\\",\\\"data\\\":{\\\"filterID\\\":4109}}\"},\"state_filter\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Filter has been created\\\",\\\"data\\\":{\\\"filterID\\\":4110}}\"},\"contract_schedule\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Delivery Schedule has been created\\\",\\\"data\\\":{\\\"deliveryScheduleIDs\\\":{\\\"Monday\\\":11972,\\\"Wednesday\\\":11973,\\\"Friday\\\":11974}}}\"}}', 200, 'success', '2026-06-15 17:40:49', '2026-06-15 19:14:05'),
(15, 2088, 20000, '12345', 'GA,HI,IL', 'SMS', '122345', '', 'Monday', '06:47:00', '17:44:00', '{\"contract_create\":{\"endpoint\":\"leadDistributionContracts\\/create.do\",\"payload\":{\"advertiserID\":281,\"verticalID\":41,\"contractName\":\"RWELL000007_Appliance Repair\",\"defaultPrice\":1,\"revenueModel\":\"Fixed\"}},\"zip_filter\":{\"endpoint\":\"leadDistributionContracts\\/addFilter.do\",\"payload\":{\"date\":\"2026-06-15\",\"value\":\"12345\",\"contractID\":2088,\"fieldID\":1369,\"operator\":\"Equals\"}},\"state_filter\":{\"endpoint\":\"leadDistributionContracts\\/addFilter.do\",\"payload\":{\"date\":\"2026-06-15\",\"value\":\"GA,HI,IL\",\"contractID\":2088,\"fieldID\":1368,\"operator\":\"Equals\"}},\"contract_schedule\":{\"endpoint\":\"leadDistributionContracts\\/createSchedule.do\",\"payload\":{\"Friday\":\"No\",\"Monday\":\"Yes\",\"Saturday\":\"No\",\"Sunday\":\"No\",\"Thursday\":\"No\",\"Tuesday\":\"No\",\"Wednesday\":\"No\",\"cap\":20000,\"contractID\":2088,\"endTime\":\"17:44:00\",\"startTime\":\"06:47:00\",\"price\":123,\"revenueCap\":900,\"type\":\"Exclusive\"}}}', '{\"contract_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Contract has been created\\\",\\\"data\\\":{\\\"contractID\\\":2088}}\"},\"zip_filter\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Filter has been created\\\",\\\"data\\\":{\\\"filterID\\\":4111}}\"},\"state_filter\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Filter has been created\\\",\\\"data\\\":{\\\"filterID\\\":4112}}\"},\"contract_schedule\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Delivery Schedule has been created\\\",\\\"data\\\":{\\\"deliveryScheduleID\\\":11975,\\\"deliveryScheduleIDs\\\":{\\\"Monday\\\":11975}}}\"}}', 200, 'success', '2026-06-15 17:49:23', '2026-06-15 19:25:16'),
(17, 2086, 2323, '23232', 'CO,CT,HI,IA', 'Email', '', 'kunal@markytek.com', 'Tuesday,Friday', '11:53:00', '17:53:00', '{\"contract_create\":{\"endpoint\":\"leadDistributionContracts\\/create.do\",\"payload\":{\"advertiserID\":279,\"verticalID\":47,\"contractName\":\"RWELL000009_Blinds\",\"defaultPrice\":1,\"revenueModel\":\"Fixed\"}},\"zip_filter\":{\"endpoint\":\"leadDistributionContracts\\/addFilter.do\",\"payload\":{\"date\":\"2026-06-15\",\"value\":\"23232\",\"contractID\":2086,\"fieldID\":1369,\"operator\":\"Equals\"}},\"state_filter\":{\"endpoint\":\"leadDistributionContracts\\/addFilter.do\",\"payload\":{\"date\":\"2026-06-15\",\"value\":\"CO,CT,HI,IA\",\"contractID\":2086,\"fieldID\":1368,\"operator\":\"Equals\"}},\"contract_schedule\":{\"endpoint\":\"leadDistributionContracts\\/createSchedule.do\",\"payload\":{\"Friday\":\"Yes\",\"Monday\":\"No\",\"Saturday\":\"No\",\"Sunday\":\"No\",\"Thursday\":\"No\",\"Tuesday\":\"Yes\",\"Wednesday\":\"No\",\"cap\":2323,\"contractID\":2086,\"endTime\":\"17:53:00\",\"startTime\":\"11:53:00\",\"price\":0,\"revenueCap\":2332,\"type\":\"Exclusive\"}}}', '{\"contract_create\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Contract has been created\\\",\\\"data\\\":{\\\"contractID\\\":2086}}\"},\"zip_filter\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Filter has been created\\\",\\\"data\\\":{\\\"filterID\\\":4107}}\"},\"state_filter\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Filter has been created\\\",\\\"data\\\":{\\\"filterID\\\":4108}}\"},\"contract_schedule\":{\"success\":true,\"http_code\":200,\"body\":\"{\\\"success\\\":true,\\\"message\\\":\\\"Delivery Schedule has been created\\\",\\\"data\\\":{\\\"deliveryScheduleIDs\\\":{\\\"Tuesday\\\":11970,\\\"Friday\\\":11971}}}\"}}', 200, 'success', '2026-06-15 18:03:55', '2026-06-15 19:13:43');

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `controller_name` varchar(255) NOT NULL,
  `fa_icon` varchar(100) NOT NULL,
  `operation` text NOT NULL,
  `sort_order` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`module_id`, `module_name`, `controller_name`, `fa_icon`, `operation`, `sort_order`) VALUES
(2, 'role_and_permissions', 'admin_roles', 'fa fa-user-secret', 'access', 3),
(3, 'admin', 'admin', 'fa fa-user', 'access|add|edit|delete|change_status', 4),
(8, 'general_settings', 'general_settings', 'fa fa-cogs', 'access|edit', 10),
(9, 'dashboard', 'dashboard', 'fa fa-dashboard', 'access', 1),
(25, 'profile', 'profile', 'fa fa-user-circle', 'access', 2),
(39, 'verticals', 'verticals', 'fa fa-archive', 'access|add|edit|delete|change_status', 7),
(55, 'user_verticals', 'user_verticals', 'fa fa-archive', 'access|add|edit|delete|change_status', 40);

-- --------------------------------------------------------

--
-- Table structure for table `module_access`
--

DROP TABLE IF EXISTS `module_access`;
CREATE TABLE `module_access` (
  `id` int(11) NOT NULL,
  `admin_role_id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  `operation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `module_access`
--

INSERT INTO `module_access` (`id`, `admin_role_id`, `module`, `operation`) VALUES
(1, 5, 'dashboard', 'access'),
(6, 5, 'units', 'units'),
(7, 5, 'taxes', 'access'),
(8, 5, 'settings', 'access'),
(9, 5, 'units', 'access'),
(10, 5, 'profile', 'access'),
(12, 5, 'change_pwd', 'change_pwd'),
(13, 5, 'change_pwd', 'edit'),
(14, 5, 'units', 'add_unit'),
(16, 2, 'profile', 'access'),
(17, 2, 'change_pwd', 'access'),
(18, 2, 'change_pwd', 'change_pwd'),
(19, 2, 'change_pwd', 'edit'),
(20, 3, 'dashboard', 'access'),
(21, 3, 'profile', 'access'),
(22, 3, 'change_pwd', 'change_pwd'),
(23, 3, 'change_pwd', 'edit'),
(24, 3, 'change_pwd', 'access'),
(25, 4, 'dashboard', 'access'),
(26, 4, 'profile', 'access'),
(27, 4, 'change_pwd', 'access'),
(28, 4, 'change_pwd', 'change_pwd'),
(29, 4, 'change_pwd', 'edit'),
(30, 5, 'change_pwd', 'access'),
(31, 6, 'dashboard', 'access'),
(32, 6, 'profile', 'access'),
(33, 6, 'change_pwd', 'access'),
(34, 6, 'change_pwd', 'change_pwd'),
(35, 6, 'change_pwd', 'edit'),
(36, 7, 'dashboard', 'access'),
(37, 7, 'profile', 'access'),
(38, 7, 'change_pwd', 'access'),
(39, 7, 'change_pwd', 'change_pwd'),
(40, 7, 'change_pwd', 'edit'),
(41, 13, 'dashboard', 'access'),
(42, 13, 'profile', 'access'),
(43, 13, 'change_pwd', 'access'),
(44, 13, 'change_pwd', 'change_pwd'),
(45, 13, 'change_pwd', 'edit'),
(46, 13, 'attendance', 'access'),
(47, 13, 'attendance', 'attendance'),
(48, 13, 'leaves', 'access'),
(49, 13, 'leaves_list', 'access'),
(50, 13, 'leaves_list', 'leaves_list'),
(51, 13, 'leaves_list', 'add'),
(52, 13, 'units', 'access'),
(53, 13, 'units', 'units'),
(54, 13, 'taxes', 'access'),
(55, 13, 'taxes', 'taxes'),
(56, 13, 'categories', 'access'),
(57, 13, 'categories', 'categories'),
(58, 13, 'categories', 'add_categorie'),
(59, 13, 'items', 'access'),
(60, 13, 'godowns', 'access'),
(61, 13, 'godowns', 'godowns'),
(62, 13, 'godowns', 'add_godown'),
(63, 13, 'items', 'items'),
(64, 13, 'items', 'add_item'),
(65, 13, 'bank_list', 'access'),
(66, 13, 'bank_list', 'bank_list'),
(67, 13, 'bank_list', 'add_bank_account'),
(68, 13, 'settings', 'access'),
(69, 13, 'inventory', 'access'),
(70, 13, 'inventory', 'add'),
(71, 13, 'inventory', 'edit'),
(72, 13, 'purchase', 'access'),
(73, 13, 'purchase_list', 'access'),
(74, 13, 'purchase_list', 'purchase_list'),
(75, 13, 'purchase_list', 'add'),
(76, 13, 'purchase_list', 'edit'),
(77, 13, 'purchase_list', 'delete'),
(78, 13, 'received', 'access'),
(79, 13, 'received', 'received'),
(80, 13, 'received', 'received_add'),
(81, 13, 'received', 'received_edit'),
(82, 13, 'supplier_payment', 'access'),
(83, 13, 'debit_notes', 'access'),
(84, 13, 'supplier_payment', 'supplier_payment'),
(85, 13, 'supplier_payment', 'add_supplier_payment'),
(86, 13, 'debit_notes', 'debit_notes'),
(87, 13, 'debit_notes', 'debit_notes_add'),
(88, 13, 'debit_notes', 'debit_notes_edit'),
(90, 13, 'godowns', 'edit_godown'),
(91, 6, 'leaves', 'access'),
(92, 6, 'leaves_list', 'access'),
(93, 6, 'attendance', 'access'),
(94, 6, 'leaves_list', 'leaves_list'),
(95, 6, 'leaves_list', 'add'),
(96, 6, 'attendance', 'attendance'),
(97, 6, 'settings', 'access'),
(98, 6, 'categories', 'access'),
(99, 6, 'categories', 'categories'),
(100, 6, 'categories', 'add_categorie'),
(101, 6, 'units', 'access'),
(102, 6, 'units', 'units'),
(103, 6, 'taxes', 'access'),
(104, 6, 'taxes', 'taxes'),
(105, 6, 'items', 'access'),
(106, 6, 'items', 'items'),
(107, 6, 'items', 'add_item'),
(109, 6, 'godowns', 'access'),
(110, 6, 'godowns', 'godowns'),
(111, 6, 'godowns', 'add_godown'),
(112, 6, 'bank_list', 'access'),
(113, 6, 'bank_list', 'bank_list'),
(114, 6, 'inventory', 'access'),
(115, 6, 'inventory', 'add'),
(116, 6, 'inventory', 'edit'),
(117, 6, 'purchase', 'access'),
(118, 6, 'purchase_list', 'access'),
(119, 6, 'purchase_list', 'purchase_list'),
(120, 6, 'purchase_list', 'add'),
(122, 6, 'received', 'access'),
(123, 6, 'received', 'received'),
(124, 6, 'received', 'received_add'),
(125, 6, 'received', 'received_edit'),
(126, 6, 'purchase_list', 'edit'),
(127, 6, 'purchase_list', 'delete'),
(128, 6, 'supplier_payment', 'access'),
(129, 6, 'supplier_payment', 'supplier_payment'),
(130, 6, 'supplier_payment', 'add_supplier_payment'),
(131, 6, 'debit_notes', 'access'),
(132, 6, 'debit_notes', 'debit_notes'),
(133, 6, 'debit_notes', 'debit_notes_add'),
(134, 6, 'debit_notes', 'debit_notes_edit'),
(135, 14, 'dashboard', 'access'),
(136, 14, 'profile', 'access'),
(137, 14, 'sales', 'access'),
(138, 14, 'invoice', 'access'),
(139, 14, 'invoice', 'invoice'),
(140, 14, 'payment', 'access'),
(141, 14, 'payment', 'payment'),
(142, 14, 'inventory', 'access'),
(143, 14, 'inventory', 'add'),
(144, 14, 'items', 'access'),
(145, 14, 'items', 'items'),
(148, 5, 'purchase', 'access'),
(149, 5, 'purchase_list', 'access'),
(150, 5, 'purchase_list', 'purchase_list'),
(151, 5, 'purchase_list', 'delete'),
(152, 5, 'received', 'access'),
(153, 5, 'received', 'received'),
(154, 5, 'supplier_payment', 'access'),
(155, 5, 'supplier_payment', 'supplier_payment'),
(156, 5, 'debit_notes', 'access'),
(157, 5, 'debit_notes', 'debit_notes_delete'),
(158, 5, 'purchase_list', 'add'),
(159, 5, 'purchase_list', 'edit'),
(160, 5, 'purchase_list', 'approve'),
(161, 5, 'purchase_list', 'change_status'),
(162, 5, 'received', 'received_delete'),
(168, 2, 'dashboard', 'access'),
(169, 2, 'user_verticals', 'access'),
(170, 2, 'user_verticals', 'change_status');

-- --------------------------------------------------------

--
-- Table structure for table `sub_module`
--

DROP TABLE IF EXISTS `sub_module`;
CREATE TABLE `sub_module` (
  `id` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `operation` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sub_module`
--

INSERT INTO `sub_module` (`id`, `parent`, `name`, `link`, `operation`, `sort_order`) VALUES
(2, 2, 'module_setting', 'module_list', 'access|module_list|module_add|module_edit|module_delete|sub_module|sub_module_add|sub_module_edit|sub_module_delete', 1),
(3, 2, 'role_and_permissions', 'permissions_list', 'access|permissions_list|change_status|delete|add|edit|set_access', 2),
(69, 25, 'view_profile', '', 'access|edit', 1),
(70, 25, 'change_password', 'change_pwd', 'access|change_pwd|edit', 2),
(99, 34, 'leave_list', 'leaves_list', 'access|leaves_list|change_status|add|edit', 0),
(104, 34, 'timesheet', 'timesheet', 'access|attendance|attendance_edit|attendance_update|', 3),
(120, 42, 'estimate_list', 'estimate', 'access|estimate|add_estimate|edit_estimate|estimate_delete', 0),
(123, 42, 'invoice_list', 'invoice', 'access|invoice|add_invoice|invoice_delete', 1),
(129, 43, 'categories', 'categories', 'access|categories|add_categorie|edit_category|change_status_category|delete_category', 0),
(130, 43, 'items', 'items', 'access|items|add_item|edit_item|change_status_item|delete_item', 2),
(131, 43, 'taxes', 'taxes', 'access|taxes|add_tax|edit_tax|change_status_tax|delete_tax', 1),
(132, 43, 'godowns', 'godowns', 'access|godowns|add_godown|edit_godown|change_status_godown|delete_godown', 4),
(133, 43, 'bank_accounts', 'bank_list', 'access|bank_list|add_bank_account|edit_bank_account|change_status_bank_account|delete_bank_account', 5),
(134, 43, 'units', 'units', 'access|units|add_unit|edit_unit|change_status_unit|delete_unit', 0),
(135, 45, 'order_list', 'purchase_list', 'access|purchase_list|add|edit|change_status|approve|delete', 0),
(136, 45, 'received_list', 'received', 'access|received|received_add|received_edit|received_delete', 1),
(137, 45, 'adjustment_list', 'debit_notes', 'access|debit_notes|debit_notes_add|debit_notes_edit|debit_notes_delete', 3),
(138, 42, 'credit_list', 'credit', 'access|credit|add_credit|edit_credit|credit_delete', 4),
(139, 45, 'supplier_payment', 'supplier_payment', 'access|supplier_payment|add_supplier_payment|supplier_payment_delete', 2),
(140, 42, 'invoice_payment', 'payment', 'access|payment|add_invoice_payment|invoice_payment_delete', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sub_module_access`
--

DROP TABLE IF EXISTS `sub_module_access`;
CREATE TABLE `sub_module_access` (
  `id` int(11) NOT NULL,
  `admin_role_id` int(11) NOT NULL,
  `sub_module_id` int(11) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_module_access`
--

INSERT INTO `sub_module_access` (`id`, `admin_role_id`, `sub_module_id`, `operation`, `created_at`) VALUES
(3, 5, 134, 'units', '2026-01-14 10:41:31'),
(4, 5, 131, 'access', '2026-01-14 10:41:52'),
(6, 5, 134, 'access', '2026-01-14 10:50:53'),
(7, 5, 69, 'access', '2026-01-14 10:56:13'),
(8, 5, 69, 'edit', '2026-01-14 10:56:14'),
(10, 5, 70, 'change_pwd', '2026-01-14 10:56:16'),
(11, 5, 70, 'edit', '2026-01-14 10:56:18'),
(12, 5, 134, 'add_unit', '2026-01-14 11:52:11'),
(13, 2, 69, 'access', '2026-01-14 20:12:50'),
(14, 2, 69, 'edit', '2026-01-14 20:12:51'),
(15, 2, 70, 'access', '2026-01-14 20:12:53'),
(16, 2, 70, 'change_pwd', '2026-01-14 20:12:54'),
(17, 2, 70, 'edit', '2026-01-14 20:12:55'),
(18, 3, 69, 'access', '2026-01-14 20:13:09'),
(19, 3, 69, 'edit', '2026-01-14 20:13:10'),
(20, 3, 70, 'change_pwd', '2026-01-14 20:13:10'),
(21, 3, 70, 'edit', '2026-01-14 20:13:11'),
(22, 3, 70, 'access', '2026-01-14 20:13:12'),
(23, 4, 69, 'access', '2026-01-14 20:13:29'),
(24, 4, 69, 'edit', '2026-01-14 20:13:30'),
(25, 4, 70, 'access', '2026-01-14 20:13:31'),
(26, 4, 70, 'change_pwd', '2026-01-14 20:13:32'),
(27, 4, 70, 'edit', '2026-01-14 20:13:32'),
(28, 5, 70, 'access', '2026-01-14 20:13:39'),
(29, 6, 69, 'access', '2026-01-14 20:13:51'),
(31, 6, 70, 'access', '2026-01-14 20:13:52'),
(32, 6, 70, 'change_pwd', '2026-01-14 20:13:53'),
(33, 6, 70, 'edit', '2026-01-14 20:13:54'),
(34, 7, 69, 'access', '2026-01-14 20:14:01'),
(35, 7, 69, 'edit', '2026-01-14 20:14:02'),
(36, 7, 70, 'access', '2026-01-14 20:14:03'),
(37, 7, 70, 'change_pwd', '2026-01-14 20:14:04'),
(38, 7, 70, 'edit', '2026-01-14 20:14:05'),
(39, 13, 69, 'access', '2026-01-14 20:14:23'),
(41, 13, 70, 'access', '2026-01-14 20:14:24'),
(42, 13, 70, 'change_pwd', '2026-01-14 20:14:25'),
(43, 13, 70, 'edit', '2026-01-14 20:14:26'),
(44, 13, 104, 'access', '2026-01-15 16:52:56'),
(45, 13, 104, 'attendance', '2026-01-15 16:52:56'),
(46, 13, 99, 'access', '2026-01-15 16:52:59'),
(47, 13, 99, 'leaves_list', '2026-01-15 16:53:02'),
(48, 13, 99, 'add', '2026-01-15 16:53:07'),
(49, 13, 134, 'access', '2026-01-15 16:54:00'),
(50, 13, 134, 'units', '2026-01-15 16:54:02'),
(51, 13, 131, 'access', '2026-01-15 16:54:04'),
(52, 13, 131, 'taxes', '2026-01-15 16:54:06'),
(53, 13, 129, 'access', '2026-01-15 16:54:07'),
(54, 13, 129, 'categories', '2026-01-15 16:54:09'),
(55, 13, 129, 'add_categorie', '2026-01-15 16:54:13'),
(56, 13, 130, 'access', '2026-01-15 16:54:21'),
(57, 13, 132, 'access', '2026-01-15 16:54:23'),
(58, 13, 132, 'godowns', '2026-01-15 16:54:24'),
(59, 13, 132, 'add_godown', '2026-01-15 16:54:25'),
(60, 13, 130, 'items', '2026-01-15 16:54:26'),
(61, 13, 130, 'add_item', '2026-01-15 16:54:28'),
(62, 13, 133, 'access', '2026-01-15 16:54:37'),
(63, 13, 133, 'bank_list', '2026-01-15 16:54:39'),
(64, 13, 133, 'add_bank_account', '2026-01-15 16:54:40'),
(65, 13, 135, 'access', '2026-01-15 16:55:24'),
(66, 13, 135, 'purchase_list', '2026-01-15 16:55:25'),
(67, 13, 135, 'add', '2026-01-15 16:55:27'),
(68, 13, 135, 'edit', '2026-01-15 16:55:28'),
(69, 13, 135, 'delete', '2026-01-15 16:55:30'),
(70, 13, 136, 'access', '2026-01-15 16:55:40'),
(71, 13, 136, 'received', '2026-01-15 16:55:43'),
(72, 13, 136, 'received_add', '2026-01-15 16:55:44'),
(73, 13, 136, 'received_edit', '2026-01-15 16:55:45'),
(74, 13, 139, 'access', '2026-01-15 16:55:47'),
(75, 13, 137, 'access', '2026-01-15 16:55:49'),
(76, 13, 139, 'supplier_payment', '2026-01-15 16:55:50'),
(77, 13, 139, 'add_supplier_payment', '2026-01-15 16:55:51'),
(78, 13, 137, 'debit_notes', '2026-01-15 16:55:52'),
(79, 13, 137, 'debit_notes_add', '2026-01-15 16:55:54'),
(80, 13, 137, 'debit_notes_edit', '2026-01-15 16:55:55'),
(81, 13, 132, 'edit_godown', '2026-01-15 17:03:34'),
(82, 6, 99, 'access', '2026-01-15 17:05:40'),
(83, 6, 104, 'access', '2026-01-15 17:05:41'),
(84, 6, 99, 'leaves_list', '2026-01-15 17:05:42'),
(85, 6, 99, 'add', '2026-01-15 17:05:44'),
(86, 6, 104, 'attendance', '2026-01-15 17:05:45'),
(87, 6, 129, 'access', '2026-01-15 17:05:55'),
(88, 6, 129, 'categories', '2026-01-15 17:05:57'),
(89, 6, 129, 'add_categorie', '2026-01-15 17:05:59'),
(90, 6, 134, 'access', '2026-01-15 17:06:01'),
(91, 6, 134, 'units', '2026-01-15 17:06:02'),
(92, 6, 131, 'access', '2026-01-15 17:06:05'),
(93, 6, 131, 'taxes', '2026-01-15 17:06:06'),
(94, 6, 130, 'access', '2026-01-15 17:06:07'),
(95, 6, 130, 'items', '2026-01-15 17:06:08'),
(96, 6, 130, 'add_item', '2026-01-15 17:06:09'),
(98, 6, 132, 'access', '2026-01-15 17:06:14'),
(99, 6, 132, 'godowns', '2026-01-15 17:06:16'),
(100, 6, 132, 'add_godown', '2026-01-15 17:06:17'),
(101, 6, 133, 'access', '2026-01-15 17:06:22'),
(102, 6, 133, 'bank_list', '2026-01-15 17:06:25'),
(103, 6, 135, 'access', '2026-01-15 17:06:44'),
(104, 6, 135, 'purchase_list', '2026-01-15 17:06:45'),
(105, 6, 135, 'add', '2026-01-15 17:06:46'),
(107, 6, 136, 'access', '2026-01-15 17:06:48'),
(108, 6, 136, 'received', '2026-01-15 17:06:50'),
(109, 6, 136, 'received_add', '2026-01-15 17:06:52'),
(110, 6, 136, 'received_edit', '2026-01-15 17:06:53'),
(111, 6, 135, 'edit', '2026-01-15 17:06:56'),
(112, 6, 135, 'delete', '2026-01-15 17:06:57'),
(113, 6, 139, 'access', '2026-01-15 17:07:01'),
(114, 6, 139, 'supplier_payment', '2026-01-15 17:07:02'),
(115, 6, 139, 'add_supplier_payment', '2026-01-15 17:07:03'),
(116, 6, 137, 'access', '2026-01-15 17:07:04'),
(117, 6, 137, 'debit_notes', '2026-01-15 17:07:05'),
(118, 6, 137, 'debit_notes_add', '2026-01-15 17:07:06'),
(119, 6, 137, 'debit_notes_edit', '2026-01-15 17:07:08'),
(120, 14, 123, 'access', '2026-01-15 17:18:14'),
(121, 14, 123, 'invoice', '2026-01-15 17:18:15'),
(122, 14, 140, 'access', '2026-01-15 17:18:17'),
(123, 14, 140, 'payment', '2026-01-15 17:18:18'),
(124, 14, 130, 'access', '2026-01-15 17:18:31'),
(125, 14, 130, 'items', '2026-01-15 17:18:32'),
(128, 5, 135, 'access', '2026-01-21 07:32:01'),
(129, 5, 135, 'purchase_list', '2026-01-21 07:32:03'),
(130, 5, 135, 'delete', '2026-01-21 07:32:04'),
(131, 5, 136, 'access', '2026-01-21 07:32:06'),
(132, 5, 136, 'received', '2026-01-21 07:32:07'),
(133, 5, 139, 'access', '2026-01-21 07:32:08'),
(134, 5, 139, 'supplier_payment', '2026-01-21 07:32:10'),
(135, 5, 137, 'access', '2026-01-21 07:32:11'),
(136, 5, 137, 'debit_notes_delete', '2026-01-21 07:32:17'),
(137, 5, 135, 'add', '2026-01-21 07:33:13'),
(138, 5, 135, 'edit', '2026-01-21 07:33:14'),
(139, 5, 135, 'approve', '2026-01-21 07:33:16'),
(140, 5, 135, 'change_status', '2026-01-21 07:33:17'),
(141, 5, 136, 'received_delete', '2026-01-21 07:33:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_activity_log`
--
ALTER TABLE `ci_activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_activity_status`
--
ALTER TABLE `ci_activity_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_admin`
--
ALTER TABLE `ci_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `admin_id_2` (`admin_id`),
  ADD KEY `mobile_no` (`mobile_no`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `ci_admin_roles`
--
ALTER TABLE `ci_admin_roles`
  ADD PRIMARY KEY (`admin_role_id`),
  ADD KEY `admin_role_id` (`admin_role_id`);

--
-- Indexes for table `ci_advertisers`
--
ALTER TABLE `ci_advertisers`
  ADD PRIMARY KEY (`advertiser_id`),
  ADD KEY `idx_advertiser_admin_id` (`admin_id`);

--
-- Indexes for table `ci_advertiser_vertical_map`
--
ALTER TABLE `ci_advertiser_vertical_map`
  ADD PRIMARY KEY (`advertiser_vertical_map_id`),
  ADD UNIQUE KEY `uk_advertiser_vertical` (`advertiser_id`,`vertical_id`),
  ADD KEY `idx_vertical_id` (`vertical_id`);

--
-- Indexes for table `ci_general_settings`
--
ALTER TABLE `ci_general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_language`
--
ALTER TABLE `ci_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_usa_states`
--
ALTER TABLE `ci_usa_states`
  ADD PRIMARY KEY (`state_id`),
  ADD UNIQUE KEY `uk_state_abbreviation` (`state_abbreviation`);

--
-- Indexes for table `ci_verticals`
--
ALTER TABLE `ci_verticals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ci_verticals_vertical_id` (`vertical_id`),
  ADD KEY `idx_ci_verticals_vertical_name` (`vertical_name`);

--
-- Indexes for table `ci_vertical_contract_map`
--
ALTER TABLE `ci_vertical_contract_map`
  ADD PRIMARY KEY (`advertiser_vertical_map_id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `module_access`
--
ALTER TABLE `module_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `RoleId` (`admin_role_id`),
  ADD KEY `idx_module_access_admin_role_id` (`admin_role_id`);

--
-- Indexes for table `sub_module`
--
ALTER TABLE `sub_module`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Parent Module ID` (`parent`),
  ADD KEY `idx_sub_module_parent` (`parent`);

--
-- Indexes for table `sub_module_access`
--
ALTER TABLE `sub_module_access`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_submodule_operation` (`admin_role_id`,`sub_module_id`,`operation`),
  ADD KEY `idx_sub_module_id` (`sub_module_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ci_activity_log`
--
ALTER TABLE `ci_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `ci_activity_status`
--
ALTER TABLE `ci_activity_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ci_admin`
--
ALTER TABLE `ci_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ci_admin_roles`
--
ALTER TABLE `ci_admin_roles`
  MODIFY `admin_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ci_advertisers`
--
ALTER TABLE `ci_advertisers`
  MODIFY `advertiser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ci_advertiser_vertical_map`
--
ALTER TABLE `ci_advertiser_vertical_map`
  MODIFY `advertiser_vertical_map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `ci_general_settings`
--
ALTER TABLE `ci_general_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ci_language`
--
ALTER TABLE `ci_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ci_usa_states`
--
ALTER TABLE `ci_usa_states`
  MODIFY `state_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `ci_verticals`
--
ALTER TABLE `ci_verticals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `module_access`
--
ALTER TABLE `module_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `sub_module`
--
ALTER TABLE `sub_module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `sub_module_access`
--
ALTER TABLE `sub_module_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ci_vertical_contract_map`
--
ALTER TABLE `ci_vertical_contract_map`
  ADD CONSTRAINT `fk_vertical_contract_advertiser_vertical_map` FOREIGN KEY (`advertiser_vertical_map_id`) REFERENCES `ci_advertiser_vertical_map` (`advertiser_vertical_map_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
