-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 10:32 AM
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
(6, '2024-2025', 1, 'Finals', 0, 1),
(7, '2019-2020', 2, 'Midterm', 0, 2),
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
  `password` text NOT NULL DEFAULT '12345',
  `year` text NOT NULL,
  `course` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batch_upload`
--

INSERT INTO `batch_upload` (`ID`, `school_id`, `firstname`, `lastname`, `email`, `password`, `year`, `course`) VALUES
(1, '2880-67', 'Karryl', 'Saldua', 'karryl@gmail.com', '12345', '4th year', 'BSIT');

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
-- Table structure for table `class_subjects`
--

CREATE TABLE `class_subjects` (
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_subjects`
--

INSERT INTO `class_subjects` (`class_id`, `subject_id`) VALUES
(4, 4),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(5, 4),
(5, 5),
(5, 6),
(5, 7),
(5, 8),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8);

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
(18, 10, 5),
(18, 11, 4),
(18, 12, 5),
(18, 13, 4),
(18, 14, 5),
(18, 15, 4),
(18, 16, 5),
(18, 17, 4),
(18, 18, 5),
(18, 19, 4),
(18, 20, 5),
(18, 21, 4),
(18, 22, 5),
(18, 23, 4),
(18, 24, 5),
(18, 25, 4),
(18, 26, 5),
(18, 27, 4),
(18, 28, 4),
(18, 29, 4),
(18, 30, 4),
(18, 31, 4),
(18, 32, 4),
(18, 33, 4),
(18, 34, 4),
(18, 35, 4),
(18, 36, 4),
(18, 37, 4),
(18, 38, 4),
(18, 39, 4),
(18, 40, 4),
(18, 41, 4),
(18, 42, 4),
(18, 43, 4),
(18, 44, 4),
(18, 45, 5),
(18, 46, 4),
(19, 7, 4),
(19, 8, 1),
(19, 9, 2),
(19, 10, 1),
(19, 11, 2),
(19, 12, 1),
(19, 13, 1),
(19, 14, 1),
(19, 15, 1),
(19, 16, 1),
(19, 17, 1),
(19, 18, 1),
(19, 19, 1),
(19, 20, 1),
(19, 21, 1),
(19, 22, 1),
(19, 23, 1),
(19, 24, 1),
(19, 25, 1),
(19, 26, 1),
(19, 27, 1),
(19, 28, 1),
(19, 29, 1),
(19, 30, 1),
(19, 31, 1),
(19, 32, 1),
(19, 33, 1),
(19, 34, 1),
(19, 35, 1),
(19, 36, 1),
(19, 37, 1),
(19, 38, 1),
(19, 39, 1),
(19, 40, 1),
(19, 41, 2),
(19, 42, 2),
(19, 43, 1),
(19, 44, 2),
(19, 45, 1),
(19, 46, 2),
(20, 47, 3),
(20, 48, 4),
(20, 49, 3),
(20, 50, 3),
(20, 51, 4),
(20, 52, 3),
(20, 53, 4),
(20, 54, 3),
(20, 55, 4),
(20, 56, 3),
(20, 57, 4),
(20, 58, 3),
(20, 59, 4),
(20, 60, 3),
(20, 61, 4),
(20, 62, 3),
(20, 63, 4),
(20, 64, 3),
(20, 65, 4),
(20, 66, 3),
(20, 67, 4),
(20, 68, 3),
(20, 69, 4),
(20, 70, 3),
(20, 71, 4),
(20, 72, 3),
(20, 73, 4),
(20, 74, 3),
(20, 75, 4),
(20, 76, 3),
(20, 77, 4),
(20, 78, 3),
(20, 79, 4),
(20, 80, 3),
(21, 7, 2),
(21, 8, 3),
(21, 9, 3),
(21, 10, 3),
(21, 11, 3),
(21, 12, 3),
(21, 13, 4),
(21, 14, 4),
(21, 15, 4),
(21, 16, 4),
(21, 17, 4),
(21, 18, 4),
(21, 19, 5),
(21, 20, 5),
(21, 21, 5),
(21, 22, 5),
(21, 23, 5),
(21, 24, 5),
(21, 25, 5),
(21, 26, 5),
(21, 27, 5),
(21, 28, 5),
(21, 29, 5),
(21, 30, 5),
(21, 31, 5),
(21, 32, 5),
(21, 33, 5),
(21, 34, 5),
(21, 35, 5),
(21, 36, 5),
(21, 37, 5),
(21, 38, 5),
(21, 39, 5),
(21, 40, 5),
(21, 41, 5),
(21, 42, 5),
(21, 43, 5),
(21, 44, 5),
(21, 45, 5),
(21, 46, 5),
(22, 7, 2),
(22, 8, 3),
(22, 9, 3),
(22, 10, 3),
(22, 11, 3),
(22, 12, 3),
(22, 13, 4),
(22, 14, 4),
(22, 15, 4),
(22, 16, 4),
(22, 17, 4),
(22, 18, 4),
(22, 19, 5),
(22, 20, 5),
(22, 21, 5),
(22, 22, 5),
(22, 23, 5),
(22, 24, 5),
(22, 25, 5),
(22, 26, 5),
(22, 27, 5),
(22, 28, 5),
(22, 29, 5),
(22, 30, 5),
(22, 31, 5),
(22, 32, 5),
(22, 33, 5),
(22, 34, 5),
(22, 35, 5),
(22, 36, 5),
(22, 37, 5),
(22, 38, 5),
(22, 39, 5),
(22, 40, 5),
(22, 41, 5),
(22, 42, 5),
(22, 43, 5),
(22, 44, 5),
(22, 45, 5),
(22, 46, 5),
(23, 7, 2),
(23, 8, 3),
(23, 9, 3),
(23, 10, 3),
(23, 11, 3),
(23, 12, 3),
(23, 13, 4),
(23, 14, 4),
(23, 15, 4),
(23, 16, 4),
(23, 17, 4),
(23, 18, 4),
(23, 19, 5),
(23, 20, 5),
(23, 21, 5),
(23, 22, 5),
(23, 23, 5),
(23, 24, 5),
(23, 25, 5),
(23, 26, 5),
(23, 27, 5),
(23, 28, 5),
(23, 29, 5),
(23, 30, 5),
(23, 31, 5),
(23, 32, 5),
(23, 33, 5),
(23, 34, 5),
(23, 35, 5),
(23, 36, 5),
(23, 37, 5),
(23, 38, 5),
(23, 39, 5),
(23, 40, 5),
(23, 41, 5),
(23, 42, 5),
(23, 43, 5),
(23, 44, 5),
(23, 45, 5),
(23, 46, 5),
(24, 7, 2),
(24, 8, 3),
(24, 9, 3),
(24, 10, 3),
(24, 11, 3),
(24, 12, 3),
(24, 13, 4),
(24, 14, 4),
(24, 15, 4),
(24, 16, 4),
(24, 17, 4),
(24, 18, 4),
(24, 19, 5),
(24, 20, 5),
(24, 21, 5),
(24, 22, 5),
(24, 23, 5),
(24, 24, 5),
(24, 25, 5),
(24, 26, 5),
(24, 27, 5),
(24, 28, 5),
(24, 29, 5),
(24, 30, 5),
(24, 31, 5),
(24, 32, 5),
(24, 33, 5),
(24, 34, 5),
(24, 35, 5),
(24, 36, 5),
(24, 37, 5),
(24, 38, 5),
(24, 39, 5),
(24, 40, 5),
(24, 41, 5),
(24, 42, 5),
(24, 43, 5),
(24, 44, 5),
(24, 45, 5),
(24, 46, 5),
(25, 7, 2),
(25, 8, 3),
(25, 9, 3),
(25, 10, 3),
(25, 11, 3),
(25, 12, 3),
(25, 13, 4),
(25, 14, 4),
(25, 15, 4),
(25, 16, 4),
(25, 17, 4),
(25, 18, 4),
(25, 19, 5),
(25, 20, 5),
(25, 21, 5),
(25, 22, 5),
(25, 23, 5),
(25, 24, 5),
(25, 25, 5),
(25, 26, 5),
(25, 27, 5),
(25, 28, 5),
(25, 29, 5),
(25, 30, 5),
(25, 31, 5),
(25, 32, 5),
(25, 33, 5),
(25, 34, 5),
(25, 35, 5),
(25, 36, 5),
(25, 37, 5),
(25, 38, 5),
(25, 39, 5),
(25, 40, 5),
(25, 41, 5),
(25, 42, 5),
(25, 43, 5),
(25, 44, 5),
(25, 45, 5),
(25, 46, 5),
(26, 7, 5),
(26, 8, 5),
(26, 9, 5),
(26, 10, 5),
(26, 11, 5),
(26, 12, 5),
(26, 13, 5),
(26, 14, 5),
(26, 15, 5),
(26, 16, 5),
(26, 17, 5),
(26, 18, 5),
(26, 19, 5),
(26, 20, 5),
(26, 21, 5),
(26, 22, 5),
(26, 23, 5),
(26, 24, 5),
(26, 25, 5),
(26, 26, 5),
(26, 27, 5),
(26, 28, 5),
(26, 29, 5),
(26, 30, 5),
(26, 31, 5),
(26, 32, 5),
(26, 33, 5),
(26, 34, 5),
(26, 35, 5),
(26, 36, 5),
(26, 37, 5),
(26, 38, 5),
(26, 39, 5),
(26, 40, 5),
(26, 41, 5),
(26, 42, 5),
(26, 43, 5),
(26, 44, 5),
(26, 45, 5),
(26, 46, 5),
(27, 7, 5),
(27, 8, 5),
(27, 9, 5),
(27, 10, 5),
(27, 11, 5),
(27, 12, 5),
(27, 13, 5),
(27, 14, 5),
(27, 15, 5),
(27, 16, 5),
(27, 17, 5),
(27, 18, 5),
(27, 19, 5),
(27, 20, 5),
(27, 21, 5),
(27, 22, 5),
(27, 23, 5),
(27, 24, 5),
(27, 25, 5),
(27, 26, 5),
(27, 27, 5),
(27, 28, 5),
(27, 29, 5),
(27, 30, 5),
(27, 31, 5),
(27, 32, 5),
(27, 33, 5),
(27, 34, 5),
(27, 35, 5),
(27, 36, 5),
(27, 37, 5),
(27, 38, 5),
(27, 39, 5),
(27, 40, 5),
(27, 41, 5),
(27, 42, 5),
(27, 43, 5),
(27, 44, 5),
(27, 45, 5),
(27, 46, 5),
(28, 7, 5),
(28, 8, 5),
(28, 9, 5),
(28, 10, 5),
(28, 11, 5),
(28, 12, 5),
(28, 13, 5),
(28, 14, 5),
(28, 15, 5),
(28, 16, 5),
(28, 17, 5),
(28, 18, 5),
(28, 19, 5),
(28, 20, 5),
(28, 21, 5),
(28, 22, 5),
(28, 23, 5),
(28, 24, 5),
(28, 25, 5),
(28, 26, 5),
(28, 27, 5),
(28, 28, 5),
(28, 29, 5),
(28, 30, 5),
(28, 31, 5),
(28, 32, 5),
(28, 33, 5),
(28, 34, 5),
(28, 35, 5),
(28, 36, 5),
(28, 37, 5),
(28, 38, 5),
(28, 39, 5),
(28, 40, 5),
(28, 41, 5),
(28, 42, 5),
(28, 43, 5),
(28, 44, 5),
(28, 45, 5),
(28, 46, 5),
(29, 7, 4),
(29, 8, 4),
(29, 9, 4),
(29, 10, 4),
(29, 11, 4),
(29, 12, 4),
(29, 13, 4),
(29, 14, 4),
(29, 15, 4),
(29, 16, 4),
(29, 17, 4),
(29, 18, 4),
(29, 19, 4),
(29, 20, 4),
(29, 21, 4),
(29, 22, 4),
(29, 23, 4),
(29, 24, 4),
(29, 25, 4),
(29, 26, 4),
(29, 27, 4),
(29, 28, 4),
(29, 29, 4),
(29, 30, 4),
(29, 31, 4),
(29, 32, 4),
(29, 33, 4),
(29, 34, 4),
(29, 35, 4),
(29, 36, 4),
(29, 37, 4),
(29, 38, 4),
(29, 39, 4),
(29, 40, 4),
(29, 41, 4),
(29, 42, 4),
(29, 43, 4),
(29, 44, 4),
(29, 45, 4),
(29, 46, 4),
(30, 7, 3),
(30, 8, 3),
(30, 9, 3),
(30, 10, 3),
(30, 11, 3),
(30, 12, 3),
(30, 13, 3),
(30, 14, 3),
(30, 15, 3),
(30, 16, 3),
(30, 17, 3),
(30, 18, 3),
(30, 19, 3),
(30, 20, 3),
(30, 21, 3),
(30, 22, 3),
(30, 23, 3),
(30, 24, 3),
(30, 25, 3),
(30, 26, 3),
(30, 27, 3),
(30, 28, 3),
(30, 29, 3),
(30, 30, 3),
(30, 31, 3),
(30, 32, 3),
(30, 33, 3),
(30, 34, 3),
(30, 35, 3),
(30, 36, 3),
(30, 37, 3),
(30, 38, 3),
(30, 39, 3),
(30, 40, 3),
(30, 41, 3),
(30, 42, 3),
(30, 43, 3),
(30, 44, 3),
(30, 45, 3),
(30, 46, 3),
(31, 7, 5),
(31, 8, 5),
(31, 9, 5),
(31, 10, 5),
(31, 11, 5),
(31, 12, 5),
(31, 13, 5),
(31, 14, 5),
(31, 15, 5),
(31, 16, 5),
(31, 17, 5),
(31, 18, 5),
(31, 19, 5),
(31, 20, 5),
(31, 21, 5),
(31, 22, 5),
(31, 23, 5),
(31, 24, 5),
(31, 25, 5),
(31, 26, 5),
(31, 27, 5),
(31, 28, 5),
(31, 29, 5),
(31, 30, 5),
(31, 31, 5),
(31, 32, 5),
(31, 33, 5),
(31, 34, 5),
(31, 35, 5),
(31, 36, 5),
(31, 37, 5),
(31, 38, 5),
(31, 39, 5),
(31, 40, 5),
(31, 41, 5),
(31, 42, 5),
(31, 43, 5),
(31, 44, 5),
(31, 45, 5),
(31, 46, 5),
(32, 7, 5),
(32, 8, 5),
(32, 9, 5),
(32, 10, 5),
(32, 11, 5),
(32, 12, 5),
(32, 13, 5),
(32, 14, 5),
(32, 15, 5),
(32, 16, 5),
(32, 17, 5),
(32, 18, 5),
(32, 19, 5),
(32, 20, 5),
(32, 21, 5),
(32, 22, 5),
(32, 23, 5),
(32, 24, 5),
(32, 25, 5),
(32, 26, 5),
(32, 27, 5),
(32, 28, 5),
(32, 29, 5),
(32, 30, 5),
(32, 31, 5),
(32, 32, 5),
(32, 33, 5),
(32, 34, 5),
(32, 35, 5),
(32, 36, 5),
(32, 37, 5),
(32, 38, 5),
(32, 39, 5),
(32, 40, 5),
(32, 41, 5),
(32, 42, 5),
(32, 43, 5),
(32, 44, 5),
(32, 45, 5),
(32, 46, 5);

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
(5, 18, 'Mr Banag is good at teaching', 'Strong (Positive)', 0.85, 0.6),
(6, 19, 'He is not good at teaching', 'Moderate (Negative)', 0.325, 0.6),
(7, 20, 'He is not really good at teaching, he often plays his phone while on class', 'Moderate (Negative)', 0.325, 0.6),
(8, 21, 'its okay nyek', 'Unknown', 0, 0),
(9, 22, 'its okay nyek', 'Unknown', 0, 0),
(10, 23, 'its okay nyek', 'Unknown', 0, 0),
(11, 24, 'its okay nyek', 'Unknown', 0, 0),
(12, 25, 'its okay nyek', 'Unknown', 0, 0),
(13, 26, 'okokokok', 'Unknown', 0, 0),
(14, 27, 'its good nice teaching', 'Unknown', 0, 0),
(15, 28, 'k', 'Unknown', 0, 0),
(16, 29, 'good', 'Unknown', 0, 0),
(17, 30, 'ok', 'Unknown', 0, 0),
(18, 31, 'okay good', 'Unknown', 0, 0),
(19, 32, 'okay good ', 'Unknown', 0, 0);

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
(18, 5, 5, 5, 5, 4, 15, '2024-11-12 18:06:58'),
(19, 5, 5, 7, 8, 6, 16, '2024-11-12 19:03:38'),
(20, 6, 5, 5, 6, 2, 19, '2024-11-17 20:11:01'),
(21, 5, 5, 5, 4, 2, 1, '2024-11-18 12:11:27'),
(22, 5, 5, 5, 4, 2, 1, '2024-11-18 12:11:36'),
(23, 5, 5, 5, 4, 2, 1, '2024-11-18 12:11:37'),
(24, 5, 5, 5, 4, 2, 1, '2024-11-18 12:12:33'),
(25, 5, 5, 5, 4, 2, 1, '2024-11-18 12:19:08'),
(26, 5, 5, 5, 8, 8, 5, '2024-11-18 12:20:44'),
(27, 5, 5, 8, 4, 2, 1, '2024-11-18 20:10:24'),
(28, 5, 5, 8, 5, 3, 2, '2024-11-18 20:11:23'),
(29, 5, 5, 5, 5, 3, 2, '2024-11-18 20:13:01'),
(30, 5, 5, 8, 6, 4, 3, '2024-11-18 22:15:13'),
(31, 5, 5, 8, 7, 6, 4, '2024-11-18 22:19:50'),
(32, 5, 5, 8, 8, 8, 5, '2024-11-18 22:22:41');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_batch_upload`
--

CREATE TABLE `faculty_batch_upload` (
  `id` int(30) NOT NULL,
  `school_id` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `department_id` int(11) NOT NULL,
  `department` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_batch_upload`
--

INSERT INTO `faculty_batch_upload` (`id`, `school_id`, `firstname`, `lastname`, `email`, `password`, `department_id`, `department`) VALUES
(1, '2880-67', 'Eugene', 'Laureano', 'eugene@gmail.com', '', 2, 'College of Computer Studies');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_classes`
--

CREATE TABLE `faculty_classes` (
  `faculty_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_classes`
--

INSERT INTO `faculty_classes` (`faculty_id`, `class_id`) VALUES
(2, 4),
(2, 5),
(2, 6),
(3, 4),
(3, 5),
(3, 6),
(4, 4),
(4, 5),
(4, 6),
(6, 4),
(6, 5),
(6, 6),
(8, 4),
(8, 5),
(8, 6);

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
  `department_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Full-time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_list`
--

INSERT INTO `faculty_list` (`id`, `school_id`, `firstname`, `lastname`, `email`, `password`, `avatar`, `date_created`, `department_id`, `status`) VALUES
(2, 'F1001', 'Rodolfo ', 'Malig-on', 'RODOLFOMALIG-ON@gmail.com', '2036c64e6673f7fc8dbeb11de643ca4d', 'no-image-available.png', '2024-10-09 18:04:09', 2, 'Part-time'),
(3, 'F1002', 'Rosalyn', 'Escudero', 'RosalynEscudero@gmail.com', 'becbba1accf33762141da2348f306eaa', 'no-image-available.png', '2024-10-09 21:43:37', 2, 'Part-time'),
(4, 'F1003', 'Lilia', 'Dela Cruz', 'LiliaDelaCruz@gmail.com', '2c0c748e5c5f9c126f76540f4fc36a42', 'no-image-available.png', '2024-10-14 14:26:17', 2, 'Full-time'),
(6, 'F1004', 'Jino ', 'Barrantes', 'JinoBarrantes@gmail.com', '9897a3cfb2bc18a9d8e01f2904620978', 'no-image-available.png', '2024-11-09 02:16:29', 2, 'Full-time'),
(8, 'F1005', 'Regie', 'Ellana', 'RegieEllan@gmail.com', '0a03624ef2fb43b129d9cdbe74591b6e', 'no-image-available.png', '2024-11-09 18:48:34', 2, 'Full-time');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_subjects`
--

CREATE TABLE `faculty_subjects` (
  `faculty_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_subjects`
--

INSERT INTO `faculty_subjects` (`faculty_id`, `subject_id`) VALUES
(2, 4),
(3, 5),
(4, 6),
(6, 7),
(8, 8);

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
(46, 5, 'The professor is sensitive to cultural, gender, and other differences among students.', 39, 10),
(47, 6, 'The professor has always dressed appropriately and is well groomed.', 0, 4),
(48, 6, 'He/she behaves appropriately at all times.', 1, 4),
(49, 6, 'He/she shows composure, exude confidence, and displays a good sense of humor.', 2, 4),
(50, 6, 'The professors appropriately/immediately responds when students communicate (timely response to the students).', 3, 5),
(51, 6, 'He/she gives positive and specific feedback to students, which reinforces behavior and helps them understand how to improve and makes progress.', 4, 5),
(52, 6, 'He/she guides the direction of the discussion.', 5, 5),
(53, 6, 'He/she specifies how learning tasks will be evaluated (if appropriate).', 6, 5),
(54, 6, 'He/she seeks feedback from students on lesson and on ease of online technology and accessibility of course.', 7, 5),
(55, 6, 'He/she shows good subject knowledge and understanding which engages students\' creativity and sense of humor during the discussion.', 8, 5),
(56, 6, 'The professor uses active-learning exercises in balance with a teacher-led presentation appropriate to the lesson.							\r\n', 9, 6),
(57, 6, 'Before sending students to active learning tasks (group work, paired discussions, polling, team problem-sovling, in-class writing), the professor provides explicit modeling and clear instructions (eg rationale, duration, product).							\r\n', 10, 6),
(58, 6, 'Instructor creates opportunities for interaction between students (breakout rooms, use of chat, collaborative google docs).							\r\n', 11, 6),
(59, 6, 'It is evident that professor is present, proactive, and engaged (if webcam on, is clearly visible and facing camera, keeps an eye on chat or Q&A, monitors waiting room, turns on/off mute as needed, minimal distractions.							\r\n', 12, 6),
(60, 6, 'The professor prepared and uses technology appropriate for the lesson, and gathers needed links and presentations before the start of class.							\r\n', 13, 7),
(61, 6, 'The professor provides relevant instructional materials with clear instructions.							\r\n', 14, 7),
(62, 6, 'The materials are made available to help students who cannot attend online classes or have technical difficulties.							\r\n', 15, 7),
(63, 6, 'The professor presents course material in a clear manner that facilitates understanding.							\r\n', 16, 7),
(64, 6, 'The professor stands and ends the class session on time.							\r\n', 17, 8),
(65, 6, 'He/she finds for questions, discussion and/or summarizing the session\'s lesson.							\r\n', 18, 8),
(66, 6, 'He/she maximizes in-class time, using active learning or applications.							\r\n', 19, 8),
(67, 6, 'He/she clearly indicates time limits for all student activities, using a time-based agenda, or visual and auditory prompts.							\r\n', 20, 8),
(68, 6, 'The professor utilizes appropriate tools and materials to motivate learners (e.g. interactive or competitive games, music, video, etc).							\r\n', 21, 9),
(69, 6, 'He/she builds in-pauses in the lesson to provide opportunities for students to ask questions and promptly responds to questions.							\r\n', 22, 9),
(70, 6, 'He/she arouses students\' interest with relevant life-learning skills (relatable stories).							\r\n', 23, 9),
(71, 6, 'He/she provide opportunities for students to take responsibility.							\r\n', 24, 9),
(72, 6, 'The professor shows consideration and provides opportunities to students.							\r\n', 25, 10),
(73, 6, 'He/she draws non-participating students into activities/ discussions and prevents specific students from dominating/ monopolizing activities/ discussions.							\r\n', 26, 10),
(74, 6, 'Addresses potentially disruptive behaviors before they impact learning environment.							\r\n', 27, 10),
(75, 6, 'The professor provides class generalized constructive and encouraging feedback on how to improve their comprehension or performance in class.							\r\n', 28, 11),
(76, 6, 'He/she attends respectfully to student\'s comprehension or confusion.							\r\n', 29, 11),
(77, 6, 'He/she shows evidence of reinforcement (such as token or certificate, positive points) appropriate to remote or online contexts.							\r\n', 30, 11),
(78, 6, 'His/her assessments are suitable for distance learning environment (different tools, role play, written activity and others.							\r\n', 31, 11),
(79, 6, 'He/she assesses students both informally and formally within the online or remote classroom through use of games, quizzes, online tests, etc.							\r\n', 32, 11),
(80, 6, 'He/she provides immediate feedback.							\r\n', 33, 11);

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
(1, 5, 2, 5, 4),
(2, 5, 3, 5, 5),
(3, 5, 4, 5, 6),
(4, 5, 6, 5, 7),
(5, 5, 8, 5, 8),
(6, 6, 2, 5, 4),
(7, 6, 3, 5, 5),
(8, 6, 4, 5, 6),
(9, 6, 6, 5, 7),
(10, 6, 8, 5, 8),
(11, 7, 2, 5, 4),
(12, 7, 3, 5, 5),
(13, 7, 4, 5, 6),
(14, 7, 6, 5, 7),
(15, 7, 8, 5, 8),
(16, 8, 2, 5, 4),
(17, 8, 3, 5, 5),
(18, 8, 4, 5, 6),
(19, 8, 6, 5, 7),
(20, 8, 8, 5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `student_classes`
--

CREATE TABLE `student_classes` (
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_classes`
--

INSERT INTO `student_classes` (`student_id`, `class_id`) VALUES
(5, 5),
(6, 5),
(7, 6),
(8, 5);

-- --------------------------------------------------------

--
-- Table structure for table `student_information`
--

CREATE TABLE `student_information` (
  `student_id` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, '2876-23', 'Xyrel', 'Genio', 'xyrelgenio08@gmail.com', 'b12d567a3b5a014584c5b6a5a8b41735', 4, 'no-image-available.png', '2024-10-22 08:37:49'),
(8, '1508-21', 'Xzander', 'Nollora', 'Xzandernollora@gmail.com', 'c8003bd07555c83c65aee1393ec40aa4', 5, 'no-image-available.png', '2024-11-18 01:43:20');

-- --------------------------------------------------------

--
-- Table structure for table `student_subject_teacher`
--

CREATE TABLE `student_subject_teacher` (
  `Student_Firstname` varchar(255) DEFAULT NULL,
  `Student_Lastname` varchar(255) DEFAULT NULL,
  `Class_Section` varchar(255) DEFAULT NULL,
  `Subject_Name` varchar(255) DEFAULT NULL,
  `Teacher_Firstname` varchar(255) DEFAULT NULL,
  `Teacher_Lastname` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_subject_teacher`
--

INSERT INTO `student_subject_teacher` (`Student_Firstname`, `Student_Lastname`, `Class_Section`, `Subject_Name`, `Teacher_Firstname`, `Teacher_Lastname`) VALUES
('Ivhan', 'Salazar', 'A2', 'Information Assurance and Security 2', 'Rodolfo ', 'Malig-on'),
('Ivhan', 'Salazar', 'A2', 'Social Issues and Professional Practices', 'Rosalyn', 'Escudero'),
('Ivhan', 'Salazar', 'A2', 'Philippine Pop Culture', 'Lilia', 'Dela Cruz'),
('Ivhan', 'Salazar', 'A2', 'IT Elective 4', 'Jino ', 'Barrantes'),
('Ivhan', 'Salazar', 'A2', 'Capstone Project and Research 2', 'Regie', 'Ellana'),
('Oliver', 'Dela Cruz', 'A2', 'Information Assurance and Security 2', 'Rodolfo ', 'Malig-on'),
('Oliver', 'Dela Cruz', 'A2', 'Social Issues and Professional Practices', 'Rosalyn', 'Escudero'),
('Oliver', 'Dela Cruz', 'A2', 'Philippine Pop Culture', 'Lilia', 'Dela Cruz'),
('Oliver', 'Dela Cruz', 'A2', 'IT Elective 4', 'Jino ', 'Barrantes'),
('Oliver', 'Dela Cruz', 'A2', 'Capstone Project and Research 2', 'Regie', 'Ellana'),
('Xyrel', 'Genio', 'A2', 'Information Assurance and Security 2', 'Rodolfo ', 'Malig-on'),
('Xyrel', 'Genio', 'A2', 'Social Issues and Professional Practices', 'Rosalyn', 'Escudero'),
('Xyrel', 'Genio', 'A2', 'Philippine Pop Culture', 'Lilia', 'Dela Cruz'),
('Xyrel', 'Genio', 'A2', 'IT Elective 4', 'Jino ', 'Barrantes'),
('Xyrel', 'Genio', 'A2', 'Capstone Project and Research 2', 'Regie', 'Ellana'),
('Xzander', 'Nollora', 'A2', 'Information Assurance and Security 2', 'Rodolfo ', 'Malig-on'),
('Xzander', 'Nollora', 'A2', 'Social Issues and Professional Practices', 'Rosalyn', 'Escudero'),
('Xzander', 'Nollora', 'A2', 'Philippine Pop Culture', 'Lilia', 'Dela Cruz'),
('Xzander', 'Nollora', 'A2', 'IT Elective 4', 'Jino ', 'Barrantes'),
('Xzander', 'Nollora', 'A2', 'Capstone Project and Research 2', 'Regie', 'Ellana');

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
(4, 'IASEC2', 'Information Assurance and Security 2', 'Advanced concepts in information security, focusing on risk management and security technologies.'),
(5, 'SIPP', 'Social Issues and Professional Practices', 'Exploration of societal challenges and ethical practices in the professional world.'),
(6, 'PPC', 'Philippine Pop Culture', 'Study of contemporary Philippine culture, media, and entertainment trends.\r\n\r\n'),
(7, 'IT4', 'IT Elective 4', 'Specialized IT course offering elective topics based on current industry needs.'),
(8, 'CAPR2', 'Capstone Project and Research 2', 'Application of knowledge to real-world projects, including research and development.');

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
-- Indexes for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD PRIMARY KEY (`class_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

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
-- Indexes for table `faculty_batch_upload`
--
ALTER TABLE `faculty_batch_upload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_classes`
--
ALTER TABLE `faculty_classes`
  ADD PRIMARY KEY (`faculty_id`,`class_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `faculty_list`
--
ALTER TABLE `faculty_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD PRIMARY KEY (`faculty_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

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
-- Indexes for table `student_classes`
--
ALTER TABLE `student_classes`
  ADD PRIMARY KEY (`student_id`,`class_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `student_information`
--
ALTER TABLE `student_information`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `class_id` (`class_id`);

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
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `batch_upload`
--
ALTER TABLE `batch_upload`
  MODIFY `ID` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `evaluation_list`
--
ALTER TABLE `evaluation_list`
  MODIFY `evaluation_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `faculty_batch_upload`
--
ALTER TABLE `faculty_batch_upload`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculty_list`
--
ALTER TABLE `faculty_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `question_list`
--
ALTER TABLE `question_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `restriction_list`
--
ALTER TABLE `restriction_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `student_list`
--
ALTER TABLE `student_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- Constraints for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD CONSTRAINT `class_subjects_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class_list` (`id`),
  ADD CONSTRAINT `class_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subject_list` (`id`);

--
-- Constraints for table `evaluation_comments`
--
ALTER TABLE `evaluation_comments`
  ADD CONSTRAINT `evaluation_comments_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluation_list` (`evaluation_id`);

--
-- Constraints for table `faculty_classes`
--
ALTER TABLE `faculty_classes`
  ADD CONSTRAINT `faculty_classes_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_list` (`id`),
  ADD CONSTRAINT `faculty_classes_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `class_list` (`id`);

--
-- Constraints for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD CONSTRAINT `faculty_subjects_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_list` (`id`),
  ADD CONSTRAINT `faculty_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subject_list` (`id`);

--
-- Constraints for table `student_classes`
--
ALTER TABLE `student_classes`
  ADD CONSTRAINT `student_classes_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_list` (`id`),
  ADD CONSTRAINT `student_classes_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `class_list` (`id`);

--
-- Constraints for table `student_information`
--
ALTER TABLE `student_information`
  ADD CONSTRAINT `student_information_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class_list` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
