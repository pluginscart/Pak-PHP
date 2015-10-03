-- --------------------------------------------------------

--
-- Table structure for table `example_cached_data`
--

CREATE TABLE IF NOT EXISTS `example_cached_data` (
  `id` int(11) NOT NULL,
  `function_name` varchar(255) NOT NULL,
  `function_parameters` longtext NOT NULL,
  `data` longtext NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
