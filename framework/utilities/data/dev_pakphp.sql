-- phpMyAdmin SQL Dump
-- version 4.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 11, 2016 at 04:28 PM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev_pakphp`
--

-- --------------------------------------------------------

--
-- Table structure for table `pakphp_api_access_data`
--

CREATE TABLE IF NOT EXISTS `pakphp_api_access_data` (
  `id` int(11) NOT NULL,
  `request_option` varchar(50) NOT NULL,
  `request_parameters` text NOT NULL,
  `request_type` varchar(50) NOT NULL,
  `response_format` varchar(50) NOT NULL,
  `response_text` longtext NOT NULL,
  `response_status` varchar(50) NOT NULL,
  `time_taken` int(11) NOT NULL,
  `meta_data` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pakphp_cached_data`
--

CREATE TABLE IF NOT EXISTS `pakphp_cached_data` (
  `id` int(11) NOT NULL,
  `function_name` varchar(255) NOT NULL,
  `function_parameters` longtext NOT NULL,
  `data` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pakphp_cached_data`
--

INSERT INTO `pakphp_cached_data` (`id`, `function_name`, `function_parameters`, `data`, `created_on`) VALUES
(2, 'InsertQuery', 'test parameters', 'test data', 1452509554),
(3, 'InsertQuery', 'test parameters', 'test data', 1452509559),
(4, 'TestFunction', 'V3lKd1lYSmhiV1YwWlhJZ01TSXNJbkJoY21GdFpYUmxjaUF5SWwwPQ==', 'dGVzdCBkYXRh', 1452511277);

-- --------------------------------------------------------

--
-- Table structure for table `pakphp_email_data`
--

CREATE TABLE IF NOT EXISTS `pakphp_email_data` (
  `id` int(11) NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `email_from` varchar(255) NOT NULL,
  `email_subject` text NOT NULL,
  `email_text` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pakphp_error_data`
--

CREATE TABLE IF NOT EXISTS `pakphp_error_data` (
  `id` int(11) NOT NULL,
  `error_level` int(11) NOT NULL,
  `error_type` enum('Error','Exception') NOT NULL,
  `error_message` longtext NOT NULL,
  `error_file` text NOT NULL,
  `error_line` int(11) NOT NULL,
  `error_context` longtext NOT NULL,
  `server_data` longtext NOT NULL,
  `mysql_query_log` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pakphp_test_data`
--

CREATE TABLE IF NOT EXISTS `pakphp_test_data` (
  `id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `option_name` varchar(255) NOT NULL,
  `object_name` varchar(255) NOT NULL,
  `function_type` varchar(20) NOT NULL,
  `function_name` varchar(255) NOT NULL,
  `function_parameters` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pakphp_variable_data`
--

CREATE TABLE IF NOT EXISTS `pakphp_variable_data` (
  `id` int(11) NOT NULL,
  `variable_name` varchar(255) NOT NULL,
  `variable_value` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pakphp_api_access_data`
--
ALTER TABLE `pakphp_api_access_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pakphp_cached_data`
--
ALTER TABLE `pakphp_cached_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pakphp_email_data`
--
ALTER TABLE `pakphp_email_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pakphp_error_data`
--
ALTER TABLE `pakphp_error_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pakphp_test_data`
--
ALTER TABLE `pakphp_test_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pakphp_variable_data`
--
ALTER TABLE `pakphp_variable_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pakphp_api_access_data`
--
ALTER TABLE `pakphp_api_access_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pakphp_cached_data`
--
ALTER TABLE `pakphp_cached_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `pakphp_email_data`
--
ALTER TABLE `pakphp_email_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pakphp_error_data`
--
ALTER TABLE `pakphp_error_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pakphp_test_data`
--
ALTER TABLE `pakphp_test_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pakphp_variable_data`
--
ALTER TABLE `pakphp_variable_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
