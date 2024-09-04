-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2024 at 08:34 PM
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
-- Database: `raveltech`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterUser` (IN `p_username` VARCHAR(100), IN `p_email` VARCHAR(150), IN `p_password` VARCHAR(255), IN `p_userrole` INT, IN `p_contact_details` VARCHAR(255), IN `p_join_code` VARCHAR(50), OUT `p_status` VARCHAR(255))   BEGIN
    DECLARE v_code_exists INT;
    DECLARE v_role_match INT;

    -- Check if the join code exists and matches the role
    SELECT COUNT(*)
    INTO v_code_exists
    FROM join_codes
    WHERE code = p_join_code AND role = p_userrole AND isUsed = 0;

    IF v_code_exists = 0 THEN
        SET p_status = 'Invalid join code or does not match the role.';
    ELSE
        -- Insert registration data
        INSERT INTO registrations (username, email, password, userrole, contact_details, join_code, isApproved)
        VALUES (p_username, p_email, p_password, p_userrole, p_contact_details, p_join_code, 0);

        -- Mark the join code as used
        UPDATE join_codes
        SET isUsed = 1
        WHERE code = p_join_code;

        SET p_status = 'Registration successful. Awaiting admin approval.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidateUser` (IN `p_email` VARCHAR(150), OUT `p_hashed_password` VARCHAR(255), OUT `p_isApproved` TINYINT(1), OUT `p_status` VARCHAR(255), OUT `p_username` VARCHAR(255))   BEGIN
    -- Initialize output variables
    SET p_hashed_password = NULL;
    SET p_isApproved = 0;
    SET p_status = '';
    SET p_username ='';

    -- Attempt to fetch the user's hashed password and approval status
    SELECT password, isApproved, username
    INTO p_hashed_password, p_isApproved, p_username
    FROM registrations
    WHERE email = p_email;

    -- Determine the status based on retrieved data
    IF p_hashed_password IS NULL THEN
        -- If no user is found with the given email
        SET p_status = 'The account does not exist in our records.';
    ELSEIF p_isApproved = 0 THEN
        -- If the user exists but is not approved
        SET p_status = 'Your account is pending approval and is not yet activated.';
    ELSE
        -- If the user is found and approved
        SET p_status = 'User found and approved.';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `join_codes`
--

CREATE TABLE `join_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `role` int(11) NOT NULL,
  `isUsed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `join_codes`
--

INSERT INTO `join_codes` (`id`, `code`, `role`, `isUsed`, `created_at`) VALUES
(1, 'userrole', 3, 1, '2024-09-03 21:19:05');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `userrole` int(11) NOT NULL,
  `contact_details` varchar(255) DEFAULT NULL,
  `join_code` varchar(50) DEFAULT NULL,
  `isApproved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `username`, `email`, `password`, `userrole`, `contact_details`, `join_code`, `isApproved`, `created_at`) VALUES
(3, 'sarveshrana16', 'sarveshrana16@gmail.com', '$2y$10$qniiAnNWAnG.HPtSpg2Fje33QlHIMTIt4JbtF4PXaZP1jEIiMYKXG', 1, '3630541256', 'userrole', 1, '2024-09-04 00:07:27'),
(5, 'myryl', 'MYRYLVELOSO@GMAIL.COM', '$2y$10$fxghhnSZJZjPSSVSy0S6tOMmRPBua5qTEwSH2xG.HkGdmdWgISzOq', 3, '+1 2363084079', 'userrole', 0, '2024-09-04 05:17:26'),
(8, 'aanya', 'sarveshrana14@hotmail.com', '$2y$10$Vlz7ZL0eQBIBhIobp0sWx.zxAs.ylJDJ40lNpZ1xrh3lk65CN72Ku', 3, '', 'userrole', 0, '2024-09-04 16:26:31'),
(11, 'Edwin', 'sarveshrana14@gmail.com', '$2y$10$JT2U00fu5WGOxMaE6zL2CuWGikEEM5nC5cceXnJvX.IZUpn5Z9OHq', 3, '', 'userrole', 1, '2024-09-04 16:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_name`, `content`) VALUES
(1, 'hero_section', '<div class=\"hero\"><div id=\"heroCarousel\" class=\"carousel slide\" data-bs-ride=\"carousel\"><div class=\"carousel-inner\"><div class=\"carousel-item active\"><div class=\"container\"><div class=\"row justify-content-between\"><div class=\"col-lg-5 col-md-6 col-sm-12\"><div class=\"intro-excerpt\"><h1>Modern Interior <span class=\"d-block\">Design Studio - Sarvesh Edited</span></h1><p class=\"mb-4\">Transform your home into a sanctuary with our carefully curated collection of sofas, dining tables, and bedroom sets. Each piece is crafted with the finest materials to ensure durability and comfort- Edited Again</p><p><a href=\"shop.php\" class=\"btn btn-secondary me-2\">Shop Now</a><a href=\"about.php\" class=\"btn btn-white-outline\">Explore</a></p></div></div><div class=\"col-lg-7 col-md-6 col-sm-12\"><div class=\"hero-img-wrap d-none d-md-block\"><img src=\"images/couch.png\" class=\"img-fluid\" style=\"max-width: 100%; height: auto;\"></div></div></div></div></div></div></div></div><div id=\"section-2\" contenteditable=\"true\"><div class=\"product-section\"><div class=\"container\"><div class=\"row\"><div class=\"col-md-12 col-lg-3 mb-5 mb-lg-0\"><h2 class=\"mb-4 section-title\">Crafted with excellent material.</h2><p class=\"mb-4\">Every piece of furniture is crafted with exceptional materials, ensuring both beauty and durability. Experience the finest craftsmanship with our meticulously designed collection. Edited Again</p></div><!-- Additional product items can be loaded here --></div></div></div></div>\n<div id=\"editor-tools\" style=\"position: fixed; top: 10px; right: 10px; z-index: 9999;\">\n    <button id=\"enable-editing\" style=\"display: none;\">Enable Editing</button>\n    <button id=\"save-changes\" style=\"display: inline-block;\">Save Changes</button>\n</div>\n\n<script>\ndocument.addEventListener(\'DOMContentLoaded\', function () {\n    let enableEditingButton = document.getElementById(\'enable-editing\');\n    let saveChangesButton = document.getElementById(\'save-changes\');\n    let isEditing = false;\n\n    enableEditingButton.addEventListener(\'click\', function () {\n        isEditing = !isEditing;\n        toggleEditing(isEditing);\n    });\n\n    saveChangesButton.addEventListener(\'click\', function () {\n        let sections = document.querySelectorAll(\'[id^=\"section-\"]\');\n        sections.forEach(function (section) {\n            let sectionId = section.id.replace(\'section-\', \'\');\n            let content = section.innerHTML;\n\n            fetch(\'save_changes.php\', {\n                method: \'POST\',\n                headers: {\n                    \'Content-Type\': \'application/json\',\n                },\n                body: JSON.stringify({ id: sectionId, content: content }),\n            })\n            .then(response => response.json())\n            .then(data => {\n                if (data.success) {\n                    alert(\'Changes saved successfully!\');\n                    toggleEditing(false);\n                } else {\n                    alert(\'Failed to save changes.\');\n                }\n            });\n        });\n    });\n\n    function toggleEditing(enable) {\n        let sections = document.querySelectorAll(\'[id^=\"section-\"]\');\n        sections.forEach(function (section) {\n            section.contentEditable = enable;\n        });\n        enableEditingButton.style.display = enable ? \'none\' : \'inline-block\';\n        saveChangesButton.style.display = enable ? \'inline-block\' : \'none\';\n    }\n});\n</script>\n\n'),
(2, 'product_section', '<div class=\"product-section\"><div class=\"container\"><div class=\"row\"><div class=\"col-md-12 col-lg-3 mb-5 mb-lg-0\"><h2 class=\"mb-4 section-title\">Crafted with excellent material.</h2><p class=\"mb-4\">Every piece of furniture is crafted with exceptional materials, ensuring both beauty and durability. Experience the finest craftsmanship with our meticulously designed collection. Edited Again</p></div><!-- Additional product items can be loaded here --></div></div></div>');

-- --------------------------------------------------------

--
-- Table structure for table `timesheets`
--

CREATE TABLE `timesheets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `hours_worked` decimal(4,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timesheets`
--

INSERT INTO `timesheets` (`id`, `email`, `date`, `hours_worked`, `description`, `created_at`) VALUES
(1, 'sarveshrana16@gmail.com', '2024-09-04', 3.00, 'worked on ravel tech mail feature', '2024-09-04 18:21:10'),
(2, 'sarveshrana16@gmail.com', '2024-09-05', 2.00, '2 more hours worked in evening', '2024-09-04 18:32:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `join_codes`
--
ALTER TABLE `join_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timesheets`
--
ALTER TABLE `timesheets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `join_codes`
--
ALTER TABLE `join_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timesheets`
--
ALTER TABLE `timesheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
