-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 01, 2018 lúc 12:13 PM
-- Phiên bản máy phục vụ: 10.1.29-MariaDB
-- Phiên bản PHP: 7.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `tms`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bonus`
--

CREATE TABLE `bonus` (
  `bonus_id` int(11) NOT NULL,
  `bonus_plus` decimal(10,0) DEFAULT NULL,
  `bonus_minus` decimal(10,0) DEFAULT NULL,
  `bonus_start_date` int(11) DEFAULT NULL,
  `bonus_end_date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `country`
--

CREATE TABLE `country` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `country`
--

INSERT INTO `country` (`country_id`, `country_name`) VALUES
(1, 'Ả Rập Saudi'),
(2, 'Afghanistan'),
(3, 'Ai Cập'),
(4, 'Albania'),
(5, 'Algeria'),
(6, 'Ấn Độ'),
(7, 'Andorra'),
(8, 'Angola'),
(9, 'Anguilla'),
(10, 'Anh Quốc'),
(11, 'Antigua và Barbuda'),
(12, 'Áo'),
(13, 'Argentina'),
(14, 'Armenia'),
(15, 'Aruba'),
(16, 'Azerbaijan'),
(17, 'Ba Lan'),
(18, 'Bahamas'),
(19, 'Bahrain'),
(20, 'Bangladesh'),
(21, 'Barbados'),
(22, 'Belarus'),
(23, 'Belize'),
(24, 'Benin'),
(25, 'Bermuda'),
(26, 'Bhutan'),
(27, 'Bỉ'),
(28, 'Bờ Biển Ngà'),
(29, 'Bồ Đào Nha'),
(30, 'Bolivia'),
(31, 'Bosna và Hercegovina'),
(32, 'Botswana'),
(33, 'Brazil'),
(34, 'Brunei'),
(35, 'Bulgaria'),
(36, 'Burkina Faso'),
(37, 'Burundi'),
(38, 'Các tiểu vương quốc Ả rập thống nhất (UEA)'),
(39, 'Cameroon'),
(40, 'Campuchia'),
(41, 'Canada'),
(42, 'Cape Verde'),
(43, 'CHDC Congo'),
(44, 'CHDCND Triều Tiên'),
(45, 'Chile'),
(46, 'CHND Trung Hoa'),
(47, 'Colombia'),
(48, 'Comoros'),
(49, 'Cộng hòa Congo'),
(50, 'Cộng hòa Dominica'),
(51, 'Cộng hòa Macedonia'),
(52, 'Cộng hòa Séc'),
(53, 'Cộng hòa Trung Phi'),
(54, 'Costa Rica'),
(55, 'Croatia'),
(56, 'Cuba'),
(57, 'Curaçao'),
(58, 'Đan Mạch'),
(59, 'Đảo Man'),
(60, 'Djibouti'),
(61, 'Dominica'),
(62, 'Đông Timor'),
(63, 'Đức'),
(64, 'Ecuador'),
(65, 'El Salvador'),
(66, 'Eritrea'),
(67, 'Estonia'),
(68, 'Ethiopia'),
(69, 'Fiji'),
(70, 'Gabon'),
(71, 'Gambia'),
(72, 'Ghana'),
(73, 'Gibraltar'),
(74, 'Greenland'),
(75, 'Grenada'),
(76, 'Gruzian11'),
(77, 'Guam'),
(78, 'Guatemala'),
(79, 'Guernsey'),
(80, 'Guinea'),
(81, 'Guinea Xích Đạo'),
(82, 'Guinea-Bissau'),
(83, 'Guyana'),
(84, 'Hà Lan'),
(85, 'Haiti'),
(86, 'Hàn Quốc'),
(87, 'Hoa Kỳ'),
(88, 'Honduras'),
(89, 'Hồng Kông'),
(90, 'Hungary'),
(91, 'Hy Lạp'),
(92, 'Iceland'),
(93, 'Indonesia'),
(94, 'Iran'),
(95, 'Iraq'),
(96, 'Ireland'),
(97, 'Israel'),
(98, 'Jamaica'),
(99, 'Jersey'),
(100, 'Jordan'),
(101, 'Kazakhstan'),
(102, 'Kenya'),
(103, 'Kiribati'),
(104, 'Kuwait'),
(105, 'Kyrgyzstan'),
(106, 'Lào'),
(107, 'Latvia'),
(108, 'Lesotho'),
(109, 'Liban'),
(110, 'Liberia'),
(111, 'Libya'),
(112, 'Liechtenstein'),
(113, 'Liên bang Micronesia'),
(114, 'Liên Bang Nga'),
(115, 'Litva'),
(116, 'Luxembourg'),
(117, 'Macau'),
(118, 'Madagascar'),
(119, 'Malawi'),
(120, 'Malaysia'),
(121, 'Maldives'),
(122, 'Mali'),
(123, 'Malta'),
(124, 'Maroc'),
(125, 'Mauritania'),
(126, 'Mauritius'),
(127, 'Mexico'),
(128, 'Moldova'),
(129, 'Monaco'),
(130, 'Mông Cổ'),
(131, 'Montenegro'),
(132, 'Montserrat'),
(133, 'Mozambique'),
(134, 'Myanmar'),
(135, 'Na Uy'),
(136, 'Nam Phi'),
(137, 'Nam Sudan'),
(138, 'Namibia'),
(139, 'Nauru'),
(140, 'Nepal'),
(141, 'New Zealand'),
(142, 'Nhật Bản'),
(143, 'Nicaragua'),
(144, 'Niger'),
(145, 'Nigeria'),
(146, 'Niue'),
(147, 'Oman'),
(148, 'Pakistan'),
(149, 'Palau'),
(150, 'Panama'),
(151, 'Papua New Guinea'),
(152, 'Paraguay'),
(153, 'Peru'),
(154, 'Phần Lan'),
(155, 'Pháp'),
(156, 'Philippines'),
(157, 'Puerto Rico'),
(158, 'Qatar'),
(159, 'Quần đảo Bắc Mariana'),
(160, 'Quần đảo Cayman'),
(161, 'Quần đảo Cook'),
(162, 'Quần đảo Falkland'),
(163, 'Quần đảo Faroe'),
(164, 'Quần đảo Marshall'),
(165, 'Quần đảo Pitcairn'),
(166, 'Quần đảo Solomon'),
(167, 'Quần đảo Turks và Caicos'),
(168, 'Quần đảo Virgin thuộc Anh'),
(169, 'Quần đảo Virgin thuộc Mỹ'),
(170, 'Romania'),
(171, 'Rwanda'),
(172, 'Saint Helena'),
(173, 'Saint Kitts và Nevis'),
(174, 'Saint Lucia'),
(175, 'Saint Vincent và Grenadines'),
(176, 'Samoa'),
(177, 'Samoa thuộc Mỹ'),
(178, 'San Marino'),
(179, 'São Tomé và Príncipe'),
(180, 'Senegal'),
(181, 'Serbia'),
(182, 'Seychelles'),
(183, 'Sierra Leone'),
(184, 'Singapore'),
(185, 'Sint Maarten'),
(186, 'Síp'),
(187, 'Slovakia'),
(188, 'Slovenia'),
(189, 'Somalia'),
(190, 'Sri Lanka'),
(191, 'Sudan'),
(192, 'Suriname'),
(193, 'Swaziland'),
(194, 'Syria'),
(195, 'Tajikistan'),
(196, 'Tanzania'),
(197, 'Tây Ban Nha'),
(198, 'Tây Sahara'),
(199, 'Tchad'),
(200, 'Thái Lan'),
(201, 'Thổ Nhĩ Kỳ'),
(202, 'Thụy Điển'),
(203, 'Thụy Sĩ'),
(204, 'Togo'),
(205, 'Tokelau'),
(206, 'Tonga'),
(207, 'Trinidad và Tobago'),
(208, 'Trung Hoa Dân Quốc (Đài Loan)'),
(209, 'Tunisia'),
(210, 'Turkmenistan'),
(211, 'Tuvalu'),
(212, 'Úc'),
(213, 'Uganda'),
(214, 'Ukraina'),
(215, 'Uruguay'),
(216, 'Uzbekistan'),
(217, 'Vanuatu'),
(218, 'Vatican'),
(219, 'Venezuela'),
(220, 'Việt Nam'),
(221, 'Vùng lãnh thổ Palestine'),
(222, 'Ý'),
(223, 'Yemen'),
(224, 'Zambia'),
(225, 'Zimbabwe');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_code` varchar(20) DEFAULT NULL,
  `department_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `department`
--

INSERT INTO `department` (`department_id`, `department_code`, `department_name`) VALUES
(3, 'GD', 'Giám đốc'),
(4, 'NS', 'Nhân sự');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `info`
--

CREATE TABLE `info` (
  `info_id` int(11) NOT NULL,
  `info_company` varchar(100) DEFAULT NULL,
  `info_mst` varchar(20) DEFAULT NULL,
  `info_address` varchar(200) DEFAULT NULL,
  `info_phone` varchar(20) DEFAULT NULL,
  `info_email` varchar(50) DEFAULT NULL,
  `info_director` varchar(50) DEFAULT NULL,
  `info_general_accountant` varchar(50) DEFAULT NULL,
  `info_accountant` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `info`
--

INSERT INTO `info` (`info_id`, `info_company`, `info_mst`, `info_address`, `info_phone`, `info_email`, `info_director`, `info_general_accountant`, `info_accountant`) VALUES
(1, 'CÔNG TY TNHH VIỆT TRA DE', '2147483648', 'Số 545, Tổ 10, Ấp Hương Phước, Xã Phước Tân, TP. Biên Hòa, Tỉnh Đồng Nai', '025 193 7677', 'it@viet-trade.org', 'Nguyễn Hoàng Minh Long', 'Phạm Hoài Thương Ly', 'Hoàng Minh Vy');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `port`
--

CREATE TABLE `port` (
  `port_id` int(11) NOT NULL,
  `port_name` varchar(50) DEFAULT NULL,
  `port_province` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `port`
--

INSERT INTO `port` (`port_id`, `port_name`, `port_province`) VALUES
(1, 'Cảng An Thới', 33),
(2, 'Cảng Cam Ranh', 32),
(3, 'Cảng Cái Lân', 49),
(4, 'Cảng Cát Lái', 31),
(5, 'Cảng Cái Mép', 2),
(6, 'Cảng Cửa Lò', 41),
(7, 'Cảng Cửa Việt', 50),
(8, 'Cảng Diêm Điền', 54),
(9, 'Cảng Dung Quất', 48),
(10, 'Cảng Đà Nẵng', 15),
(11, 'Cảng Đình Vũ', 27),
(12, 'Cảng Nha Trang', 32),
(13, 'Cảng Hải Phòng', 27),
(14, 'Cảng Hiệp Phước', 31),
(15, 'Cảng Hòn Gai', 49),
(16, 'Cảng Kỳ Hà', 47),
(17, 'Cảng Nghi Sơn', 56),
(18, 'Cảng Ninh Phúc', 42),
(19, 'Cảng Quy Nhơn', 9),
(20, 'Cảng Sa Kỳ', 48),
(21, 'Cảng Sài Gòn', 31),
(22, 'Cảng Sơn Dương', 25),
(23, 'Cảng Tân Cảng Sài Gòn', 31),
(24, 'Cảng Thị Vải', 2),
(25, 'Cảng Tiên Sa', 15),
(26, 'Cảng Vân Phong', 32),
(27, 'Cảng Vũng Áng', 25),
(28, 'Cảng Vũng Rô', 45),
(29, 'Cảng Vũng Tàu', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `position`
--

CREATE TABLE `position` (
  `position_id` int(11) NOT NULL,
  `position_code` varchar(20) DEFAULT NULL,
  `position_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `position`
--

INSERT INTO `position` (`position_id`, `position_code`, `position_name`) VALUES
(1, 'GD', 'Giám đốc');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `province`
--

CREATE TABLE `province` (
  `province_id` int(11) NOT NULL,
  `province_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `province`
--

INSERT INTO `province` (`province_id`, `province_name`) VALUES
(1, 'An Giang'),
(2, 'Bà Rịa - Vũng Tàu'),
(3, 'Bạc Liêu'),
(4, 'Bắc Kạn'),
(5, 'Bắc Giang'),
(6, 'Bắc Ninh'),
(7, 'Bến Tre'),
(8, 'Bình Dương'),
(9, 'Bình Định'),
(10, 'Bình Phước'),
(11, 'Bình Thuận'),
(12, 'Cà Mau'),
(13, 'Cao Bằng'),
(14, 'Cần Thơ'),
(15, 'Đà Nẵng'),
(16, 'Đắk Lắk'),
(17, 'Đắk Nông'),
(18, 'Điện Biên'),
(19, 'Đồng Nai'),
(20, 'Đồng Tháp'),
(21, 'Gia Lai'),
(22, 'Hà Giang'),
(23, 'Hà Nam'),
(24, 'Hà Nội'),
(25, 'Hà Tĩnh'),
(26, 'Hải Dương'),
(27, 'Hải Phòng'),
(28, 'Hậu Giang'),
(29, 'Hòa Bình'),
(30, 'Hưng Yên'),
(31, 'TP. Hồ Chí Minh'),
(32, 'Khánh Hòa'),
(33, 'Kiên Giang'),
(34, 'Kon Tum'),
(35, 'Lai Châu'),
(36, 'Lâm Đồng'),
(37, 'Lạng Sơn'),
(38, 'Lào Cai'),
(39, 'Long An'),
(40, 'Nam Định'),
(41, 'Nghệ An'),
(42, 'Ninh Bình'),
(43, 'Ninh Thuận'),
(44, 'Phú Thọ'),
(45, 'Phú Yên'),
(46, 'Quảng Bình'),
(47, 'Quảng Nam'),
(48, 'Quảng Ngãi'),
(49, 'Quảng Ninh'),
(50, 'Quảng Trị'),
(51, 'Sóc Trăng'),
(52, 'Sơn La'),
(53, 'Tây Ninh'),
(54, 'Thái Bình'),
(55, 'Thái Nguyên'),
(56, 'Thanh Hóa'),
(57, 'Thừa Thiên Huế'),
(58, 'Tiền Giang'),
(59, 'Trà Vinh'),
(60, 'Tuyên Quang'),
(61, 'Vĩnh Long'),
(62, 'Vĩnh Phúc'),
(63, 'Yên Bái');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `role_status` int(1) NOT NULL DEFAULT '1' COMMENT '1:active|0:inactive',
  `role_permission` text,
  `role_permission_action` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `role`
--

INSERT INTO `role` (`role_id`, `role_name`, `role_status`, `role_permission`, `role_permission_action`) VALUES
(1, 'Quản trị cấp cao', 1, '[\"all\"]', '[\"all\"]'),
(2, 'Báo cáo, tổng hợp', 1, '[\"oil\",\"road\",\"warehouse\",\"customer\",\"vehicle\",\"romooc\",\"place\",\"route\",\"salary\",\"salarybonus\",\"steersman\",\"staff\",\"department\",\"importstock\",\"exportstock\",\"house\",\"sparepart\",\"repair\",\"roadcost\",\"checkingcost\",\"insurancecost\",\"sparevehicle\",\"sparevehiclelist\",\"sparedrap\",\"stock\",\"used\",\"spareparttracking\",\"shipment\",\"newshipment\",\"shipmenttemp\",\"driver\",\"vehiclework\",\"vehicleromooc\",\"vehicleromooc\",\"tollcost\",\"marketing\",\"shipmentlist\",\"loanlist\",\"sell\",\"receiptvoucher\",\"paymentvoucher\",\"internaltransfer\",\"bankbalance\",\"receivable\",\"payable\",\"loan\",\"importstock\",\"exportstock\",\"stock\",\"vat\",\"vat\",\"exvat\",\"sales\",\"cost\",\"noinvoice\",\"tolls\",\"salary\",\"salary\",\"repairsalary\",\"costlist\",\"bank\",\"account\",\"trucking\",\"customership\",\"truckinglist\",\"repairlist\",\"roadcostlist\",\"checkingcostlist\",\"insurancecostlist\",\"oilreport\",\"advance\",\"commission\",\"quantity\",\"profit\",\"round\",\"officecost\",\"vehicleanalytics\",\"report\"]', '{\"oil\":\"oil\",\"road\":\"road\",\"warehouse\":\"warehouse\",\"customer\":\"customer\",\"vehicle\":\"vehicle\",\"romooc\":\"romooc\",\"place\":\"place\",\"route\":\"route\",\"salary\":\"salary\",\"salarybonus\":\"salarybonus\",\"steersman\":\"steersman\",\"staff\":\"staff\",\"department\":\"department\",\"importstock\":\"importstock\",\"exportstock\":\"exportstock\",\"house\":\"house\",\"sparepart\":\"sparepart\",\"repair\":\"repair\",\"roadcost\":\"roadcost\",\"checkingcost\":\"checkingcost\",\"insurancecost\":\"insurancecost\",\"sparevehicle\":\"sparevehicle\",\"sparevehiclelist\":\"sparevehiclelist\",\"sparedrap\":\"sparedrap\",\"stock\":\"stock\",\"used\":\"used\",\"spareparttracking\":\"spareparttracking\",\"shipment\":\"shipment\",\"newshipment\":\"newshipment\",\"shipmenttemp\":\"shipmenttemp\",\"driver\":\"driver\",\"vehiclework\":\"vehiclework\",\"vehicleromooc\":\"vehicleromooc\",\"tollcost\":\"tollcost\",\"marketing\":\"marketing\",\"shipmentlist\":\"shipmentlist\",\"loanlist\":\"loanlist\",\"sell\":\"sell\",\"receiptvoucher\":\"receiptvoucher\",\"paymentvoucher\":\"paymentvoucher\",\"internaltransfer\":\"internaltransfer\",\"bankbalance\":\"bankbalance\",\"receivable\":\"receivable\",\"payable\":\"payable\",\"loan\":\"loan\",\"vat\":\"vat\",\"exvat\":\"exvat\",\"sales\":\"sales\",\"cost\":\"cost\",\"noinvoice\":\"noinvoice\",\"tolls\":\"tolls\",\"repairsalary\":\"repairsalary\",\"costlist\":\"costlist\",\"bank\":\"bank\",\"account\":\"account\",\"trucking\":\"trucking\",\"customership\":\"customership\",\"truckinglist\":\"truckinglist\",\"repairlist\":\"repairlist\",\"roadcostlist\":\"roadcostlist\",\"checkingcostlist\":\"checkingcostlist\",\"insurancecostlist\":\"insurancecostlist\",\"oilreport\":\"oilreport\",\"advance\":\"advance\",\"commission\":\"commission\",\"quantity\":\"quantity\",\"profit\":\"profit\",\"round\":\"round\",\"officecost\":\"officecost\",\"vehicleanalytics\":\"vehicleanalytics\",\"report\":\"report\"}'),
(3, 'Kế toán', 1, '[\"salary\",\"importstock\",\"exportstock\",\"stock\",\"receiptvoucher\",\"paymentvoucher\",\"internaltransfer\",\"bankbalance\",\"receivable\",\"payable\",\"loan\",\"importstock\",\"exportstock\",\"stock\",\"vat\",\"vat\",\"exvat\",\"sales\",\"cost\",\"noinvoice\",\"tolls\",\"salary\",\"salary\",\"repairsalary\",\"costlist\",\"bank\",\"account\"]', '{\"salary\":\"salary\",\"importstock\":\"importstock\",\"exportstock\":\"exportstock\",\"stock\":\"stock\",\"receiptvoucher\":\"receiptvoucher\",\"paymentvoucher\":\"paymentvoucher\",\"internaltransfer\":\"internaltransfer\",\"bankbalance\":\"bankbalance\",\"receivable\":\"receivable\",\"payable\":\"payable\",\"loan\":\"loan\",\"vat\":\"vat\",\"exvat\":\"exvat\",\"sales\":\"sales\",\"cost\":\"cost\",\"noinvoice\":\"noinvoice\",\"tolls\":\"tolls\",\"repairsalary\":\"repairsalary\",\"costlist\":\"costlist\",\"bank\":\"bank\",\"account\":\"account\"}'),
(4, 'Kinh doanh', 1, '[\"customer\",\"place\",\"route\",\"marketing\",\"shipmentlist\",\"loanlist\",\"sell\",\"customership\",\"truckinglist\",\"commission\"]', '{\"customer\":\"customer\",\"place\":\"place\",\"route\":\"route\",\"marketing\":\"marketing\",\"shipmentlist\":\"shipmentlist\",\"loanlist\":\"loanlist\",\"sell\":\"sell\",\"customership\":\"customership\",\"truckinglist\":\"truckinglist\",\"commission\":\"commission\"}'),
(5, 'Điều độ', 1, '[\"warehouse\",\"vehicle\",\"romooc\",\"place\",\"route\",\"steersman\",\"importstock\",\"exportstock\",\"house\",\"sparepart\",\"repair\",\"roadcost\",\"checkingcost\",\"insurancecost\",\"sparevehicle\",\"sparevehiclepass\",\"sparevehiclelist\",\"sparedrap\",\"stock\",\"used\",\"spareparttracking\",\"shipment\",\"newshipment\",\"shipmenttemp\",\"driver\",\"vehiclework\",\"vehicleromooc\",\"vehicleromooc\",\"tollcost\",\"roadcostlist\",\"checkingcostlist\",\"insurancecostlist\",\"oilreport\",\"advance\"]', '{\"warehouse\":\"warehouse\",\"vehicle\":\"vehicle\",\"romooc\":\"romooc\",\"place\":\"place\",\"route\":\"route\",\"steersman\":\"steersman\",\"importstock\":\"importstock\",\"exportstock\":\"exportstock\",\"house\":\"house\",\"sparepart\":\"sparepart\",\"repair\":\"repair\",\"roadcost\":\"roadcost\",\"checkingcost\":\"checkingcost\",\"insurancecost\":\"insurancecost\",\"sparevehicle\":\"sparevehicle\",\"sparevehiclepass\":\"sparevehiclepass\",\"sparevehiclelist\":\"sparevehiclelist\",\"sparedrap\":\"sparedrap\",\"stock\":\"stock\",\"used\":\"used\",\"spareparttracking\":\"spareparttracking\",\"shipment\":\"shipment\",\"newshipment\":\"newshipment\",\"shipmenttemp\":\"shipmenttemp\",\"driver\":\"driver\",\"vehiclework\":\"vehiclework\",\"vehicleromooc\":\"vehicleromooc\",\"tollcost\":\"tollcost\",\"roadcostlist\":\"roadcostlist\",\"checkingcostlist\":\"checkingcostlist\",\"insurancecostlist\":\"insurancecostlist\",\"oilreport\":\"oilreport\",\"advance\":\"advance\"}'),
(6, 'Kho', 1, '[\"importstock\",\"exportstock\",\"house\",\"sparepart\",\"repair\",\"roadcost\",\"checkingcost\",\"insurancecost\",\"sparevehicle\",\"sparevehiclelist\",\"sparedrap\",\"stock\",\"used\",\"spareparttracking\",\"importstock\",\"exportstock\",\"stock\"]', '{\"importstock\":\"importstock\",\"exportstock\":\"exportstock\",\"house\":\"house\",\"sparepart\":\"sparepart\",\"repair\":\"repair\",\"roadcost\":\"roadcost\",\"checkingcost\":\"checkingcost\",\"insurancecost\":\"insurancecost\",\"sparevehicle\":\"sparevehicle\",\"sparevehiclelist\":\"sparevehiclelist\",\"sparedrap\":\"sparedrap\",\"stock\":\"stock\",\"used\":\"used\",\"spareparttracking\":\"spareparttracking\"}'),
(7, 'Nhân sự', 1, '[\"salary\",\"salarybonus\",\"steersman\",\"staff\",\"department\",\"salary\",\"salary\"]', '{\"salary\":\"salary\",\"salarybonus\":\"salarybonus\",\"steersman\":\"steersman\",\"staff\":\"staff\",\"department\":\"department\"}'),
(8, 'Vật tư, kỹ thuật', 1, '[\"importstock\",\"exportstock\",\"house\",\"sparepart\",\"repair\",\"roadcost\",\"checkingcost\",\"insurancecost\",\"sparevehicle\",\"sparevehiclelist\",\"sparedrap\",\"stock\",\"used\",\"spareparttracking\",\"importstock\",\"exportstock\",\"stock\"]', '{\"importstock\":\"importstock\",\"exportstock\":\"exportstock\",\"house\":\"house\",\"sparepart\":\"sparepart\",\"repair\":\"repair\",\"roadcost\":\"roadcost\",\"checkingcost\":\"checkingcost\",\"insurancecost\":\"insurancecost\",\"sparevehicle\":\"sparevehicle\",\"sparevehiclelist\":\"sparevehiclelist\",\"sparedrap\":\"sparedrap\",\"stock\":\"stock\",\"used\":\"used\",\"spareparttracking\":\"spareparttracking\"}'),
(9, 'Tài xế', 1, '[\"salary\",\"salary\",\"salary\"]', '{\"salary\":\"salary\"}');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `shipping_name` varchar(50) DEFAULT NULL,
  `shipping_country` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `shipping_name`, `shipping_country`) VALUES
(1, 'MSC', 222),
(2, 'CMA-CGM', 155);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `staff_code` varchar(20) DEFAULT NULL,
  `staff_name` varchar(100) DEFAULT NULL,
  `staff_address` varchar(200) DEFAULT NULL,
  `staff_cmnd` varchar(12) DEFAULT NULL,
  `staff_birthday` int(11) DEFAULT NULL,
  `staff_phone` varchar(15) DEFAULT NULL,
  `staff_email` varchar(50) DEFAULT NULL,
  `staff_bank_account` varchar(20) DEFAULT NULL,
  `staff_bank` varchar(50) DEFAULT NULL,
  `staff_gender` int(11) DEFAULT NULL COMMENT '0:Nam | 1:Nữ',
  `staff_position` int(11) DEFAULT NULL,
  `staff_department` int(11) DEFAULT NULL,
  `staff_start_date` int(11) DEFAULT NULL,
  `staff_end_date` int(11) DEFAULT NULL,
  `staff_account` int(11) DEFAULT NULL,
  `staff_gplx` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `staff`
--

INSERT INTO `staff` (`staff_id`, `staff_code`, `staff_name`, `staff_address`, `staff_cmnd`, `staff_birthday`, `staff_phone`, `staff_email`, `staff_bank_account`, `staff_bank`, `staff_gender`, `staff_position`, `staff_department`, `staff_start_date`, `staff_end_date`, `staff_account`, `staff_gplx`) VALUES
(1, 'NV01', 'Nguyễn Văn A', 'Đồng Nai', '121323232', 1525107600, '0902 085 911', 'a@a.com', '12323', 'ACB', 0, 1, 3, 1526317200, NULL, 0, '1212232323');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `unit`
--

CREATE TABLE `unit` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `unit`
--

INSERT INTO `unit` (`unit_id`, `unit_name`) VALUES
(1, 'KG'),
(2, 'Tấn'),
(3, 'Cont 20'),
(4, 'Cont 40'),
(5, 'Cont 45'),
(6, 'Chuyến');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  `user_lock` int(11) DEFAULT NULL,
  `user_group` int(11) DEFAULT NULL,
  `user_dept` int(11) DEFAULT NULL,
  `permission` text,
  `permission_action` text,
  `lasted_online` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `user_email`, `create_time`, `role`, `user_lock`, `user_group`, `user_dept`, `permission`, `permission_action`, `lasted_online`) VALUES
(1, 'admin', '81dc9bdb52d04dc20036dbd8313ed055', 'ngoton007@yahoo.com', 1527218218, 1, 0, NULL, NULL, '[\"all\"]', '[\"all\"]', 1527823930),
(3, 'user', '81dc9bdb52d04dc20036dbd8313ed055', 'ngoton008@yahoo.com', 1527444158, 2, NULL, NULL, NULL, '[\"oil\",\"road\",\"warehouse\",\"customer\",\"vehicle\",\"romooc\",\"place\",\"route\",\"salary\",\"salarybonus\",\"steersman\",\"staff\",\"department\",\"importstock\",\"exportstock\",\"house\",\"sparepart\",\"repair\",\"roadcost\",\"checkingcost\",\"insurancecost\",\"sparevehicle\",\"sparevehiclelist\",\"sparedrap\",\"stock\",\"used\",\"spareparttracking\",\"shipment\",\"newshipment\",\"shipmenttemp\",\"driver\",\"vehiclework\",\"vehicleromooc\",\"vehicleromooc\",\"tollcost\",\"marketing\",\"shipmentlist\",\"loanlist\",\"sell\",\"receiptvoucher\",\"paymentvoucher\",\"internaltransfer\",\"bankbalance\",\"receivable\",\"payable\",\"loan\",\"importstock\",\"exportstock\",\"stock\",\"vat\",\"vat\",\"exvat\",\"sales\",\"cost\",\"noinvoice\",\"tolls\",\"salary\",\"salary\",\"repairsalary\",\"costlist\",\"bank\",\"account\",\"trucking\",\"customership\",\"truckinglist\",\"repairlist\",\"roadcostlist\",\"checkingcostlist\",\"insurancecostlist\",\"oilreport\",\"advance\",\"commission\",\"quantity\",\"profit\",\"round\",\"officecost\",\"vehicleanalytics\",\"report\"]', '{\"oil\":\"oil\",\"road\":\"road\",\"warehouse\":\"warehouse\",\"customer\":\"customer\",\"vehicle\":\"vehicle\",\"romooc\":\"romooc\",\"place\":\"place\",\"route\":\"route\",\"salary\":\"salary\",\"salarybonus\":\"salarybonus\",\"steersman\":\"steersman\",\"staff\":\"staff\",\"department\":\"department\",\"importstock\":\"importstock\",\"exportstock\":\"exportstock\",\"house\":\"house\",\"sparepart\":\"sparepart\",\"repair\":\"repair\",\"roadcost\":\"roadcost\",\"checkingcost\":\"checkingcost\",\"insurancecost\":\"insurancecost\",\"sparevehicle\":\"sparevehicle\",\"sparevehiclelist\":\"sparevehiclelist\",\"sparedrap\":\"sparedrap\",\"stock\":\"stock\",\"used\":\"used\",\"spareparttracking\":\"spareparttracking\",\"shipment\":\"shipment\",\"newshipment\":\"newshipment\",\"shipmenttemp\":\"shipmenttemp\",\"driver\":\"driver\",\"vehiclework\":\"vehiclework\",\"vehicleromooc\":\"vehicleromooc\",\"tollcost\":\"tollcost\",\"marketing\":\"marketing\",\"shipmentlist\":\"shipmentlist\",\"loanlist\":\"loanlist\",\"sell\":\"sell\",\"receiptvoucher\":\"receiptvoucher\",\"paymentvoucher\":\"paymentvoucher\",\"internaltransfer\":\"internaltransfer\",\"bankbalance\":\"bankbalance\",\"receivable\":\"receivable\",\"payable\":\"payable\",\"loan\":\"loan\",\"vat\":\"vat\",\"exvat\":\"exvat\",\"sales\":\"sales\",\"cost\":\"cost\",\"noinvoice\":\"noinvoice\",\"tolls\":\"tolls\",\"repairsalary\":\"repairsalary\",\"costlist\":\"costlist\",\"bank\":\"bank\",\"account\":\"account\",\"trucking\":\"trucking\",\"customership\":\"customership\",\"truckinglist\":\"truckinglist\",\"repairlist\":\"repairlist\",\"roadcostlist\":\"roadcostlist\",\"checkingcostlist\":\"checkingcostlist\",\"insurancecostlist\":\"insurancecostlist\",\"oilreport\":\"oilreport\",\"advance\":\"advance\",\"commission\":\"commission\",\"quantity\":\"quantity\",\"profit\":\"profit\",\"round\":\"round\",\"officecost\":\"officecost\",\"vehicleanalytics\":\"vehicleanalytics\",\"report\":\"report\"}', 1527445758);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_log`
--

CREATE TABLE `user_log` (
  `user_log_id` int(11) NOT NULL,
  `user_log` int(11) DEFAULT NULL,
  `user_log_date` int(11) DEFAULT NULL,
  `user_log_table` varchar(50) DEFAULT NULL,
  `user_log_action` varchar(100) DEFAULT NULL,
  `user_log_data` text,
  `user_log_table_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `user_log`
--

INSERT INTO `user_log` (`user_log_id`, `user_log`, `user_log_date`, `user_log_table`, `user_log_action`, `user_log_data`, `user_log_table_name`) VALUES
(1, 1, 1527220943, 'user', 'Cập nhật', '{\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"role\":\"1\",\"user_email\":\"ngoton007@yahoo.com\",\"user_lock\":\"0\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclepass\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\",\\\"info\\\",\\\"user\\\",\\\"permission\\\",\\\"backup\\\",\\\"update\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclepass\\\":\\\"sparevehiclepass\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\",\\\"info\\\":\\\"info\\\",\\\"user\\\":\\\"user\\\",\\\"permission\\\":\\\"permission\\\",\\\"backup\\\":\\\"backup\\\",\\\"update\\\":\\\"update\\\"}\"}', 'Tài khoản'),
(2, 1, 1527221504, 'user', 'Cập nhật t', '{\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"role\":\"1\",\"user_email\":\"ngoton007@yahoo.com\",\"user_lock\":\"0\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclepass\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\",\\\"info\\\",\\\"user\\\",\\\"permission\\\",\\\"backup\\\",\\\"update\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclepass\\\":\\\"sparevehiclepass\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\",\\\"info\\\":\\\"info\\\",\\\"user\\\":\\\"user\\\",\\\"permission\\\":\\\"permission\\\",\\\"backup\\\":\\\"backup\\\",\\\"update\\\":\\\"update\\\"}\"}', 'Tài khoản'),
(3, 1, 1527221768, 'user', 'Thêm mới', '{\"username\":\"user\",\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"user_email\":\"ngoton008@yahoo.com\",\"create_time\":1527221768,\"role\":\"2\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Tài khoản'),
(4, 1, 1527222391, 'user', 'Đổi mật khẩu', '{\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\"}', 'Tài khoản'),
(5, 1, 1527221151, 'user', 'Đổi mật khẩu', '{\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\"}', 'Tài khoản'),
(6, 1, 1527228808, 'user', 'Cập nhật thông tin', '{\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"role\":\"2\",\"user_email\":\"ngoton008@yahoo.com\",\"user_lock\":\"0\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Tài khoản'),
(7, 1, 1527229180, 'user', 'Cập nhật thông tin', '{\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"role\":\"2\",\"user_email\":\"ngoton008@yahoo.com\",\"user_lock\":\"0\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Tài khoản'),
(8, 1, 1527229566, 'user', 'Cập nhật thông tin', '{\"role\":\"2\",\"user_email\":\"ngoton008@yahoo.com\",\"user_lock\":\"0\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Tài khoản'),
(9, 1, 1527233024, 'user', 'Thêm mới', '{\"username\":\"user2\",\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"user_email\":\"a@a.com\",\"create_time\":1527233024,\"role\":\"1\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclepass\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\",\\\"info\\\",\\\"user\\\",\\\"permission\\\",\\\"backup\\\",\\\"update\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclepass\\\":\\\"sparevehiclepass\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\",\\\"info\\\":\\\"info\\\",\\\"user\\\":\\\"user\\\",\\\"permission\\\":\\\"permission\\\",\\\"backup\\\":\\\"backup\\\",\\\"update\\\":\\\"update\\\"}\"}', 'Tài khoản'),
(10, 1, 1527234007, 'user', 'Thêm mới', '{\"username\":\"asas\",\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"user_email\":\"\",\"create_time\":1527234007,\"role\":\"2\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Tài khoản'),
(11, 1, 1527234013, 'user', 'Thêm mới', '{\"username\":\"asa1\",\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"user_email\":\"\",\"create_time\":1527234013,\"role\":\"2\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Tài khoản'),
(12, 1, 1527234024, 'user', 'Xóa', '\"5\"', 'Tài khoản'),
(13, 1, 1527234180, 'user', 'Xóa', '[\"4\"]', 'Tài khoản'),
(14, 1, 1527345652, 'user', 'Xóa', '[\"\"]', 'Tài khoản'),
(15, 1, 1527368520, 'user', 'Cập nhật thông tin', '{\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"role\":\"2\",\"user_email\":\"ngoton008@yahoo.com\",\"user_lock\":\"0\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Tài khoản'),
(16, 1, 1527437299, 'user', 'Xóa', '[\"\"]', 'Tài khoản'),
(17, 1, 1527443769, 'user', 'Xóa', '\"2\"', 'Tài khoản'),
(18, 1, 1527444158, 'user', 'Thêm mới', '{\"username\":\"user\",\"password\":\"81dc9bdb52d04dc20036dbd8313ed055\",\"user_email\":\"ngoton008@yahoo.com\",\"create_time\":1527444158,\"role\":\"2\",\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Tài khoản');
INSERT INTO `user_log` (`user_log_id`, `user_log`, `user_log_date`, `user_log_table`, `user_log_action`, `user_log_data`, `user_log_table_name`) VALUES
(19, 1, 1527446534, 'role', 'Phân quyền', '{\"role_permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"role_permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Nhóm người dùng'),
(20, 1, 1527446544, 'user', 'Phân quyền', '{\"permission\":\"[\\\"oil\\\",\\\"road\\\",\\\"warehouse\\\",\\\"customer\\\",\\\"vehicle\\\",\\\"romooc\\\",\\\"place\\\",\\\"route\\\",\\\"salary\\\",\\\"salarybonus\\\",\\\"steersman\\\",\\\"staff\\\",\\\"department\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"house\\\",\\\"sparepart\\\",\\\"repair\\\",\\\"roadcost\\\",\\\"checkingcost\\\",\\\"insurancecost\\\",\\\"sparevehicle\\\",\\\"sparevehiclelist\\\",\\\"sparedrap\\\",\\\"stock\\\",\\\"used\\\",\\\"spareparttracking\\\",\\\"shipment\\\",\\\"newshipment\\\",\\\"shipmenttemp\\\",\\\"driver\\\",\\\"vehiclework\\\",\\\"vehicleromooc\\\",\\\"vehicleromooc\\\",\\\"tollcost\\\",\\\"marketing\\\",\\\"shipmentlist\\\",\\\"loanlist\\\",\\\"sell\\\",\\\"receiptvoucher\\\",\\\"paymentvoucher\\\",\\\"internaltransfer\\\",\\\"bankbalance\\\",\\\"receivable\\\",\\\"payable\\\",\\\"loan\\\",\\\"importstock\\\",\\\"exportstock\\\",\\\"stock\\\",\\\"vat\\\",\\\"vat\\\",\\\"exvat\\\",\\\"sales\\\",\\\"cost\\\",\\\"noinvoice\\\",\\\"tolls\\\",\\\"salary\\\",\\\"salary\\\",\\\"repairsalary\\\",\\\"costlist\\\",\\\"bank\\\",\\\"account\\\",\\\"trucking\\\",\\\"customership\\\",\\\"truckinglist\\\",\\\"repairlist\\\",\\\"roadcostlist\\\",\\\"checkingcostlist\\\",\\\"insurancecostlist\\\",\\\"oilreport\\\",\\\"advance\\\",\\\"commission\\\",\\\"quantity\\\",\\\"profit\\\",\\\"round\\\",\\\"officecost\\\",\\\"vehicleanalytics\\\",\\\"report\\\"]\",\"permission_action\":\"{\\\"oil\\\":\\\"oil\\\",\\\"road\\\":\\\"road\\\",\\\"warehouse\\\":\\\"warehouse\\\",\\\"customer\\\":\\\"customer\\\",\\\"vehicle\\\":\\\"vehicle\\\",\\\"romooc\\\":\\\"romooc\\\",\\\"place\\\":\\\"place\\\",\\\"route\\\":\\\"route\\\",\\\"salary\\\":\\\"salary\\\",\\\"salarybonus\\\":\\\"salarybonus\\\",\\\"steersman\\\":\\\"steersman\\\",\\\"staff\\\":\\\"staff\\\",\\\"department\\\":\\\"department\\\",\\\"importstock\\\":\\\"importstock\\\",\\\"exportstock\\\":\\\"exportstock\\\",\\\"house\\\":\\\"house\\\",\\\"sparepart\\\":\\\"sparepart\\\",\\\"repair\\\":\\\"repair\\\",\\\"roadcost\\\":\\\"roadcost\\\",\\\"checkingcost\\\":\\\"checkingcost\\\",\\\"insurancecost\\\":\\\"insurancecost\\\",\\\"sparevehicle\\\":\\\"sparevehicle\\\",\\\"sparevehiclelist\\\":\\\"sparevehiclelist\\\",\\\"sparedrap\\\":\\\"sparedrap\\\",\\\"stock\\\":\\\"stock\\\",\\\"used\\\":\\\"used\\\",\\\"spareparttracking\\\":\\\"spareparttracking\\\",\\\"shipment\\\":\\\"shipment\\\",\\\"newshipment\\\":\\\"newshipment\\\",\\\"shipmenttemp\\\":\\\"shipmenttemp\\\",\\\"driver\\\":\\\"driver\\\",\\\"vehiclework\\\":\\\"vehiclework\\\",\\\"vehicleromooc\\\":\\\"vehicleromooc\\\",\\\"tollcost\\\":\\\"tollcost\\\",\\\"marketing\\\":\\\"marketing\\\",\\\"shipmentlist\\\":\\\"shipmentlist\\\",\\\"loanlist\\\":\\\"loanlist\\\",\\\"sell\\\":\\\"sell\\\",\\\"receiptvoucher\\\":\\\"receiptvoucher\\\",\\\"paymentvoucher\\\":\\\"paymentvoucher\\\",\\\"internaltransfer\\\":\\\"internaltransfer\\\",\\\"bankbalance\\\":\\\"bankbalance\\\",\\\"receivable\\\":\\\"receivable\\\",\\\"payable\\\":\\\"payable\\\",\\\"loan\\\":\\\"loan\\\",\\\"vat\\\":\\\"vat\\\",\\\"exvat\\\":\\\"exvat\\\",\\\"sales\\\":\\\"sales\\\",\\\"cost\\\":\\\"cost\\\",\\\"noinvoice\\\":\\\"noinvoice\\\",\\\"tolls\\\":\\\"tolls\\\",\\\"repairsalary\\\":\\\"repairsalary\\\",\\\"costlist\\\":\\\"costlist\\\",\\\"bank\\\":\\\"bank\\\",\\\"account\\\":\\\"account\\\",\\\"trucking\\\":\\\"trucking\\\",\\\"customership\\\":\\\"customership\\\",\\\"truckinglist\\\":\\\"truckinglist\\\",\\\"repairlist\\\":\\\"repairlist\\\",\\\"roadcostlist\\\":\\\"roadcostlist\\\",\\\"checkingcostlist\\\":\\\"checkingcostlist\\\",\\\"insurancecostlist\\\":\\\"insurancecostlist\\\",\\\"oilreport\\\":\\\"oilreport\\\",\\\"advance\\\":\\\"advance\\\",\\\"commission\\\":\\\"commission\\\",\\\"quantity\\\":\\\"quantity\\\",\\\"profit\\\":\\\"profit\\\",\\\"round\\\":\\\"round\\\",\\\"officecost\\\":\\\"officecost\\\",\\\"vehicleanalytics\\\":\\\"vehicleanalytics\\\",\\\"report\\\":\\\"report\\\"}\"}', 'Người dùng'),
(21, 1, 1527472619, 'info', 'Cập nhật thông tin', '{\"info_company\":\"C\\u00d4NG TY TNHH VI\\u1ec6T TRA DE\",\"info_mst\":\"2147483647\",\"info_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X\\u00e3 Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean H\\u00f2a, T\\u1ec9nh \\u0110\\u1ed3ng Nai\",\"info_phone\":\"025 193 7677\",\"info_email\":\"it@viet-trade.org\",\"info_director\":\"Nguy\\u1ec5n Ho\\u00e0ng Minh Long\",\"info_general_accountant\":\"Ph\\u1ea1m Ho\\u00e0i Th\\u01b0\\u01a1ng Ly\",\"info_accountant\":\"Ho\\u00e0ng Minh Vy\"}', 'Công ty'),
(22, 1, 1527472724, 'info', 'Cập nhật thông tin', '{\"info_company\":\"C\\u00d4NG TY TNHH VI\\u1ec6T TRA DE\",\"info_mst\":\"2147483647\",\"info_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X\\u00e3 Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean H\\u00f2a, T\\u1ec9nh \\u0110\\u1ed3ng Nai\",\"info_phone\":\"025 193 7677\",\"info_email\":\"it@viet-trade.org\",\"info_director\":\"Nguy\\u1ec5n Ho\\u00e0ng Minh Long\",\"info_general_accountant\":\"Ph\\u1ea1m Ho\\u00e0i Th\\u01b0\\u01a1ng Ly\",\"info_accountant\":\"Ho\\u00e0ng Minh Vy\"}', 'Công ty'),
(23, 1, 1527472787, 'info', 'Cập nhật thông tin', '{\"info_company\":\"C\\u00d4NG TY TNHH VI\\u1ec6T TRA DE\",\"info_mst\":\"2147483648\",\"info_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X\\u00e3 Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean H\\u00f2a, T\\u1ec9nh \\u0110\\u1ed3ng Nai\",\"info_phone\":\"025 193 7677\",\"info_email\":\"it@viet-trade.org\",\"info_director\":\"Nguy\\u1ec5n Ho\\u00e0ng Minh Long\",\"info_general_accountant\":\"Ph\\u1ea1m Ho\\u00e0i Th\\u01b0\\u01a1ng Ly\",\"info_accountant\":\"Ho\\u00e0ng Minh Vy\"}', 'Công ty'),
(24, 1, 1527602005, 'department', 'Thêm mới', '{\"department_code\":\"GD\",\"department_name\":\"Gi\\u00e1m \\u0111\\u1ed1c\"}', 'Phòng ban'),
(25, 1, 1527602305, 'department', 'Cập nhật', '{\"department_code\":\"GD1\",\"department_name\":\"Gi\\u00e1m \\u0111\\u1ed1c1\"}', 'Phòng ban'),
(26, 1, 1527602318, 'department', 'Cập nhật', '{\"department_code\":\"GD\",\"department_name\":\"Gi\\u00e1m \\u0111\\u1ed1c\"}', 'Phòng ban'),
(27, 1, 1527602363, 'department', 'Thêm mới', '{\"department_code\":\"NS\",\"department_name\":\"Nh\\u00e2n s\\u1ef1\"}', 'Phòng ban'),
(28, 1, 1527602381, 'user', 'Xóa', '\"2\"', 'Phòng ban'),
(29, 1, 1527602395, 'user', 'Xóa', '[\"1\"]', 'Phòng ban'),
(30, 1, 1527602413, 'department', 'Thêm mới', '{\"department_code\":\"GD\",\"department_name\":\"Gi\\u00e1m \\u0111\\u1ed1c\"}', 'Phòng ban'),
(31, 1, 1527603604, 'position', 'Thêm mới', '{\"position_code\":\"GD\",\"position_name\":\"Gi\\u00e1m \\u0111\\u1ed1c\"}', 'Chức vụ'),
(32, 1, 1527603615, 'position', 'Cập nhật', '{\"position_code\":\"GD1\",\"position_name\":\"Gi\\u00e1m \\u0111\\u1ed1c1\"}', 'Chức vụ'),
(33, 1, 1527603624, 'position', 'Cập nhật', '{\"position_code\":\"GD\",\"position_name\":\"Gi\\u00e1m \\u0111\\u1ed1c\"}', 'Chức vụ'),
(34, 1, 1527603655, 'position', 'Thêm mới', '{\"position_code\":\"KT\",\"position_name\":\"K\\u1ebf to\\u00e1n\"}', 'Chức vụ'),
(35, 1, 1527603676, 'position', 'Cập nhật', '{\"position_code\":\"KT\",\"position_name\":\"K\\u1ebf to\\u00e1n\"}', 'Chức vụ'),
(36, 1, 1527603687, 'user', 'Xóa', '\"2\"', 'chức vụ'),
(37, 1, 1527609458, 'department', 'Cập nhật', '{\"department_code\":\"\",\"department_name\":\"\"}', 'Phòng ban'),
(38, 1, 1527609510, 'department', 'Cập nhật', '{\"department_code\":\"GD\",\"department_name\":\"Gi\\u00e1m \\u0111\\u1ed1c\"}', 'Phòng ban'),
(39, 1, 1527609534, 'department', 'Thêm mới', '{\"department_code\":\"NS\",\"department_name\":\"Nh\\u00e2n s\\u1ef1\"}', 'Phòng ban'),
(40, 1, 1527609700, 'position', 'Cập nhật', '{\"position_code\":\"GD1\",\"position_name\":\"Gi\\u00e1m \\u0111\\u1ed1c1\"}', 'Chức vụ'),
(41, 1, 1527609708, 'position', 'Cập nhật', '{\"position_code\":\"GD\",\"position_name\":\"Gi\\u00e1m \\u0111\\u1ed1c\"}', 'Chức vụ'),
(42, 1, 1527617486, 'staff', 'Thêm mới', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"1\",\"staff_position\":\"1\",\"staff_department\":\"4\",\"staff_start_date\":1526317200,\"staff_end_date\":1527094800,\"staff_account\":\"1\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(43, 1, 1527618533, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 9111\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"1\",\"staff_position\":\"1\",\"staff_department\":\"4\",\"staff_start_date\":1526317200,\"staff_end_date\":1527094800,\"staff_account\":\"3\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(44, 1, 1527618556, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"4\",\"staff_start_date\":1526317200,\"staff_end_date\":1527094800,\"staff_account\":\"\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(45, 1, 1527618768, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":1527094800,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(46, 1, 1527618782, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":1527613200,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(47, 1, 1527618862, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":1527613200,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(48, 1, 1527620504, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":1527613200,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(49, 1, 1527620518, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":null,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(50, 1, 1527787021, 'shipping', 'Thêm mới', '{\"shipping_country\":\"222\",\"shipping_name\":\"MSC\"}', 'Hãng tàu'),
(51, 1, 1527787081, 'shipping', 'Thêm mới', '{\"shipping_country\":\"155\",\"shipping_name\":\"CMA-CMG\"}', 'Hãng tàu'),
(52, 1, 1527787094, 'shipping', 'Cập nhật', '{\"shipping_country\":\"155\",\"shipping_name\":\"CMA-CGM\"}', 'Hãng tàu'),
(53, 1, 1527788809, 'unit', 'Thêm mới', '{\"unit_name\":\"KG\"}', 'Đơn vị tính'),
(54, 1, 1527788818, 'unit', 'Thêm mới', '{\"unit_name\":\"T\\u1ea5n\"}', 'Đơn vị tính'),
(55, 1, 1527788833, 'unit', 'Thêm mới', '{\"unit_name\":\"Cont 20\"}', 'Đơn vị tính'),
(56, 1, 1527788839, 'unit', 'Thêm mới', '{\"unit_name\":\"Cont 40\"}', 'Đơn vị tính'),
(57, 1, 1527788845, 'unit', 'Thêm mới', '{\"unit_name\":\"Cont 45\"}', 'Đơn vị tính'),
(58, 1, 1527788896, 'unit', 'Thêm mới', '{\"unit_name\":\"Chuy\\u1ebfn\"}', 'Đơn vị tính');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bonus`
--
ALTER TABLE `bonus`
  ADD PRIMARY KEY (`bonus_id`);

--
-- Chỉ mục cho bảng `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`);

--
-- Chỉ mục cho bảng `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Chỉ mục cho bảng `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`info_id`);

--
-- Chỉ mục cho bảng `port`
--
ALTER TABLE `port`
  ADD PRIMARY KEY (`port_id`);

--
-- Chỉ mục cho bảng `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`position_id`);

--
-- Chỉ mục cho bảng `province`
--
ALTER TABLE `province`
  ADD PRIMARY KEY (`province_id`);

--
-- Chỉ mục cho bảng `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Chỉ mục cho bảng `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`);

--
-- Chỉ mục cho bảng `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Chỉ mục cho bảng `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`unit_id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Chỉ mục cho bảng `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`user_log_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bonus`
--
ALTER TABLE `bonus`
  MODIFY `bonus_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `country`
--
ALTER TABLE `country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT cho bảng `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `info`
--
ALTER TABLE `info`
  MODIFY `info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `port`
--
ALTER TABLE `port`
  MODIFY `port_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `position`
--
ALTER TABLE `position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `province`
--
ALTER TABLE `province`
  MODIFY `province_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT cho bảng `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `unit`
--
ALTER TABLE `unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `user_log`
--
ALTER TABLE `user_log`
  MODIFY `user_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
