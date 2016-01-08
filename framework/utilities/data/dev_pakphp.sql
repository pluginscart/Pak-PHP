-- phpMyAdmin SQL Dump
-- version 4.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 08, 2016 at 03:44 PM
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
-- Table structure for table `api_access_data`
--

CREATE TABLE IF NOT EXISTS `api_access_data` (
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
-- Table structure for table `cached_data`
--

CREATE TABLE IF NOT EXISTS `cached_data` (
  `id` int(11) NOT NULL,
  `function_name` varchar(255) NOT NULL,
  `function_parameters` longtext NOT NULL,
  `data` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cached_data`
--

INSERT INTO `cached_data` (`id`, `function_name`, `function_parameters`, `data`, `created_on`) VALUES
(7, 'TestFunction', 'V3lKd1lYSmhiV1YwWlhJZ01TSXNJbkJoY21GdFpYUmxjaUF5SWwwPQ==', 'dGVzdCBkYXRh', 1452247815),
(35, 'InsertQuery', 'test parameters', 'test data', 1448935438),
(36, 'InsertQuery', 'test parameters', 'test data', 1448935442),
(37, 'InsertQuery', 'test parameters', 'test data', 1448935518),
(38, 'InsertQuery', 'test parameters', 'test data', 1448935520),
(39, 'InsertQuery', 'test parameters', 'test data', 1448935521);

-- --------------------------------------------------------

--
-- Table structure for table `error_data`
--

CREATE TABLE IF NOT EXISTS `error_data` (
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
-- Table structure for table `test_data`
--

CREATE TABLE IF NOT EXISTS `test_data` (
  `id` int(11) NOT NULL,
  `object_name` varchar(255) NOT NULL,
  `function_name` varchar(255) NOT NULL,
  `function_parameters` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `variable_data`
--

CREATE TABLE IF NOT EXISTS `variable_data` (
  `id` int(11) NOT NULL,
  `variable_name` varchar(255) NOT NULL,
  `variable_value` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_access_data`
--
ALTER TABLE `api_access_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cached_data`
--
ALTER TABLE `cached_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `error_data`
--
ALTER TABLE `error_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_data`
--
ALTER TABLE `test_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variable_data`
--
ALTER TABLE `variable_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_access_data`
--
ALTER TABLE `api_access_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cached_data`
--
ALTER TABLE `cached_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT for table `error_data`
--
ALTER TABLE `error_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `test_data`
--
ALTER TABLE `test_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `variable_data`
--
ALTER TABLE `variable_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
