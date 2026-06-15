-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2026 at 09:01 PM
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

CREATE TABLE `ci_activity_log` (
  `id` int(11) NOT NULL,
  `activity_id` tinyint(4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ci_activity_status`
--

CREATE TABLE `ci_activity_status` (
  `id` int(11) NOT NULL,
  `description` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ci_admin`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `ci_admin_roles`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `ci_advertisers`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `ci_advertiser_vertical_map`
--

CREATE TABLE `ci_advertiser_vertical_map` (
  `advertiser_vertical_map_id` int(11) NOT NULL,
  `advertiser_id` int(11) NOT NULL,
  `vertical_id` int(11) NOT NULL COMMENT 'References ci_verticals.id',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ci_general_settings`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `ci_language`
--

CREATE TABLE `ci_language` (
  `id` int(11) NOT NULL,
  `name` varchar(225) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(15) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ci_usa_states`
--

CREATE TABLE `ci_usa_states` (
  `state_id` int(10) UNSIGNED NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `state_abbreviation` char(2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ci_verticals`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `ci_vertical_contract_map`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `controller_name` varchar(255) NOT NULL,
  `fa_icon` varchar(100) NOT NULL,
  `operation` text NOT NULL,
  `sort_order` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `module_access`
--

CREATE TABLE `module_access` (
  `id` int(11) NOT NULL,
  `admin_role_id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  `operation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_module`
--

CREATE TABLE `sub_module` (
  `id` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `operation` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_module_access`
--

CREATE TABLE `sub_module_access` (
  `id` int(11) NOT NULL,
  `admin_role_id` int(11) NOT NULL,
  `sub_module_id` int(11) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_activity_status`
--
ALTER TABLE `ci_activity_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_admin`
--
ALTER TABLE `ci_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_admin_roles`
--
ALTER TABLE `ci_admin_roles`
  MODIFY `admin_role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_advertisers`
--
ALTER TABLE `ci_advertisers`
  MODIFY `advertiser_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_advertiser_vertical_map`
--
ALTER TABLE `ci_advertiser_vertical_map`
  MODIFY `advertiser_vertical_map_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_general_settings`
--
ALTER TABLE `ci_general_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_language`
--
ALTER TABLE `ci_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_usa_states`
--
ALTER TABLE `ci_usa_states`
  MODIFY `state_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_verticals`
--
ALTER TABLE `ci_verticals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `module_access`
--
ALTER TABLE `module_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_module`
--
ALTER TABLE `sub_module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_module_access`
--
ALTER TABLE `sub_module_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
