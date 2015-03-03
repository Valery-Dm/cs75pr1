-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2015 at 11:56 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cs75finance`
--

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE IF NOT EXISTS `shares` (
`sharesid` int(4) NOT NULL,
  `sharesname` varchar(20) NOT NULL,
  `sharesq` int(6) NOT NULL,
  `sharesprice` decimal(10,2) NOT NULL,
  `sharesuser` int(4) DEFAULT NULL,
  `sharesdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sharesquote` varchar(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shares`
--

INSERT INTO `shares` (`sharesid`, `sharesname`, `sharesq`, `sharesprice`, `sharesuser`, `sharesdate`, `sharesquote`) VALUES
(5, '"Facebook Inc."', 10, '78.97', 3, '2015-03-01 21:37:10', '"FB"'),
(6, '"Ford Motor Compan"', 5, '16.34', 3, '2015-03-01 22:08:09', '"F"'),
(7, '"Google Inc."', 3, '558.40', 9, '2015-03-01 22:17:52', '"GOOG"'),
(8, '"Ford Motor Compan"', 5, '16.34', 40, '2015-03-01 22:29:06', '"F"'),
(11, '"Sandridge Energy "', 34, '1.77', 3, '2015-03-02 01:12:43', '"SD"'),
(12, '"Regions Financial"', 5, '9.61', 3, '2015-03-02 01:38:41', '"RF"'),
(13, '"Ryder System Inc"', 3, '94.39', 3, '2015-03-02 19:44:56', '"R"'),
(14, '"Sprint Corporatio"', 12, '5.12', 3, '2015-03-02 19:53:45', '"S"'),
(15, '"Facebook Inc."', 6, '79.65', 3, '2015-03-02 19:54:54', '"FB"'),
(16, '"Agilent Technolog"', 11, '42.47', 3, '2015-03-02 20:47:12', '"A"');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`userid` int(4) NOT NULL,
  `username` varchar(40) NOT NULL,
  `userpass` varchar(255) NOT NULL,
  `cash` decimal(10,2) NOT NULL DEFAULT '10000.00'
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `userpass`, `cash`) VALUES
(3, 'user01', '$2y$10$LnT9a8iCnkfHQBoOxdEB/OYpEJlNV3MsazO3ApfdBhEpgfSylQcx.', '7659.89'),
(7, 'user02', '$2y$10$gTmKZGO07./TWvLO6QwjiuFaW4Gydw6t2fRPoF1z/wB.98AgfJpEC', '10000.00'),
(9, 'user03', '$2y$10$LN5y5uyJBBG87kHX6ce.Heo0FRvPkeADFWe8FJhFmp4T6h.QHEN4G', '8324.80'),
(12, 'user04', '$2y$10$t8S2ZuHcgIZjxHfyl8TpHuhcLEeaWj/PQEppLo4lEtshoIgFIQkjK', '10000.00'),
(13, 'user05', '$2y$10$3jSvs3b8jwRagK99qFJ.EuNz5B7JwX9tSNEtKAvOPz422Qz7c6bUe', '10000.00'),
(35, 'user06', '$2y$10$e4O7QW4gOu8sNXCSfd9w4ODAEITqHei3whLXbCba.Y2Swpwq7KqMe', '10000.00'),
(36, 'user07', '$2y$10$SPqhgc/9qgyS7CET/0Lg6OHdP/bQfqv08EFgr34ZwA09M9283anwq', '10000.00'),
(37, 'user08', '$2y$10$DTbmaYA43V81egX4eAfxyu4vYEYf3kwcW.Net0E8LBCI80xUHiu1.', '10000.00'),
(40, 'user09', '$2y$10$gVvyCR61LAAK7phkRteIqu1gD1NsZzaNu6yConGM25zAO9RphpiOC', '9160.62'),
(41, 'user10', '$2y$10$bJipkA3udyV/Vt2gwPty3e3aIsSqeNjp8oPBvm35VaC7llqggbNvu', '10000.00'),
(42, 'user11', '$2y$10$RgvUlK4PrgzrLi0dujKVbOMLVJnb5/UgDlezu54HExju3S/hC3nsW', '10000.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shares`
--
ALTER TABLE `shares`
 ADD PRIMARY KEY (`sharesid`), ADD KEY `sharesname` (`sharesname`), ADD KEY `sharesuser` (`sharesuser`), ADD KEY `sharesquote` (`sharesquote`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`userid`), ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
MODIFY `sharesid` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `userid` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=43;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
ADD CONSTRAINT `shares_ibfk_1` FOREIGN KEY (`sharesuser`) REFERENCES `users` (`userid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
