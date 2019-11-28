-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2019 at 08:59 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flexi`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `UID` int(11) NOT NULL,
  `SID` int(12) NOT NULL,
  `UActivity` text NOT NULL,
  `UTimeS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`UID`, `SID`, `UActivity`, `UTimeS`) VALUES
(1, 1, 'borrow', '2019-11-28 18:39:16'),
(2, 1, 'borrow', '2019-11-28 18:39:26'),
(3, 1, 'borrow', '2019-11-28 18:44:51'),
(4, 1, 'borrow', '2019-11-28 18:50:15'),
(5, 1, 'read', '2019-11-28 18:51:07'),
(6, 1, 'read', '2019-11-28 18:51:50'),
(7, 1, 'borrow', '2019-11-28 18:56:04'),
(8, 3, 'read', '2019-11-28 18:56:48'),
(9, 3, 'other', '2019-11-28 18:57:28'),
(10, 3, 'other', '2019-11-28 18:57:47'),
(11, 2, 'read', '2019-11-28 19:02:32'),
(12, 1, 'read', '2019-11-28 19:18:24');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `BID` int(11) NOT NULL,
  `bTitle` text NOT NULL,
  `bAuthor` text NOT NULL,
  `bPublisher` text NOT NULL,
  `bBlurb` longtext NOT NULL,
  `bEdition` varchar(10) NOT NULL DEFAULT '',
  `bAccNo` varchar(50) NOT NULL DEFAULT '',
  `bPoPub` varchar(50) NOT NULL DEFAULT '',
  `bSubject` varchar(100) NOT NULL DEFAULT '',
  `bCartegory` varchar(100) NOT NULL DEFAULT '',
  `bYoPub` varchar(4) NOT NULL,
  `bReserve` int(1) NOT NULL,
  `bTimeS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`BID`, `bTitle`, `bAuthor`, `bPublisher`, `bBlurb`, `bEdition`, `bAccNo`, `bPoPub`, `bSubject`, `bCartegory`, `bYoPub`, `bReserve`, `bTimeS`) VALUES
(1, '10 Minute Guide to Access', 'Knuth, Donald E.', 'Publishers', 'This is a small description about this book', '2nd', '23453456CF', 'Capetown, South Afrika', 'Computer Studies', '', '1986', 1, '2019-06-22 10:32:20'),
(2, '101 User Commands', 'Hakim, Jack', 'Company Name', 'This is a small description about this book', '4th', 'D34654645656', 'Capetown, South Afrika', 'Computer Studies', '', '1986', 1, '2019-06-22 10:32:20'),
(3, '101 Uses of dBASE in Libraries (Supplement to Computers in Libraries, No. 12)', 'Jacobs, Russell', 'A K PETERS LTD', 'This is a small description about this book', '2nd', 'F345645764574', 'Capetown, South Afrika', 'Computer Studies', '', '1986', 1, '2019-06-22 10:32:20'),
(4, '7 Keys to Learning Os/2 2.1', 'Metzger, Philip W.', 'A SYSTEM PUBNS', 'This is a small description about this book', '2nd', '346456G546457', 'Capetown, South Afrika', 'Computer Studies', '', '1990', 0, '2019-06-22 10:32:20'),
(5, '80386 Protected Mode Programming in C', 'Boddie, John', 'AA BALKEMA', 'This is a small description about this book', '2nd', '45645Y475675Y', 'Capetown, South Afrika', 'Computer Studies', '', '1990', 0, '2019-06-22 10:32:20'),
(6, '85 dBASE IV : User-Defined Functions and Procedures', 'Sydow, Dan Parks', 'AMER ASSN OF RETIRED PERSONS', 'This is a small description about this book', '2nd', '345634T46456', 'Capetown, South Afrika', 'Computer Studies', '', '1991', 0, '2019-06-22 10:32:20'),
(7, 'A Beginner\'s Approach to Using Microsoft Windows (3.1', 'Lloyd, John', 'ABACUS SOFTWARE', 'This is a small description about this book', '2nd', '89078O89Y8979', 'Capetown, South Afrika', 'Computer Studies', '', '1992', 1, '2019-06-22 10:32:20'),
(8, 'A Beginner\'s Guide To Wordstar, 1-2-3, And Dbase : For Computers Using Pc-dos Or Ms-dos', 'Thiel, James R.', 'Abc Teletraining', 'This is a small description about this book', '2nd', '345345353444Q', 'Capetown, South Afrika', 'Computer Studies', 'Computers', '1990', 1, '2019-11-28 13:41:26'),
(9, 'A Book on C : Programming in C', 'Ingham, Kenneth', 'ABC-CLIO', 'This is a small description about this book', '2nd', 'CVNF3756JTYJTY', 'Capetown, South Afrika', 'Computer Studies', '', '1993', 1, '2019-06-22 10:32:20'),
(10, 'A Brief Course in Qbasic With an Introduction to Visual Basic/Book and Disk', 'Wellin, Paul', 'ABLEX PUB CORP', 'This is a small description about this book', '2nd', '21342342RWERT', 'Capetown, South Afrika', 'Computer Studies', '', '1992', 1, '2019-06-22 10:32:20'),
(11, 'A Brief Introduction to Basic', 'Kamin, Sam', 'Ablex Pub', 'This is a small description about this book', '2nd', 'JYJ76876J6JJ6JJ6J', 'Capetown, South Afrika', 'Computer Studies', '', '1991', 0, '2019-06-22 10:32:20'),
(12, 'A Brief Introduction to Basic : Qbasic-Quick Basic Version', 'Gaylord, Richard', 'ACADEMIC PR', 'This is a small description about this book', '2nd', 'HYT55U674343', 'Capetown, South Afrika', 'Computer Studies', '', '1990', 0, '2019-06-22 10:32:20'),
(13, 'A Brief Introduction to DOS 3.3.', 'Curry, Dave', 'ACCESS PUB', 'This is a small description about this book', '2nd', 'TRETY546456456', 'Capetown, South Afrika', 'Computer Studies', '', '1993', 0, '2019-06-22 10:32:20'),
(14, 'A C User\'s Guide to ANSI C (Addison-Wesley Professional Computing Series', 'Gardner, Juanita Mercado', 'AMER COLLEGE OF RADIOLOGY', 'This is a small description about this book', '2nd', '34563546RRTY', 'Capetown, South Afrika', 'Computer Studies', '', '1995', 0, '2019-06-22 10:32:20'),
(15, 'A C++ Tool Kit', 'Knuth, Donald E.', 'AMER CHEMICAL SOCIETY', 'This is a small description about this book', '2nd', '345645745RTUTYU', 'Dubai, UAE', 'Computer Studies', '', '1993', 0, '2019-06-22 10:32:20'),
(16, 'A Casebook : Four Software Tools', 'Hakim, Jack', 'ADAM HILGER', 'This is a small description about this book', '2nd', '34654756UTRUT', 'Dubai, UAE', 'Computer Studies', '', '1984', 1, '2019-06-22 10:32:20'),
(17, 'A Casebook for a First Course in Statistics and Data Analysis/Book and Disk', 'Winchell, Jeff', 'ADAMS HALL PUB', 'This is a small description about this book', '2nd', '3456346RYTR', 'Dubai, UAE', 'Computer Studies', '', '1985', 0, '2019-06-22 10:32:20'),
(18, 'A Code Mapping Scheme for Dataflow Software Pipelining (The Kluwer International Series in Engineering and Computer Science', 'Clark, Claudia', 'ADARE PUB', 'This is a small description about this book', '2nd', '6453YTRUJ4576', 'Dubai, UAE', 'Computer Studies', '', '1986', 1, '2019-06-22 10:32:20'),
(19, 'A Computerized Audit Practice Case (Micro, Inc.', 'Scott, Jack', 'ADDISON-WESLEY PUB CO', 'This is a small description about this book', '2nd', 'RTE564563RYEY4', 'Dubai, UAE', 'Computer Studies', '', '1988', 1, '2019-06-22 10:32:20'),
(20, 'A Cry of Silence', 'Coolbaugh, James', 'Addison-Wesley Publishing Co Inc.', 'This is a small description about this book', '2nd', '3454TYERYTRY45', 'Dubai, UAE', 'Computer Studies', '', '1994', 0, '2019-06-22 10:32:20'),
(21, 'A Data-Driven Methodology', 'Ladd, Scott Robert', 'Addison-Wesley', 'This is a small description about this book', '2nd', '3456436RETY', 'Dubai, UAE', 'Computer Studies', '', '1990', 0, '2019-06-22 10:32:20'),
(22, 'A First Course in Computer Science With Ada/Book and Disk', 'Gabriel, Richard P.', 'ADVANCED MICRO SUPPLIES INC', 'This is a small description about this book', '2nd', '546456457ETYRT', 'Dubai, UAE', 'Computer Studies', '', '1995', 0, '2019-06-22 10:32:20'),
(23, 'A First Course in Computer Science With Turbo Pascal : Versions 4.0, 5.0, and 5.5 (Principles of Computer Science Series', 'Mitchell, John C.', 'ADVANSTAR COMMUNICATIONS', 'This is a small description about this book', '2nd', 'RTERTY45645745', 'Tokyo, Japan', 'Computer Studies', '', '1994', 1, '2019-06-22 10:32:20'),
(24, 'A First Course in Modula-2', 'Smolka, G.', 'AFH SOFTECH', 'This is a small description about this book', '2nd', 'RTERYTTRY5765', 'Tokyo, Japan', 'Computer Studies', '', '1995', 0, '2019-06-22 10:32:20'),
(25, 'A First Course in Optimization Theory', 'Bowler, Norm', 'AFIPS PR', 'This is a small description about this book', '2nd', '34645756YUTYU', 'Tokyo, Japan', 'Computer Studies', '', '1995', 0, '2019-06-22 10:32:20'),
(26, 'Business Management Software for IBM and Compatible DOS Computers (Icp Software Directory Books', 'Sundaram, Rangarajan K.', 'AGRICULTURE & NATURAL RESOURCES', 'This is a small description about this book', '4th', '456345756UTYUY', 'Tokyo, Japan', 'Computer Studies', '', '1994', 0, '2019-06-22 10:32:20'),
(27, 'Business Planning With IBM Personal Decision Software', 'Torkelson, Cary', 'AKADEMIAI KIADO', 'This is a small description about this book', '2nd', '364567457RTFG', 'Tokyo, Japan', 'Computer Studies', '', '1994', 1, '2019-06-22 10:32:20'),
(28, 'Business Programming in C for Dos-Based Systems (The Dryden Press Series in Information System', 'Ashcroft, E.A.', 'ALBION BOOKS', 'This is a small description about this book', '2nd', 'RETRY457656756UT', 'Tokyo, Japan', 'Computer Studies', '', '1996', 1, '2019-06-22 10:32:20'),
(29, 'Business Programming Using Foxpro', 'Orgun, M.A.', 'ALFRED PUB CO', 'This is a small description about this book', '2nd', 'YAHJ3777375YY6311', 'Chicago, USA', 'Computer Studies', '', '1994', 1, '2019-06-22 10:32:20'),
(30, 'Business Software Applications : Dos, Wordperfect, Lotus dBASE IV', 'Webster, Bruce', 'ALFRED WALLER LTD', 'This is a small description about this book', '2nd', 'FHTRHE657567', 'Tokyo, Japan', 'Computer Studies', '', '1994', 0, '2019-06-22 10:32:20'),
(31, 'Cognition and Computer Programming', 'Kamp, Di', 'ALGORITHMICS PR', 'This is a small description about this book', '2nd', '56465UTY', 'Tokyo, Japan', 'Computer Studies', '', '1993', 0, '2019-06-22 10:32:20'),
(32, 'Cognition and Computer Programming (Ablex Series in Computational Science', 'Cooper, Michael D.', 'ALLEGRO NEW MEDIA', 'This is a small description about this book', '2nd', 'UTYIUYIYU645', 'Tokyo, Japan', 'Computer Studies', '', '1994', 1, '2019-06-22 10:32:20'),
(33, 'Cognition and Computer Programming (Ablex Series in Computational Science', 'Loukides, Mike', 'ALLERTON PR', 'This is a small description about this book', '2nd', 'TRYTRY56577Y', 'Tokyo, Japan', 'Computer Studies', '', '1994', 0, '2019-06-22 10:32:20'),
(34, 'Cognitive Aspects of Visual Languages and Visual Interfaces (Human Factors in Information Technology, Vol 11', 'Nichols, Bradford', 'ALLEYSIDE PR (UPSTL)', 'This is a small description about this book', '2nd', '34645756YUTYU', 'Tokyo, Japan', 'Computer Studies', '', '1994', 0, '2019-06-22 10:32:20'),
(35, 'Collection Development and Finance : A Guide to Strategic Library-Materials Budgeting (Frontiers of Access to Library Materials, No 2', 'Franklin,Carl', 'ALLYN & BACON', 'This is a small description about this book', '2nd', '575676YFUULIY', 'Iowa, USA', 'Computer Studies', '', '1995', 1, '2019-06-22 10:32:20'),
(36, 'College Explorer, 1994', 'Ender, Thomas', 'ALPHA BOOKS', 'This is a small description about this book', '2nd', 'BNMUJU8785', 'Michigan, USA', 'Computer Studies', '', '1991', 0, '2019-06-22 10:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `issue`
--

CREATE TABLE `issue` (
  `IID` int(11) NOT NULL,
  `SID` int(12) NOT NULL,
  `BID` int(12) NOT NULL,
  `iDuration` int(12) NOT NULL,
  `iState` int(1) NOT NULL,
  `iTimeS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `issue`
--

INSERT INTO `issue` (`IID`, `SID`, `BID`, `iDuration`, `iState`, `iTimeS`) VALUES
(1, 1, 4, 7, 2, '2019-11-28 18:55:39'),
(2, 1, 5, 7, 0, '2019-11-28 18:56:04');

-- --------------------------------------------------------

--
-- Table structure for table `libcusts`
--

CREATE TABLE `libcusts` (
  `LID` int(11) NOT NULL,
  `LAYear` varchar(4) NOT NULL,
  `LName` varchar(50) NOT NULL,
  `LNumb` varchar(50) NOT NULL,
  `LStream` varchar(50) NOT NULL,
  `LBan` int(1) NOT NULL,
  `LForm` int(2) NOT NULL DEFAULT '1',
  `LType` varchar(10) NOT NULL,
  `LTimeS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `libcusts`
--

INSERT INTO `libcusts` (`LID`, `LAYear`, `LName`, `LNumb`, `LStream`, `LBan`, `LForm`, `LType`, `LTimeS`) VALUES
(1, '2019', 'Skye Ayanna Martin', '6754433', 'White', 0, 1, 'student', '2019-11-28 17:49:32'),
(2, '2010', 'Alejandro Peters', '20107721002', '', 0, 1, 'staff', '2019-11-28 17:50:10'),
(3, '2019', 'Paul Omwami Mutai', '6754431', 'White', 0, 1, 'student', '2019-11-28 18:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `LID` int(11) NOT NULL,
  `UID` int(12) NOT NULL,
  `lMessage` longtext NOT NULL,
  `lTimeS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`LID`, `UID`, `lMessage`, `lTimeS`) VALUES
(35, 2, 'Logged in to the system...', '2019-06-22 08:48:11'),
(36, 2, 'Logged out of the system...', '2019-06-22 08:48:14'),
(39, 2, 'Logged in to the system...', '2019-06-22 08:48:31'),
(40, 2, 'Logged out of the system...', '2019-06-22 08:48:50'),
(42, 1, 'Successfully deleted user logs...', '2019-06-22 09:44:13'),
(43, 1, 'Performed action \'block\' to a library user...', '2019-06-22 09:48:16'),
(44, 1, 'Logged out of the system...', '2019-06-22 09:48:18'),
(45, 1, 'Logged in to the system...', '2019-06-22 09:48:27'),
(46, 1, 'Performed action \'unblock\' to a library user...', '2019-06-22 09:48:34'),
(47, 1, 'Logged out of the system...', '2019-06-22 09:48:36'),
(48, 1, 'Logged in to the system...', '2019-06-22 09:48:39'),
(49, 1, 'Updated personal profile details...', '2019-06-22 09:57:21'),
(50, 1, 'Logged out of the system...', '2019-06-22 09:57:22'),
(51, 1, 'Logged in to the system...', '2019-06-22 09:57:28'),
(52, 1, 'Successfully marked own notification \'Read\'...', '2019-06-22 09:57:40'),
(53, 1, 'Successfully marked own notification \'Unread\'...', '2019-06-22 09:57:49'),
(54, 1, 'Updated personal profile details...', '2019-06-22 10:00:46'),
(55, 1, 'Logged out of the system...', '2019-06-22 10:00:46'),
(56, 1, 'Logged in to the system...', '2019-06-22 10:00:56'),
(57, 1, 'Successfully marked own notifications \'Read\'...', '2019-06-22 10:01:19'),
(58, 1, 'Successfully marked own notification \'Unread\'...', '2019-06-22 10:01:28'),
(59, 1, 'Successfully marked own notification \'Unread\'...', '2019-06-22 10:01:31'),
(60, 1, 'Successfully imported books records to the system...', '2019-06-22 10:32:20'),
(61, 1, 'Logged in to the system...', '2019-11-28 13:39:34'),
(62, 1, 'Successfully updated 1 book record in the system...', '2019-11-28 13:41:26'),
(63, 1, 'Logged out of the system...', '2019-11-28 13:52:14'),
(64, 1, 'Logged in to the system...', '2019-11-28 13:52:51'),
(65, 1, 'Logged out of the system...', '2019-11-28 17:27:23'),
(66, 1, 'Logged in to the system...', '2019-11-28 17:32:03'),
(67, 1, 'Successfully added 1 student record to the system...', '2019-11-28 17:49:32'),
(68, 1, 'Successfully added 1 staff record to the system...', '2019-11-28 17:50:10'),
(69, 1, 'Successfully added 1 student record to the system...', '2019-11-28 18:29:13'),
(70, 1, 'Performed action \'block\' to a library user...', '2019-11-28 18:30:39'),
(71, 1, 'Issued 0 books successfully, 1 books failed...', '2019-11-28 18:39:16'),
(72, 1, 'Issued 0 books successfully, 1 books failed...', '2019-11-28 18:39:26'),
(73, 1, 'Issued 0 books successfully, 1 books failed...', '2019-11-28 18:44:51'),
(74, 1, 'Issued 1 books successfully...', '2019-11-28 18:50:15'),
(75, 1, 'Checked in a person for \'reading\' successfully...', '2019-11-28 18:51:50'),
(76, 1, 'Successfully marked a book as \'lost\'...', '2019-11-28 18:55:39'),
(77, 1, 'Issued 1 books successfully...', '2019-11-28 18:56:04'),
(78, 1, 'Checked in a person for \'reading\' successfully...', '2019-11-28 18:56:48'),
(79, 1, 'Checked in a person for \'other\' successfully...', '2019-11-28 18:57:28'),
(80, 1, 'Checked in a person for \'other\' successfully...', '2019-11-28 18:57:47'),
(81, 1, 'Checked in a person for \'reading\' successfully...', '2019-11-28 19:02:32'),
(82, 1, 'Checked in a person for \'reading\' successfully...', '2019-11-28 19:18:24'),
(83, 1, 'Updated personal profile details...', '2019-11-28 19:56:33'),
(84, 1, 'Logged in to the system...', '2019-11-28 19:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `NID` int(11) NOT NULL,
  `NMsg` text NOT NULL,
  `NTo` int(11) NOT NULL,
  `NRead` int(11) NOT NULL,
  `NTimeS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`NID`, `NMsg`, `NTo`, `NRead`, `NTimeS`) VALUES
(1, 'Hello Martin Nzuki, you updated your Flexi Library Management System profile. If this was not you, please report this activity to the administrator or to, Martin Nzuki for further security analysis.', 1, 0, '2019-06-22 10:01:28'),
(2, 'Hello Martin Nzuki, you updated your Flexi Library Management System profile. If this was not you, please report this activity to the administrator or to, Martin Nzuki for further security analysis.', 1, 0, '2019-06-22 10:01:31'),
(3, 'Hello Martin Nzuki, you updated your Flexi Library Management System profile. If this was not you, please report this activity to the administrator or to, Martin Nzuki for further security analysis.', 1, 0, '2019-11-28 19:56:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UID` int(11) NOT NULL,
  `uName` varchar(100) NOT NULL,
  `uIDNumber` varchar(20) NOT NULL,
  `uAuth` varchar(10) NOT NULL,
  `uBlock` int(1) NOT NULL,
  `uSecQ` longtext NOT NULL,
  `uSecA` longtext NOT NULL,
  `uUsername` varchar(20) NOT NULL,
  `uPassword` text NOT NULL,
  `uSession` text NOT NULL,
  `uTimeS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UID`, `uName`, `uIDNumber`, `uAuth`, `uBlock`, `uSecQ`, `uSecA`, `uUsername`, `uPassword`, `uSession`, `uTimeS`) VALUES
(1, 'Martin Nzuki', '12345678', 'admin', 0, 'Which model was my first car?', 'audi', 'martinnzuki', 'd7aa6e14b6c5a97c156deb635013e491f5e0123d', 'm5pdo1vbp3e2j72ln9nftnbl61', '2019-11-28 19:56:33'),
(2, 'Peter Kimutai', '12345670', 'lib-user', 1, 'Which hospital was I born?', '1234', 'peterkimutai', 'f23a77b10a9cde21799627dee506b1b4eebb91d6', '', '2019-11-28 18:30:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`UID`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`BID`);

--
-- Indexes for table `issue`
--
ALTER TABLE `issue`
  ADD PRIMARY KEY (`IID`);

--
-- Indexes for table `libcusts`
--
ALTER TABLE `libcusts`
  ADD PRIMARY KEY (`LID`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`LID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`NID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `BID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `issue`
--
ALTER TABLE `issue`
  MODIFY `IID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `libcusts`
--
ALTER TABLE `libcusts`
  MODIFY `LID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `LID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `NID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
