-- phpMyAdmin SQL Dump
-- version 4.4.15.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 13, 2015 at 11:48 AM
-- Server version: 5.6.27
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `se1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `ID` int(11) NOT NULL,
  `Username` varchar(150) NOT NULL,
  `Password` varchar(150) NOT NULL,
  `Salt` varchar(250) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `HospitalName` varchar(150) NOT NULL,
  `HospitalID` varchar(100) NOT NULL,
  `DOB` varchar(50) NOT NULL,
  `Location` varchar(150) NOT NULL,
  `AccessCode` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`ID`, `Username`, `Password`, `Salt`, `Name`, `Email`, `HospitalName`, `HospitalID`, `DOB`, `Location`, `AccessCode`) VALUES
(8, 'test', '097be732d86f9698a16b2d918148b1e8e52106b2f47d12be38835f8b4cf2ce7c', 'f86', 'test', 'test@test.com', 'test', '0123456', '01/01/1978', 'Lowell, MA', 'ZBxh7vCBHv'),
(9, 'test1', '6753b9f455f895ab0027532fdf85298af1ffa266d0ae53aa7af396ce42524233', '7aa', 'test', 'test1@test.com', 'test', '0123456', '01/01/1978', 'Test,Test', 'eE81ZOFsEZ');

-- --------------------------------------------------------

--
-- Table structure for table `authorization`
--

CREATE TABLE IF NOT EXISTS `authorization` (
  `ID` int(11) NOT NULL,
  `AccessCode` varchar(50) NOT NULL,
  `users_UserID` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authorization`
--

INSERT INTO `authorization` (`ID`, `AccessCode`, `users_UserID`) VALUES
(8, 'eE81ZOFsEZ', 2),
(9, 'ZBxh7vCBHv', 2);

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE IF NOT EXISTS `devices` (
  `ID` int(11) NOT NULL,
  `Type` varchar(70) NOT NULL,
  `Make` varchar(70) NOT NULL,
  `Serial` varchar(70) NOT NULL,
  `Model` varchar(70) NOT NULL,
  `Name` varchar(70) NOT NULL,
  `users_UserID` varchar(70) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`ID`, `Type`, `Make`, `Serial`, `Model`, `Name`, `users_UserID`) VALUES
(2, 'Tablet', 'Samsung', '12345678912345', 'SM-T530NU', 'Samsung Galaxy Tab 4', '2'),
(4, 'Smart Watch', 'Samsung', '12345678912345', 'SM-R3800VSAXAR', 'Samsung Gear 2 Smartwatch', '2'),
(5, 'Smart Phone', 'Samsung', '12345678912345', 'SCH-I605', 'Samsung Galaxy Note 2', '2');

-- --------------------------------------------------------

--
-- Table structure for table `recovery`
--

CREATE TABLE IF NOT EXISTS `recovery` (
  `ID` int(11) NOT NULL,
  `Email` text NOT NULL,
  `RequestID` text NOT NULL,
  `ExpDate` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recovery`
--

INSERT INTO `recovery` (`ID`, `Email`, `RequestID`, `ExpDate`) VALUES
(10, 'puzzledplane@yahoo.com', 'y6cgYz6Xmj5k', '04/30/2015 02:26PM'),
(11, 'puzzledplane@yahoo.com', 'c3vNptWa9Rno', '04/30/2015 02:35PM'),
(12, 'puzzledplane@yahoo.com', 'FYXmu6UDSops', '04/30/2015 02:40PM'),
(13, 'puzzledplane@yahoo.com', 'kkHzWX96wjXc', '04/30/2015 02:43PM');

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE IF NOT EXISTS `sensors` (
  `ID` int(11) NOT NULL,
  `users_UserID` int(11) NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Data` text NOT NULL,
  `Date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sensors`
--

INSERT INTO `sensors` (`ID`, `users_UserID`, `Type`, `Data`, `Date`) VALUES
(1, 2, 'Steps', '6750', '2015-10-20 21:18:34'),
(2, 2, 'Steps', '6750', '2015-10-21 22:08:21'),
(3, 2, 'Steps', '6750', '2014-10-21 21:18:34'),
(4, 2, 'Steps', '20000', '2015-10-02 22:32:57'),
(5, 2, 'Steps', '20000', '2015-10-01 22:32:57'),
(6, 2, 'Steps', '20000', '2015-10-03 22:32:57'),
(7, 2, 'Steps', '1500', '2015-10-14 22:32:57'),
(8, 2, 'Steps', '1500', '2015-10-17 22:32:57'),
(10, 2, 'Steps', '6750', '2015-10-22 13:11:37'),
(11, 2, 'Steps', '6750', '2015-10-26 22:44:24'),
(12, 2, 'Steps', '3200', '2015-10-25 22:44:24'),
(14, 2, 'Steps', '3160', '2015-10-27 22:44:24'),
(15, 2, 'Steps', '2750', '2015-10-28 15:21:56'),
(16, 2, 'Steps', '2780', '2015-10-29 10:53:30'),
(17, 2, 'Steps', '2780', '2015-11-04 16:41:50'),
(18, 2, 'Steps', '3160', '2015-11-03 22:44:24'),
(19, 2, 'HB', '75', '2015-11-18 15:33:11'),
(20, 2, 'Steps', '3160', '2015-11-18 15:58:47'),
(21, 2, 'Steps', '3685', '2015-11-17 15:58:47'),
(22, 2, 'HB', '100', '2015-11-16 15:33:11'),
(23, 2, 'HB', '60', '2015-11-01 15:33:11'),
(24, 2, 'HB', '65', '2015-11-19 11:05:34'),
(25, 2, 'Steps', '4200', '2015-11-19 11:20:01'),
(26, 2, 'Steps', '4200', '2015-11-09 11:20:01'),
(27, 2, 'HB', '68', '2015-11-29 11:54:41'),
(28, 2, 'HB', '70', '2015-11-30 11:54:41'),
(29, 2, 'Steps', '4800', '2015-11-30 22:08:21'),
(30, 2, 'Steps', '8800', '2015-11-29 22:08:21'),
(31, 3, 'Steps', '3000', '2015-12-02 21:08:23'),
(33, 2, 'Steps', '7500', '2015-12-02 21:33:32'),
(34, 3, 'HB', '80', '2015-12-02 21:41:40'),
(37, 2, 'HB', '67', '2015-12-02 21:49:08'),
(38, 2, 'Steps', '56', '2015-12-04 15:56:49'),
(39, 2, 'Steps', '12', '2015-12-09 16:26:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL,
  `Username` varchar(150) NOT NULL,
  `Password` varchar(150) NOT NULL,
  `Salt` varchar(250) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Gender` varchar(50) NOT NULL,
  `DOB` varchar(50) NOT NULL,
  `Height` varchar(50) NOT NULL,
  `Weight` varchar(50) NOT NULL,
  `Location` varchar(150) NOT NULL,
  `Authorize` varchar(255) DEFAULT '',
  `StepGoal` int(11) NOT NULL DEFAULT '6000',
  `API_Key` varchar(15) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Username`, `Password`, `Salt`, `Name`, `Email`, `Gender`, `DOB`, `Height`, `Weight`, `Location`, `Authorize`, `StepGoal`, `API_Key`) VALUES
(2, 'test', 'd17af2223afc2f8e86c34082624424faaf16508a3eafd5f7a98608f526c93f57', '3ba', 'test user', 'puzzledplane@yahoo.com', 'Male', '01/01/1990', '6 ft 1 in ', '150', 'Lowell, MA', '', 6000, '2wGVHj5v53KaAY5'),
(4, 'test12', '2986f9a317c0e20e299a38a1e5beeb602e03798eb788c42d1e490615703f968d', '10f', 'test', 'test@test.com1', 'Male', '09/21/1985', '6 ft 2 in ', '180', 'Lowell,MA', NULL, 6000, 'oolda5kcFb6eUau'),
(5, 'test123', '14ce6ef9b0d36cc51954a2b10bd66dac03789422dbd1beefde7d540b1a22d98b', 'c9c', 'test', 'test@test.com', 'Male', '09/21/1985', '6 ft 2 in ', '180', 'Lowell,MA', NULL, 6000, 'xE3XTzouFon1wco');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Username_2` (`Username`),
  ADD UNIQUE KEY `AccessCode` (`AccessCode`);

--
-- Indexes for table `authorization`
--
ALTER TABLE `authorization`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `users_UserID` (`users_UserID`),
  ADD KEY `users_UserID_2` (`users_UserID`),
  ADD KEY `users_UserID_3` (`users_UserID`);

--
-- Indexes for table `recovery`
--
ALTER TABLE `recovery`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `sensors`
--
ALTER TABLE `sensors`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `users_UserID` (`users_UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Username_2` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `authorization`
--
ALTER TABLE `authorization`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `recovery`
--
ALTER TABLE `recovery`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `sensors`
--
ALTER TABLE `sensors`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
