-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 23, 2018 lúc 11:26 AM
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
(1, 'Quản trị cấp cao', 1, '[\"oil\",\"road\",\"warehouse\",\"customer\",\"vehicle\",\"romooc\",\"place\",\"route\",\"salary\",\"salarybonus\",\"steersman\",\"staff\",\"department\",\"importstock\",\"exportstock\",\"house\",\"sparepart\",\"repair\",\"roadcost\",\"checkingcost\",\"insurancecost\",\"sparevehicle\",\"sparevehiclepass\",\"sparevehiclelist\",\"sparedrap\",\"stock\",\"used\",\"spareparttracking\",\"shipment\",\"newshipment\",\"shipmenttemp\",\"driver\",\"vehiclework\",\"vehicleromooc\",\"vehicleromooc\",\"tollcost\",\"marketing\",\"shipmentlist\",\"loanlist\",\"sell\",\"receiptvoucher\",\"paymentvoucher\",\"internaltransfer\",\"bankbalance\",\"receivable\",\"payable\",\"loan\",\"importstock\",\"exportstock\",\"stock\",\"vat\",\"vat\",\"exvat\",\"sales\",\"cost\",\"noinvoice\",\"tolls\",\"salary\",\"salary\",\"repairsalary\",\"costlist\",\"bank\",\"account\",\"trucking\",\"customership\",\"truckinglist\",\"repairlist\",\"roadcostlist\",\"checkingcostlist\",\"insurancecostlist\",\"oilreport\",\"advance\",\"commission\",\"quantity\",\"profit\",\"round\",\"officecost\",\"vehicleanalytics\",\"report\",\"info\",\"user\",\"permission\",\"backup\",\"update\"]', '{\"oil\":\"oil\",\"road\":\"road\",\"warehouse\":\"warehouse\",\"customer\":\"customer\",\"vehicle\":\"vehicle\",\"romooc\":\"romooc\",\"place\":\"place\",\"route\":\"route\",\"salary\":\"salary\",\"salarybonus\":\"salarybonus\",\"steersman\":\"steersman\",\"staff\":\"staff\",\"department\":\"department\",\"importstock\":\"importstock\",\"exportstock\":\"exportstock\",\"house\":\"house\",\"sparepart\":\"sparepart\",\"repair\":\"repair\",\"roadcost\":\"roadcost\",\"checkingcost\":\"checkingcost\",\"insurancecost\":\"insurancecost\",\"sparevehicle\":\"sparevehicle\",\"sparevehiclepass\":\"sparevehiclepass\",\"sparevehiclelist\":\"sparevehiclelist\",\"sparedrap\":\"sparedrap\",\"stock\":\"stock\",\"used\":\"used\",\"spareparttracking\":\"spareparttracking\",\"shipment\":\"shipment\",\"newshipment\":\"newshipment\",\"shipmenttemp\":\"shipmenttemp\",\"driver\":\"driver\",\"vehiclework\":\"vehiclework\",\"vehicleromooc\":\"vehicleromooc\",\"tollcost\":\"tollcost\",\"marketing\":\"marketing\",\"shipmentlist\":\"shipmentlist\",\"loanlist\":\"loanlist\",\"sell\":\"sell\",\"receiptvoucher\":\"receiptvoucher\",\"paymentvoucher\":\"paymentvoucher\",\"internaltransfer\":\"internaltransfer\",\"bankbalance\":\"bankbalance\",\"receivable\":\"receivable\",\"payable\":\"payable\",\"loan\":\"loan\",\"vat\":\"vat\",\"exvat\":\"exvat\",\"sales\":\"sales\",\"cost\":\"cost\",\"noinvoice\":\"noinvoice\",\"tolls\":\"tolls\",\"repairsalary\":\"repairsalary\",\"costlist\":\"costlist\",\"bank\":\"bank\",\"account\":\"account\",\"trucking\":\"trucking\",\"customership\":\"customership\",\"truckinglist\":\"truckinglist\",\"repairlist\":\"repairlist\",\"roadcostlist\":\"roadcostlist\",\"checkingcostlist\":\"checkingcostlist\",\"insurancecostlist\":\"insurancecostlist\",\"oilreport\":\"oilreport\",\"advance\":\"advance\",\"commission\":\"commission\",\"quantity\":\"quantity\",\"profit\":\"profit\",\"round\":\"round\",\"officecost\":\"officecost\",\"vehicleanalytics\":\"vehicleanalytics\",\"report\":\"report\",\"info\":\"info\",\"user\":\"user\",\"permission\":\"permission\",\"backup\":\"backup\",\"update\":\"update\"}'),
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
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `create_time` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  `user_lock` int(11) DEFAULT NULL,
  `user_group` int(11) DEFAULT NULL,
  `user_dept` int(11) DEFAULT NULL,
  `permission` text,
  `permission_action` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `create_time`, `role`, `user_lock`, `user_group`, `user_dept`, `permission`, `permission_action`) VALUES
(1, 'admin', '81dc9bdb52d04dc20036dbd8313ed055', 0, 1, NULL, NULL, NULL, '[\"oil\",\"road\",\"warehouse\",\"customer\",\"vehicle\",\"romooc\",\"place\",\"route\",\"salary\",\"salarybonus\",\"steersman\",\"staff\",\"department\",\"importstock\",\"exportstock\",\"house\",\"sparepart\",\"repair\",\"roadcost\",\"checkingcost\",\"insurancecost\",\"sparevehicle\",\"sparevehiclepass\",\"sparevehiclelist\",\"sparedrap\",\"stock\",\"used\",\"spareparttracking\",\"shipment\",\"newshipment\",\"shipmenttemp\",\"driver\",\"vehiclework\",\"vehicleromooc\",\"vehicleromooc\",\"tollcost\",\"marketing\",\"shipmentlist\",\"loanlist\",\"sell\",\"receiptvoucher\",\"paymentvoucher\",\"internaltransfer\",\"bankbalance\",\"receivable\",\"payable\",\"loan\",\"importstock\",\"exportstock\",\"stock\",\"vat\",\"vat\",\"exvat\",\"sales\",\"cost\",\"noinvoice\",\"tolls\",\"salary\",\"salary\",\"repairsalary\",\"costlist\",\"bank\",\"account\",\"trucking\",\"customership\",\"truckinglist\",\"repairlist\",\"roadcostlist\",\"checkingcostlist\",\"insurancecostlist\",\"oilreport\",\"advance\",\"commission\",\"quantity\",\"profit\",\"round\",\"officecost\",\"vehicleanalytics\",\"report\",\"info\",\"user\",\"permission\",\"backup\",\"update\"]', '{\"oil\":\"oil\",\"road\":\"road\",\"warehouse\":\"warehouse\",\"customer\":\"customer\",\"vehicle\":\"vehicle\",\"romooc\":\"romooc\",\"place\":\"place\",\"route\":\"route\",\"salary\":\"salary\",\"salarybonus\":\"salarybonus\",\"steersman\":\"steersman\",\"staff\":\"staff\",\"department\":\"department\",\"importstock\":\"importstock\",\"exportstock\":\"exportstock\",\"house\":\"house\",\"sparepart\":\"sparepart\",\"repair\":\"repair\",\"roadcost\":\"roadcost\",\"checkingcost\":\"checkingcost\",\"insurancecost\":\"insurancecost\",\"sparevehicle\":\"sparevehicle\",\"sparevehiclepass\":\"sparevehiclepass\",\"sparevehiclelist\":\"sparevehiclelist\",\"sparedrap\":\"sparedrap\",\"stock\":\"stock\",\"used\":\"used\",\"spareparttracking\":\"spareparttracking\",\"shipment\":\"shipment\",\"newshipment\":\"newshipment\",\"shipmenttemp\":\"shipmenttemp\",\"driver\":\"driver\",\"vehiclework\":\"vehiclework\",\"vehicleromooc\":\"vehicleromooc\",\"tollcost\":\"tollcost\",\"marketing\":\"marketing\",\"shipmentlist\":\"shipmentlist\",\"loanlist\":\"loanlist\",\"sell\":\"sell\",\"receiptvoucher\":\"receiptvoucher\",\"paymentvoucher\":\"paymentvoucher\",\"internaltransfer\":\"internaltransfer\",\"bankbalance\":\"bankbalance\",\"receivable\":\"receivable\",\"payable\":\"payable\",\"loan\":\"loan\",\"vat\":\"vat\",\"exvat\":\"exvat\",\"sales\":\"sales\",\"cost\":\"cost\",\"noinvoice\":\"noinvoice\",\"tolls\":\"tolls\",\"repairsalary\":\"repairsalary\",\"costlist\":\"costlist\",\"bank\":\"bank\",\"account\":\"account\",\"trucking\":\"trucking\",\"customership\":\"customership\",\"truckinglist\":\"truckinglist\",\"repairlist\":\"repairlist\",\"roadcostlist\":\"roadcostlist\",\"checkingcostlist\":\"checkingcostlist\",\"insurancecostlist\":\"insurancecostlist\",\"oilreport\":\"oilreport\",\"advance\":\"advance\",\"commission\":\"commission\",\"quantity\":\"quantity\",\"profit\":\"profit\",\"round\":\"round\",\"officecost\":\"officecost\",\"vehicleanalytics\":\"vehicleanalytics\",\"report\":\"report\",\"info\":\"info\",\"user\":\"user\",\"permission\":\"permission\",\"backup\":\"backup\",\"update\":\"update\"}');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
