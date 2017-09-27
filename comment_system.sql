-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2017 at 10:14 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `comment_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(100) NOT NULL,
  `fullname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `comment` text NOT NULL,
  `page_url` text NOT NULL,
  `date` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `fullname`, `email`, `comment`, `page_url`, `date`) VALUES
(9, 'ASAS', 'asas@gmail.com', 'asas', 'aHR0cDovL2xvY2FsaG9zdC9jb21tZW50X3N5c3RlbV92aWFfYWpheF9qcXVlcnlfcGhwLw==', '1506543142'),
(10, 'NANDAN', 'nandan@gmail.com', 'Comment System Using Ajax, Jquery and PHP', 'aHR0cDovL2xvY2FsaG9zdC9jb21tZW50X3N5c3RlbV92aWFfYWpheF9qcXVlcnlfcGhwLw==', '1506543193'),
(7, 'ASASAS', 'asas@gmail.com', 'asasasa', 'aHR0cDovL2xvY2FsaG9zdC9jb21tZW50X3N5c3RlbV92aWFfYWpheF9qcXVlcnlfcGhwLw==', '1506527681'),
(8, 'DEMO', 'demo@gmail.com', 'demo text', 'aHR0cDovL2xvY2FsaG9zdC9jb21tZW50X3N5c3RlbV92aWFfYWpheF9qcXVlcnlfcGhwLw==', '1506527791'),
(5, 'YADU', 'ynandan55@gmail.com', 'hello', '<br />\n<b>Notice</b>:  Undefined index: HTTPS in <b>C:\\xampp\\htdocs\\comment_system_via_ajax_jquery_php\\index.php</b> on line <b>50</b><br />\naHR0cDovL2xvY2FsaG9zdC9jb21tZW50X3N5c3RlbV92aWFfYWpheF9qcXVlcnlfcGhwLw==', '1506519033');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `admin_email_address` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `admin_email_address`) VALUES
(1, 'ynandan55@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
