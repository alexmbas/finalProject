-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 04:15 PM
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
-- Database: `serverside`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(2, 'Bottom Side Control'),
(3, 'S-Mount'),
(4, 'Guard Bottom'),
(5, 'Guard Top'),
(6, 'Half Guard Top'),
(7, 'Knee On Belly Top'),
(8, 'Top Mount'),
(9, 'Back Control'),
(10, 'Turtle Offensive'),
(11, 'Bottom Half Guard'),
(12, 'Open Guard'),
(13, 'Mount Bottom'),
(14, 'Back Defense'),
(15, 'Turtle Defensive'),
(16, 'Open Passing');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `technique_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `technique_id`, `username`, `comment`, `created_at`) VALUES
(2, 11, 'Coach Test', 'Coach Testing here again, you deleted my last comment using manager tools so now I\'m trying to comment here! HA!', '2025-07-26 19:10:52'),
(3, 13, 'Coach Test', 'Coach Test here, trying out the CAPTCHA update which gave me a headache!', '2025-07-26 19:23:02'),
(4, 15, 'Coach Test', 'Another round of debugging and testing', '2025-08-03 15:06:32'),
(5, 18, 'Coach Test', 'Making sure everything works now', '2025-08-03 15:27:38'),
(6, 18, 'Coach Test', 'One last debug comment test!', '2025-08-03 15:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `timestamp`) VALUES
(1, 'Gotta redo my blog as well!', 'Bleh! Here goes nothing.', '2025-07-24 23:57:49'),
(2, 'And ONE MORE for good measure!', 'Just making sure everything works!', '2025-07-24 23:57:49'),
(3, 'Yeehaw!', 'check check check this out!', '2025-07-24 23:59:14');

-- --------------------------------------------------------

--
-- Table structure for table `techniques`
--

CREATE TABLE `techniques` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `belt_level` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `techniques`
--

INSERT INTO `techniques` (`id`, `name`, `type`, `belt_level`, `description`, `created_at`, `image`, `category_id`) VALUES
(3, 'Side Control Shot-Gun Armbar', 'Submission', 'Blue', 'A more powerful armbar, with less control', '2025-07-26 18:30:32', NULL, 2),
(4, 'Rear Naked Choke', 'Submission', 'White', 'Choking the opponent out from behind', '2025-07-26 18:47:45', NULL, 9),
(5, 'Toreando Pass', 'Guard Pass', 'Blue', 'Use your opponents momentum to pass their legs, like a Matador!', '2025-07-26 18:48:20', NULL, 12),
(6, 'Kipping Technique', 'Escape', 'Purple', 'An awkward but elite level bottom mount escape, kip your legs and watch your opponents strongest offensive option become a reversal', '2025-07-26 18:49:00', NULL, 13),
(7, 'John Wayne Sweep', 'Sweep', 'Blue', 'Inspired by the legendary actor John Wayne, pivot your legs to the side and ruin your opponents base. Just like a cowboy!', '2025-07-26 18:50:21', NULL, 11),
(8, 'Leg Lock', 'Submission', 'Brown', 'Dangerous leg lock submission from half guard top. Respect the tap!', '2025-07-26 18:50:55', NULL, 6),
(9, 'Knee Cut', 'Guard Pass', 'Purple', 'Establish headquarters position and slice through your opponents guard with your knee', '2025-07-26 18:51:25', NULL, 5),
(10, 'Smash Pass', 'Guard Pass', 'Brown', 'Build a base above your opponents Torso and use pressure and weight to crush their hips, they\'ll either concede position or feel the pressure of your weight. Bonus Effective use if you\'re a heavyweight! :D ', '2025-07-26 18:52:15', NULL, 6),
(11, 'Cartwheel Pass', 'Guard Pass', 'Blue', 'For the more athletically inclined, give yourself an Instagram highlight reel by cartwheeling over your opponent!', '2025-07-26 18:52:46', NULL, 16),
(12, 'Head and Arm Choke', 'Submission', 'White', 'A front face choke, cousin to the Triangle choke except using your arms. ', '2025-07-26 18:53:20', NULL, 8),
(13, 'Ezikel Choke', 'Submission', 'White', 'The best named submission, and the greatest for trolling your opponents and making them quit Jiu Jitsu. You can hit this from anywhere, especially if your opponents don\'t know the fundamentals: Two hands in, two hands out!', '2025-07-26 18:54:07', NULL, 13),
(14, 'Omoplata', 'Submission', 'Purple', 'A wacky and flexible position where you are bending their arm over their head with your leg....don\'t ask me, I can\'t move like that anymore!', '2025-07-26 18:55:06', NULL, 4),
(15, 'Test', 'Test', 'Black', '<p>Test</p>', '2025-07-29 15:41:55', 'assets/images/uploads/6888edfc181d2.jpg', 9),
(18, 'Arm Triangle', 'Choke', 'blue', '<p>Testing the edit functionality now, after some debugging.</p>', '2025-08-03 15:26:33', '', 8);

-- --------------------------------------------------------

--
-- Table structure for table `tweets`
--

CREATE TABLE `tweets` (
  `id` int(11) NOT NULL,
  `status` varchar(140) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tweets`
--

INSERT INTO `tweets` (`id`, `status`) VALUES
(1, 'Testing this again'),
(2, 'Gotta love deleting mySQL and having to re-setup everything again'),
(3, 'Testing Testing 1231023102381038108310381083210831031023821038213082130138108310831083108301380123818310301831083103108321032018310380218321');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `is_admin`, `created_at`) VALUES
(1, 'alexmiddletontalljits50@gmail.com', '$2y$10$ZXmZ9kwzxZ2uaKk424uqM.OMjm28xMCIbwlTeh5T.HYSoawkDx4XC', 1, '2025-07-26 19:31:39'),
(2, 'Coach Test', '$2y$10$3OpRZsoV0oMfeEuqpIquC.dy8sMiIc9fde51bBmncrZibgR579Dsm', 1, '2025-07-26 19:56:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `technique_id` (`technique_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `techniques`
--
ALTER TABLE `techniques`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tweets`
--
ALTER TABLE `tweets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `techniques`
--
ALTER TABLE `techniques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tweets`
--
ALTER TABLE `tweets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`technique_id`) REFERENCES `techniques` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
