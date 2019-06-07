-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th6 07, 2019 lúc 07:36 AM
-- Phiên bản máy phục vụ: 5.5.31
-- Phiên bản PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `caimep_tms`
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

--
-- Đang đổ dữ liệu cho bảng `bonus`
--

INSERT INTO `bonus` (`bonus_id`, `bonus_plus`, `bonus_minus`, `bonus_start_date`, `bonus_end_date`) VALUES
(1, '20000', '10000', 1525107600, 1527699600),
(2, '30000', '15000', 1527786000, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `booking_date` int(11) DEFAULT NULL,
  `booking_code` varchar(20) DEFAULT NULL,
  `booking_customer` int(11) DEFAULT NULL,
  `booking_number` varchar(100) DEFAULT NULL,
  `booking_type` int(11) DEFAULT NULL COMMENT '1:Hàng nhập | 2:Hàng xuất | 3:Khác',
  `booking_shipping` int(11) DEFAULT NULL,
  `booking_shipping_name` varchar(50) DEFAULT NULL,
  `booking_shipping_number` varchar(50) DEFAULT NULL,
  `booking_place_from` int(11) DEFAULT NULL,
  `booking_place_to` int(11) DEFAULT NULL,
  `booking_start_date` int(11) DEFAULT NULL,
  `booking_end_date` int(11) DEFAULT NULL,
  `booking_comment` text,
  `booking_sum` float DEFAULT NULL,
  `booking_total` decimal(16,2) DEFAULT NULL,
  `booking_create_user` int(11) DEFAULT NULL,
  `booking_update_user` int(11) DEFAULT NULL,
  `booking_status` int(11) DEFAULT NULL COMMENT '1:Đã nhận | 2:Đang chạy | 3:Hoàn thành',
  `booking_sum_receive` float DEFAULT NULL,
  `booking_sum_run` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `booking`
--

INSERT INTO `booking` (`booking_id`, `booking_date`, `booking_code`, `booking_customer`, `booking_number`, `booking_type`, `booking_shipping`, `booking_shipping_name`, `booking_shipping_number`, `booking_place_from`, `booking_place_to`, `booking_start_date`, `booking_end_date`, `booking_comment`, `booking_sum`, `booking_total`, `booking_create_user`, `booking_update_user`, `booking_status`, `booking_sum_receive`, `booking_sum_run`) VALUES
(1, 1527786000, 'DH001', 2, '8624223', 2, 2, 'CMA', '4232', 1, 2, 1528650000, 1529427600, 'Hang nhập', 3, '4100000.00', 1, 1, 2, 3, NULL),
(2, 1529254800, 'DH002', 3, '0435265353', 1, 1, 'A', '642', 2, 1, 1529254800, 1530291600, 'Hàng dễ vỡ', 2, '3000000.00', 1, NULL, 2, 1, NULL),
(3, 1529514000, 'DH003', 3, 'BK352724', 1, 2, '', '', 2, 2, 1529514000, 1529514000, '', 1000, '10000000.00', 1, NULL, NULL, NULL, NULL),
(4, 1529514000, 'DH004', 3, '', 1, 3, '', '', 2, 2, 1529514000, 1529514000, '', 0, '0.00', 1, NULL, NULL, NULL, NULL),
(5, 1531674000, 'DH005', 2, '1234567', 2, 0, '', '', 3, 3, 1531674000, 1531674000, '', 5, '0.00', 1, NULL, 2, 5, NULL),
(6, 1531674000, 'DH006', 3, '', 1, 0, '', '', 3, 3, 1531674000, 1531674000, '', 5, '0.00', 1, NULL, NULL, NULL, NULL),
(7, 1534957200, NULL, 3, 'booking 1', 2, 0, '', '', NULL, NULL, NULL, NULL, '', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(8, 1540400400, NULL, 3, '', 1, 0, '', '', NULL, NULL, NULL, NULL, '', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(9, 1541091600, NULL, 3, '0123456', 2, 0, '', '', NULL, NULL, NULL, NULL, '', NULL, NULL, 1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_detail`
--

CREATE TABLE `booking_detail` (
  `booking_detail_id` int(11) NOT NULL,
  `booking` int(11) DEFAULT NULL,
  `booking_detail_container` varchar(50) DEFAULT NULL,
  `booking_detail_seal` varchar(50) DEFAULT NULL,
  `booking_detail_number` float DEFAULT NULL,
  `booking_detail_unit` int(11) DEFAULT NULL,
  `booking_detail_customer_sub` varchar(200) DEFAULT NULL,
  `booking_detail_price` decimal(14,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `booking_detail`
--

INSERT INTO `booking_detail` (`booking_detail_id`, `booking`, `booking_detail_container`, `booking_detail_seal`, `booking_detail_number`, `booking_detail_unit`, `booking_detail_customer_sub`, `booking_detail_price`) VALUES
(3, 1, 'CU0232355', '2354223', 2, 4, '3', '2000000.00'),
(4, 1, 'AU64542', '343434', 1, 3, '1', '100000.00'),
(5, 2, 'PI23243532', '434342', 1, 5, '4', '1000000.00'),
(6, 2, 'LU4354522', '6565454', 1, 4, '', '2000000.00'),
(7, 3, '', '', 1000, 2, '1', '10000.00'),
(8, 5, '', '', 5, 4, '', '0.00'),
(9, 6, '', '', 5, 5, '', '0.00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `brand`
--

INSERT INTO `brand` (`brand_id`, `brand_name`) VALUES
(1, 'AUDI'),
(2, 'BMW'),
(3, 'C&C'),
(4, 'CHENGLONG'),
(5, 'CHEVROLET'),
(6, 'DAEHAN'),
(7, 'DAEWOO'),
(8, 'DAMSEL'),
(9, 'ĐẦU KÉO MỸ'),
(10, 'DONGBEN'),
(11, 'DONGFENG'),
(12, 'FAW'),
(13, 'FORCIA'),
(14, 'FORD'),
(15, 'FUSIN'),
(16, 'FUSO'),
(17, 'HINO'),
(18, 'HOKA'),
(19, 'HONDA'),
(20, 'HOWO'),
(21, 'HYUNDAI'),
(22, 'ISUZU'),
(23, 'JAC'),
(24, 'KAMAZ'),
(25, 'KIA'),
(26, 'LAMBORGHINI'),
(27, 'LAND ROVER'),
(28, 'LEXUS'),
(29, 'MAZDA'),
(30, 'MERCEDES-BENZ'),
(31, 'MITSUBISHI'),
(32, 'NISSAN'),
(33, 'PEUGEOT'),
(34, 'PORSCHE'),
(35, 'SHACMAN'),
(36, 'SINOTRUK'),
(37, 'SUZUKI'),
(38, 'SYM'),
(39, 'T&T MOTOR'),
(40, 'THACO'),
(41, 'TMT MOTORS'),
(42, 'TOYOTA'),
(43, 'VEAM'),
(44, 'VINAXUKI'),
(45, 'VOLKSWAGEN'),
(46, 'VOLVO');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `checking_cost`
--

CREATE TABLE `checking_cost` (
  `checking_cost_id` int(11) NOT NULL,
  `checking_cost_date` int(11) DEFAULT NULL,
  `checking_cost_code` varchar(20) DEFAULT NULL,
  `checking_cost_customer` int(11) DEFAULT NULL,
  `checking_cost_vat` decimal(10,0) DEFAULT NULL,
  `checking_cost_price` decimal(10,0) DEFAULT NULL,
  `checking_cost_start_date` int(11) DEFAULT NULL,
  `checking_cost_end_date` int(11) DEFAULT NULL,
  `checking_cost_vehicle` varchar(200) DEFAULT NULL,
  `checking_cost_romooc` varchar(200) DEFAULT NULL,
  `checking_cost_comment` varchar(255) DEFAULT NULL,
  `checking_cost_total_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contact_person`
--

CREATE TABLE `contact_person` (
  `contact_person_id` int(11) NOT NULL,
  `contact_person_name` varchar(50) DEFAULT NULL,
  `contact_person_address` varchar(100) DEFAULT NULL,
  `contact_person_phone` varchar(20) DEFAULT NULL,
  `contact_person_mobile` varchar(20) DEFAULT NULL,
  `contact_person_birthday` int(11) DEFAULT NULL,
  `contact_person_email` varchar(50) DEFAULT NULL,
  `contact_person_position` varchar(20) DEFAULT NULL,
  `contact_person_department` varchar(20) DEFAULT NULL,
  `contact_person_customer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `contact_person`
--

INSERT INTO `contact_person` (`contact_person_id`, `contact_person_name`, `contact_person_address`, `contact_person_phone`, `contact_person_mobile`, `contact_person_birthday`, `contact_person_email`, `contact_person_position`, `contact_person_department`, `contact_person_customer`) VALUES
(1, 'Ngô Tôn', 'BH-DN', '0902 085 911', '0902 085 911', 1528650000, 'ngoton007@yahoo.com', 'IT', 'IT', 1),
(2, 'Mr A', '', '', '0323 252 131', 1529341200, 'a@samsung.com', 'Sale', 'Sale', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coordinate`
--

CREATE TABLE `coordinate` (
  `coordinate_id` int(11) NOT NULL,
  `coordinate_date` int(11) DEFAULT NULL,
  `coordinate_create_user` int(11) DEFAULT NULL,
  `coordinate_update_user` int(11) DEFAULT NULL,
  `coordinate_code` varchar(20) DEFAULT NULL,
  `coordinate_vehicle` int(11) DEFAULT NULL,
  `coordinate_booking` int(11) DEFAULT NULL,
  `coordinate_booking_number` varchar(100) DEFAULT NULL,
  `coordinate_type` int(11) DEFAULT NULL,
  `coordinate_number` float DEFAULT NULL,
  `coordinate_unit` int(11) DEFAULT NULL,
  `coordinate_place` int(11) DEFAULT NULL,
  `coordinate_comment` text,
  `coordinate_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `coordinate`
--

INSERT INTO `coordinate` (`coordinate_id`, `coordinate_date`, `coordinate_create_user`, `coordinate_update_user`, `coordinate_code`, `coordinate_vehicle`, `coordinate_booking`, `coordinate_booking_number`, `coordinate_type`, `coordinate_number`, `coordinate_unit`, `coordinate_place`, `coordinate_comment`, `coordinate_status`) VALUES
(1, 1541782800, 1, NULL, 'DX01', 2, 0, '1676', 1, 1, 6, 4, '', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cost_list`
--

CREATE TABLE `cost_list` (
  `cost_list_id` int(11) NOT NULL,
  `cost_list_code` varchar(20) DEFAULT NULL,
  `cost_list_name` varchar(50) DEFAULT NULL,
  `cost_list_type` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `cost_list`
--

INSERT INTO `cost_list` (`cost_list_id`, `cost_list_code`, `cost_list_name`, `cost_list_type`) VALUES
(1, 'NC', 'Nâng cont', 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cost_type`
--

CREATE TABLE `cost_type` (
  `cost_type_id` int(11) NOT NULL,
  `cost_type_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `cost_type`
--

INSERT INTO `cost_type` (`cost_type_id`, `cost_type_name`) VALUES
(1, 'Hành chính'),
(2, 'Mua hàng'),
(3, 'Nhân sự'),
(4, 'Sửa chữa, Bảo trì'),
(5, 'Nhiên liệu'),
(6, 'Cầu đường'),
(7, 'Tạm ứng'),
(8, 'Chi hộ'),
(9, 'Hoa hồng'),
(10, 'Công an'),
(11, 'Bồi dưỡng'),
(12, 'Kho bãi'),
(13, 'Hải quan'),
(14, 'Khác');

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
-- Cấu trúc bảng cho bảng `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_company` varchar(200) DEFAULT NULL,
  `customer_mst` varchar(20) DEFAULT NULL,
  `customer_address` varchar(255) DEFAULT NULL,
  `customer_province` int(11) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_mobile` varchar(20) DEFAULT NULL,
  `customer_email` varchar(50) DEFAULT NULL,
  `customer_bank_account` int(11) DEFAULT NULL,
  `customer_bank_name` varchar(50) DEFAULT NULL,
  `customer_bank_branch` varchar(50) DEFAULT NULL,
  `customer_sub` varchar(200) DEFAULT NULL,
  `customer_type` int(11) DEFAULT NULL COMMENT '1:Khách hàng | 2:Đối tác | 3:Cá nhân'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_code`, `customer_name`, `customer_company`, `customer_mst`, `customer_address`, `customer_province`, `customer_phone`, `customer_mobile`, `customer_email`, `customer_bank_account`, `customer_bank_name`, `customer_bank_branch`, `customer_sub`, `customer_type`) VALUES
(1, 'NCC01', 'Việt Trade', 'Công ty TNHH Việt Trade', '3603295302', 'Số 545, Tổ 10, Ấp Hương Phước, X. Phước Tân, TP. Biên Hoà, Đồng Nai', 19, '0251 393 7677', '0902 085 911', 'it@viet-trade.org', 23, 'ACB', 'Đồng Nai', '1,2', 2),
(2, 'KH01', 'Samsung', 'Công ty TNHH Samsung Vina', '3603422324', 'Q9', 31, '0283 943 231', '', '', 0, '', '', '3', 1),
(3, 'KH02', 'Pepsi', 'Công ty TNHH Pepsico', '', '', 31, '', '', '', 0, '', '', '4,5', 1),
(4, 'NCC02', 'Cảng Cát Lái', 'Công ty cổ phần Cát Lái', '', '', 31, '', '', '', 0, '', '', '', 2),
(5, 'NCC03', 'Cục quản lý đường bộ', '', '', '', 31, '', '', '', 0, '', '', '', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_sub`
--

CREATE TABLE `customer_sub` (
  `customer_sub_id` int(11) NOT NULL,
  `customer_sub_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `customer_sub`
--

INSERT INTO `customer_sub` (`customer_sub_id`, `customer_sub_name`) VALUES
(1, 'Lốp xe'),
(2, 'Vỏ xe'),
(3, 'Tivi'),
(4, 'Bia'),
(5, 'Nước ngọt');

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
-- Cấu trúc bảng cho bảng `dispatch`
--

CREATE TABLE `dispatch` (
  `dispatch_id` int(11) NOT NULL,
  `dispatch_date` int(11) DEFAULT NULL,
  `dispatch_code` varchar(20) DEFAULT NULL,
  `dispatch_shipment_temp` int(11) DEFAULT NULL,
  `dispatch_customer` int(11) DEFAULT NULL,
  `dispatch_booking_type` int(11) DEFAULT NULL,
  `dispatch_booking` int(11) DEFAULT NULL,
  `dispatch_booking_detail` int(11) DEFAULT NULL,
  `dispatch_booking_detail_number` float DEFAULT NULL,
  `dispatch_place_from` int(11) DEFAULT NULL,
  `dispatch_place_to` int(11) DEFAULT NULL,
  `dispatch_vehicle` int(11) DEFAULT NULL,
  `dispatch_romooc` int(11) DEFAULT NULL,
  `dispatch_staff` int(11) DEFAULT NULL,
  `dispatch_start_date` int(11) DEFAULT NULL,
  `dispatch_end_date` int(11) DEFAULT NULL,
  `dispatch_port_from` int(11) DEFAULT NULL,
  `dispatch_port_to` int(11) DEFAULT NULL,
  `dispatch_ton` float DEFAULT NULL,
  `dispatch_unit` int(11) DEFAULT NULL,
  `dispatch_comment` text NOT NULL,
  `dispatch_create_user` int(11) DEFAULT NULL,
  `dispatch_update_user` int(11) DEFAULT NULL,
  `dispatch_status` int(11) DEFAULT NULL,
  `dispatch_shipment_number` int(11) DEFAULT NULL,
  `dispatch_shipment_temp_sub` int(11) DEFAULT NULL,
  `dispatch_customer_sub` int(11) DEFAULT NULL,
  `dispatch_booking_type_sub` int(11) DEFAULT NULL,
  `dispatch_booking_sub` int(11) DEFAULT NULL,
  `dispatch_booking_detail_sub` int(11) DEFAULT NULL,
  `dispatch_booking_detail_number_sub` float DEFAULT NULL,
  `dispatch_place_from_sub` int(11) DEFAULT NULL,
  `dispatch_place_to_sub` int(11) DEFAULT NULL,
  `dispatch_start_date_sub` int(11) DEFAULT NULL,
  `dispatch_end_date_sub` int(11) DEFAULT NULL,
  `dispatch_port_from_sub` int(11) DEFAULT NULL,
  `dispatch_port_to_sub` int(11) DEFAULT NULL,
  `dispatch_ton_sub` float DEFAULT NULL,
  `dispatch_unit_sub` int(11) DEFAULT NULL,
  `dispatch_comment_sub` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `dispatch`
--

INSERT INTO `dispatch` (`dispatch_id`, `dispatch_date`, `dispatch_code`, `dispatch_shipment_temp`, `dispatch_customer`, `dispatch_booking_type`, `dispatch_booking`, `dispatch_booking_detail`, `dispatch_booking_detail_number`, `dispatch_place_from`, `dispatch_place_to`, `dispatch_vehicle`, `dispatch_romooc`, `dispatch_staff`, `dispatch_start_date`, `dispatch_end_date`, `dispatch_port_from`, `dispatch_port_to`, `dispatch_ton`, `dispatch_unit`, `dispatch_comment`, `dispatch_create_user`, `dispatch_update_user`, `dispatch_status`, `dispatch_shipment_number`, `dispatch_shipment_temp_sub`, `dispatch_customer_sub`, `dispatch_booking_type_sub`, `dispatch_booking_sub`, `dispatch_booking_detail_sub`, `dispatch_booking_detail_number_sub`, `dispatch_place_from_sub`, `dispatch_place_to_sub`, `dispatch_start_date_sub`, `dispatch_end_date_sub`, `dispatch_port_from_sub`, `dispatch_port_to_sub`, `dispatch_ton_sub`, `dispatch_unit_sub`, `dispatch_comment_sub`) VALUES
(1, 1529341200, 'DX01', 2, 2, 2, 1, NULL, NULL, 1, 2, 2, 1, 1, 1529254800, 1529427600, 0, 3, 2, 3, 'Hàng xuất', 1, 1, 1, 1, 3, 3, 1, 2, NULL, NULL, 2, 1, 1529254800, 1530291600, 3, 3, 40, 2, 'Lô phụ'),
(8, 1529946000, 'DX03', 3, 3, 1, 2, NULL, NULL, 2, 3, 2, 1, 1, 1529254800, 1530291600, 0, 0, 0, 3, '', 1, 1, NULL, NULL, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 6, ''),
(9, 1529946000, 'DX04', 3, 3, 1, 2, NULL, NULL, 2, 1, 2, 1, 1, 1529254800, 1530291600, 0, 0, 1, 4, '', 1, 1, 1, 1, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 6, ''),
(10, 1531674000, 'DX05', 0, 2, 2, 5, NULL, NULL, 3, 3, 1, 0, 0, 1531674000, 1531674000, 0, 0, 0, 6, '', 1, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 6, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `driver`
--

CREATE TABLE `driver` (
  `driver_id` int(11) NOT NULL,
  `driver_vehicle` int(11) DEFAULT NULL,
  `driver_staff` int(11) DEFAULT NULL,
  `driver_start_date` int(11) DEFAULT NULL,
  `driver_end_date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `driver`
--

INSERT INTO `driver` (`driver_id`, `driver_vehicle`, `driver_staff`, `driver_start_date`, `driver_end_date`) VALUES
(1, 2, 1, 1527786000, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `export_stock`
--

CREATE TABLE `export_stock` (
  `export_stock_id` int(11) NOT NULL,
  `export_stock_code` varchar(50) DEFAULT NULL,
  `export_stock_date` int(11) DEFAULT NULL,
  `export_stock_total` float DEFAULT NULL,
  `export_stock_price` decimal(10,0) DEFAULT NULL,
  `export_stock_vat` decimal(10,0) DEFAULT NULL,
  `export_stock_comment` varchar(255) DEFAULT NULL,
  `export_stock_house` int(11) DEFAULT NULL,
  `export_stock_create_user` int(11) DEFAULT NULL,
  `export_stock_update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `export_stock`
--

INSERT INTO `export_stock` (`export_stock_id`, `export_stock_code`, `export_stock_date`, `export_stock_total`, `export_stock_price`, `export_stock_vat`, `export_stock_comment`, `export_stock_house`, `export_stock_create_user`, `export_stock_update_user`) VALUES
(2, 'PNK01', 1531674000, 2, '2000000', '200000', 'XK', 0, 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gas`
--

CREATE TABLE `gas` (
  `gas_id` int(11) NOT NULL,
  `gas_date` int(11) DEFAULT NULL,
  `gas_vehicle` int(11) DEFAULT NULL,
  `gas_km` int(11) DEFAULT NULL,
  `gas_km_gps` int(11) DEFAULT NULL,
  `gas_lit` float DEFAULT NULL,
  `gas_create_user` int(11) DEFAULT NULL,
  `gas_update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `gas`
--

INSERT INTO `gas` (`gas_id`, `gas_date`, `gas_vehicle`, `gas_km`, `gas_km_gps`, `gas_lit`, `gas_create_user`, `gas_update_user`) VALUES
(1, 1530061560, 2, 1000, 1200, 50, 1, 1),
(2, 1530205560, 2, 2000, 2000, 160, 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `house`
--

CREATE TABLE `house` (
  `house_id` int(11) NOT NULL,
  `house_code` varchar(20) DEFAULT NULL,
  `house_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `house`
--

INSERT INTO `house` (`house_id`, `house_code`, `house_name`) VALUES
(1, 'LX', 'Lốp xe');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_stock`
--

CREATE TABLE `import_stock` (
  `import_stock_id` int(11) NOT NULL,
  `import_stock_code` varchar(50) DEFAULT NULL,
  `import_stock_date` int(11) DEFAULT NULL,
  `import_stock_total` float DEFAULT NULL,
  `import_stock_price` decimal(10,0) DEFAULT NULL,
  `import_stock_vat` decimal(10,0) DEFAULT NULL,
  `import_stock_comment` varchar(255) DEFAULT NULL,
  `import_stock_invoice_number` varchar(20) DEFAULT NULL,
  `import_stock_invoice_date` int(11) DEFAULT NULL,
  `import_stock_customer` int(11) DEFAULT NULL,
  `import_stock_deliver` varchar(50) DEFAULT NULL,
  `import_stock_deliver_address` varchar(200) DEFAULT NULL,
  `import_stock_house` int(11) DEFAULT NULL,
  `import_stock_create_user` int(11) DEFAULT NULL,
  `import_stock_update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `import_stock`
--

INSERT INTO `import_stock` (`import_stock_id`, `import_stock_code`, `import_stock_date`, `import_stock_total`, `import_stock_price`, `import_stock_vat`, `import_stock_comment`, `import_stock_invoice_number`, `import_stock_invoice_date`, `import_stock_customer`, `import_stock_deliver`, `import_stock_deliver_address`, `import_stock_house`, `import_stock_create_user`, `import_stock_update_user`) VALUES
(1, 'PNK01', 1531242000, 5, '5000000', '300000', 'Nhập kho', '000423', 1531242000, 1, 'A', 'BH', 1, 1, 1),
(2, 'PNK02', 1531242000, 22, '22000000', '10000', 'a', '745', 1531242000, 1, 'a', 'a', 1, 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_stock_cost`
--

CREATE TABLE `import_stock_cost` (
  `import_stock_cost_id` int(11) NOT NULL,
  `import_stock_cost_list` int(11) DEFAULT NULL,
  `import_stock_cost_money` decimal(10,0) DEFAULT NULL,
  `import_stock_cost_money_vat` int(11) DEFAULT NULL,
  `import_stock_cost_comment` text,
  `import_stock_cost_customer` int(11) DEFAULT NULL,
  `import_stock_cost_invoice` varchar(20) DEFAULT NULL,
  `import_stock_cost_invoice_date` int(11) DEFAULT NULL,
  `import_stock` int(11) DEFAULT NULL,
  `import_stock_cost_create_user` int(11) DEFAULT NULL,
  `import_stock_cost_update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `import_stock_cost`
--

INSERT INTO `import_stock_cost` (`import_stock_cost_id`, `import_stock_cost_list`, `import_stock_cost_money`, `import_stock_cost_money_vat`, `import_stock_cost_comment`, `import_stock_cost_customer`, `import_stock_cost_invoice`, `import_stock_cost_invoice_date`, `import_stock`, `import_stock_cost_create_user`, `import_stock_cost_update_user`) VALUES
(1, 1, '200000', 1, 'Nâng', 4, '000534', 1531328400, 1, 1, 1),
(2, 1, '28888', 1, 'aa', 3, '655', 1531242000, 2, 1, 1);

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
-- Cấu trúc bảng cho bảng `insurance_cost`
--

CREATE TABLE `insurance_cost` (
  `insurance_cost_id` int(11) NOT NULL,
  `insurance_cost_date` int(11) DEFAULT NULL,
  `insurance_cost_code` varchar(20) DEFAULT NULL,
  `insurance_cost_customer` int(11) DEFAULT NULL,
  `insurance_cost_vat` decimal(10,0) DEFAULT NULL,
  `insurance_cost_price` decimal(10,0) DEFAULT NULL,
  `insurance_cost_start_date` int(11) DEFAULT NULL,
  `insurance_cost_end_date` int(11) DEFAULT NULL,
  `insurance_cost_vehicle` varchar(200) DEFAULT NULL,
  `insurance_cost_romooc` varchar(200) DEFAULT NULL,
  `insurance_cost_comment` varchar(255) DEFAULT NULL,
  `insurance_cost_total_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lift`
--

CREATE TABLE `lift` (
  `lift_id` int(11) NOT NULL,
  `lift_on` decimal(10,0) DEFAULT NULL,
  `lift_off` decimal(10,0) DEFAULT NULL,
  `lift_on_null` decimal(10,0) DEFAULT NULL,
  `lift_off_null` decimal(10,0) DEFAULT NULL,
  `lift_unit` int(11) DEFAULT NULL,
  `lift_place` int(11) DEFAULT NULL,
  `lift_start_date` int(11) DEFAULT NULL,
  `lift_end_date` int(11) DEFAULT NULL,
  `lift_customer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `lift`
--

INSERT INTO `lift` (`lift_id`, `lift_on`, `lift_off`, `lift_on_null`, `lift_off_null`, `lift_unit`, `lift_place`, `lift_start_date`, `lift_end_date`, `lift_customer`) VALUES
(1, '590000', '465000', '400000', '350000', 3, 3, 1527786000, NULL, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `oil`
--

CREATE TABLE `oil` (
  `oil_id` int(11) NOT NULL,
  `oil_way` varchar(20) DEFAULT NULL,
  `oil_lit` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `oil`
--

INSERT INTO `oil` (`oil_id`, `oil_way`, `oil_lit`) VALUES
(1, 'Rỗng', 0.32),
(2, 'Lên núi', 0.45);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `place`
--

CREATE TABLE `place` (
  `place_id` int(11) NOT NULL,
  `place_code` varchar(20) DEFAULT NULL,
  `place_name` varchar(50) DEFAULT NULL,
  `place_province` int(11) DEFAULT NULL,
  `place_lat` decimal(10,6) DEFAULT NULL,
  `place_long` decimal(10,6) DEFAULT NULL,
  `place_port` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `place`
--

INSERT INTO `place` (`place_id`, `place_code`, `place_name`, `place_province`, `place_lat`, `place_long`, `place_port`) VALUES
(1, 'SS', 'Samsung', 31, '10.773435', '106.703876', NULL),
(2, 'PEP', 'PEPSI', 19, '11.068631', '107.167598', NULL),
(3, 'CL', 'Cảng Cát Lái', 31, '10.757996', '106.788932', 1),
(4, 'AMATA', 'AMATA', 19, '10.949585', '106.871760', 0),
(5, 'VSIP1', 'VSIP1', 8, '10.924517', '106.713651', 0),
(6, 'AV', 'ÂU VIỆT', 7, '10.182521', '106.331657', 0);

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
(1, 'GD', 'Giám đốc'),
(2, 'PGD', 'Phó giám đốc'),
(3, 'TP', 'Trưởng phòng');

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
-- Cấu trúc bảng cho bảng `repair`
--

CREATE TABLE `repair` (
  `repair_id` int(11) NOT NULL,
  `repair_date` int(11) DEFAULT NULL,
  `repair_number` varchar(20) DEFAULT NULL,
  `repair_code` int(11) DEFAULT NULL,
  `repair_vehicle` int(11) DEFAULT NULL,
  `repair_romooc` int(11) DEFAULT NULL,
  `repair_price` decimal(10,0) DEFAULT NULL,
  `repair_staff` int(11) DEFAULT NULL,
  `repair_create_user` int(11) DEFAULT NULL,
  `repair_update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `repair`
--

INSERT INTO `repair` (`repair_id`, `repair_date`, `repair_number`, `repair_code`, `repair_vehicle`, `repair_romooc`, `repair_price`, `repair_staff`, `repair_create_user`, `repair_update_user`) VALUES
(1, 1530896400, 'PSC001', 2, 2, 0, '250000', 1, 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `repair_code`
--

CREATE TABLE `repair_code` (
  `repair_code_id` int(11) NOT NULL,
  `repair_code_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `repair_code`
--

INSERT INTO `repair_code` (`repair_code_id`, `repair_code_name`) VALUES
(1, 'THAY NHỚT MÁY'),
(2, 'THAY NHỚT HỘP SỐ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `repair_list`
--

CREATE TABLE `repair_list` (
  `repair_list_id` int(11) NOT NULL,
  `repair` int(11) DEFAULT NULL,
  `repair_list_comment` varchar(200) DEFAULT NULL,
  `repair_list_price` decimal(10,0) DEFAULT NULL,
  `repair_list_end_date` int(11) DEFAULT NULL,
  `repair_list_date` int(11) DEFAULT NULL,
  `repair_list_vehicle` int(11) DEFAULT NULL,
  `repair_list_romooc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `repair_list`
--

INSERT INTO `repair_list` (`repair_list_id`, `repair`, `repair_list_comment`, `repair_list_price`, `repair_list_end_date`, `repair_list_date`, `repair_list_vehicle`, `repair_list_romooc`) VALUES
(1, 1, 'Thay nhớt', '200000', 1530896400, 1530896400, 2, 0),
(2, 1, 'Rửa', '50000', 1531501200, 1530896400, 2, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `road`
--

CREATE TABLE `road` (
  `road_id` int(11) NOT NULL,
  `road_place_from` int(11) DEFAULT NULL,
  `road_place_to` int(11) DEFAULT NULL,
  `road_route_from` int(11) DEFAULT NULL,
  `road_route_to` int(11) DEFAULT NULL,
  `road_time` float DEFAULT NULL,
  `road_km` float DEFAULT NULL,
  `road_oil` float DEFAULT NULL,
  `road_oil_ton` float DEFAULT NULL,
  `road_bridge` decimal(10,0) DEFAULT NULL,
  `road_police` decimal(10,0) DEFAULT NULL,
  `road_tire` decimal(10,0) DEFAULT NULL,
  `road_over` decimal(10,0) DEFAULT NULL,
  `road_add` decimal(10,0) DEFAULT NULL,
  `road_salary` decimal(10,0) DEFAULT NULL,
  `road_start_date` int(11) DEFAULT NULL,
  `road_end_date` int(11) DEFAULT NULL,
  `road_salary_import` decimal(10,0) DEFAULT NULL,
  `road_salary_export` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `road`
--

INSERT INTO `road` (`road_id`, `road_place_from`, `road_place_to`, `road_route_from`, `road_route_to`, `road_time`, `road_km`, `road_oil`, `road_oil_ton`, `road_bridge`, `road_police`, `road_tire`, `road_over`, `road_add`, `road_salary`, `road_start_date`, `road_end_date`, `road_salary_import`, `road_salary_export`) VALUES
(1, 1, 2, 2, 3, 1.5, 35.55, 13.326, 0.5, '25000', '200000', '150000', '20000', '200000', '500000', 1527786000, NULL, NULL, NULL),
(2, 1, 2, 1, 2, 2, 40, 15.4, 5, '35000', '100000', '200000', '50000', '500000', '200000', 1527786000, 0, NULL, NULL),
(3, 2, 1, 1, 2, 2.5, 37.5, 16.55, 2, '20000', '400000', '200000', '10000', '600000', '500000', 1525107600, 1527699600, NULL, NULL),
(4, 2, 1, 1, 2, 3, 17, 7.39, 5, '10000', '300000', '200000', '50000', '100000', '200000', 1527786000, NULL, NULL, NULL),
(5, 3, 1, 3, 3, 0, 50, 16, 0, '0', '0', '0', '0', '1000000', '200', 1531674000, 1531674000, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `road_cost`
--

CREATE TABLE `road_cost` (
  `road_cost_id` int(11) NOT NULL,
  `road_cost_date` int(11) DEFAULT NULL,
  `road_cost_code` varchar(20) DEFAULT NULL,
  `road_cost_customer` int(11) DEFAULT NULL,
  `road_cost_vat` decimal(10,0) DEFAULT NULL,
  `road_cost_price` decimal(10,0) DEFAULT NULL,
  `road_cost_start_date` int(11) DEFAULT NULL,
  `road_cost_end_date` int(11) DEFAULT NULL,
  `road_cost_vehicle` varchar(200) DEFAULT NULL,
  `road_cost_romooc` varchar(200) DEFAULT NULL,
  `road_cost_comment` varchar(255) DEFAULT NULL,
  `road_cost_total_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `road_cost`
--

INSERT INTO `road_cost` (`road_cost_id`, `road_cost_date`, `road_cost_code`, `road_cost_customer`, `road_cost_vat`, `road_cost_price`, `road_cost_start_date`, `road_cost_end_date`, `road_cost_vehicle`, `road_cost_romooc`, `road_cost_comment`, `road_cost_total_number`) VALUES
(1, 1530205200, '0000054', 5, '10000', '100000', 1530205200, 1561741200, '2', '1', 'Đường bộ', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `road_oil`
--

CREATE TABLE `road_oil` (
  `road_oil_id` int(11) NOT NULL,
  `road` int(11) DEFAULT NULL,
  `road_oil_way` int(11) DEFAULT NULL,
  `road_oil_km` float DEFAULT NULL,
  `road_oil_lit` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `road_oil`
--

INSERT INTO `road_oil` (`road_oil_id`, `road`, `road_oil_way`, `road_oil_km`, `road_oil_lit`) VALUES
(1, 1, 1, 20.55, 6.576),
(2, 1, 2, 15, 6.75),
(7, 2, 1, 20, 6.4),
(8, 2, 2, 20, 9),
(9, 3, 1, 2.5, 0.8),
(10, 3, 2, 35, 15.75),
(11, 4, 1, 2, 0.64),
(12, 4, 2, 15, 6.75),
(13, 5, 1, 50, 16);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `road_toll`
--

CREATE TABLE `road_toll` (
  `road_toll_id` int(11) NOT NULL,
  `road` int(11) DEFAULT NULL,
  `toll` int(11) DEFAULT NULL,
  `road_toll_money` decimal(10,0) DEFAULT NULL,
  `road_toll_vat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `road_toll`
--

INSERT INTO `road_toll` (`road_toll_id`, `road`, `toll`, `road_toll_money`, `road_toll_vat`) VALUES
(1, 1, 1, '10000', 1),
(2, 1, 2, '15000', 0),
(5, 2, 1, '15000', 0),
(6, 2, 2, '20000', 1),
(7, 3, 2, '20000', 1),
(8, 4, 1, '10000', 1);

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
-- Cấu trúc bảng cho bảng `romooc`
--

CREATE TABLE `romooc` (
  `romooc_id` int(11) NOT NULL,
  `romooc_number` varchar(20) DEFAULT NULL,
  `romooc_license` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `romooc`
--

INSERT INTO `romooc` (`romooc_id`, `romooc_number`, `romooc_license`) VALUES
(1, '51C-129.35', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `route`
--

CREATE TABLE `route` (
  `route_id` int(11) NOT NULL,
  `route_name` varchar(50) DEFAULT NULL,
  `route_province` int(11) DEFAULT NULL,
  `route_lat` decimal(10,6) DEFAULT NULL,
  `route_long` decimal(10,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `route`
--

INSERT INTO `route` (`route_id`, `route_name`, `route_province`, `route_lat`, `route_long`) VALUES
(1, 'Ngã 3 Vũng Tàu', 19, '10.905751', '106.848631'),
(2, 'Cảng Cát Lái', 31, '10.757996', '106.788932'),
(3, 'Cảng Cái Mép', 2, '10.538545', '107.031798');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipdeposit`
--

CREATE TABLE `shipdeposit` (
  `shipdeposit_id` int(11) NOT NULL,
  `shipdeposit_shipping` int(11) DEFAULT NULL,
  `shipdeposit_unit` int(11) DEFAULT NULL,
  `shipdeposit_money` decimal(10,0) DEFAULT NULL,
  `shipdeposit_start_date` int(11) DEFAULT NULL,
  `shipdeposit_end_date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `shipdeposit`
--

INSERT INTO `shipdeposit` (`shipdeposit_id`, `shipdeposit_shipping`, `shipdeposit_unit`, `shipdeposit_money`, `shipdeposit_start_date`, `shipdeposit_end_date`) VALUES
(1, 1, 4, '2000000', 1529946000, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipment`
--

CREATE TABLE `shipment` (
  `shipment_id` int(11) NOT NULL,
  `shipment_date` int(11) DEFAULT NULL,
  `shipment_dispatch` int(11) DEFAULT NULL,
  `shipment_customer` int(11) DEFAULT NULL,
  `shipment_type` int(11) DEFAULT NULL,
  `shipment_do` varchar(100) DEFAULT NULL,
  `shipment_vehicle` int(11) DEFAULT NULL,
  `shipment_romooc` int(11) DEFAULT NULL,
  `shipment_staff` int(11) DEFAULT NULL,
  `shipment_booking` int(11) DEFAULT NULL,
  `shipment_booking_detail` int(11) DEFAULT NULL,
  `shipment_container` varchar(20) DEFAULT NULL,
  `shipment_ton_receive` float DEFAULT NULL,
  `shipment_ton` float DEFAULT NULL,
  `shipment_unit` int(11) DEFAULT NULL,
  `shipment_place_from` int(11) DEFAULT NULL,
  `shipment_place_to` int(11) DEFAULT NULL,
  `shipment_start_date` int(11) DEFAULT NULL,
  `shipment_end_date` int(11) DEFAULT NULL,
  `shipment_port_from` int(11) DEFAULT NULL,
  `shipment_port_to` int(11) DEFAULT NULL,
  `shipment_comment` varchar(255) DEFAULT NULL,
  `shipment_create_user` int(11) DEFAULT NULL,
  `shipment_update_user` int(11) DEFAULT NULL,
  `shipment_status` int(11) DEFAULT NULL,
  `shipment_lock` int(11) DEFAULT NULL,
  `shipment_price` decimal(14,2) DEFAULT NULL,
  `shipment_cost` decimal(14,2) DEFAULT NULL,
  `shipment_sub` int(11) DEFAULT NULL,
  `shipment_road` text,
  `shipment_cost_detail` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `shipment`
--

INSERT INTO `shipment` (`shipment_id`, `shipment_date`, `shipment_dispatch`, `shipment_customer`, `shipment_type`, `shipment_do`, `shipment_vehicle`, `shipment_romooc`, `shipment_staff`, `shipment_booking`, `shipment_booking_detail`, `shipment_container`, `shipment_ton_receive`, `shipment_ton`, `shipment_unit`, `shipment_place_from`, `shipment_place_to`, `shipment_start_date`, `shipment_end_date`, `shipment_port_from`, `shipment_port_to`, `shipment_comment`, `shipment_create_user`, `shipment_update_user`, `shipment_status`, `shipment_lock`, `shipment_price`, `shipment_cost`, `shipment_sub`, `shipment_road`, `shipment_cost_detail`) VALUES
(7, 1529341200, 1, 2, 2, '', 2, 1, 1, 1, 4, 'AU64542', 1, 1, 3, 1, 2, 1529255160, 1529427960, 0, 3, 'Hàng xuất', 1, 1, NULL, NULL, '0.00', '1840000.00', 0, '1,2', '{\"shipment_cost_lift_on\":\"0\",\"shipment_cost_lift_off\":\"0\",\"shipment_cost_lift_on_null\":\"0\",\"shipment_cost_lift_off_null\":\"350000\",\"shipment_cost_deposit\":\"0\",\"shipment_cost_clean\":\"0\",\"shipment_cost_trans\":\"0\",\"shipment_cost_weight\":\"0\",\"shipment_cost_document\":\"0\",\"shipment_cost_toll\":\"60000\",\"shipment_cost_cont\":\"480000\",\"shipment_cost_ton\":\"150000\",\"shipment_cost_police\":\"300000\",\"shipment_cost_tire\":\"350000\",\"shipment_cost_release\":\"50000\",\"shipment_cost_park\":\"100000\"}'),
(10, 1530118800, 9, 3, 1, '', 2, 1, 1, 2, 5, 'PI23243532', 1, 1, 5, 2, 1, 1529255160, 1530291960, 0, 0, '', 1, 1, NULL, NULL, '0.00', '1140000.00', 0, '4', '{\"shipment_cost_lift_on\":\"0\",\"shipment_cost_lift_off\":\"0\",\"shipment_cost_lift_on_null\":\"0\",\"shipment_cost_lift_off_null\":\"0\",\"shipment_cost_deposit\":\"0\",\"shipment_cost_clean\":\"0\",\"shipment_cost_trans\":\"0\",\"shipment_cost_weight\":\"0\",\"shipment_cost_document\":\"0\",\"shipment_cost_toll\":\"10000\",\"shipment_cost_cont\":\"480000\",\"shipment_cost_ton\":\"150000\",\"shipment_cost_police\":\"300000\",\"shipment_cost_tire\":\"200000\",\"shipment_cost_release\":\"0\",\"shipment_cost_park\":\"0\"}');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipment_cost`
--

CREATE TABLE `shipment_cost` (
  `shipment_cost_id` int(11) NOT NULL,
  `shipment_cost_list` int(11) DEFAULT NULL,
  `shipment_cost_money` decimal(10,0) DEFAULT NULL,
  `shipment_cost_money_vat` int(11) DEFAULT NULL,
  `shipment_cost_comment` text,
  `shipment_cost_customer` int(11) DEFAULT NULL,
  `shipment_cost_invoice` varchar(20) DEFAULT NULL,
  `shipment_cost_invoice_date` int(11) DEFAULT NULL,
  `shipment_cost_shipment` int(11) DEFAULT NULL,
  `shipment_cost_create_user` int(11) DEFAULT NULL,
  `shipment_cost_update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `shipment_cost`
--

INSERT INTO `shipment_cost` (`shipment_cost_id`, `shipment_cost_list`, `shipment_cost_money`, `shipment_cost_money_vat`, `shipment_cost_comment`, `shipment_cost_customer`, `shipment_cost_invoice`, `shipment_cost_invoice_date`, `shipment_cost_shipment`, `shipment_cost_create_user`, `shipment_cost_update_user`) VALUES
(1, 1, '500000', 1, 'Nâng hạ', 1, '0045247', 1529600400, 4, 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipment_temp`
--

CREATE TABLE `shipment_temp` (
  `shipment_temp_id` int(11) NOT NULL,
  `shipment_temp_date` int(11) DEFAULT NULL,
  `shipment_temp_owner` int(11) DEFAULT NULL,
  `shipment_temp_booking` int(11) DEFAULT NULL,
  `shipment_temp_status` int(11) DEFAULT NULL,
  `shipment_temp_ton` float DEFAULT NULL,
  `shipment_temp_ton_use` float DEFAULT NULL,
  `shipment_temp_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `shipment_temp`
--

INSERT INTO `shipment_temp` (`shipment_temp_id`, `shipment_temp_date`, `shipment_temp_owner`, `shipment_temp_booking`, `shipment_temp_status`, `shipment_temp_ton`, `shipment_temp_ton_use`, `shipment_temp_number`) VALUES
(2, 1529427600, 1, 1, 1, 3, 2, 3),
(3, 1529254800, 1, 2, 1, 1, NULL, 1),
(4, 1531674000, 1, 5, 0, 5, NULL, 0);

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
(2, 'CMA-CGM', 155),
(3, 'UASC', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `spare_drap`
--

CREATE TABLE `spare_drap` (
  `spare_drap_id` int(11) NOT NULL,
  `spare_part` int(11) DEFAULT NULL,
  `spare_part_number` float DEFAULT NULL,
  `spare_vehicle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `spare_part`
--

CREATE TABLE `spare_part` (
  `spare_part_id` int(11) NOT NULL,
  `spare_part_code` int(11) DEFAULT NULL,
  `spare_part_name` varchar(50) DEFAULT NULL,
  `spare_part_seri` varchar(50) DEFAULT NULL,
  `spare_part_brand` varchar(50) DEFAULT NULL,
  `spare_part_date_manufacture` int(11) DEFAULT NULL,
  `spare_part_unit` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `spare_part`
--

INSERT INTO `spare_part` (`spare_part_id`, `spare_part_code`, `spare_part_name`, `spare_part_seri`, `spare_part_brand`, `spare_part_date_manufacture`, `spare_part_unit`) VALUES
(1, 1, 'Double Road 11.00R20 DR801', 'OT2524653636', 'DR', 1514739600, 'Bộ'),
(2, 2, 'VAT0002', 'AR246432535', 'AT', 1531846800, 'L'),
(3, 1, 'Double Road 11.00R20 DR801', 'OT2424312', '', 0, ''),
(4, 1, 'Double Road 11.00R20 DR801', 'AD9755242', 'DB', 1531242000, ''),
(5, 1, 'Double Road 11.00R20 DR801', 'BD5353637', 'DB', 1531242000, ''),
(6, 1, 'Double Road 11.00R20 DR801', 'Af023235', 'DB', 1531242000, 'Bộ'),
(7, 2, 'VAT', '', 'AT', 1531846800, 'L'),
(8, 1, 'Double Road 11.00R20 DR801', 'SDF256457', 'DB', 1531242000, ''),
(9, 1, 'Double Road 11.00R20 DR801', 'ADDD24535', 'DB', 1531242000, ''),
(10, 1, 'Double Road 11.00R20 DR801', 'SAS763434', 'DB', 1531242000, ''),
(11, 1, 'Double Road 11.00R20 DR801', 'ADD42342', 'DB', 1531242000, ''),
(12, 1, 'Double Road 11.00R20 DR801', 'fd5343434', 'DB', 1531242000, ''),
(13, 1, 'Double Road 11.00R20 DR801', 'Asdsd34', 'DB', 1531242000, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `spare_part_code`
--

CREATE TABLE `spare_part_code` (
  `spare_part_code_id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `spare_part_code`
--

INSERT INTO `spare_part_code` (`spare_part_code_id`, `code`, `name`) VALUES
(1, 'DR11R20DR801', 'Double Road 11.00R20 DR801'),
(2, 'VT', 'VAT');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `spare_stock`
--

CREATE TABLE `spare_stock` (
  `spare_stock_id` int(11) NOT NULL,
  `spare_stock_date` int(11) DEFAULT NULL,
  `spare_stock_code` int(11) DEFAULT NULL,
  `spare_part` int(11) DEFAULT NULL,
  `spare_stock_unit` varchar(20) DEFAULT NULL,
  `spare_stock_number` float DEFAULT NULL,
  `spare_stock_price` decimal(10,0) DEFAULT NULL,
  `spare_stock_vat` int(11) DEFAULT NULL,
  `spare_stock_vat_percent` float DEFAULT NULL,
  `spare_stock_vat_price` decimal(10,0) DEFAULT NULL,
  `import_stock` int(11) DEFAULT NULL,
  `export_stock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `spare_stock`
--

INSERT INTO `spare_stock` (`spare_stock_id`, `spare_stock_date`, `spare_stock_code`, `spare_part`, `spare_stock_unit`, `spare_stock_number`, `spare_stock_price`, `spare_stock_vat`, `spare_stock_vat_percent`, `spare_stock_vat_price`, `import_stock`, `export_stock`) VALUES
(4, 1531242000, 2, 7, 'L', 22, '1000000', NULL, 1, '10000', 2, NULL),
(8, 1531242000, 1, 10, '', 1, '1000000', NULL, 10, '100000', 1, NULL),
(17, 1531242000, 1, 12, '', 1, '1000000', NULL, 10, '100000', 1, NULL),
(18, 1531242000, 1, 13, '', 1, '1000000', NULL, 10, '100000', 1, NULL),
(20, 1531674000, 1, 10, '', 1, '1000000', NULL, 10, '100000', NULL, 2),
(21, 1531674000, 1, 12, '', 1, '1000000', NULL, 10, '100000', NULL, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `spare_vehicle`
--

CREATE TABLE `spare_vehicle` (
  `spare_vehicle_id` int(11) NOT NULL,
  `vehicle` int(11) DEFAULT NULL,
  `romooc` int(11) DEFAULT NULL,
  `spare_part` int(11) DEFAULT NULL,
  `start_time` int(11) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  `spare_part_number` float DEFAULT NULL,
  `export_stock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `toll`
--

CREATE TABLE `toll` (
  `toll_id` int(11) NOT NULL,
  `toll_code` varchar(50) DEFAULT NULL,
  `toll_name` varchar(200) DEFAULT NULL,
  `toll_mst` varchar(20) DEFAULT NULL,
  `toll_type` int(11) DEFAULT NULL COMMENT '1:Vé thu phí | 2:Cước đường bộ',
  `toll_symbol` varchar(10) DEFAULT NULL,
  `toll_province` int(11) DEFAULT NULL,
  `toll_lat` decimal(11,7) DEFAULT NULL,
  `toll_long` decimal(11,7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `toll`
--

INSERT INTO `toll` (`toll_id`, `toll_code`, `toll_name`, `toll_mst`, `toll_type`, `toll_symbol`, `toll_province`, `toll_lat`, `toll_long`) VALUES
(1, 'QL 51 T1', 'Công ty CP phát triển đường cao tốc Biên Hòa - Vũng Tàu', '3603023253', 1, 'AA/02', 19, '10.8606451', '106.9257565'),
(2, 'Xa lộ Hà Nội', 'Trạm thu phí xa lộ Hà Nội', '3030435465', 2, 'AB/032', 31, '10.8217354', '106.7587531');

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
(1, 'admin', '81dc9bdb52d04dc20036dbd8313ed055', 'ngoton007@yahoo.com', 1527218218, 1, 0, NULL, NULL, '[\"all\"]', '[\"all\"]', 1559888972),
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
(58, 1, 1527788896, 'unit', 'Thêm mới', '{\"unit_name\":\"Chuy\\u1ebfn\"}', 'Đơn vị tính'),
(59, 1, 1527861847, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":1530032400,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(60, 1, 1527861857, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":false,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(61, 1, 1527861936, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":null,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(62, 1, 1527863741, 'bonus', 'Thêm mới', '{\"bonus_start_date\":1527786000,\"bonus_end_date\":null,\"bonus_plus\":\"20000\",\"bonus_minus\":\"10000\"}', 'Thưởng phạt dầu'),
(63, 1, 1527863998, 'bonus', 'Cập nhật', '{\"bonus_start_date\":1527786000,\"bonus_end_date\":null,\"bonus_plus\":\"20000\",\"bonus_minus\":\"10000\"}', 'Thưởng phạt dầu'),
(64, 1, 1527864118, 'bonus', 'Thêm mới', '{\"bonus_start_date\":1529427600,\"bonus_end_date\":null,\"bonus_plus\":\"20000\",\"bonus_minus\":\"15000\"}', 'Thưởng phạt dầu'),
(65, 1, 1527864151, 'bonus', 'Cập nhật', '{\"bonus_start_date\":1527786000,\"bonus_end_date\":null,\"bonus_plus\":\"20000\",\"bonus_minus\":\"15000\"}', 'Thưởng phạt dầu'),
(66, 1, 1527864169, 'bonus', 'Cập nhật', '{\"bonus_start_date\":1525107600,\"bonus_end_date\":1527699600,\"bonus_plus\":\"20000\",\"bonus_minus\":\"10000\"}', 'Thưởng phạt dầu'),
(67, 1, 1527869124, 'route', 'Thêm mới', '{\"route_province\":\"19\",\"route_name\":\"Ng\\u00e3 3 V\\u0169ng T\\u00e0u\"}', 'Địa điểm'),
(68, 1, 1527869135, 'route', 'Cập nhật', '{\"route_province\":\"19\",\"route_name\":\"Ng\\u00e3 3 V\\u0169ng T\\u00e0u\"}', 'Địa điểm'),
(69, 1, 1527869727, 'place', 'Thêm mới', '{\"place_province\":\"31\",\"place_name\":\"Samsung\"}', 'Kho hàng'),
(70, 1, 1527869736, 'place', 'Cập nhật', '{\"place_province\":\"31\",\"place_name\":\"Samsung\"}', 'Kho hàng'),
(71, 1, 1527871818, 'place', 'Cập nhật', '{\"place_province\":\"31\",\"place_name\":\"Samsung\"}', 'Kho hàng'),
(72, 1, 1527871874, 'bonus', 'Cập nhật', '{\"bonus_start_date\":1527786000,\"bonus_end_date\":null,\"bonus_plus\":\"30000\",\"bonus_minus\":\"15000\"}', 'Thưởng phạt dầu'),
(73, 1, 1527873326, 'place', 'Cập nhật', '{\"place_province\":\"31\",\"place_name\":\"Samsung\",\"place_code\":\"SS\"}', 'Kho hàng'),
(74, 1, 1527875040, 'romooc', 'Thêm mới', '{\"romooc_number\":\"51C-1293\"}', 'Mooc'),
(75, 1, 1527875055, 'romooc', 'Cập nhật', '{\"romooc_number\":\"51C-129.35\"}', 'Mooc'),
(76, 1, 1527903198, 'vehicle', 'Thêm mới', '{\"vehicle_brand\":\"7\",\"vehicle_model\":\"A00\",\"vehicle_year\":\"2018\",\"vehicle_country\":\"86\",\"vehicle_owner\":\"1\",\"vehicle_number\":\"51B-2943\"}', 'Xe'),
(77, 1, 1527903263, 'vehicle', 'Cập nhật', '{\"vehicle_brand\":\"7\",\"vehicle_model\":\"A01\",\"vehicle_year\":\"2018\",\"vehicle_country\":\"86\",\"vehicle_owner\":\"\",\"vehicle_number\":\"51B-2943\"}', 'Xe'),
(78, 1, 1527903399, 'vehicle', 'Cập nhật', '{\"vehicle_brand\":\"7\",\"vehicle_model\":\"A01\",\"vehicle_year\":\"2018\",\"vehicle_country\":\"86\",\"vehicle_owner\":null,\"vehicle_number\":\"51B-2943\"}', 'Xe'),
(79, 1, 1527903456, 'vehicle', 'Cập nhật', '{\"vehicle_brand\":\"7\",\"vehicle_model\":\"A01\",\"vehicle_year\":\"2018\",\"vehicle_country\":\"86\",\"vehicle_owner\":\"1\",\"vehicle_number\":\"51B-2943\"}', 'Xe'),
(80, 1, 1528035274, 'position', 'Thêm mới', '{\"position_code\":\"PGD\",\"position_name\":\"Ph\\u00f3 gi\\u00e1m \\u0111\\u1ed1c\"}', 'Chức vụ'),
(81, 1, 1528036457, 'position', 'Thêm mới', '{\"position_code\":\"TP\",\"position_name\":\"Tr\\u01b0\\u1edfng ph\\u00f2ng\"}', 'Chức vụ'),
(82, 1, 1528036464, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"3\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":null,\"staff_account\":\"0\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(83, 1, 1528036490, 'staff', 'Cập nhật', '{\"staff_code\":\"NV01\",\"staff_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"staff_address\":\"\\u0110\\u1ed3ng Nai\",\"staff_cmnd\":\"121323232\",\"staff_birthday\":1525107600,\"staff_phone\":\"0902 085 911\",\"staff_email\":\"a@a.com\",\"staff_bank_account\":\"12323\",\"staff_bank\":\"ACB\",\"staff_gender\":\"0\",\"staff_position\":\"3\",\"staff_department\":\"3\",\"staff_start_date\":1526317200,\"staff_end_date\":null,\"staff_account\":\"Kh\\u00f4ng s\\u1eed d\\u1ee5ng\",\"staff_gplx\":\"1212232323\"}', 'Nhân viên'),
(84, 1, 1528049987, 'customer', 'Thêm mới', '{\"customer_code\":\"KH01\",\"customer_name\":\"Vi\\u1ec7t Trade\",\"customer_company\":\"C\\u00f4ng ty TNHH Vi\\u1ec7t Trade\",\"customer_mst\":\"3603295302\",\"customer_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X. Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean Ho\\u00e0, \\u0110\\u1ed3ng Nai\",\"customer_province\":\"19\",\"customer_phone\":\"0251 393 7677\",\"customer_mobile\":\"0902 085 911\",\"customer_email\":\"it@viet-trade.org\",\"customer_bank_account\":\"023\",\"customer_bank_name\":\"ACB\",\"customer_bank_branch\":\"\\u0110\\u1ed3ng Nai\",\"customer_type\":\"1\",\"customer_sub\":\"1\"}', 'Khách hàng - đối tác'),
(85, 1, 1528078662, 'customer', 'Cập nhật', '{\"customer_code\":\"KH01\",\"customer_name\":\"Vi\\u1ec7t Trade\",\"customer_company\":\"C\\u00f4ng ty TNHH Vi\\u1ec7t Trade\",\"customer_mst\":\"3603295302\",\"customer_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X. Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean Ho\\u00e0, \\u0110\\u1ed3ng Nai\",\"customer_province\":\"19\",\"customer_phone\":\"0251 393 7677\",\"customer_mobile\":\"0902 085 911\",\"customer_email\":\"it@viet-trade.org\",\"customer_bank_account\":\"23\",\"customer_bank_name\":\"ACB\",\"customer_bank_branch\":\"\\u0110\\u1ed3ng Nai\",\"customer_type\":\"1\",\"customer_sub\":\"1\"}', 'Khách hàng - đối tác'),
(86, 1, 1528079050, 'customer', 'Cập nhật', '{\"customer_code\":\"KH01\",\"customer_name\":\"Vi\\u1ec7t Trade\",\"customer_company\":\"C\\u00f4ng ty TNHH Vi\\u1ec7t Trade\",\"customer_mst\":\"3603295302\",\"customer_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X. Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean Ho\\u00e0, \\u0110\\u1ed3ng Nai\",\"customer_province\":\"19\",\"customer_phone\":\"0251 393 7677\",\"customer_mobile\":\"0902 085 911\",\"customer_email\":\"it@viet-trade.org\",\"customer_bank_account\":\"23\",\"customer_bank_name\":\"ACB\",\"customer_bank_branch\":\"\\u0110\\u1ed3ng Nai\",\"customer_type\":\"1\",\"customer_sub\":\"1\"}', 'Khách hàng - đối tác'),
(87, 1, 1528079123, 'customer', 'Cập nhật', '{\"customer_code\":\"NCC01\",\"customer_name\":\"Vi\\u1ec7t Trade\",\"customer_company\":\"C\\u00f4ng ty TNHH Vi\\u1ec7t Trade\",\"customer_mst\":\"3603295302\",\"customer_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X. Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean Ho\\u00e0, \\u0110\\u1ed3ng Nai\",\"customer_province\":\"19\",\"customer_phone\":\"0251 393 7677\",\"customer_mobile\":\"0902 085 911\",\"customer_email\":\"it@viet-trade.org\",\"customer_bank_account\":\"23\",\"customer_bank_name\":\"ACB\",\"customer_bank_branch\":\"\\u0110\\u1ed3ng Nai\",\"customer_type\":\"2\",\"customer_sub\":\"1\"}', 'Khách hàng - đối tác'),
(88, 1, 1528079250, 'customer', 'Cập nhật', '{\"customer_code\":\"NCC01\",\"customer_name\":\"Vi\\u1ec7t Trade\",\"customer_company\":\"C\\u00f4ng ty TNHH Vi\\u1ec7t Trade\",\"customer_mst\":\"3603295302\",\"customer_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X. Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean Ho\\u00e0, \\u0110\\u1ed3ng Nai\",\"customer_province\":\"19\",\"customer_phone\":\"0251 393 7677\",\"customer_mobile\":\"0902 085 911\",\"customer_email\":\"it@viet-trade.org\",\"customer_bank_account\":\"23\",\"customer_bank_name\":\"ACB\",\"customer_bank_branch\":\"\\u0110\\u1ed3ng Nai\",\"customer_type\":\"2\",\"customer_sub\":\"1\"}', 'Khách hàng - đối tác'),
(89, 1, 1528079279, 'customer', 'Cập nhật', '{\"customer_code\":\"NCC01\",\"customer_name\":\"Vi\\u1ec7t Trade\",\"customer_company\":\"C\\u00f4ng ty TNHH Vi\\u1ec7t Trade\",\"customer_mst\":\"3603295302\",\"customer_address\":\"S\\u1ed1 545, T\\u1ed5 10, \\u1ea4p H\\u01b0\\u01a1ng Ph\\u01b0\\u1edbc, X. Ph\\u01b0\\u1edbc T\\u00e2n, TP. Bi\\u00ean Ho\\u00e0, \\u0110\\u1ed3ng Nai\",\"customer_province\":\"19\",\"customer_phone\":\"0251 393 7677\",\"customer_mobile\":\"0902 085 911\",\"customer_email\":\"it@viet-trade.org\",\"customer_bank_account\":\"23\",\"customer_bank_name\":\"ACB\",\"customer_bank_branch\":\"\\u0110\\u1ed3ng Nai\",\"customer_type\":\"2\",\"customer_sub\":\"1,2\"}', 'Khách hàng - đối tác'),
(90, 1, 1528084608, 'oil', 'Thêm mới', '{\"oil_way\":\"R\\u1ed7ng\",\"oil_lit\":\"0.32\"}', 'Định mức dầu'),
(91, 1, 1528084620, 'oil', 'Cập nhật', '{\"oil_way\":\"R\\u1ed7ng\",\"oil_lit\":\"0.33\"}', 'Định mức dầu'),
(92, 1, 1528084627, 'oil', 'Cập nhật', '{\"oil_way\":\"R\\u1ed7ng\",\"oil_lit\":\"0.32\"}', 'Định mức dầu'),
(93, 1, 1528127432, 'warehouse', 'Thêm mới', '{\"warehouse_place\":\"1\",\"warehouse_start_date\":1527786000,\"warehouse_end_date\":null,\"warehouse_cont\":\"\",\"warehouse_ton\":\"150000\",\"warehouse_add\":\"200000\",\"warehouse_weight\":\"20000\",\"warehouse_clean\":\"10000\",\"warehouse_gate\":\"250000\"}', 'Bồi dưỡng kho'),
(94, 1, 1528127723, 'warehouse', 'Cập nhật', '{\"warehouse_place\":\"1\",\"warehouse_start_date\":1527786000,\"warehouse_end_date\":null,\"warehouse_cont\":\"480000\",\"warehouse_ton\":\"150000\",\"warehouse_add\":\"200000\",\"warehouse_weight\":\"20000\",\"warehouse_clean\":\"10000\",\"warehouse_gate\":\"250000\"}', 'Bồi dưỡng kho'),
(95, 1, 1528127993, 'place', 'Thêm mới', '{\"place_province\":\"31\",\"place_name\":\"PEPSI\",\"place_code\":\"PEP\"}', 'Kho hàng'),
(96, 1, 1528128129, 'warehouse', 'Cập nhật', '{\"warehouse_place\":\"2\",\"warehouse_start_date\":1527786000,\"warehouse_end_date\":null,\"warehouse_cont\":\"480000\",\"warehouse_ton\":\"150000\",\"warehouse_add\":\"200000\",\"warehouse_weight\":\"20000\",\"warehouse_clean\":\"10000\",\"warehouse_gate\":\"250000\"}', 'Bồi dưỡng kho'),
(97, 1, 1528128136, 'warehouse', 'Cập nhật', '{\"warehouse_place\":\"1\",\"warehouse_start_date\":1527786000,\"warehouse_end_date\":null,\"warehouse_cont\":\"480000\",\"warehouse_ton\":\"150000\",\"warehouse_add\":\"200000\",\"warehouse_weight\":\"20000\",\"warehouse_clean\":\"10000\",\"warehouse_gate\":\"250000\"}', 'Bồi dưỡng kho'),
(98, 1, 1528189705, 'place', 'Cập nhật', '{\"place_province\":\"19\",\"place_name\":\"PEPSI\",\"place_code\":\"PEP\",\"place_lat\":\"11.0686305\",\"place_long\":\"107.16759760000002\"}', 'Kho hàng'),
(99, 1, 1528189751, 'place', 'Cập nhật', '{\"place_province\":\"31\",\"place_name\":\"Samsung\",\"place_code\":\"SS\",\"place_lat\":\"10.8230989\",\"place_long\":\"106.6296638\"}', 'Kho hàng'),
(100, 1, 1528190578, 'route', 'Cập nhật', '{\"route_province\":\"19\",\"route_name\":\"Ng\\u00e3 3 V\\u0169ng T\\u00e0u\",\"route_lat\":\"10.905750648800774\",\"route_long\":\"106.84863129220412\"}', 'Địa điểm'),
(101, 1, 1528265742, 'oil', 'Thêm mới', '{\"oil_way\":\"L\\u00ean n\\u00fai\",\"oil_lit\":\"0.45\"}', 'Định mức dầu'),
(102, 1, 1528302719, 'toll', 'Thêm mới', '{\"toll_province\":\"19\",\"toll_name\":\"3603023253\",\"toll_code\":\"QL 51 T1\",\"toll_mst\":\"\",\"toll_type\":\"1\",\"toll_symbol\":\"AA\\/02\",\"toll_lat\":\"10.8606451\",\"toll_long\":\"106.92575650000003\"}', 'Trạm thu phí'),
(103, 1, 1528302815, 'toll', 'Cập nhật', '{\"toll_province\":\"19\",\"toll_name\":\"C\\u00f4ng ty CP ph\\u00e1t tri\\u1ec3n \\u0111\\u01b0\\u1eddng cao t\\u1ed1c Bi\\u00ean H\\u00f2a - V\\u0169ng T\\u00e0u\",\"toll_code\":\"QL 51 T1\",\"toll_mst\":\"3603023253\",\"toll_type\":\"1\",\"toll_symbol\":\"AA\\/02\",\"toll_lat\":\"10.8606451\",\"toll_long\":\"106.92575650000003\"}', 'Trạm thu phí'),
(104, 1, 1528304482, 'toll', 'Thêm mới', '{\"toll_province\":\"31\",\"toll_name\":\"Tr\\u1ea1m thu ph\\u00ed xa l\\u1ed9 H\\u00e0 N\\u1ed9i\",\"toll_code\":\"Xa l\\u1ed9 H\\u00e0 N\\u1ed9i\",\"toll_mst\":\"3030435465\",\"toll_type\":\"2\",\"toll_symbol\":\"AB\\/032\",\"toll_lat\":\"10.9031623\",\"toll_long\":\"106.84420820000003\"}', 'Trạm thu phí'),
(105, 1, 1528304554, 'route', 'Thêm mới', '{\"route_province\":\"31\",\"route_name\":\"C\\u1ea3ng C\\u00e1t L\\u00e1i\",\"route_lat\":\"10.757996\",\"route_long\":\"106.78893160000007\"}', 'Địa điểm'),
(106, 1, 1528304588, 'route', 'Thêm mới', '{\"route_province\":\"2\",\"route_name\":\"C\\u1ea3ng C\\u00e1i M\\u00e9p\",\"route_lat\":\"10.5385454\",\"route_long\":\"107.03179769999997\"}', 'Địa điểm'),
(107, 1, 1528305943, 'toll', 'Cập nhật', '{\"toll_province\":\"31\",\"toll_name\":\"Tr\\u1ea1m thu ph\\u00ed xa l\\u1ed9 H\\u00e0 N\\u1ed9i\",\"toll_code\":\"Xa l\\u1ed9 H\\u00e0 N\\u1ed9i\",\"toll_mst\":\"3030435465\",\"toll_type\":\"2\",\"toll_symbol\":\"AB\\/032\",\"toll_lat\":\"10.821735444106489\",\"toll_long\":\"106.75875306129456\"}', 'Trạm thu phí'),
(108, 1, 1528343592, 'road', 'Thêm mới', '{\"road_place_from\":\"1\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"1\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"1.5\",\"road_km\":\"35\",\"road_oil\":\"[{\\\"road_oil_way\\\":\\\"1\\\"\\\"road_oil_lit\\\":\\\"3.2\\\"\\\"road_oil_km\\\":\\\"10\\\"\\\"id_road_oil\\\":\\\"\\\"}{\\\"road_oil_way\\\":\\\"2\\\"\\\"road_oil_lit\\\":\\\"11.25\\\"\\\"road_oil_km\\\":\\\"25\\\"\\\"id_road_oil\\\":\\\"\\\"}{\\\"toll\\\":\\\"1\\\"\\\"road_toll_vat\\\":\\\"on\\\"\\\"road_toll_money\\\":\\\"20000\\\"\\\"id_road_toll\\\":\\\"\\\"}{\\\"toll\\\":\\\"2\\\"\\\"road_toll_vat\\\":\\\"on\\\"\\\"road_toll_money\\\":\\\"10000\\\"\\\"id_road_toll\\\":\\\"\\\"}]\",\"road_oil_ton\":\"0.5\",\"road_bridge\":\"30000\",\"road_police\":\"200000\",\"road_tire\":\"150000\",\"road_over\":\"20000\",\"road_add\":\"200000\",\"road_salary\":\"500000\"}', 'Định mức tuyến đường'),
(109, 1, 1528352294, 'road', 'Cập nhật', '{\"road_place_from\":\"1\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"1\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"1.50\",\"road_km\":\"30\",\"road_oil\":\"12.2\",\"road_oil_ton\":\"0.50\",\"road_bridge\":\"30000\",\"road_police\":\"200000.00\",\"road_tire\":\"150000.00\",\"road_over\":\"20000.00\",\"road_add\":\"200000.00\",\"road_salary\":\"500000.00\"}', 'Định mức tuyến đường'),
(110, 1, 1528352389, 'road', 'Cập nhật', '{\"road_place_from\":\"1\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"1\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"1.50\",\"road_km\":\"35\",\"road_oil\":\"13.8\",\"road_oil_ton\":\"0.50\",\"road_bridge\":\"30000\",\"road_police\":\"200000.00\",\"road_tire\":\"150000.00\",\"road_over\":\"20000.00\",\"road_add\":\"200000.00\",\"road_salary\":\"500000.00\"}', 'Định mức tuyến đường'),
(111, 1, 1528352458, 'road', 'Cập nhật', '{\"road_place_from\":\"1\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"1\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"1.50\",\"road_km\":\"35\",\"road_oil\":\"13.8\",\"road_oil_ton\":\"0.50\",\"road_bridge\":\"30000\",\"road_police\":\"200000.00\",\"road_tire\":\"150000.00\",\"road_over\":\"20000.00\",\"road_add\":\"200000.00\",\"road_salary\":\"500000.00\"}', 'Định mức tuyến đường'),
(112, 1, 1528352984, 'road', 'Cập nhật', '{\"road_place_from\":\"1\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"1\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"1.50\",\"road_km\":\"35\",\"road_oil\":\"13.15\",\"road_oil_ton\":\"0.50\",\"road_bridge\":\"25000\",\"road_police\":\"200000.00\",\"road_tire\":\"150000.00\",\"road_over\":\"20000.00\",\"road_add\":\"200000.00\",\"road_salary\":\"500000.00\"}', 'Định mức tuyến đường'),
(113, 1, 1528353177, 'road', 'Cập nhật', '{\"road_place_from\":\"1\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"1\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"1.50\",\"road_km\":\"35.00\",\"road_oil\":\"13.15\",\"road_oil_ton\":\"0.50\",\"road_bridge\":\"25000.00\",\"road_police\":\"200000.00\",\"road_tire\":\"150000.00\",\"road_over\":\"20000.00\",\"road_add\":\"200000.00\",\"road_salary\":\"500000.00\"}', 'Định mức tuyến đường'),
(114, 1, 1528353344, 'road', 'Cập nhật', '{\"road_place_from\":\"1\",\"road_place_to\":\"2\",\"road_route_from\":\"2\",\"road_route_to\":\"3\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"1.50\",\"road_km\":\"35.00\",\"road_oil\":\"13.15\",\"road_oil_ton\":\"0.50\",\"road_bridge\":\"25000.00\",\"road_police\":\"200000.00\",\"road_tire\":\"150000.00\",\"road_over\":\"20000.00\",\"road_add\":\"200000.00\",\"road_salary\":\"500000.00\"}', 'Định mức tuyến đường'),
(115, 1, 1528353457, 'road', 'Thêm mới', '{\"road_place_from\":\"1\",\"road_place_to\":\"2\",\"road_route_from\":\"1\",\"road_route_to\":\"2\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"2\",\"road_km\":\"40\",\"road_oil\":\"15.4\",\"road_oil_ton\":\"5\",\"road_bridge\":\"35000\",\"road_police\":\"100000\",\"road_tire\":\"200000\",\"road_over\":\"50000\",\"road_add\":\"500000\",\"road_salary\":\"200000\"}', 'Định mức tuyến đường'),
(116, 1, 1528356878, 'road', 'Cập nhật', '{\"road_place_from\":\"1\",\"road_place_to\":\"2\",\"road_route_from\":\"2\",\"road_route_to\":\"3\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"1.5\",\"road_km\":\"35.55\",\"road_oil\":\"13.326\",\"road_oil_ton\":\"0.5\",\"road_bridge\":\"25000\",\"road_police\":\"200000\",\"road_tire\":\"150000\",\"road_over\":\"20000\",\"road_add\":\"200000\",\"road_salary\":\"500000\"}', 'Định mức tuyến đường'),
(117, 1, 1528360852, 'road', 'Thêm mới', '{\"road_place_from\":\"2\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"2\",\"road_start_date\":false,\"road_end_date\":null,\"road_time\":\"2.5\",\"road_km\":\"37.5\",\"road_oil\":\"16.55\",\"road_oil_ton\":\"2\",\"road_bridge\":\"20000\",\"road_police\":\"400000\",\"road_tire\":\"200000\",\"road_over\":\"10000\",\"road_add\":\"600000\",\"road_salary\":\"500000\"}', 'Định mức tuyến đường'),
(118, 1, 1528360884, 'road', 'Cập nhật', '{\"road_place_from\":\"2\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"2\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"2.5\",\"road_km\":\"37.5\",\"road_oil\":\"16.55\",\"road_oil_ton\":\"2\",\"road_bridge\":\"20000\",\"road_police\":\"400000\",\"road_tire\":\"200000\",\"road_over\":\"10000\",\"road_add\":\"600000\",\"road_salary\":\"500000\"}', 'Định mức tuyến đường'),
(119, 1, 1528360951, 'road', 'Thêm mới', '{\"road_place_from\":\"2\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"2\",\"road_start_date\":1528563600,\"road_end_date\":null,\"road_time\":\"3\",\"road_km\":\"17\",\"road_oil\":\"7.39\",\"road_oil_ton\":\"5\",\"road_bridge\":\"10000\",\"road_police\":\"300000\",\"road_tire\":\"200000\",\"road_over\":\"50000\",\"road_add\":\"100000\",\"road_salary\":\"200000\"}', 'Định mức tuyến đường'),
(120, 1, 1528360985, 'road', 'Cập nhật', '{\"road_place_from\":\"2\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"2\",\"road_start_date\":1525107600,\"road_end_date\":1527699600,\"road_time\":\"2.5\",\"road_km\":\"37.5\",\"road_oil\":\"16.55\",\"road_oil_ton\":\"2\",\"road_bridge\":\"20000\",\"road_police\":\"400000\",\"road_tire\":\"200000\",\"road_over\":\"10000\",\"road_add\":\"600000\",\"road_salary\":\"500000\"}', 'Định mức tuyến đường'),
(121, 1, 1528360993, 'road', 'Cập nhật', '{\"road_place_from\":\"2\",\"road_place_to\":\"1\",\"road_route_from\":\"1\",\"road_route_to\":\"2\",\"road_start_date\":1527786000,\"road_end_date\":null,\"road_time\":\"3\",\"road_km\":\"17\",\"road_oil\":\"7.39\",\"road_oil_ton\":\"5\",\"road_bridge\":\"10000\",\"road_police\":\"300000\",\"road_tire\":\"200000\",\"road_over\":\"50000\",\"road_add\":\"100000\",\"road_salary\":\"200000\"}', 'Định mức tuyến đường'),
(122, 1, 1528365629, 'cost_list', 'Thêm mới', '{\"cost_list_type\":\"8\",\"cost_list_name\":\"N\\u00e2ng cont\",\"cost_list_code\":\"NC\"}', 'Danh mục chi phí'),
(123, 1, 1528365643, 'cost_list', 'Cập nhật', '{\"cost_list_type\":\"2\",\"cost_list_name\":\"N\\u00e2ng cont1\",\"cost_list_code\":\"NC1\"}', 'Danh mục chi phí'),
(124, 1, 1528365655, 'cost_list', 'Cập nhật', '{\"cost_list_type\":\"8\",\"cost_list_name\":\"N\\u00e2ng cont\",\"cost_list_code\":\"NC\"}', 'Danh mục chi phí'),
(125, 1, 1528429681, 'vehicle', 'Thêm mới', '{\"vehicle_brand\":\"1\",\"vehicle_model\":\"E310\",\"vehicle_year\":\"2018\",\"vehicle_country\":\"220\",\"vehicle_owner\":null,\"vehicle_number\":\"50A-134.65\"}', 'Xe'),
(126, 1, 1528429704, 'vehicle_work', 'Thêm mới', '{\"vehicle_work_start_date\":1528995600,\"vehicle_work_end_date\":null,\"vehicle\":\"2\"}', 'Tạm dừng xe'),
(127, 1, 1528430114, 'vehicle_work', 'Cập nhật', '{\"vehicle_work_start_date\":1528995600,\"vehicle_work_end_date\":1530291600,\"vehicle\":\"1\"}', 'Tạm dừng xe'),
(128, 1, 1528439875, 'driver', 'Thêm mới', '{\"driver_start_date\":1527786000,\"driver_end_date\":1530291600,\"driver_vehicle\":\"1\",\"driver_staff\":\"1\"}', 'Bàn giao xe'),
(129, 1, 1528439875, 'staff', 'Thêm mới', '{\"staff_code\":\"NV02\",\"staff_name\":\"\",\"staff_address\":\"\",\"staff_cmnd\":\"\",\"staff_birthday\":false,\"staff_phone\":\"\",\"staff_email\":\"\",\"staff_bank_account\":\"\",\"staff_bank\":\"\",\"staff_gender\":\"0\",\"staff_position\":\"1\",\"staff_department\":\"3\",\"staff_start_date\":false,\"staff_end_date\":null,\"staff_account\":\"\",\"staff_gplx\":\"\"}', 'Nhân viên'),
(130, 1, 1528439895, 'driver', 'Cập nhật', '{\"driver_start_date\":1527786000,\"driver_end_date\":null,\"driver_vehicle\":\"2\",\"driver_staff\":\"1\"}', 'Bàn giao xe'),
(131, 1, 1528439895, 'vehicle', 'Thêm mới', '{\"vehicle_brand\":\"1\",\"vehicle_model\":\"\",\"vehicle_year\":\"\",\"vehicle_country\":\"220\",\"vehicle_owner\":null,\"vehicle_number\":\"\"}', 'Xe'),
(132, 1, 1528452095, 'vehicle_romooc', 'Thêm mới', '{\"vehicle\":\"1\",\"romooc\":\"1\",\"start_time\":1528390800}', 'Thay lắp mooc'),
(133, 1, 1528508364, 'vehicle_romooc', 'Cập nhật', '{\"start_time\":1527786000,\"end_time\":null,\"vehicle\":\"2\",\"romooc\":\"1\"}', 'Thay lắp mooc'),
(134, 1, 1528510453, 'house', 'Thêm mới', '{\"house_code\":\"LX\",\"house_name\":\"L\\u1ed1p xe\"}', 'Kho vật tư'),
(135, 1, 1528510460, 'house', 'Cập nhật', '{\"house_code\":\"LX1\",\"house_name\":\"L\\u1ed1p xe1\"}', 'Kho vật tư'),
(136, 1, 1528510466, 'house', 'Cập nhật', '{\"house_code\":\"LX\",\"house_name\":\"L\\u1ed1p xe\"}', 'Kho vật tư'),
(137, 1, 1528729233, 'customer', 'Thêm mới', '{\"customer_code\":\"KH01\",\"customer_name\":\"Samsung\",\"customer_company\":\"C\\u00f4ng ty TNHH Samsung Vina\",\"customer_mst\":\"3603422324\",\"customer_address\":\"Q9\",\"customer_province\":\"31\",\"customer_phone\":\"0283 943 231\",\"customer_mobile\":\"\",\"customer_email\":\"\",\"customer_bank_account\":\"\",\"customer_bank_name\":\"\",\"customer_bank_branch\":\"\",\"customer_type\":\"1\",\"customer_sub\":\"3\"}', 'Khách hàng - đối tác'),
(138, 1, 1528729416, 'booking', 'Thêm mới', '{\"booking_date\":false,\"booking_code\":\"\",\"booking_customer\":\"2\",\"booking_number\":\"8624223\",\"booking_type\":\"1\",\"booking_shipping\":\"2\",\"booking_shipping_name\":\"CMA\",\"booking_shipping_number\":\"4232\",\"booking_place_from\":\"1\",\"booking_place_to\":\"2\",\"booking_start_date\":1528650000,\"booking_end_date\":1528650000,\"booking_sum\":\"2\",\"booking_total\":\"4000000\",\"booking_comment\":\"Hang nh\\u1eadp\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(139, 1, 1528736993, 'booking', 'Cập nhật', '{\"booking_date\":1527786000,\"booking_code\":\"DH001\",\"booking_customer\":\"2\",\"booking_number\":\"8624223\",\"booking_type\":\"2\",\"booking_shipping\":\"2\",\"booking_shipping_name\":\"CMA\",\"booking_shipping_number\":\"4232\",\"booking_place_from\":\"1\",\"booking_place_to\":\"2\",\"booking_start_date\":1528650000,\"booking_end_date\":1528650000,\"booking_sum\":\"3\",\"booking_total\":\"4100000\",\"booking_comment\":\"Hang nh\\u1eadp\",\"booking_update_user\":\"1\"}', 'Đơn hàng'),
(140, 1, 1528737166, 'booking', 'Cập nhật', '{\"booking_date\":1527786000,\"booking_code\":\"DH001\",\"booking_customer\":\"2\",\"booking_number\":\"8624223\",\"booking_type\":\"2\",\"booking_shipping\":\"2\",\"booking_shipping_name\":\"CMA\",\"booking_shipping_number\":\"4232\",\"booking_place_from\":\"1\",\"booking_place_to\":\"2\",\"booking_start_date\":1528650000,\"booking_end_date\":1528650000,\"booking_sum\":\"3\",\"booking_total\":\"4100000\",\"booking_comment\":\"Hang nh\\u1eadp\",\"booking_update_user\":\"1\"}', 'Đơn hàng'),
(141, 1, 1528879090, 'booking', 'Cập nhật', '{\"booking_date\":1527786000,\"booking_code\":\"DH001\",\"booking_customer\":\"2\",\"booking_number\":\"8624223\",\"booking_type\":\"2\",\"booking_shipping\":\"2\",\"booking_shipping_name\":\"CMA\",\"booking_shipping_number\":\"4232\",\"booking_place_from\":\"1\",\"booking_place_to\":\"2\",\"booking_start_date\":1528650000,\"booking_end_date\":1529427600,\"booking_sum\":\"3\",\"booking_total\":\"4100000\",\"booking_comment\":\"Hang nh\\u1eadp\",\"booking_update_user\":\"1\"}', 'Đơn hàng'),
(142, 1, 1528880565, 'shipment_temp', 'Nhận', '{\"shipment_temp_date\":1529427600,\"shipment_temp_owner\":\"1\",\"shipment_temp_booking\":\"1\",\"shipment_temp_status\":0,\"shipment_temp_ton\":\"2\",\"shipment_temp_number\":\"1\"}', 'Lô hàng mới'),
(143, 1, 1528949123, 'shipment_temp', 'Cập nhật', '{\"shipment_temp_ton\":\"1\",\"shipment_temp_number\":\"2\"}', 'Đơn hàng nhận'),
(144, 1, 1528960229, 'shipment_temp', 'Xóa', '\"1\"', 'Đơn hàng nhận'),
(145, 1, 1528960256, 'shipment_temp', 'Nhận', '{\"shipment_temp_date\":1529427600,\"shipment_temp_owner\":\"1\",\"shipment_temp_booking\":\"1\",\"shipment_temp_status\":0,\"shipment_temp_ton\":\"3\",\"shipment_temp_number\":\"3\"}', 'Lô hàng mới'),
(146, 1, 1529337916, 'customer', 'Thêm mới', '{\"customer_code\":\"KH02\",\"customer_name\":\"Pepsi\",\"customer_company\":\"C\\u00f4ng ty TNHH Pepsico\",\"customer_mst\":\"\",\"customer_address\":\"\",\"customer_province\":\"31\",\"customer_phone\":\"\",\"customer_mobile\":\"\",\"customer_email\":\"\",\"customer_bank_account\":\"\",\"customer_bank_name\":\"\",\"customer_bank_branch\":\"\",\"customer_type\":\"1\",\"customer_sub\":\"4,5\"}', 'Khách hàng - đối tác'),
(147, 1, 1529338031, 'booking', 'Thêm mới', '{\"booking_date\":1529254800,\"booking_code\":\"DH002\",\"booking_customer\":\"3\",\"booking_number\":\"0435265353\",\"booking_type\":\"1\",\"booking_shipping\":\"1\",\"booking_shipping_name\":\"A\",\"booking_shipping_number\":\"642\",\"booking_place_from\":\"2\",\"booking_place_to\":\"1\",\"booking_start_date\":1529254800,\"booking_end_date\":1530291600,\"booking_sum\":\"2\",\"booking_total\":\"3000000\",\"booking_comment\":\"H\\u00e0ng d\\u1ec5 v\\u1ee1\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(148, 1, 1529343860, 'shipment_temp', 'Nhận', '{\"shipment_temp_date\":1529254800,\"shipment_temp_owner\":\"1\",\"shipment_temp_booking\":\"2\",\"shipment_temp_status\":0,\"shipment_temp_ton\":\"1\",\"shipment_temp_number\":\"1\"}', 'Lô hàng mới'),
(149, 1, 1529343908, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"51C-129.35\",\"dispatch_staff\":\"Nguy\\u1ec5n V\\u0103n A\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1528650000,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"HI\",\"dispatch_shipment_temp\":\"2\",\"dispatch_booking_detail\":\"3\",\"dispatch_booking_detail_number\":\"1\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(150, 1, 1529350572, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"HI\",\"dispatch_shipment_temp\":\"2\",\"dispatch_booking_detail\":\"3\",\"dispatch_booking_detail_number\":\"2\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(151, 1, 1529396934, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"HI\",\"dispatch_shipment_temp\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(152, 1, 1529397311, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"HI\",\"dispatch_shipment_temp\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(153, 1, 1529397336, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(154, 1, 1529480086, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(155, 1, 1529485592, 'shipment', 'Thêm mới', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"2\",\"shipment_type\":\"2\",\"shipment_do\":\"423\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"1\",\"shipment_booking_detail\":\"3\",\"shipment_container\":\"CU0232355\",\"shipment_ton_receive\":\"2\",\"shipment_ton\":\"2\",\"shipment_unit\":\"4\",\"shipment_place_from\":\"1\",\"shipment_place_to\":\"2\",\"shipment_start_date\":1529254800,\"shipment_end_date\":1529427600,\"shipment_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"shipment_price\":\"2000000.00\",\"shipment_create_user\":\"1\"}', 'Phiếu vận chuyển');
INSERT INTO `user_log` (`user_log_id`, `user_log`, `user_log_date`, `user_log_table`, `user_log_action`, `user_log_data`, `user_log_table_name`) VALUES
(156, 1, 1529511420, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(157, 1, 1529512094, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(158, 1, 1529515086, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(159, 1, 1529515477, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(160, 1, 1529517144, 'shipment', 'Thêm mới', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"P\\u1ee6\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529258400,\"shipment_end_date\":1530289800,\"shipment_comment\":\"L\\u00f4 ph\\u1ee5\",\"shipment_price\":\"1000000.00\",\"shipment_create_user\":\"1\"}', 'Phiếu vận chuyển'),
(161, 1, 1529517480, 'shipment', 'Thêm mới', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"A2243\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"6\",\"shipment_container\":\"LU4354522\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"4\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_comment\":\"L\\u00f4 ph\\u1ee5\",\"shipment_price\":\"2000000.00\",\"shipment_sub\":\"1\",\"shipment_create_user\":\"1\"}', 'Phiếu vận chuyển'),
(162, 1, 1529590987, 'booking', 'Thêm mới', '{\"booking_date\":1529514000,\"booking_code\":\"DH003\",\"booking_customer\":\"3\",\"booking_number\":\"BK352724\",\"booking_type\":\"1\",\"booking_shipping\":\"2\",\"booking_shipping_name\":\"\",\"booking_shipping_number\":\"\",\"booking_place_from\":\"2\",\"booking_place_to\":\"2\",\"booking_start_date\":1529514000,\"booking_end_date\":1529514000,\"booking_sum\":\"1000\",\"booking_total\":\"10000000\",\"booking_comment\":\"\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(163, 1, 1529591028, 'shipping', 'Thêm mới', '{\"shipping_country\":\"1\",\"shipping_name\":\"UASC\"}', 'Hãng tàu'),
(164, 1, 1529591043, 'booking', 'Thêm mới', '{\"booking_date\":1529514000,\"booking_code\":\"DH004\",\"booking_customer\":\"3\",\"booking_number\":\"\",\"booking_type\":\"1\",\"booking_shipping\":\"3\",\"booking_shipping_name\":\"\",\"booking_shipping_number\":\"\",\"booking_place_from\":\"2\",\"booking_place_to\":\"2\",\"booking_start_date\":1529514000,\"booking_end_date\":1529514000,\"booking_sum\":\"\",\"booking_total\":\"\",\"booking_comment\":\"\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(165, 1, 1529718433, 'shipment', 'Thêm mới', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"2\",\"shipment_type\":\"2\",\"shipment_do\":\"B242562\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"1\",\"shipment_booking_detail\":\"4\",\"shipment_container\":\"AU64542\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"3\",\"shipment_place_from\":\"1\",\"shipment_place_to\":\"2\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1529427960,\"shipment_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"shipment_price\":\"100000.00\",\"shipment_sub\":\"0\",\"shipment_road\":\"1,2\",\"shipment_create_user\":\"1\"}', 'Phiếu vận chuyển'),
(166, 1, 1529724556, 'shipment', 'Xóa', '\"2\"', 'phiếu vận chuyển'),
(167, 1, 1529724589, 'shipment', 'Thêm mới', '{\"shipment_date\":1529686800,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"P042423\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_comment\":\"L\\u00f4 ph\\u1ee5\",\"shipment_price\":\"1000000.00\",\"shipment_sub\":\"1\",\"shipment_road\":\"4\",\"shipment_create_user\":\"1\"}', 'Phiếu vận chuyển'),
(168, 1, 1529770437, 'shipment', 'Cập nhật', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"2\",\"shipment_type\":\"2\",\"shipment_do\":\"B242562\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"1\",\"shipment_booking_detail\":\"4\",\"shipment_container\":\"AU64542\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"3\",\"shipment_place_from\":\"1\",\"shipment_place_to\":\"2\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1529427960,\"shipment_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"shipment_price\":\"undefined\",\"shipment_sub\":\"0\",\"shipment_road\":\"1,2\",\"shipment_update_user\":\"1\"}', 'Phiếu vận chuyển'),
(169, 1, 1529860721, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"4\",\"dispatch_port_to\":\"\",\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"5\",\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(170, 1, 1529861883, 'shipment', 'Cập nhật', '{\"shipment_date\":1529686800,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"P042423\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_port_from\":\"4\",\"shipment_port_to\":\"\",\"shipment_comment\":\"L\\u00f4 ph\\u1ee5\",\"shipment_price\":\"undefined\",\"shipment_sub\":\"1\",\"shipment_road\":\"4\",\"shipment_update_user\":\"1\"}', 'Phiếu vận chuyển'),
(171, 1, 1529861941, 'shipment', 'Cập nhật', '{\"shipment_date\":1529686800,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"P042423\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_port_from\":\"4\",\"shipment_port_to\":\"\",\"shipment_comment\":\"L\\u00f4 ph\\u1ee5\",\"shipment_price\":\"undefined\",\"shipment_sub\":\"1\",\"shipment_road\":\"4\",\"shipment_update_user\":\"1\"}', 'Phiếu vận chuyển'),
(172, 1, 1529862131, 'shipment', 'Cập nhật', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"2\",\"shipment_type\":\"2\",\"shipment_do\":\"423\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"1\",\"shipment_booking_detail\":\"3\",\"shipment_container\":\"CU0232355\",\"shipment_ton_receive\":\"2\",\"shipment_ton\":\"2\",\"shipment_unit\":\"4\",\"shipment_place_from\":\"1\",\"shipment_place_to\":\"2\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1529427960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"5\",\"shipment_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"shipment_price\":\"undefined\",\"shipment_sub\":\"0\",\"shipment_road\":\"1,2\",\"shipment_update_user\":\"1\"}', 'Phiếu vận chuyển'),
(173, 1, 1529893254, 'place', 'Thêm mới', '{\"place_province\":\"31\",\"place_name\":\"C\\u1ea3ng C\\u00e1t L\\u00e1i\",\"place_code\":\"CL\",\"place_lat\":\"10.757996\",\"place_long\":\"106.78893160000007\",\"place_port\":\"1\"}', 'Cảng'),
(174, 1, 1529893270, 'place', 'Cập nhật', '{\"place_province\":\"31\",\"place_name\":\"C\\u1ea3ng C\\u00e1t L\\u00e1i1\",\"place_code\":\"CL\",\"place_lat\":\"10.7771236\",\"place_long\":\"106.80860660000008\",\"place_port\":\"1\"}', 'Cảng'),
(175, 1, 1529893276, 'place', 'Cập nhật', '{\"place_province\":\"31\",\"place_name\":\"C\\u1ea3ng C\\u00e1t L\\u00e1i\",\"place_code\":\"CL\",\"place_lat\":\"10.757996\",\"place_long\":\"106.78893160000007\",\"place_port\":\"1\"}', 'Cảng'),
(176, 1, 1529893366, 'place', 'Cập nhật', '{\"place_province\":\"31\",\"place_name\":\"Samsung1\",\"place_code\":\"SS\",\"place_lat\":\"10.823099\",\"place_long\":\"106.62966400000005\",\"place_port\":null}', 'Kho hàng'),
(177, 1, 1529893371, 'place', 'Cập nhật', '{\"place_province\":\"31\",\"place_name\":\"Samsung\",\"place_code\":\"SS\",\"place_lat\":\"10.7734346\",\"place_long\":\"106.70387559999995\",\"place_port\":null}', 'Kho hàng'),
(178, 1, 1529934462, 'lift', 'Thêm mới', '{\"lift_start_date\":1527786000,\"lift_end_date\":null,\"lift_place\":\"3\",\"lift_customer\":\"\",\"lift_on\":\"590000\",\"lift_off\":\"465000\"}', 'Phí nâng hạ'),
(179, 1, 1529940344, 'customer', 'Thêm mới', '{\"customer_code\":\"NCC02\",\"customer_name\":\"C\\u1ea3ng C\\u00e1t L\\u00e1i\",\"customer_company\":\"C\\u00f4ng ty c\\u1ed5 ph\\u1ea7n C\\u00e1t L\\u00e1i\",\"customer_mst\":\"\",\"customer_address\":\"\",\"customer_province\":\"31\",\"customer_phone\":\"\",\"customer_mobile\":\"\",\"customer_email\":\"\",\"customer_bank_account\":\"\",\"customer_bank_name\":\"\",\"customer_bank_branch\":\"\",\"customer_type\":\"2\",\"customer_sub\":\"\"}', 'Khách hàng - đối tác'),
(180, 1, 1529940600, 'lift', 'Cập nhật', '{\"lift_start_date\":1527786000,\"lift_end_date\":null,\"lift_place\":\"3\",\"lift_unit\":\"3\",\"lift_customer\":\"4\",\"lift_on\":\"590000\",\"lift_off\":\"465000\"}', 'Phí nâng hạ'),
(181, 1, 1529986652, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"undefined\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"3\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"4\",\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"40\",\"dispatch_unit_sub\":\"2\",\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(182, 1, 1529986703, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"3\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"4\",\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"40\",\"dispatch_unit_sub\":\"2\",\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(183, 1, 1529986874, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX02\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"3\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"5\",\"dispatch_comment\":\"Nh\\u1eadp\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"1\",\"dispatch_place_to_sub\":\"2\",\"dispatch_start_date_sub\":1528650000,\"dispatch_end_date_sub\":1529427600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"1\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"Ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"2\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"1\",\"dispatch_booking_type_sub\":\"2\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(184, 1, 1529987386, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"3\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"4\",\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"40\",\"dispatch_unit_sub\":\"2\",\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(185, 1, 1529987444, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX02\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"3\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"5\",\"dispatch_comment\":\"Nh\\u1eadp\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"3\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"1\",\"dispatch_place_to_sub\":\"2\",\"dispatch_start_date_sub\":1528650000,\"dispatch_end_date_sub\":1529427600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"1\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"Ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"2\",\"dispatch_customer_sub\":\"2\",\"dispatch_booking_sub\":\"1\",\"dispatch_booking_type_sub\":\"2\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(186, 1, 1529987540, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX03\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(187, 1, 1529993381, 'dispatch', 'Xóa', '\"3\"', 'Lệnh điều xe'),
(188, 1, 1529993483, 'dispatch', 'Xóa', '\"2\"', 'Lệnh điều xe'),
(189, 1, 1529993606, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX02\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"1\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"nh\\u1eadp\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(190, 1, 1529993675, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX03\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1528650000,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(191, 1, 1529993832, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX03\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"0\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"0\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(192, 1, 1529994865, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX04\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(193, 1, 1529994975, 'dispatch', 'Xóa', '\"4\"', 'Lệnh điều xe'),
(194, 1, 1529994978, 'dispatch', 'Xóa', '\"5\"', 'Lệnh điều xe'),
(195, 1, 1529994982, 'dispatch', 'Xóa', '\"6\"', 'Lệnh điều xe'),
(196, 1, 1529995055, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX02\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(197, 1, 1529996677, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX03\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1528650000,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(198, 1, 1529996711, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX04\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"3\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(199, 1, 1529997037, 'dispatch', 'Xóa', '\"7\"', 'Lệnh điều xe'),
(200, 1, 1529997047, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX03\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"0\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"3\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"0\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(201, 1, 1529997441, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"3\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"3\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"4\",\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"40\",\"dispatch_unit_sub\":\"2\",\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(202, 1, 1529997494, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"3\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"3\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"3\",\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"40\",\"dispatch_unit_sub\":\"2\",\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(203, 1, 1529998997, 'lift', 'Cập nhật', '{\"lift_start_date\":1527786000,\"lift_end_date\":null,\"lift_place\":\"3\",\"lift_unit\":\"3\",\"lift_customer\":\"4\",\"lift_on\":\"590000\",\"lift_off\":\"465000\",\"lift_on_null\":\"400000\",\"lift_off_null\":\"350000\"}', 'Phí nâng hạ'),
(204, 1, 1529999013, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"3\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"3\",\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"40\",\"dispatch_unit_sub\":\"2\",\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(205, 1, 1530003060, 'shipdeposit', 'Thêm mới', '{\"shipdeposit_start_date\":1529946000,\"shipdeposit_end_date\":null,\"shipdeposit_shipping\":\"2\",\"shipdeposit_unit\":\"3\",\"shipdeposit_money\":\"2000000\"}', 'Phí cược cont'),
(206, 1, 1530003193, 'shipdeposit', 'Cập nhật', '{\"shipdeposit_start_date\":1529946000,\"shipdeposit_end_date\":null,\"shipdeposit_shipping\":\"2\",\"shipdeposit_unit\":\"4\",\"shipdeposit_money\":\"2000000\"}', 'Phí cược cont'),
(207, 1, 1530003903, 'shipdeposit', 'Cập nhật', '{\"shipdeposit_start_date\":1529946000,\"shipdeposit_end_date\":null,\"shipdeposit_shipping\":\"1\",\"shipdeposit_unit\":\"4\",\"shipdeposit_money\":\"2000000\"}', 'Phí cược cont'),
(208, 1, 1530003919, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX04\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"1\",\"dispatch_unit\":\"3\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"3\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"0\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(209, 1, 1530003933, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX04\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"1\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"1\",\"dispatch_unit\":\"4\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"3\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"0\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(210, 1, 1530004427, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529341200,\"dispatch_code\":\"DX01\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"1\",\"dispatch_place_to\":\"2\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1529427600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"3\",\"dispatch_ton\":\"2\",\"dispatch_unit\":\"3\",\"dispatch_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"dispatch_shipment_temp\":\"2\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"1\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"2\",\"dispatch_place_to_sub\":\"1\",\"dispatch_start_date_sub\":1529254800,\"dispatch_end_date_sub\":1530291600,\"dispatch_port_from_sub\":\"3\",\"dispatch_port_to_sub\":\"3\",\"dispatch_ton_sub\":\"40\",\"dispatch_unit_sub\":\"2\",\"dispatch_comment_sub\":\"L\\u00f4 ph\\u1ee5\",\"dispatch_shipment_temp_sub\":\"3\",\"dispatch_customer_sub\":\"3\",\"dispatch_booking_sub\":\"2\",\"dispatch_booking_type_sub\":\"1\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(211, 1, 1530062071, 'dispatch', 'Cập nhật', '{\"dispatch_date\":1529946000,\"dispatch_code\":\"DX03\",\"dispatch_vehicle\":\"2\",\"dispatch_romooc\":\"1\",\"dispatch_staff\":\"1\",\"dispatch_place_from\":\"2\",\"dispatch_place_to\":\"3\",\"dispatch_start_date\":1529254800,\"dispatch_end_date\":1530291600,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"0\",\"dispatch_unit\":\"3\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"3\",\"dispatch_customer\":\"3\",\"dispatch_booking\":\"2\",\"dispatch_booking_type\":\"1\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"0\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_update_user\":\"1\"}', 'Lệnh điều xe'),
(212, 1, 1530062125, 'shipment', 'Xóa', '\"4\"', 'phiếu vận chuyển'),
(213, 1, 1530072691, 'shipment', 'Thêm mới', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"2\",\"shipment_type\":\"2\",\"shipment_do\":\"\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"1\",\"shipment_booking_detail\":\"4\",\"shipment_container\":\"AU64542\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"3\",\"shipment_place_from\":\"1\",\"shipment_place_to\":\"2\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1529427960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"3\",\"shipment_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"shipment_price\":\"100000.00\",\"shipment_sub\":\"0\",\"shipment_road\":\"1,2\",\"shipment_cost\":\"1690000\",\"shipment_create_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"350000\\\",\\\"shipment_cost_deposit\\\":\\\"0\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"60000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"350000\\\"}\"}', 'Phiếu vận chuyển'),
(214, 1, 1530072994, 'shipment', 'Xóa', '\"6\"', 'phiếu vận chuyển'),
(215, 1, 1530073048, 'shipment', 'Thêm mới', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"2\",\"shipment_type\":\"2\",\"shipment_do\":\"\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"1\",\"shipment_booking_detail\":\"4\",\"shipment_container\":\"AU64542\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"3\",\"shipment_place_from\":\"1\",\"shipment_place_to\":\"2\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1529427960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"3\",\"shipment_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"shipment_price\":\"100000.00\",\"shipment_sub\":\"0\",\"shipment_road\":\"1,2\",\"shipment_create_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"350000\\\",\\\"shipment_cost_deposit\\\":\\\"0\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"60000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"350000\\\"}\"}', 'Phiếu vận chuyển'),
(216, 1, 1530148184, 'shipment', 'Xóa', '\"3\"', 'phiếu vận chuyển'),
(217, 1, 1530148189, 'shipment', 'Xóa', '\"1\"', 'phiếu vận chuyển'),
(218, 1, 1530148193, 'shipment', 'Xóa', '\"5\"', 'phiếu vận chuyển'),
(219, 1, 1530148295, 'shipment', 'Thêm mới', '{\"shipment_date\":1530118800,\"shipment_dispatch\":\"9\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"B5344\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"\",\"shipment_comment\":\"\",\"shipment_price\":\"1000000.00\",\"shipment_sub\":\"0\",\"shipment_road\":\"4\",\"shipment_create_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"0\\\",\\\"shipment_cost_deposit\\\":\\\"0\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"10000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"200000\\\"}\"}', 'Phiếu vận chuyển'),
(220, 1, 1530157539, 'shipment', 'Thêm mới', '{\"shipment_date\":1530118800,\"shipment_dispatch\":\"9\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"6\",\"shipment_container\":\"LU4354522\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"4\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"\",\"shipment_comment\":\"\",\"shipment_price\":\"2000000.00\",\"shipment_sub\":\"0\",\"shipment_road\":\"4\",\"shipment_create_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"0\\\",\\\"shipment_cost_deposit\\\":\\\"2000000\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"10000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"200000\\\"}\"}', 'Phiếu vận chuyển'),
(221, 1, 1530157551, 'shipment', 'Xóa', '\"9\"', 'phiếu vận chuyển'),
(222, 1, 1530157555, 'shipment', 'Xóa', '\"8\"', 'phiếu vận chuyển'),
(223, 1, 1530158233, 'shipment', 'Thêm mới', '{\"shipment_date\":1530118800,\"shipment_dispatch\":\"9\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_port_from\":null,\"shipment_port_to\":null,\"shipment_comment\":\"\",\"shipment_price\":\"1000000.00\",\"shipment_sub\":\"0\",\"shipment_road\":\"4\",\"shipment_create_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"0\\\",\\\"shipment_cost_deposit\\\":\\\"0\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"10000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"200000\\\"}\"}', 'Phiếu vận chuyển'),
(224, 1, 1530170302, 'shipment', 'Cập nhật', '{\"shipment_date\":1530118800,\"shipment_dispatch\":\"9\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"\",\"shipment_comment\":\"\",\"shipment_price\":\"undefined\",\"shipment_sub\":\"0\",\"shipment_road\":\"4\",\"shipment_update_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"0\\\",\\\"shipment_cost_deposit\\\":\\\"0\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"10000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"200000\\\"}\"}', 'Phiếu vận chuyển'),
(225, 1, 1530170309, 'shipment', 'Cập nhật', '{\"shipment_date\":1530118800,\"shipment_dispatch\":\"9\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"\",\"shipment_comment\":\"\",\"shipment_price\":\"undefined\",\"shipment_sub\":\"0\",\"shipment_road\":\"4\",\"shipment_update_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"0\\\",\\\"shipment_cost_deposit\\\":\\\"0\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"10000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"200000\\\"}\"}', 'Phiếu vận chuyển'),
(226, 1, 1530170784, 'shipment', 'Cập nhật', '{\"shipment_date\":1529341200,\"shipment_dispatch\":\"1\",\"shipment_customer\":\"2\",\"shipment_type\":\"2\",\"shipment_do\":\"\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"1\",\"shipment_booking_detail\":\"4\",\"shipment_container\":\"AU64542\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"3\",\"shipment_place_from\":\"1\",\"shipment_place_to\":\"2\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1529427960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"3\",\"shipment_comment\":\"H\\u00e0ng xu\\u1ea5t\",\"shipment_price\":\"undefined\",\"shipment_sub\":\"0\",\"shipment_road\":\"1,2\",\"shipment_update_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"350000\\\",\\\"shipment_cost_deposit\\\":\\\"0\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"60000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"350000\\\",\\\"shipment_cost_release\\\":\\\"50000\\\",\\\"shipment_cost_park\\\":\\\"100000\\\"}\"}', 'Phiếu vận chuyển'),
(227, 1, 1530170796, 'shipment', 'Cập nhật', '{\"shipment_date\":1530118800,\"shipment_dispatch\":\"9\",\"shipment_customer\":\"3\",\"shipment_type\":\"1\",\"shipment_do\":\"\",\"shipment_vehicle\":\"2\",\"shipment_romooc\":\"1\",\"shipment_staff\":\"1\",\"shipment_booking\":\"2\",\"shipment_booking_detail\":\"5\",\"shipment_container\":\"PI23243532\",\"shipment_ton_receive\":\"1\",\"shipment_ton\":\"1\",\"shipment_unit\":\"5\",\"shipment_place_from\":\"2\",\"shipment_place_to\":\"1\",\"shipment_start_date\":1529255160,\"shipment_end_date\":1530291960,\"shipment_port_from\":\"\",\"shipment_port_to\":\"\",\"shipment_comment\":\"\",\"shipment_price\":\"undefined\",\"shipment_sub\":\"0\",\"shipment_road\":\"4\",\"shipment_update_user\":\"1\",\"shipment_cost_detail\":\"{\\\"shipment_cost_lift_on\\\":\\\"0\\\",\\\"shipment_cost_lift_off\\\":\\\"0\\\",\\\"shipment_cost_lift_on_null\\\":\\\"0\\\",\\\"shipment_cost_lift_off_null\\\":\\\"0\\\",\\\"shipment_cost_deposit\\\":\\\"0\\\",\\\"shipment_cost_clean\\\":\\\"0\\\",\\\"shipment_cost_trans\\\":\\\"0\\\",\\\"shipment_cost_weight\\\":\\\"0\\\",\\\"shipment_cost_document\\\":\\\"0\\\",\\\"shipment_cost_toll\\\":\\\"10000\\\",\\\"shipment_cost_cont\\\":\\\"480000\\\",\\\"shipment_cost_ton\\\":\\\"150000\\\",\\\"shipment_cost_police\\\":\\\"300000\\\",\\\"shipment_cost_tire\\\":\\\"200000\\\",\\\"shipment_cost_release\\\":\\\"0\\\",\\\"shipment_cost_park\\\":\\\"0\\\"}\"}', 'Phiếu vận chuyển');
INSERT INTO `user_log` (`user_log_id`, `user_log`, `user_log_date`, `user_log_table`, `user_log_action`, `user_log_data`, `user_log_table_name`) VALUES
(228, 1, 1530196820, 'gas', 'Thêm mới', '{\"gas_date\":false,\"gas_km\":\"1000\",\"gas_km_gps\":\"1200\",\"gas_lit\":\"50\",\"gas_vehicle\":\"2\",\"gas_create_user\":\"1\"}', 'Đổ dầu'),
(229, 1, 1530196949, 'gas', 'Cập nhật', '{\"gas_date\":1530147660,\"gas_km\":\"1000\",\"gas_km_gps\":\"1200\",\"gas_lit\":\"50\",\"gas_vehicle\":\"2\",\"gas_update_user\":\"1\"}', 'Đổ dầu'),
(230, 1, 1530204762, 'vehicle', 'Cập nhật', '{\"vehicle_brand\":\"1\",\"vehicle_model\":\"E310\",\"vehicle_year\":\"2018\",\"vehicle_country\":\"220\",\"vehicle_owner\":null,\"vehicle_number\":\"50A-134.65\",\"vehicle_oil\":\"40\",\"vehicle_volume\":\"400\"}', 'Xe'),
(231, 1, 1530204774, 'vehicle', 'Cập nhật', '{\"vehicle_brand\":\"7\",\"vehicle_model\":\"A01\",\"vehicle_year\":\"2018\",\"vehicle_country\":\"86\",\"vehicle_owner\":\"1\",\"vehicle_number\":\"51B-2943\",\"vehicle_oil\":\"30\",\"vehicle_volume\":\"300\"}', 'Xe'),
(232, 1, 1530206264, 'gas', 'Cập nhật', '{\"gas_date\":1530061560,\"gas_km\":\"1000\",\"gas_km_gps\":\"1200\",\"gas_lit\":\"50\",\"gas_vehicle\":\"2\",\"gas_update_user\":\"1\"}', 'Đổ dầu'),
(233, 1, 1530206301, 'gas', 'Thêm mới', '{\"gas_date\":1530205200,\"gas_km\":\"1300\",\"gas_km_gps\":\"1400\",\"gas_lit\":\"60\",\"gas_vehicle\":\"2\",\"gas_create_user\":\"1\"}', 'Đổ dầu'),
(234, 1, 1530206857, 'gas', 'Cập nhật', '{\"gas_date\":1530205560,\"gas_km\":\"1300\",\"gas_km_gps\":\"1400\",\"gas_lit\":\"160\",\"gas_vehicle\":\"2\",\"gas_update_user\":\"1\"}', 'Đổ dầu'),
(235, 1, 1530206923, 'gas', 'Cập nhật', '{\"gas_date\":1530205560,\"gas_km\":\"1200\",\"gas_km_gps\":\"1400\",\"gas_lit\":\"160\",\"gas_vehicle\":\"2\",\"gas_update_user\":\"1\"}', 'Đổ dầu'),
(236, 1, 1530206955, 'gas', 'Cập nhật', '{\"gas_date\":1530205560,\"gas_km\":\"2000\",\"gas_km_gps\":\"2000\",\"gas_lit\":\"160\",\"gas_vehicle\":\"2\",\"gas_update_user\":\"1\"}', 'Đổ dầu'),
(237, 1, 1530257897, 'customer', 'Thêm mới', '{\"customer_code\":\"NCC03\",\"customer_name\":\"C\\u1ee5c qu\\u1ea3n l\\u00fd \\u0111\\u01b0\\u1eddng b\\u1ed9\",\"customer_company\":\"\",\"customer_mst\":\"\",\"customer_address\":\"\",\"customer_province\":\"31\",\"customer_phone\":\"\",\"customer_mobile\":\"\",\"customer_email\":\"\",\"customer_bank_account\":\"\",\"customer_bank_name\":\"\",\"customer_bank_branch\":\"\",\"customer_type\":\"2\",\"customer_sub\":\"\"}', 'Khách hàng - đối tác'),
(238, 1, 1530257937, 'road_cost', 'Thêm mới', '{\"road_cost_date\":1530205200,\"road_cost_start_date\":1530205200,\"road_cost_end_date\":1561741200,\"road_cost_vehicle\":\"2\",\"road_cost_romooc\":\"\",\"road_cost_customer\":\"5\",\"road_cost_price\":\"100000\",\"road_cost_vat\":\"10000\",\"road_cost_comment\":\"\\u0110\\u01b0\\u1eddng b\\u1ed9\"}', 'Phí sử dụng đường bộ'),
(239, 1, 1530266241, 'road_cost', 'Cập nhật', '{\"road_cost_date\":1530205200,\"road_cost_start_date\":1530205200,\"road_cost_end_date\":1561741200,\"road_cost_vehicle\":\"2,1\",\"road_cost_romooc\":null,\"road_cost_customer\":\"5\",\"road_cost_price\":\"100000\",\"road_cost_vat\":\"10000\",\"road_cost_comment\":\"\\u0110\\u01b0\\u1eddng b\\u1ed9\",\"road_cost_code\":\"0000054\"}', 'Phí sử dụng đường bộ'),
(240, 1, 1530266259, 'road_cost', 'Cập nhật', '{\"road_cost_date\":1530205200,\"road_cost_start_date\":1530205200,\"road_cost_end_date\":1561741200,\"road_cost_vehicle\":\"1\",\"road_cost_romooc\":\"1\",\"road_cost_customer\":\"5\",\"road_cost_price\":\"100000\",\"road_cost_vat\":\"10000\",\"road_cost_comment\":\"\\u0110\\u01b0\\u1eddng b\\u1ed9\",\"road_cost_code\":\"0000054\"}', 'Phí sử dụng đường bộ'),
(241, 1, 1530266440, 'road_cost', 'Cập nhật', '{\"road_cost_date\":1530205200,\"road_cost_start_date\":1530205200,\"road_cost_end_date\":1561741200,\"road_cost_vehicle\":null,\"road_cost_romooc\":\"1\",\"road_cost_customer\":\"5\",\"road_cost_price\":\"100000\",\"road_cost_vat\":\"10000\",\"road_cost_comment\":\"\\u0110\\u01b0\\u1eddng b\\u1ed9\",\"road_cost_code\":\"0000054\",\"road_cost_total_number\":1}', 'Phí sử dụng đường bộ'),
(242, 1, 1530266455, 'road_cost', 'Cập nhật', '{\"road_cost_date\":1530205200,\"road_cost_start_date\":1530205200,\"road_cost_end_date\":1561741200,\"road_cost_vehicle\":\"2\",\"road_cost_romooc\":\"1\",\"road_cost_customer\":\"5\",\"road_cost_price\":\"100000\",\"road_cost_vat\":\"10000\",\"road_cost_comment\":\"\\u0110\\u01b0\\u1eddng b\\u1ed9\",\"road_cost_code\":\"0000054\",\"road_cost_total_number\":2}', 'Phí sử dụng đường bộ'),
(243, 1, 1530775762, 'spare_part_code', 'Thêm mới', '{\"code\":\"DR11R20\",\"name\":\"Double Road 11.00R20\"}', 'Mã vật tư'),
(244, 1, 1530775790, 'spare_part_code', 'Cập nhật', '{\"code\":\"DR11R20DR801\",\"name\":\"Double Road 11.00R20 DR801\"}', 'Mã vật tư'),
(245, 1, 1530777137, 'spare_part_code', 'Thêm mới', '{\"code\":\"VT\",\"name\":\"VAT\"}', 'Mã vật tư'),
(246, 1, 1530777267, 'spare_part', 'Thêm mới', '{\"spare_part_code\":\"1\",\"spare_part_name\":\"Double Road 11.00R20 DR801\",\"spare_part_seri\":\"OT2524653636\",\"spare_part_brand\":\"DR\",\"spare_part_date_manufacture\":\"05-07-2018\",\"spare_part_unit\":\"B\\u1ed9\"}', 'Vật tư'),
(247, 1, 1530777771, 'spare_part', 'Cập nhật', '{\"spare_part_code\":\"1\",\"spare_part_name\":\"Double Road 11.00R20 DR801\",\"spare_part_seri\":\"OT2524653636\",\"spare_part_brand\":\"DR\",\"spare_part_date_manufacture\":1514739600,\"spare_part_unit\":\"B\\u1ed9\"}', 'Vật tư'),
(248, 1, 1530779074, 'spare_part', 'Thêm mới', '{\"spare_part_code\":\"2\",\"spare_part_name\":\"VAT0002\",\"spare_part_seri\":\"AR246432535\",\"spare_part_brand\":\"AT\",\"spare_part_date_manufacture\":1531846800,\"spare_part_unit\":\"L\"}', 'Vật tư'),
(249, 1, 1530779110, 'spare_part', 'Thêm mới', '{\"spare_part_code\":\"1\",\"spare_part_name\":\"Double Road 11.00R20 DR801\",\"spare_part_seri\":\"OT2424312\",\"spare_part_brand\":\"\",\"spare_part_date_manufacture\":false,\"spare_part_unit\":\"\"}', 'Vật tư'),
(250, 1, 1530842119, 'repair_code', 'Thêm mới', '{\"repair_code_name\":\"THAY NH\\u1edaT M\\u00c1Y\"}', 'Danh mục sửa chữa'),
(251, 1, 1530842135, 'repair_code', 'Thêm mới', '{\"repair_code_name\":\"THAY NH\\u1edaT H\\u1ed8P S\\u1ed0\"}', 'Danh mục sửa chữa'),
(252, 1, 1530928325, 'repair', 'Thêm mới', '{\"repair_date\":1530896400,\"repair_code\":\"2\",\"repair_number\":\"\",\"repair_vehicle\":\"2\",\"repair_romooc\":\"\",\"repair_price\":\"250,000\",\"repair_staff\":\"1\",\"repair_create_user\":\"1\"}', 'Phiếu sửa chữa'),
(253, 1, 1530929700, 'repair', 'Cập nhật', '{\"repair_date\":1530896400,\"repair_code\":\"2\",\"repair_number\":\"PSC001\",\"repair_vehicle\":\"2\",\"repair_romooc\":\"\",\"repair_price\":\"250000\",\"repair_staff\":\"1\",\"repair_update_user\":\"1\"}', 'Phiếu sửa chữa'),
(254, 1, 1531376437, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(255, 1, 1531378132, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(256, 1, 1531378232, 'spare_stock', 'Xóa', '\"3\"', 'Chi tiết phiếu nhập kho'),
(257, 1, 1531378260, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(258, 1, 1531378636, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(259, 1, 1531380762, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(260, 1, 1531380814, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(261, 1, 1531380895, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(262, 1, 1531380969, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(263, 1, 1531381287, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(264, 1, 1531381890, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(265, 1, 1531382255, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK02\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"a\",\"import_stock_invoice_number\":\"745\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"a\",\"import_stock_deliver_address\":\"a\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(266, 1, 1531382290, 'spare_stock', 'Xóa', '\"2\"', 'Chi tiết phiếu nhập kho'),
(267, 1, 1531382304, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(268, 1, 1531382509, 'spare_stock', 'Xóa', '\"2\"', 'Chi tiết phiếu nhập kho (code)'),
(269, 1, 1531382512, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(270, 1, 1531382584, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(271, 1, 1531382617, 'spare_stock', 'Xóa', '\"7\"', 'Chi tiết phiếu nhập kho'),
(272, 1, 1531382619, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(273, 1, 1531382790, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(274, 1, 1531383306, 'spare_stock', 'Xóa', '\"2\"', 'Chi tiết phiếu nhập kho (code)'),
(275, 1, 1531383672, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(276, 1, 1531386218, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(277, 1, 1531388117, 'spare_stock', 'Xóa', '\"2\"', 'Chi tiết phiếu nhập kho (code)'),
(278, 1, 1531388120, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(279, 1, 1531388132, 'spare_stock', 'Xóa', '\"10\"', 'Chi tiết phiếu nhập kho'),
(280, 1, 1531388152, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(281, 1, 1531388483, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(282, 1, 1531388514, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(283, 1, 1531388593, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(284, 1, 1531388780, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(285, 1, 1531388834, 'import_stock', 'Cập nhật', '{\"import_stock_date\":1531242000,\"import_stock_code\":\"PNK01\",\"import_stock_customer\":\"1\",\"import_stock_comment\":\"Nh\\u1eadp kho\",\"import_stock_invoice_number\":\"000423\",\"import_stock_invoice_date\":1531242000,\"import_stock_deliver\":\"A\",\"import_stock_deliver_address\":\"BH\",\"import_stock_house\":\"1\",\"import_stock_update_user\":\"1\"}', 'Phiếu nhập kho'),
(286, 1, 1531536150, 'spare_stock', 'Xóa', '\"2\"', 'Chi tiết phiếu nhập kho (code)'),
(287, 1, 1531541814, 'export_stock', 'Thêm mới', '{\"export_stock_date\":1531501200,\"export_stock_code\":\"PNK01\",\"export_stock_comment\":\"XK\",\"export_stock_house\":\"1\",\"export_stock_create_user\":\"1\"}', 'Phiếu xuất kho'),
(288, 1, 1531710947, 'export_stock', 'Xóa', '\"1\"', 'Phiếu xuất kho'),
(289, 1, 1531710972, 'export_stock', 'Thêm mới', '{\"export_stock_date\":1531674000,\"export_stock_code\":\"PNK01\",\"export_stock_comment\":\"XK\",\"export_stock_house\":\"1\",\"export_stock_create_user\":\"1\"}', 'Phiếu xuất kho'),
(290, 1, 1531727722, 'export_stock', 'Cập nhật', '{\"export_stock_date\":1531674000,\"export_stock_code\":\"PNK01\",\"export_stock_comment\":\"XK\",\"export_stock_house\":\"selected=\",\"export_stock_update_user\":\"1\"}', 'Phiếu xuất kho'),
(291, 1, 1531727735, 'export_stock', 'Cập nhật', '{\"export_stock_date\":1531674000,\"export_stock_code\":\"PNK01\",\"export_stock_comment\":\"XK\",\"export_stock_house\":\"\",\"export_stock_update_user\":\"1\"}', 'Phiếu xuất kho'),
(292, 1, 1531727759, 'export_stock', 'Cập nhật', '{\"export_stock_date\":1531674000,\"export_stock_code\":\"PNK01\",\"export_stock_comment\":\"XK\",\"export_stock_house\":\"\",\"export_stock_update_user\":\"1\"}', 'Phiếu xuất kho'),
(293, 1, 1531727935, 'spare_stock', 'Xóa', '\"2\"', 'Chi tiết phiếu xuất kho (code)'),
(294, 1, 1531727939, 'export_stock', 'Cập nhật', '{\"export_stock_date\":1531674000,\"export_stock_code\":\"PNK01\",\"export_stock_comment\":\"XK\",\"export_stock_house\":\"\",\"export_stock_update_user\":\"1\"}', 'Phiếu xuất kho'),
(295, 1, 1531731183, 'booking', 'Thêm mới', '{\"booking_date\":1531674000,\"booking_code\":\"DH005\",\"booking_customer\":\"2\",\"booking_number\":\"1234567\",\"booking_type\":\"2\",\"booking_shipping\":\"\",\"booking_shipping_name\":\"\",\"booking_shipping_number\":\"\",\"booking_place_from\":\"3\",\"booking_place_to\":\"3\",\"booking_start_date\":1531674000,\"booking_end_date\":1531674000,\"booking_sum\":\"5\",\"booking_total\":\"0\",\"booking_comment\":\"\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(296, 1, 1531731321, 'road', 'Thêm mới', '{\"road_place_from\":\"3\",\"road_place_to\":\"1\",\"road_route_from\":\"3\",\"road_route_to\":\"3\",\"road_start_date\":1531674000,\"road_end_date\":1531674000,\"road_time\":\"\",\"road_km\":\"50\",\"road_oil\":\"16\",\"road_oil_ton\":\"\",\"road_bridge\":\"\",\"road_police\":\"\",\"road_tire\":\"\",\"road_over\":\"\",\"road_add\":\"1000000\",\"road_salary\":\"200\"}', 'Định mức tuyến đường'),
(297, 1, 1531731440, 'shipment_temp', 'Nhận', '{\"shipment_temp_date\":1531674000,\"shipment_temp_owner\":\"1\",\"shipment_temp_booking\":\"5\",\"shipment_temp_status\":0,\"shipment_temp_ton\":\"5\",\"shipment_temp_number\":\"\"}', 'Lô hàng mới'),
(298, 1, 1531731612, 'dispatch', 'Thêm mới', '{\"dispatch_date\":1531674000,\"dispatch_code\":\"DX05\",\"dispatch_vehicle\":\"1\",\"dispatch_romooc\":\"undefined\",\"dispatch_staff\":\"undefined\",\"dispatch_place_from\":\"3\",\"dispatch_place_to\":\"3\",\"dispatch_start_date\":1531674000,\"dispatch_end_date\":1531674000,\"dispatch_port_from\":\"\",\"dispatch_port_to\":\"\",\"dispatch_ton\":\"\",\"dispatch_unit\":\"6\",\"dispatch_comment\":\"\",\"dispatch_shipment_temp\":\"\",\"dispatch_customer\":\"2\",\"dispatch_booking\":\"5\",\"dispatch_booking_type\":\"2\",\"dispatch_place_from_sub\":\"\",\"dispatch_place_to_sub\":\"\",\"dispatch_start_date_sub\":false,\"dispatch_end_date_sub\":false,\"dispatch_port_from_sub\":\"\",\"dispatch_port_to_sub\":\"\",\"dispatch_ton_sub\":\"\",\"dispatch_unit_sub\":\"6\",\"dispatch_comment_sub\":\"\",\"dispatch_shipment_temp_sub\":\"\",\"dispatch_customer_sub\":\"\",\"dispatch_booking_sub\":\"\",\"dispatch_booking_type_sub\":\"\",\"dispatch_create_user\":\"1\"}', 'Lệnh điều xe'),
(299, 1, 1531732263, 'booking', 'Thêm mới', '{\"booking_date\":1531674000,\"booking_code\":\"DH006\",\"booking_customer\":\"3\",\"booking_number\":\"\",\"booking_type\":\"1\",\"booking_shipping\":\"\",\"booking_shipping_name\":\"\",\"booking_shipping_number\":\"\",\"booking_place_from\":\"3\",\"booking_place_to\":\"3\",\"booking_start_date\":1531674000,\"booking_end_date\":1531674000,\"booking_sum\":\"5\",\"booking_total\":\"0\",\"booking_comment\":\"\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(300, 1, 1531734455, 'unit', 'Xóa', '[\"\"]', 'Đơn vị tính'),
(301, 1, 1534991341, 'booking', 'Thêm mới', '{\"booking_date\":1534957200,\"booking_customer\":\"3\",\"booking_number\":\"booking 1\",\"booking_type\":\"2\",\"booking_shipping\":\"\",\"booking_shipping_name\":\"\",\"booking_shipping_number\":\"\",\"booking_comment\":\"\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(302, 1, 1540439945, 'place', 'Thêm mới', '{\"place_province\":\"19\",\"place_name\":\"AMATA\",\"place_code\":\"AMATA\",\"place_lat\":\"10.949585\",\"place_long\":\"106.87175990000003\",\"place_port\":null}', 'Kho hàng'),
(303, 1, 1540439979, 'place', 'Thêm mới', '{\"place_province\":\"8\",\"place_name\":\"VSIP1\",\"place_code\":\"VSIP1\",\"place_lat\":\"10.924517\",\"place_long\":\"106.71365089999995\",\"place_port\":null}', 'Kho hàng'),
(304, 1, 1540440002, 'place', 'Thêm mới', '{\"place_province\":\"7\",\"place_name\":\"\\u00c2U VI\\u1ec6T\",\"place_code\":\"AV\",\"place_lat\":\"10.182521\",\"place_long\":\"106.33165699999995\",\"place_port\":null}', 'Kho hàng'),
(305, 1, 1540443464, 'booking', 'Thêm mới', '{\"booking_date\":1540400400,\"booking_customer\":\"3\",\"booking_number\":\"\",\"booking_type\":\"1\",\"booking_shipping\":\"\",\"booking_shipping_name\":\"\",\"booking_shipping_number\":\"\",\"booking_comment\":\"\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(306, 1, 1541131223, 'booking', 'Thêm mới', '{\"booking_date\":1541091600,\"booking_customer\":\"3\",\"booking_number\":\"0123456\",\"booking_type\":\"2\",\"booking_shipping\":\"\",\"booking_shipping_name\":\"\",\"booking_shipping_number\":\"\",\"booking_comment\":\"\",\"booking_create_user\":\"1\"}', 'Đơn hàng'),
(307, 1, 1541131236, 'booking', 'Cập nhật', '{\"booking_date\":1541091600,\"booking_customer\":\"3\",\"booking_number\":\"0123456\",\"booking_type\":\"2\",\"booking_shipping\":\"\",\"booking_shipping_name\":\"\",\"booking_shipping_number\":\"\",\"booking_comment\":\"\",\"booking_update_user\":\"1\"}', 'Đơn hàng'),
(308, 1, 1541810404, 'coordinate', 'Thêm mới', '{\"coordinate_date\":1541782800,\"coordinate_code\":\"DX01\",\"coordinate_vehicle\":\"2\",\"coordinate_booking\":\"\",\"coordinate_booking_number\":\"1676\",\"coordinate_place\":\"4\",\"coordinate_unit\":\"6\",\"coordinate_number\":\"1\",\"coordinate_type\":\"1\",\"coordinate_comment\":\"\",\"coordinate_create_user\":\"1\"}', 'Lệnh điều xe'),
(309, 1, 1556931835, 'vehicle_romooc', 'Xóa', '\"1\"', 'Thay lắp mooc'),
(310, 1, 1556931919, 'staff', 'Xóa', '\"1\"', 'Nhân viên'),
(311, 1, 1556936012, 'toll', 'Cập nhật', '{\"toll_province\":\"19\",\"toll_name\":\"C\\u00f4ng ty CP ph\\u00e1t tri\\u1ec3n \\u0111\\u01b0\\u1eddng cao t\\u1ed1c Bi\\u00ean H\\u00f2a - V\\u0169ng T\\u00e0u\",\"toll_code\":\"QL 51 T1\",\"toll_mst\":\"3603023253\",\"toll_type\":\"2\",\"toll_symbol\":\"AA\\/02\",\"toll_lat\":\"10.8606451\",\"toll_long\":\"106.92575650000003\"}', 'Trạm thu phí'),
(312, 1, 1556936031, 'toll', 'Cập nhật', '{\"toll_province\":\"19\",\"toll_name\":\"C\\u00f4ng ty CP ph\\u00e1t tri\\u1ec3n \\u0111\\u01b0\\u1eddng cao t\\u1ed1c Bi\\u00ean H\\u00f2a - V\\u0169ng T\\u00e0u\",\"toll_code\":\"QL 51 T1\",\"toll_mst\":\"3603023253\",\"toll_type\":\"1\",\"toll_symbol\":\"AA\\/02\",\"toll_lat\":\"10.8606451\",\"toll_long\":\"106.92575650000003\"}', 'Trạm thu phí');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_number` varchar(20) DEFAULT NULL,
  `vehicle_brand` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(20) DEFAULT NULL,
  `vehicle_year` int(11) DEFAULT NULL,
  `vehicle_country` int(11) DEFAULT NULL,
  `vehicle_owner` int(11) DEFAULT NULL COMMENT '1:Thuê',
  `vehicle_oil` float DEFAULT NULL,
  `vehicle_volume` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `vehicle`
--

INSERT INTO `vehicle` (`vehicle_id`, `vehicle_number`, `vehicle_brand`, `vehicle_model`, `vehicle_year`, `vehicle_country`, `vehicle_owner`, `vehicle_oil`, `vehicle_volume`) VALUES
(1, '51B-2943', '7', 'A01', 2018, 86, 1, 30, 300),
(2, '50A-134.65', '1', 'E310', 2018, 220, NULL, 40, 400);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vehicle_romooc`
--

CREATE TABLE `vehicle_romooc` (
  `vehicle_romooc_id` int(11) NOT NULL,
  `vehicle` int(11) DEFAULT NULL,
  `romooc` int(11) DEFAULT NULL,
  `start_time` int(11) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vehicle_work`
--

CREATE TABLE `vehicle_work` (
  `vehicle_work_id` int(11) NOT NULL,
  `vehicle` int(11) DEFAULT NULL,
  `vehicle_work_start_date` int(11) DEFAULT NULL,
  `vehicle_work_end_date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `vehicle_work`
--

INSERT INTO `vehicle_work` (`vehicle_work_id`, `vehicle`, `vehicle_work_start_date`, `vehicle_work_end_date`) VALUES
(1, 1, 1528995600, 1530291600);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `warehouse`
--

CREATE TABLE `warehouse` (
  `warehouse_id` int(11) NOT NULL,
  `warehouse_place` int(11) DEFAULT NULL,
  `warehouse_cont` decimal(14,2) DEFAULT NULL,
  `warehouse_ton` decimal(14,2) DEFAULT NULL,
  `warehouse_add` decimal(14,2) DEFAULT NULL,
  `warehouse_weight` decimal(14,2) DEFAULT NULL,
  `warehouse_clean` decimal(14,2) DEFAULT NULL,
  `warehouse_gate` decimal(14,2) DEFAULT NULL,
  `warehouse_start_date` int(11) DEFAULT NULL,
  `warehouse_end_date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `warehouse`
--

INSERT INTO `warehouse` (`warehouse_id`, `warehouse_place`, `warehouse_cont`, `warehouse_ton`, `warehouse_add`, `warehouse_weight`, `warehouse_clean`, `warehouse_gate`, `warehouse_start_date`, `warehouse_end_date`) VALUES
(1, 1, '480000.00', '150000.00', '200000.00', '20000.00', '10000.00', '250000.00', 1527786000, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bonus`
--
ALTER TABLE `bonus`
  ADD PRIMARY KEY (`bonus_id`);

--
-- Chỉ mục cho bảng `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`);

--
-- Chỉ mục cho bảng `booking_detail`
--
ALTER TABLE `booking_detail`
  ADD PRIMARY KEY (`booking_detail_id`);

--
-- Chỉ mục cho bảng `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Chỉ mục cho bảng `checking_cost`
--
ALTER TABLE `checking_cost`
  ADD PRIMARY KEY (`checking_cost_id`);

--
-- Chỉ mục cho bảng `contact_person`
--
ALTER TABLE `contact_person`
  ADD PRIMARY KEY (`contact_person_id`);

--
-- Chỉ mục cho bảng `coordinate`
--
ALTER TABLE `coordinate`
  ADD PRIMARY KEY (`coordinate_id`);

--
-- Chỉ mục cho bảng `cost_list`
--
ALTER TABLE `cost_list`
  ADD PRIMARY KEY (`cost_list_id`);

--
-- Chỉ mục cho bảng `cost_type`
--
ALTER TABLE `cost_type`
  ADD PRIMARY KEY (`cost_type_id`);

--
-- Chỉ mục cho bảng `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`);

--
-- Chỉ mục cho bảng `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Chỉ mục cho bảng `customer_sub`
--
ALTER TABLE `customer_sub`
  ADD PRIMARY KEY (`customer_sub_id`);

--
-- Chỉ mục cho bảng `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Chỉ mục cho bảng `dispatch`
--
ALTER TABLE `dispatch`
  ADD PRIMARY KEY (`dispatch_id`);

--
-- Chỉ mục cho bảng `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`driver_id`);

--
-- Chỉ mục cho bảng `export_stock`
--
ALTER TABLE `export_stock`
  ADD PRIMARY KEY (`export_stock_id`);

--
-- Chỉ mục cho bảng `gas`
--
ALTER TABLE `gas`
  ADD PRIMARY KEY (`gas_id`);

--
-- Chỉ mục cho bảng `house`
--
ALTER TABLE `house`
  ADD PRIMARY KEY (`house_id`);

--
-- Chỉ mục cho bảng `import_stock`
--
ALTER TABLE `import_stock`
  ADD PRIMARY KEY (`import_stock_id`);

--
-- Chỉ mục cho bảng `import_stock_cost`
--
ALTER TABLE `import_stock_cost`
  ADD PRIMARY KEY (`import_stock_cost_id`);

--
-- Chỉ mục cho bảng `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`info_id`);

--
-- Chỉ mục cho bảng `insurance_cost`
--
ALTER TABLE `insurance_cost`
  ADD PRIMARY KEY (`insurance_cost_id`);

--
-- Chỉ mục cho bảng `lift`
--
ALTER TABLE `lift`
  ADD PRIMARY KEY (`lift_id`);

--
-- Chỉ mục cho bảng `oil`
--
ALTER TABLE `oil`
  ADD PRIMARY KEY (`oil_id`);

--
-- Chỉ mục cho bảng `place`
--
ALTER TABLE `place`
  ADD PRIMARY KEY (`place_id`);

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
-- Chỉ mục cho bảng `repair`
--
ALTER TABLE `repair`
  ADD PRIMARY KEY (`repair_id`);

--
-- Chỉ mục cho bảng `repair_code`
--
ALTER TABLE `repair_code`
  ADD PRIMARY KEY (`repair_code_id`);

--
-- Chỉ mục cho bảng `repair_list`
--
ALTER TABLE `repair_list`
  ADD PRIMARY KEY (`repair_list_id`);

--
-- Chỉ mục cho bảng `road`
--
ALTER TABLE `road`
  ADD PRIMARY KEY (`road_id`);

--
-- Chỉ mục cho bảng `road_cost`
--
ALTER TABLE `road_cost`
  ADD PRIMARY KEY (`road_cost_id`);

--
-- Chỉ mục cho bảng `road_oil`
--
ALTER TABLE `road_oil`
  ADD PRIMARY KEY (`road_oil_id`);

--
-- Chỉ mục cho bảng `road_toll`
--
ALTER TABLE `road_toll`
  ADD PRIMARY KEY (`road_toll_id`);

--
-- Chỉ mục cho bảng `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Chỉ mục cho bảng `romooc`
--
ALTER TABLE `romooc`
  ADD PRIMARY KEY (`romooc_id`);

--
-- Chỉ mục cho bảng `route`
--
ALTER TABLE `route`
  ADD PRIMARY KEY (`route_id`);

--
-- Chỉ mục cho bảng `shipdeposit`
--
ALTER TABLE `shipdeposit`
  ADD PRIMARY KEY (`shipdeposit_id`);

--
-- Chỉ mục cho bảng `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`shipment_id`);

--
-- Chỉ mục cho bảng `shipment_cost`
--
ALTER TABLE `shipment_cost`
  ADD PRIMARY KEY (`shipment_cost_id`);

--
-- Chỉ mục cho bảng `shipment_temp`
--
ALTER TABLE `shipment_temp`
  ADD PRIMARY KEY (`shipment_temp_id`);

--
-- Chỉ mục cho bảng `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`);

--
-- Chỉ mục cho bảng `spare_drap`
--
ALTER TABLE `spare_drap`
  ADD PRIMARY KEY (`spare_drap_id`);

--
-- Chỉ mục cho bảng `spare_part`
--
ALTER TABLE `spare_part`
  ADD PRIMARY KEY (`spare_part_id`);

--
-- Chỉ mục cho bảng `spare_part_code`
--
ALTER TABLE `spare_part_code`
  ADD PRIMARY KEY (`spare_part_code_id`);

--
-- Chỉ mục cho bảng `spare_stock`
--
ALTER TABLE `spare_stock`
  ADD PRIMARY KEY (`spare_stock_id`);

--
-- Chỉ mục cho bảng `spare_vehicle`
--
ALTER TABLE `spare_vehicle`
  ADD PRIMARY KEY (`spare_vehicle_id`);

--
-- Chỉ mục cho bảng `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Chỉ mục cho bảng `toll`
--
ALTER TABLE `toll`
  ADD PRIMARY KEY (`toll_id`);

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
-- Chỉ mục cho bảng `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- Chỉ mục cho bảng `vehicle_romooc`
--
ALTER TABLE `vehicle_romooc`
  ADD PRIMARY KEY (`vehicle_romooc_id`);

--
-- Chỉ mục cho bảng `vehicle_work`
--
ALTER TABLE `vehicle_work`
  ADD PRIMARY KEY (`vehicle_work_id`);

--
-- Chỉ mục cho bảng `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`warehouse_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bonus`
--
ALTER TABLE `bonus`
  MODIFY `bonus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT cho bảng `booking_detail`
--
ALTER TABLE `booking_detail`
  MODIFY `booking_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT cho bảng `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT cho bảng `checking_cost`
--
ALTER TABLE `checking_cost`
  MODIFY `checking_cost_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT cho bảng `contact_person`
--
ALTER TABLE `contact_person`
  MODIFY `contact_person_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `coordinate`
--
ALTER TABLE `coordinate`
  MODIFY `coordinate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `cost_list`
--
ALTER TABLE `cost_list`
  MODIFY `cost_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `cost_type`
--
ALTER TABLE `cost_type`
  MODIFY `cost_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT cho bảng `country`
--
ALTER TABLE `country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;
--
-- AUTO_INCREMENT cho bảng `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT cho bảng `customer_sub`
--
ALTER TABLE `customer_sub`
  MODIFY `customer_sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT cho bảng `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT cho bảng `dispatch`
--
ALTER TABLE `dispatch`
  MODIFY `dispatch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT cho bảng `driver`
--
ALTER TABLE `driver`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `export_stock`
--
ALTER TABLE `export_stock`
  MODIFY `export_stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `gas`
--
ALTER TABLE `gas`
  MODIFY `gas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `house`
--
ALTER TABLE `house`
  MODIFY `house_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `import_stock`
--
ALTER TABLE `import_stock`
  MODIFY `import_stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `import_stock_cost`
--
ALTER TABLE `import_stock_cost`
  MODIFY `import_stock_cost_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `info`
--
ALTER TABLE `info`
  MODIFY `info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `insurance_cost`
--
ALTER TABLE `insurance_cost`
  MODIFY `insurance_cost_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT cho bảng `lift`
--
ALTER TABLE `lift`
  MODIFY `lift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `oil`
--
ALTER TABLE `oil`
  MODIFY `oil_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `place`
--
ALTER TABLE `place`
  MODIFY `place_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT cho bảng `port`
--
ALTER TABLE `port`
  MODIFY `port_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT cho bảng `position`
--
ALTER TABLE `position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT cho bảng `province`
--
ALTER TABLE `province`
  MODIFY `province_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT cho bảng `repair`
--
ALTER TABLE `repair`
  MODIFY `repair_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `repair_code`
--
ALTER TABLE `repair_code`
  MODIFY `repair_code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `repair_list`
--
ALTER TABLE `repair_list`
  MODIFY `repair_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `road`
--
ALTER TABLE `road`
  MODIFY `road_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT cho bảng `road_cost`
--
ALTER TABLE `road_cost`
  MODIFY `road_cost_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `road_oil`
--
ALTER TABLE `road_oil`
  MODIFY `road_oil_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT cho bảng `road_toll`
--
ALTER TABLE `road_toll`
  MODIFY `road_toll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT cho bảng `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT cho bảng `romooc`
--
ALTER TABLE `romooc`
  MODIFY `romooc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `route`
--
ALTER TABLE `route`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT cho bảng `shipdeposit`
--
ALTER TABLE `shipdeposit`
  MODIFY `shipdeposit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `shipment`
--
ALTER TABLE `shipment`
  MODIFY `shipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT cho bảng `shipment_cost`
--
ALTER TABLE `shipment_cost`
  MODIFY `shipment_cost_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `shipment_temp`
--
ALTER TABLE `shipment_temp`
  MODIFY `shipment_temp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT cho bảng `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT cho bảng `spare_drap`
--
ALTER TABLE `spare_drap`
  MODIFY `spare_drap_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT cho bảng `spare_part`
--
ALTER TABLE `spare_part`
  MODIFY `spare_part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT cho bảng `spare_part_code`
--
ALTER TABLE `spare_part_code`
  MODIFY `spare_part_code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `spare_stock`
--
ALTER TABLE `spare_stock`
  MODIFY `spare_stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT cho bảng `spare_vehicle`
--
ALTER TABLE `spare_vehicle`
  MODIFY `spare_vehicle_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT cho bảng `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT cho bảng `toll`
--
ALTER TABLE `toll`
  MODIFY `toll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
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
  MODIFY `user_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=313;
--
-- AUTO_INCREMENT cho bảng `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT cho bảng `vehicle_romooc`
--
ALTER TABLE `vehicle_romooc`
  MODIFY `vehicle_romooc_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT cho bảng `vehicle_work`
--
ALTER TABLE `vehicle_work`
  MODIFY `vehicle_work_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT cho bảng `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `warehouse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
