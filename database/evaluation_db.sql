-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2024 at 08:18 AM
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
-- Database: `evaluation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_list`
--

CREATE TABLE `academic_list` (
  `id` int(30) NOT NULL,
  `year` text NOT NULL,
  `semester` int(30) NOT NULL,
  `term` text NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '0=Pending,1=Start,2=Closed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_list`
--

INSERT INTO `academic_list` (`id`, `year`, `semester`, `term`, `is_default`, `status`) VALUES
(5, '2024-2025', 1, 'Midterm', 1, 1),
(6, '2024-2025', 2, 'Finals', 0, 1),
(7, '2019-2020', 2, '', 0, 0),
(8, '2025-2026', 1, 'Midterm', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `batch_upload`
--

CREATE TABLE `batch_upload` (
  `ID` int(30) NOT NULL,
  `school_id` varchar(100) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `class_id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batch_uploads`
--

CREATE TABLE `batch_uploads` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploaded_by` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_list`
--

CREATE TABLE `class_list` (
  `id` int(30) NOT NULL,
  `curriculum` text NOT NULL,
  `level` text NOT NULL,
  `section` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_list`
--

INSERT INTO `class_list` (`id`, `curriculum`, `level`, `section`) VALUES
(4, 'BSIT', '4', 'A1'),
(5, 'BSIT', '4', 'A2'),
(6, 'BSIT', '4', 'A3');

-- --------------------------------------------------------

--
-- Table structure for table `criteria_list`
--

CREATE TABLE `criteria_list` (
  `id` int(30) NOT NULL,
  `criteria` text NOT NULL,
  `order_by` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria_list`
--

INSERT INTO `criteria_list` (`id`, `criteria`, `order_by`) VALUES
(4, 'Professional Manner', 0),
(5, 'Communication with Student', 1),
(6, 'Student Engagement', 2),
(7, 'Learning Materials', 3),
(8, 'Time Management', 4),
(9, 'Experiential Learning Provided to Students', 5),
(10, 'Respect the Uniqueness of the Students', 6),
(11, 'Assessment and Feedback', 7);

-- --------------------------------------------------------

--
-- Table structure for table `department_list`
--

CREATE TABLE `department_list` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_list`
--

INSERT INTO `department_list` (`id`, `name`, `description`) VALUES
(2, 'College of Computer Studies', 'BSIT & BSCS'),
(3, 'College of Engineering (COE)', 'Bachelor of Science in Computer Engineering & Bachelor of Science in Industrial Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_answers`
--

CREATE TABLE `evaluation_answers` (
  `evaluation_id` int(30) NOT NULL,
  `question_id` int(30) NOT NULL,
  `rate` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_answers`
--

INSERT INTO `evaluation_answers` (`evaluation_id`, `question_id`, `rate`) VALUES
(1, 1, 5),
(1, 6, 4),
(1, 3, 5),
(2, 1, 5),
(2, 6, 5),
(2, 3, 4),
(3, 1, 5),
(3, 6, 5),
(3, 3, 4),
(4, 1, 1),
(4, 6, 5),
(4, 3, 5),
(5, 7, 1),
(5, 8, 1),
(5, 9, 1),
(5, 10, 1),
(5, 11, 1),
(5, 12, 1),
(5, 13, 2),
(5, 14, 2),
(5, 15, 2),
(5, 16, 2),
(5, 17, 2),
(5, 18, 2),
(5, 19, 3),
(5, 20, 3),
(5, 21, 3),
(5, 22, 3),
(5, 23, 3),
(5, 24, 3),
(5, 25, 4),
(5, 26, 4),
(5, 27, 4),
(5, 28, 4),
(5, 29, 4),
(5, 30, 5),
(5, 31, 5),
(5, 32, 5),
(5, 33, 5),
(5, 34, 5),
(5, 35, 5),
(5, 36, 4),
(5, 37, 3),
(5, 38, 2),
(5, 39, 1),
(5, 40, 2),
(5, 41, 3),
(5, 42, 4),
(5, 43, 5),
(5, 44, 4),
(5, 45, 3),
(5, 46, 2),
(6, 7, 5),
(6, 8, 5),
(6, 9, 5),
(6, 10, 5),
(6, 11, 5),
(6, 12, 5),
(6, 13, 5),
(6, 14, 5),
(6, 15, 5),
(6, 16, 5),
(6, 17, 5),
(6, 18, 5),
(6, 19, 5),
(6, 20, 5),
(6, 21, 5),
(6, 22, 5),
(6, 23, 5),
(6, 24, 5),
(6, 25, 5),
(6, 26, 5),
(6, 27, 5),
(6, 28, 5),
(6, 29, 5),
(6, 30, 5),
(6, 31, 5),
(6, 32, 5),
(6, 33, 5),
(6, 34, 5),
(6, 35, 5),
(6, 36, 5),
(6, 37, 5),
(6, 38, 5),
(6, 39, 5),
(6, 40, 5),
(6, 41, 5),
(6, 42, 5),
(6, 43, 5),
(6, 44, 5),
(6, 45, 5),
(6, 46, 5),
(7, 7, 5),
(7, 8, 5),
(7, 9, 5),
(7, 10, 5),
(7, 11, 5),
(7, 12, 5),
(7, 13, 5),
(7, 14, 5),
(7, 15, 5),
(7, 16, 5),
(7, 17, 5),
(7, 18, 5),
(7, 19, 5),
(7, 20, 5),
(7, 21, 5),
(7, 22, 5),
(7, 23, 5),
(7, 24, 5),
(7, 25, 5),
(7, 26, 5),
(7, 27, 5),
(7, 28, 5),
(7, 29, 5),
(7, 30, 5),
(7, 31, 5),
(7, 32, 5),
(7, 33, 5),
(7, 34, 5),
(7, 35, 5),
(7, 36, 5),
(7, 37, 5),
(7, 38, 5),
(7, 39, 5),
(7, 40, 5),
(7, 41, 5),
(7, 42, 5),
(7, 43, 5),
(7, 44, 5),
(7, 45, 5),
(7, 46, 5),
(8, 7, 5),
(8, 8, 5),
(8, 9, 5),
(8, 10, 5),
(8, 11, 5),
(8, 12, 5),
(8, 13, 5),
(8, 14, 5),
(8, 15, 5),
(8, 16, 5),
(8, 17, 5),
(8, 18, 5),
(8, 19, 5),
(8, 20, 5),
(8, 21, 5),
(8, 22, 5),
(8, 23, 5),
(8, 24, 5),
(8, 25, 5),
(8, 26, 5),
(8, 27, 5),
(8, 28, 5),
(8, 29, 5),
(8, 30, 5),
(8, 31, 5),
(8, 32, 5),
(8, 33, 5),
(8, 34, 5),
(8, 35, 5),
(8, 36, 5),
(8, 37, 5),
(8, 38, 5),
(8, 39, 5),
(8, 40, 5),
(8, 41, 5),
(8, 42, 5),
(8, 43, 5),
(8, 44, 5),
(8, 45, 5),
(8, 46, 5),
(9, 7, 5),
(9, 8, 5),
(9, 9, 5),
(9, 10, 5),
(9, 11, 5),
(9, 12, 5),
(9, 13, 5),
(9, 14, 5),
(9, 15, 5),
(9, 16, 5),
(9, 17, 5),
(9, 18, 5),
(9, 19, 5),
(9, 20, 5),
(9, 21, 5),
(9, 22, 5),
(9, 23, 5),
(9, 24, 5),
(9, 25, 5),
(9, 26, 5),
(9, 27, 5),
(9, 28, 5),
(9, 29, 5),
(9, 30, 5),
(9, 31, 5),
(9, 32, 5),
(9, 33, 5),
(9, 34, 5),
(9, 35, 5),
(9, 36, 5),
(9, 37, 5),
(9, 38, 5),
(9, 39, 5),
(9, 40, 5),
(9, 41, 5),
(9, 42, 5),
(9, 43, 5),
(9, 44, 5),
(9, 45, 5),
(9, 46, 5),
(10, 7, 4),
(10, 8, 4),
(10, 9, 4),
(10, 10, 4),
(10, 11, 4),
(10, 12, 4),
(10, 13, 4),
(10, 14, 4),
(10, 15, 4),
(10, 16, 4),
(10, 17, 4),
(10, 18, 4),
(10, 19, 4),
(10, 20, 4),
(10, 21, 4),
(10, 22, 4),
(10, 23, 4),
(10, 24, 4),
(10, 25, 3),
(10, 26, 3),
(10, 27, 3),
(10, 28, 3),
(10, 29, 3),
(10, 30, 3),
(10, 31, 3),
(10, 32, 3),
(10, 33, 3),
(10, 34, 3),
(10, 35, 3),
(10, 36, 3),
(10, 37, 3),
(10, 38, 3),
(10, 39, 3),
(10, 40, 3),
(10, 41, 4),
(10, 42, 4),
(10, 43, 4),
(10, 44, 4),
(10, 45, 4),
(10, 46, 4),
(11, 7, 5),
(11, 8, 5),
(11, 9, 5),
(11, 10, 5),
(11, 11, 5),
(11, 12, 5),
(11, 13, 5),
(11, 14, 5),
(11, 15, 5),
(11, 16, 5),
(11, 17, 5),
(11, 18, 5),
(11, 19, 5),
(11, 20, 5),
(11, 21, 5),
(11, 22, 5),
(11, 23, 5),
(11, 24, 5),
(11, 25, 5),
(11, 26, 5),
(11, 27, 5),
(11, 28, 5),
(11, 29, 5),
(11, 30, 5),
(11, 31, 5),
(11, 32, 5),
(11, 33, 5),
(11, 34, 5),
(11, 35, 5),
(11, 36, 5),
(11, 37, 5),
(11, 38, 5),
(11, 39, 5),
(11, 40, 5),
(11, 41, 5),
(11, 42, 5),
(11, 43, 5),
(11, 44, 5),
(11, 45, 5),
(11, 46, 5),
(12, 7, 1),
(12, 8, 1),
(12, 9, 1),
(12, 10, 1),
(12, 11, 1),
(12, 12, 1),
(12, 13, 1),
(12, 14, 1),
(12, 15, 1),
(12, 16, 1),
(12, 17, 1),
(12, 18, 1),
(12, 19, 1),
(12, 20, 1),
(12, 21, 1),
(12, 22, 1),
(12, 23, 1),
(12, 24, 1),
(12, 25, 1),
(12, 26, 1),
(12, 27, 1),
(12, 28, 1),
(12, 29, 1),
(12, 30, 1),
(12, 31, 1),
(12, 32, 1),
(12, 33, 1),
(12, 34, 1),
(12, 35, 1),
(12, 36, 1),
(12, 37, 1),
(12, 38, 1),
(12, 39, 1),
(12, 40, 1),
(12, 41, 1),
(12, 42, 1),
(12, 43, 1),
(12, 44, 1),
(12, 45, 1),
(12, 46, 1),
(13, 7, 1),
(13, 8, 1),
(13, 9, 1),
(13, 10, 1),
(13, 11, 1),
(13, 12, 1),
(13, 13, 1),
(13, 14, 1),
(13, 15, 1),
(13, 16, 1),
(13, 17, 1),
(13, 18, 5),
(13, 19, 1),
(13, 20, 1),
(13, 21, 1),
(13, 22, 1),
(13, 23, 1),
(13, 24, 1),
(13, 25, 1),
(13, 26, 1),
(13, 27, 1),
(13, 28, 1),
(13, 29, 1),
(13, 30, 1),
(13, 31, 1),
(13, 32, 1),
(13, 33, 1),
(13, 34, 1),
(13, 35, 1),
(13, 36, 1),
(13, 37, 1),
(13, 38, 1),
(13, 39, 1),
(13, 40, 1),
(13, 41, 1),
(13, 42, 1),
(13, 43, 1),
(13, 44, 1),
(13, 45, 1),
(13, 46, 1),
(14, 7, 5),
(14, 8, 5),
(14, 9, 5),
(14, 10, 5),
(14, 11, 5),
(14, 12, 5),
(14, 13, 5),
(14, 14, 5),
(14, 15, 5),
(14, 16, 5),
(14, 17, 5),
(14, 18, 5),
(14, 19, 5),
(14, 20, 5),
(14, 21, 5),
(14, 22, 5),
(14, 23, 5),
(14, 24, 5),
(14, 25, 5),
(14, 26, 5),
(14, 27, 5),
(14, 28, 5),
(14, 29, 5),
(14, 30, 5),
(14, 31, 5),
(14, 32, 5),
(14, 33, 5),
(14, 34, 5),
(14, 35, 5),
(14, 36, 5),
(14, 37, 5),
(14, 38, 5),
(14, 39, 5),
(14, 40, 5),
(14, 41, 5),
(14, 42, 5),
(14, 43, 5),
(14, 44, 5),
(14, 45, 5),
(14, 46, 5),
(15, 7, 4),
(15, 8, 4),
(15, 9, 4),
(15, 10, 4),
(15, 11, 4),
(15, 12, 4),
(15, 13, 4),
(15, 14, 4),
(15, 15, 4),
(15, 16, 4),
(15, 17, 4),
(15, 18, 4),
(15, 19, 4),
(15, 20, 4),
(15, 21, 4),
(15, 22, 4),
(15, 23, 4),
(15, 24, 4),
(15, 25, 4),
(15, 26, 4),
(15, 27, 4),
(15, 28, 4),
(15, 29, 4),
(15, 30, 4),
(15, 31, 4),
(15, 32, 4),
(15, 33, 4),
(15, 34, 4),
(15, 35, 4),
(15, 36, 4),
(15, 37, 4),
(15, 38, 4),
(15, 39, 4),
(15, 40, 4),
(15, 41, 4),
(15, 42, 4),
(15, 43, 4),
(15, 44, 4),
(15, 45, 4),
(15, 46, 4),
(16, 7, 4),
(16, 8, 4),
(16, 9, 4),
(16, 10, 4),
(16, 11, 4),
(16, 12, 4),
(16, 13, 4),
(16, 14, 4),
(16, 15, 4),
(16, 16, 4),
(16, 17, 4),
(16, 18, 4),
(16, 19, 4),
(16, 20, 4),
(16, 21, 4),
(16, 22, 4),
(16, 23, 4),
(16, 24, 4),
(16, 25, 4),
(16, 26, 4),
(16, 27, 4),
(16, 28, 4),
(16, 29, 4),
(16, 30, 4),
(16, 31, 4),
(16, 32, 4),
(16, 33, 4),
(16, 34, 4),
(16, 35, 4),
(16, 36, 4),
(16, 37, 4),
(16, 38, 4),
(16, 39, 4),
(16, 40, 4),
(16, 41, 4),
(16, 42, 4),
(16, 43, 4),
(16, 44, 4),
(16, 45, 4),
(16, 46, 4),
(17, 7, 3),
(17, 8, 4),
(17, 9, 4),
(17, 10, 4),
(17, 11, 4),
(17, 12, 3),
(17, 13, 4),
(17, 14, 3),
(17, 15, 4),
(17, 16, 3),
(17, 17, 4),
(17, 18, 3),
(17, 19, 3),
(17, 20, 4),
(17, 21, 3),
(17, 22, 4),
(17, 23, 3),
(17, 24, 4),
(17, 25, 3),
(17, 26, 4),
(17, 27, 3),
(17, 28, 4),
(17, 29, 3),
(17, 30, 4),
(17, 31, 4),
(17, 32, 5),
(17, 33, 5),
(17, 34, 5),
(17, 35, 4),
(17, 36, 5),
(17, 37, 4),
(17, 38, 5),
(17, 39, 4),
(17, 40, 4),
(17, 41, 4),
(17, 42, 3),
(17, 43, 4),
(17, 44, 3),
(17, 45, 4),
(17, 46, 2),
(18, 7, 5),
(18, 8, 4),
(18, 9, 4),
(18, 10, 3),
(18, 11, 4),
(18, 12, 4),
(18, 13, 4),
(18, 14, 4),
(18, 15, 4),
(18, 16, 4),
(18, 17, 4),
(18, 18, 4),
(18, 19, 4),
(18, 20, 3),
(18, 21, 3),
(18, 22, 3),
(18, 23, 3),
(18, 24, 3),
(18, 25, 4),
(18, 26, 4),
(18, 27, 4),
(18, 28, 4),
(18, 29, 4),
(18, 30, 5),
(18, 31, 5),
(18, 32, 5),
(18, 33, 5),
(18, 34, 5),
(18, 35, 4),
(18, 36, 4),
(18, 37, 4),
(18, 38, 4),
(18, 39, 4),
(18, 40, 4),
(18, 41, 4),
(18, 42, 4),
(18, 43, 4),
(18, 44, 3),
(18, 45, 2),
(18, 46, 2),
(19, 7, 5),
(19, 8, 4),
(19, 9, 4),
(19, 10, 3),
(19, 11, 4),
(19, 12, 4),
(19, 13, 4),
(19, 14, 4),
(19, 15, 4),
(19, 16, 4),
(19, 17, 4),
(19, 18, 4),
(19, 19, 4),
(19, 20, 3),
(19, 21, 3),
(19, 22, 3),
(19, 23, 3),
(19, 24, 3),
(19, 25, 4),
(19, 26, 4),
(19, 27, 4),
(19, 28, 4),
(19, 29, 4),
(19, 30, 5),
(19, 31, 5),
(19, 32, 5),
(19, 33, 5),
(19, 34, 5),
(19, 35, 4),
(19, 36, 4),
(19, 37, 4),
(19, 38, 4),
(19, 39, 4),
(19, 40, 4),
(19, 41, 4),
(19, 42, 4),
(19, 43, 4),
(19, 44, 3),
(19, 45, 2),
(19, 46, 2),
(20, 7, 5),
(20, 8, 4),
(20, 9, 4),
(20, 10, 3),
(20, 11, 4),
(20, 12, 4),
(20, 13, 4),
(20, 14, 4),
(20, 15, 4),
(20, 16, 4),
(20, 17, 4),
(20, 18, 4),
(20, 19, 4),
(20, 20, 3),
(20, 21, 3),
(20, 22, 3),
(20, 23, 3),
(20, 24, 3),
(20, 25, 4),
(20, 26, 4),
(20, 27, 4),
(20, 28, 4),
(20, 29, 4),
(20, 30, 5),
(20, 31, 5),
(20, 32, 5),
(20, 33, 5),
(20, 34, 5),
(20, 35, 4),
(20, 36, 4),
(20, 37, 4),
(20, 38, 4),
(20, 39, 4),
(20, 40, 4),
(20, 41, 4),
(20, 42, 4),
(20, 43, 4),
(20, 44, 3),
(20, 45, 2),
(20, 46, 2),
(21, 7, 2),
(21, 8, 2),
(21, 9, 2),
(21, 10, 2),
(21, 11, 2),
(21, 12, 2),
(21, 13, 2),
(21, 14, 2),
(21, 15, 2),
(21, 16, 2),
(21, 17, 2),
(21, 18, 2),
(21, 19, 2),
(21, 20, 2),
(21, 21, 2),
(21, 22, 2),
(21, 23, 2),
(21, 24, 2),
(21, 25, 2),
(21, 26, 2),
(21, 27, 2),
(21, 28, 2),
(21, 29, 2),
(21, 30, 2),
(21, 31, 2),
(21, 32, 2),
(21, 33, 2),
(21, 34, 2),
(21, 35, 2),
(21, 36, 2),
(21, 37, 2),
(21, 38, 2),
(21, 39, 2),
(21, 40, 2),
(21, 41, 2),
(21, 42, 2),
(21, 43, 2),
(21, 44, 2),
(21, 45, 2),
(21, 46, 2),
(22, 7, 2),
(22, 8, 2),
(22, 9, 2),
(22, 10, 2),
(22, 11, 2),
(22, 12, 2),
(22, 13, 2),
(22, 14, 2),
(22, 15, 2),
(22, 16, 2),
(22, 17, 2),
(22, 18, 2),
(22, 19, 2),
(22, 20, 2),
(22, 21, 2),
(22, 22, 2),
(22, 23, 2),
(22, 24, 2),
(22, 25, 2),
(22, 26, 2),
(22, 27, 2),
(22, 28, 2),
(22, 29, 2),
(22, 30, 2),
(22, 31, 2),
(22, 32, 2),
(22, 33, 2),
(22, 34, 2),
(22, 35, 2),
(22, 36, 2),
(22, 37, 2),
(22, 38, 2),
(22, 39, 2),
(22, 40, 2),
(22, 41, 2),
(22, 42, 2),
(22, 43, 2),
(22, 44, 2),
(22, 45, 2),
(22, 46, 2),
(23, 7, 2),
(23, 8, 2),
(23, 9, 2),
(23, 10, 2),
(23, 11, 2),
(23, 12, 2),
(23, 13, 2),
(23, 14, 2),
(23, 15, 2),
(23, 16, 2),
(23, 17, 2),
(23, 18, 2),
(23, 19, 2),
(23, 20, 2),
(23, 21, 2),
(23, 22, 2),
(23, 23, 2),
(23, 24, 2),
(23, 25, 2),
(23, 26, 2),
(23, 27, 2),
(23, 28, 2),
(23, 29, 2),
(23, 30, 2),
(23, 31, 2),
(23, 32, 2),
(23, 33, 2),
(23, 34, 2),
(23, 35, 2),
(23, 36, 2),
(23, 37, 2),
(23, 38, 2),
(23, 39, 2),
(23, 40, 2),
(23, 41, 2),
(23, 42, 2),
(23, 43, 2),
(23, 44, 2),
(23, 45, 2),
(23, 46, 2),
(24, 7, 2),
(24, 8, 2),
(24, 9, 2),
(24, 10, 2),
(24, 11, 2),
(24, 12, 2),
(24, 13, 2),
(24, 14, 2),
(24, 15, 2),
(24, 16, 2),
(24, 17, 2),
(24, 18, 2),
(24, 19, 2),
(24, 20, 2),
(24, 21, 2),
(24, 22, 2),
(24, 23, 2),
(24, 24, 2),
(24, 25, 2),
(24, 26, 2),
(24, 27, 2),
(24, 28, 2),
(24, 29, 2),
(24, 30, 2),
(24, 31, 2),
(24, 32, 2),
(24, 33, 2),
(24, 34, 2),
(24, 35, 2),
(24, 36, 2),
(24, 37, 2),
(24, 38, 2),
(24, 39, 2),
(24, 40, 2),
(24, 41, 2),
(24, 42, 2),
(24, 43, 2),
(24, 44, 2),
(24, 45, 2),
(24, 46, 2),
(25, 7, 1),
(25, 8, 1),
(25, 9, 1),
(25, 10, 1),
(25, 11, 1),
(25, 12, 1),
(25, 13, 1),
(25, 14, 1),
(25, 15, 1),
(25, 16, 1),
(25, 17, 2),
(25, 18, 2),
(25, 19, 2),
(25, 20, 2),
(25, 21, 2),
(25, 22, 2),
(25, 23, 2),
(25, 24, 2),
(25, 25, 2),
(25, 26, 2),
(25, 27, 2),
(25, 28, 2),
(25, 29, 2),
(25, 30, 2),
(25, 31, 2),
(25, 32, 2),
(25, 33, 2),
(25, 34, 2),
(25, 35, 2),
(25, 36, 2),
(25, 37, 2),
(25, 38, 2),
(25, 39, 2),
(25, 40, 2),
(25, 41, 2),
(25, 42, 2),
(25, 43, 2),
(25, 44, 2),
(25, 45, 2),
(25, 46, 2),
(26, 7, 3),
(26, 8, 3),
(26, 9, 3),
(26, 10, 3),
(26, 11, 3),
(26, 12, 3),
(26, 13, 3),
(26, 14, 3),
(26, 15, 3),
(26, 16, 3),
(26, 17, 3),
(26, 18, 3),
(26, 19, 3),
(26, 20, 3),
(26, 21, 3),
(26, 22, 3),
(26, 23, 3),
(26, 24, 3),
(26, 25, 3),
(26, 26, 3),
(26, 27, 3),
(26, 28, 3),
(26, 29, 3),
(26, 30, 3),
(26, 31, 3),
(26, 32, 3),
(26, 33, 3),
(26, 34, 3),
(26, 35, 3),
(26, 36, 3),
(26, 37, 3),
(26, 38, 3),
(26, 39, 3),
(26, 40, 3),
(26, 41, 3),
(26, 42, 3),
(26, 43, 3),
(26, 44, 3),
(26, 45, 3),
(26, 46, 3),
(27, 7, 3),
(27, 8, 3),
(27, 9, 3),
(27, 10, 3),
(27, 11, 3),
(27, 12, 3),
(27, 13, 3),
(27, 14, 3),
(27, 15, 3),
(27, 16, 3),
(27, 17, 3),
(27, 18, 3),
(27, 19, 3),
(27, 20, 3),
(27, 21, 3),
(27, 22, 3),
(27, 23, 3),
(27, 24, 3),
(27, 25, 3),
(27, 26, 3),
(27, 27, 3),
(27, 28, 3),
(27, 29, 3),
(27, 30, 3),
(27, 31, 3),
(27, 32, 3),
(27, 33, 3),
(27, 34, 3),
(27, 35, 3),
(27, 36, 3),
(27, 37, 3),
(27, 38, 3),
(27, 39, 3),
(27, 40, 3),
(27, 41, 3),
(27, 42, 3),
(27, 43, 3),
(27, 44, 3),
(27, 45, 3),
(27, 46, 3),
(28, 7, 3),
(28, 8, 3),
(28, 9, 3),
(28, 10, 3),
(28, 11, 3),
(28, 12, 3),
(28, 13, 3),
(28, 14, 3),
(28, 15, 3),
(28, 16, 3),
(28, 17, 3),
(28, 18, 3),
(28, 19, 3),
(28, 20, 3),
(28, 21, 3),
(28, 22, 3),
(28, 23, 3),
(28, 24, 3),
(28, 25, 3),
(28, 26, 3),
(28, 27, 3),
(28, 28, 3),
(28, 29, 3),
(28, 30, 3),
(28, 31, 3),
(28, 32, 3),
(28, 33, 3),
(28, 34, 3),
(28, 35, 3),
(28, 36, 3),
(28, 37, 2),
(28, 38, 3),
(28, 39, 2),
(28, 40, 3),
(28, 41, 2),
(28, 42, 3),
(28, 43, 2),
(28, 44, 3),
(28, 45, 2),
(28, 46, 3);

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_comments`
--

CREATE TABLE `evaluation_comments` (
  `id` int(11) NOT NULL,
  `evaluation_id` int(30) NOT NULL,
  `comment` text NOT NULL,
  `sentiment` varchar(20) NOT NULL,
  `polarity` float NOT NULL,
  `subjectivity` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_comments`
--

INSERT INTO `evaluation_comments` (`id`, `evaluation_id`, `comment`, `sentiment`, `polarity`, `subjectivity`) VALUES
(1, 14, 'Mr. Tuazon is bad at teaching', 'Strong (Negative)', 0.15, 0.666667),
(2, 15, 'Mr Tuazon is kind good in teaching but he doesn\'t talk that much. He often just use powerpoints to teach but he is still kinda good', 'Strong (Positive)', 0.775, 0.575),
(3, 16, 'He is a good teacher', 'Strong (Positive)', 0.85, 0.6),
(5, 18, 'Mr. Tuazon is good at teaching', 'Unknown', 0, 0),
(6, 19, 'Mr. Tuazon is good at teaching', 'Unknown', 0, 0),
(7, 20, 'Mr. Tuazon is good at teaching', 'Unknown', 0, 0),
(15, 28, 'He is very bad at teaching', 'Unknown', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_list`
--

CREATE TABLE `evaluation_list` (
  `evaluation_id` int(30) NOT NULL,
  `academic_id` int(30) NOT NULL,
  `class_id` int(30) NOT NULL,
  `student_id` int(30) NOT NULL,
  `subject_id` int(30) NOT NULL,
  `faculty_id` int(30) NOT NULL,
  `restriction_id` int(30) NOT NULL,
  `date_taken` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_list`
--

INSERT INTO `evaluation_list` (`evaluation_id`, `academic_id`, `class_id`, `student_id`, `subject_id`, `faculty_id`, `restriction_id`, `date_taken`) VALUES
(1, 3, 1, 1, 1, 1, 8, '2020-12-15 16:26:51'),
(2, 3, 2, 2, 2, 1, 9, '2020-12-15 16:33:37'),
(3, 3, 1, 3, 1, 1, 8, '2020-12-15 20:18:49'),
(4, 3, 1, 4, 1, 1, 8, '2024-10-09 15:27:37'),
(6, 5, 5, 5, 9, 3, 12, '2024-10-14 13:51:27'),
(13, 5, 5, 7, 5, 4, 15, '2024-10-22 08:43:59'),
(14, 5, 5, 5, 5, 2, 11, '2024-10-25 20:08:17'),
(15, 5, 5, 7, 5, 2, 11, '2024-10-25 20:20:13'),
(16, 5, 5, 5, 8, 6, 16, '2024-11-10 01:13:57'),
(18, 5, 5, 6, 5, 2, 11, '2024-11-12 18:02:43'),
(19, 5, 5, 6, 5, 2, 11, '2024-11-12 18:02:47'),
(20, 5, 5, 6, 5, 2, 11, '2024-11-12 18:02:54'),
(28, 5, 5, 6, 8, 6, 16, '2024-11-12 19:00:33');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_list`
--

CREATE TABLE `faculty_list` (
  `id` int(30) NOT NULL,
  `school_id` varchar(100) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `avatar` text NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_list`
--

INSERT INTO `faculty_list` (`id`, `school_id`, `firstname`, `lastname`, `email`, `password`, `avatar`, `date_created`, `department_id`) VALUES
(2, 'F-1234-56', 'Francis', 'Tuazon', 'francistuazon@gmail.com', '8d709b4b6461aef614529a83d883c64b', 'no-image-available.png', '2024-10-09 18:04:09', NULL),
(3, 'F-5432-10', 'Xavier', 'Malig-on', 'xaviermaligon123@gmail.com', 'f2d9af001d5aa6a89899a7f793ae51d1', 'no-image-available.png', '2024-10-09 21:43:37', NULL),
(4, 'F-1111-23', 'Andro', 'Banag', 'androbanag@gmail.com', '03a3a9f4390e1fb41063a87a5584ecb1', 'no-image-available.png', '2024-10-14 14:26:17', NULL),
(6, '2875-22', 'Moises', 'Mascarinas', 'moises@gmail.com', 'd08f86bd35f1e73459d64c80adb6e212', 'no-image-available.png', '2024-11-09 02:16:29', 2),
(8, '1234', 'Jae', 'Bustamante', 'jaebustamante@gmail.com', '92f4615ba18c3ad94b2248da4945a112', 'no-image-available.png', '2024-11-09 18:48:34', 2);

-- --------------------------------------------------------

--
-- Table structure for table `question_list`
--

CREATE TABLE `question_list` (
  `id` int(30) NOT NULL,
  `academic_id` int(30) NOT NULL,
  `question` text NOT NULL,
  `order_by` int(30) NOT NULL,
  `criteria_id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_list`
--

INSERT INTO `question_list` (`id`, `academic_id`, `question`, `order_by`, `criteria_id`) VALUES
(1, 3, 'Sample Question', 0, 1),
(3, 3, 'Test', 2, 2),
(5, 0, 'Question 101', 0, 1),
(6, 3, 'Sample 101', 4, 1),
(7, 5, 'The professor demonstrates punctuality by starting and ending classes on time.', 0, 4),
(8, 5, 'The professor is well-prepared for each class session with a clear lesson plan.', 1, 4),
(9, 5, 'The professor communicates respectfully and professionally with all students.', 2, 4),
(10, 5, 'The professor adheres to ethical standards in teaching and interactions.', 3, 4),
(11, 5, 'The professor maintains a professional appearance appropriate for the learning environment.', 4, 4),
(12, 5, 'The professor effectively manages the classroom to create a positive learning atmosphere.', 5, 4),
(13, 5, 'The professor explains concepts clearly and in an understandable manner.', 6, 5),
(14, 5, 'The professor encourages questions and fosters open classroom discussions.', 7, 5),
(15, 5, 'The professor responds promptly and effectively to student inquiries and emails.', 8, 5),
(16, 5, 'The professor is accessible outside of class hours for additional support.', 9, 5),
(17, 5, 'The professor uses various communication channels effectively (e.g., email, online platforms).', 10, 5),
(18, 5, 'The professor provides timely updates and important course information.', 11, 5),
(19, 5, 'The professor effectively balances active-learning exercises with traditional lectures appropriate to the lesson.', 12, 6),
(20, 5, 'The professor provides clear instructions and modeling before assigning active learning tasks such as group work or discussions.', 13, 6),
(21, 5, 'The professor facilitates opportunities for student interaction through breakout rooms, chats, or collaborative tools.', 14, 6),
(22, 5, 'The professor maintains a consistent presence and remains engaged during class sessions.', 15, 6),
(23, 5, 'The professor utilizes interactive technologies to enhance student participation and engagement.', 16, 6),
(24, 5, 'The professor encourages and supports active student involvement in all class activities.', 17, 6),
(25, 5, 'The learning materials provided are relevant and align with the course objectives.', 18, 7),
(26, 5, 'The professor offers clear and comprehensive lecture notes and/or slides.', 19, 7),
(27, 5, 'The learning materials are accessible and easy to understand.', 20, 7),
(28, 5, 'The professor incorporates a variety of resources (e.g., articles, videos, case studies) to enhance learning.', 21, 7),
(29, 5, 'The materials used are current and reflect the latest developments in the field.', 22, 7),
(30, 5, 'The professor effectively utilizes class time to cover the necessary material.', 23, 8),
(31, 5, 'The pacing of lessons is appropriate, allowing for comprehension and engagement.', 24, 8),
(32, 5, 'The professor provides timely feedback on assignments and assessments.', 25, 8),
(33, 5, 'Assignment deadlines are reasonable and clearly communicated in advance.', 26, 8),
(34, 5, 'The professor balances the course workload to prevent overloading students.', 27, 8),
(35, 5, 'The professor incorporates practical activities that enhance understanding of the subject matter.', 28, 9),
(36, 5, 'Opportunities for experiential learning, such as projects or simulations, are provided.', 29, 9),
(37, 5, 'The professor uses real-world examples to illustrate theoretical concepts.', 30, 9),
(38, 5, 'The course includes hands-on activities that engage students in active learning.', 31, 9),
(39, 5, 'The professor encourages participation in field experiences or internships when appropriate.', 32, 9),
(40, 5, 'Experiential learning activities are well-structured and effectively integrated into the course.', 33, 9),
(41, 5, 'The professor demonstrates respect for the diverse backgrounds and perspectives of students.', 34, 10),
(42, 5, 'The professor creates an inclusive classroom environment where all students feel valued.', 35, 10),
(43, 5, 'The professor accommodates individual learning needs and provides support when necessary.', 36, 10),
(44, 5, 'The professor encourages students to share their unique perspectives and experiences.', 37, 10),
(45, 5, 'The professor treats all students fairly and without bias.', 38, 10),
(46, 5, 'The professor is sensitive to cultural, gender, and other differences among students.', 39, 10);

-- --------------------------------------------------------

--
-- Table structure for table `restriction_list`
--

CREATE TABLE `restriction_list` (
  `id` int(30) NOT NULL,
  `academic_id` int(30) NOT NULL,
  `faculty_id` int(30) NOT NULL,
  `class_id` int(30) NOT NULL,
  `subject_id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restriction_list`
--

INSERT INTO `restriction_list` (`id`, `academic_id`, `faculty_id`, `class_id`, `subject_id`) VALUES
(8, 3, 1, 1, 1),
(9, 3, 1, 2, 2),
(10, 3, 1, 3, 3),
(11, 5, 2, 5, 5),
(12, 5, 3, 5, 5),
(15, 5, 4, 5, 5),
(16, 5, 6, 5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `student_list`
--

CREATE TABLE `student_list` (
  `id` int(30) NOT NULL,
  `school_id` varchar(100) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `class_id` int(30) NOT NULL,
  `avatar` text NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_list`
--

INSERT INTO `student_list` (`id`, `school_id`, `firstname`, `lastname`, `email`, `password`, `class_id`, `avatar`, `date_created`) VALUES
(5, '2875-23', 'Ivhan', 'Salazar', 'ivhanchris761@gmail.com', 'd97c63b4eae1689cc0ce09882ba50908', 5, '1728469980_EL1_3334.JPG', '2024-10-09 18:33:28'),
(6, '2880-23', 'Oliver', 'Dela Cruz', 'oliver.delacruz2222@gmail.com', '553fcb594976460e66e32da18a2b6f88', 5, '1728470340_Screenshot 2024-10-09 183924.png', '2024-10-09 18:39:37'),
(7, '2876-23', 'Xyrel', 'Genio', 'xyrelgenio08@gmail.com', 'b12d567a3b5a014584c5b6a5a8b41735', 5, 'no-image-available.png', '2024-10-22 08:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `subject_list`
--

CREATE TABLE `subject_list` (
  `id` int(30) NOT NULL,
  `code` varchar(50) NOT NULL,
  `subject` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_list`
--

INSERT INTO `subject_list` (`id`, `code`, `subject`, `description`) VALUES
(4, 'ITPRAC', 'Practicum (486 Hours)', 'Practicum'),
(5, 'CAP413', 'Capstone', 'Capstone Project and Research'),
(6, 'BP113', 'Basic Photography', 'Basic Photography'),
(7, 'GEC107', 'Art Appreciation', 'Art App'),
(8, 'GAD213', '2D and 3D Digital Animation', '2D and 3D'),
(9, 'IAS413', 'Information Assurance and Security 2', 'IAS 2'),
(10, 'LIT102', 'Philippine Popular Culture', 'Phil Pop');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `cover_img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `address`, `cover_img`) VALUES
(1, 'Lyceum of Alabang Faculty Evaluation System', 'college.registrar@lyceumalabang.edu.ph', '(02) 8856-9323 | 885', 'Km.30 National Road, Muntinlupa, 1773 Metro Manila', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `avatar` text NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `type` int(1) NOT NULL DEFAULT 1,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `avatar`, `date_created`, `type`, `department_id`) VALUES
(1, 'Administrator', '', 'admin@admin.com', '0192023a7bbd73250516f069df18b500', '1607135820_avatar.jpg', '2020-11-26 10:57:04', 1, NULL),
(3, 'Guidance', 'Salazar', 'guidance@gmail.com', '692f6f113b5f5daf81c5bcbddc62b67e', 'no-image-available.png', '2024-10-27 02:43:58', 2, NULL),
(5, 'Regie', 'Ellana', 'regie@gmail.com', 'ec11cd0681f59d61a09518527ca7e458', 'no-image-available.png', '2024-11-09 02:15:46', 2, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_list`
--
ALTER TABLE `academic_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `batch_upload`
--
ALTER TABLE `batch_upload`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `batch_uploads`
--
ALTER TABLE `batch_uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_list`
--
ALTER TABLE `class_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `criteria_list`
--
ALTER TABLE `criteria_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_list`
--
ALTER TABLE `department_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation_comments`
--
ALTER TABLE `evaluation_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluation_id` (`evaluation_id`);

--
-- Indexes for table `evaluation_list`
--
ALTER TABLE `evaluation_list`
  ADD PRIMARY KEY (`evaluation_id`);

--
-- Indexes for table `faculty_list`
--
ALTER TABLE `faculty_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question_list`
--
ALTER TABLE `question_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restriction_list`
--
ALTER TABLE `restriction_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_list`
--
ALTER TABLE `student_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject_list`
--
ALTER TABLE `subject_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
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
-- AUTO_INCREMENT for table `academic_list`
--
ALTER TABLE `academic_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `batch_upload`
--
ALTER TABLE `batch_upload`
  MODIFY `ID` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `batch_uploads`
--
ALTER TABLE `batch_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_list`
--
ALTER TABLE `class_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `criteria_list`
--
ALTER TABLE `criteria_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `department_list`
--
ALTER TABLE `department_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `evaluation_comments`
--
ALTER TABLE `evaluation_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `evaluation_list`
--
ALTER TABLE `evaluation_list`
  MODIFY `evaluation_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `faculty_list`
--
ALTER TABLE `faculty_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `question_list`
--
ALTER TABLE `question_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `restriction_list`
--
ALTER TABLE `restriction_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student_list`
--
ALTER TABLE `student_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subject_list`
--
ALTER TABLE `subject_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluation_comments`
--
ALTER TABLE `evaluation_comments`
  ADD CONSTRAINT `evaluation_comments_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluation_list` (`evaluation_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
