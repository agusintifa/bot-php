-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2019 at 11:21 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bot`
--

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` varchar(50) NOT NULL,
  `chat_id` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `pesan` mediumtext NOT NULL,
  `file_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `chat_id`, `tag`, `type`, `pesan`, `file_id`) VALUES
('20190406004327', '-1001206205304', '#lorem', 'hashtag', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ut ullamcorper risus. Sed eu nunc non justo pulvinar commodo. Suspendisse et faucibus tortor. Suspendisse vehicula semper rutrum. Aliquam eu tempor ligula, eu sodales dui. Nam sed pellentesque velit, venenatis dictum elit. Cras ut lorem nec mauris tempor elementum. Donec tristique at justo in ultricies. Pellentesque interdum finibus sapien, ac dapibus lectus porttitor non. Aenean feugiat ante ac nisi blandit hendrerit. Mauris blandit neque sit amet metus bibendum, sed mattis ante sollicitudin. Sed quis porttitor mauris. Morbi gravida, felis ut lacinia consectetur, turpis purus consequat quam, et luctus nisi augue ac libero. Vivamus sapien ante, molestie at arcu sed, hendrerit vulputate libero. Vivamus sit amet nisi nulla. Interdum et malesuada fames ac ante ipsum primis in faucibus.', '0'),
('20190406140251', '-1001206205304', '#syarat', 'application', ' caption di ganti', 'BQADBQADcwADrF-5V1dmSHY8KtKoAg'),
('20190406140354', '-1001206205304', '#nocap', 'application', ' ini captionnya', 'BQADBQADcgADrF-5V7bqv3zF4hUBAg'),
('20190406140916', '-1001206205304', '#nama', 'hashtag', ' indisanad', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
