-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 24, 2025 lúc 10:47 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `htqlnt`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `TaiKhoan` varchar(255) NOT NULL,
  `MatKhau` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`TaiKhoan`, `MatKhau`) VALUES
('admin1', '123');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chu_khu_tro`
--

CREATE TABLE `chu_khu_tro` (
  `CKT_SODT` char(10) NOT NULL,
  `CKT_HOTEN` varchar(255) NOT NULL,
  `CKT_GIOITINH` varchar(255) DEFAULT NULL,
  `CKT_MATKHAU` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chu_khu_tro`
--

INSERT INTO `chu_khu_tro` (`CKT_SODT`, `CKT_HOTEN`, `CKT_GIOITINH`, `CKT_MATKHAU`) VALUES
('0900000001', 'Nguyễn Văn An', 'Nam', 'pass1'),
('0900000002', 'Trần Thị Bình', 'Nữ', 'pass2'),
('0900000003', 'Lê Văn Cường', 'Nam', 'pass3'),
('0900000004', 'Phạm Thị Dung', 'Nữ', 'pass4'),
('0900000005', 'Hoàng Văn Hùng', 'Nam', 'pass5'),
('0900000006', 'Đỗ Thị Lan', 'Nữ', 'pass6'),
('0900000007', 'Vũ Văn Minh', 'Nam', 'pass7'),
('0900000008', 'Bùi Thị Ngọc', 'Nữ', 'pass8'),
('0900000009', 'Phan Văn Phúc', 'Nam', 'pass9'),
('0900000010', 'Đinh Thị Quyên', 'Nữ', 'pass10'),
('0900000011', 'Phạm Văn Long', 'Nam', 'pass11'),
('0900000012', 'Trần Thị Mai', 'Nữ', 'pass12'),
('0900000013', 'Lê Thị Hạnh', 'Nữ', 'pass13'),
('0900000014', 'Nguyễn Văn Tuấn', 'Nam', 'pass14'),
('0900000015', 'Đinh Thị Nga', 'Nữ', 'pass15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `duong`
--

CREATE TABLE `duong` (
  `DUONG_MA` char(5) NOT NULL,
  `XP_MA` char(5) NOT NULL,
  `DUONG_TEN` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `duong`
--

INSERT INTO `duong` (`DUONG_MA`, `XP_MA`, `DUONG_TEN`) VALUES
('D001', 'XP001', 'Đường 3/2'),
('D002', 'XP002', 'Đường Cách Mạng Tháng 8'),
('D003', 'XP003', 'Đường Nguyễn Văn Cừ'),
('D004', 'XP004', 'Đường Trần Phú'),
('D005', 'XP026', 'Đường Lê Lợi'),
('D006', 'XP033', 'Đường Hùng Vương'),
('D007', 'XP034', 'Đường Phan Đình Phùng'),
('D008', 'XP006', 'Đường Lý Thái Tổ'),
('D009', 'XP009', 'Đường Tôn Đức Thắng'),
('D010', 'XP010', 'Đường Quang Trung'),
('D011', 'XP011', 'Đường Võ Thị Sáu'),
('D012', 'XP012', 'Đường Lê Hồng Phong'),
('D013', 'XP013', 'Đường Phan Bội Châu'),
('D014', 'XP014', 'Đường Bùi Thị Xuân'),
('D015', 'XP015', 'Đường Lê Duẩn'),
('D016', 'XP016', 'Đường Nguyễn Trãi'),
('D017', 'XP017', 'Đường Lê Hồng Phong'),
('D018', 'XP018', 'Đường Lý Tự Lực'),
('D019', 'XP019', 'Đường Hoàng Diệu'),
('D020', 'XP020', 'Đường Trần Duy Hưng'),
('D022', 'XP036', ''),
('D023', 'XP037', 'Xa lộ Tân Thành'),
('DU021', 'XP035', 'Đường 3 Tháng 2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gia_thue`
--

CREATE TABLE `gia_thue` (
  `LP_MALOAIPHONG` char(5) NOT NULL,
  `KT_MAKT` char(5) NOT NULL,
  `GT_NGAYAPDUNG` datetime NOT NULL,
  `GT_GIA` float NOT NULL,
  `GT_NGAYKETTHUC` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `gia_thue`
--

INSERT INTO `gia_thue` (`LP_MALOAIPHONG`, `KT_MAKT`, `GT_NGAYAPDUNG`, `GT_GIA`, `GT_NGAYKETTHUC`) VALUES
('LP001', 'KT001', '2024-01-01 00:00:00', 1500000, '2024-06-30 00:00:00'),
('LP001', 'KT006', '2024-01-25 00:00:00', 1600000, '2024-06-30 00:00:00'),
('LP001', 'KT014', '2025-03-15 00:00:00', 1700000, '2025-08-31 00:00:00'),
('LP002', 'KT002', '2024-01-05 00:00:00', 2000000, '2024-07-31 00:00:00'),
('LP002', 'KT007', '2024-02-01 00:00:00', 2000000, '2024-07-31 00:00:00'),
('LP002', 'KT015', '2025-03-20 00:00:00', 2100000, '2025-09-30 00:00:00'),
('LP003', 'KT003', '2024-01-10 00:00:00', 2500000, '2024-08-31 00:00:00'),
('LP003', 'KT008', '2024-02-05 00:00:00', 2550000, '2024-08-31 00:00:00'),
('LP004', 'KT004', '2024-01-15 00:00:00', 3000000, '2024-09-30 00:00:00'),
('LP004', 'KT009', '2024-02-10 00:00:00', 3100000, '2024-09-30 00:00:00'),
('LP005', 'KT005', '2024-01-20 00:00:00', 3600000, '2024-10-31 00:00:00'),
('LP005', 'KT010', '2024-02-15 00:00:00', 3500000, '2024-10-31 00:00:00'),
('LP006', 'KT011', '2025-03-01 00:00:00', 1600000, '2025-08-31 00:00:00'),
('LP006', 'KT013', '2025-03-01 00:00:00', 1650000, '2025-08-31 00:00:00'),
('LP007', 'KT010', '2025-03-05 00:00:00', 2200000, '2025-09-30 00:00:00'),
('LP007', 'KT012', '2025-03-05 00:00:00', 2000000, '2025-09-30 00:00:00'),
('LP008', 'KT004', '2025-03-10 00:00:00', 2900000, '2025-10-31 00:00:00'),
('LP008', 'KT013', '2025-03-10 00:00:00', 2800000, '2025-10-31 00:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khach_hang`
--

CREATE TABLE `khach_hang` (
  `KH_CCCD` char(12) NOT NULL,
  `KH_TEN` varchar(255) NOT NULL,
  `KH_SDT` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khach_hang`
--

INSERT INTO `khach_hang` (`KH_CCCD`, `KH_TEN`, `KH_SDT`) VALUES
('012345678901', 'Đỗ Thị Mai', '0900000000'),
('012345678999', 'Tran Van A', '0337108778'),
('112233445566', 'Nguyễn Thị Lan', '0901234567'),
('123456778901', 'Tran Van B', '0123456896'),
('123456789012', 'Trần Văn Đạt', '0911111111'),
('123456789091', 'Tran Van B', '0123456789'),
('223344556677', 'Trần Văn Quang', '0912345678'),
('234567890123', 'Lê Thị Hoa', '0922222222'),
('334455667788', 'Lê Thị My', '0923456789'),
('345678901234', 'Nguyễn Văn Nam', '0933333333'),
('445566778899', 'Phạm Văn Hoàng', '0934567890'),
('456789012345', 'Phạm Thị Lan', '0944444444'),
('556677889900', 'Vũ Thị Hạnh', '0945678901'),
('567890123456', 'Hoàng Văn Minh', '0955555555'),
('678901234567', 'Vũ Thị Hương', '0966666666'),
('789012345678', 'Đặng Văn Huy', '0977777777'),
('890123456789', 'Bùi Thị Phương', '0988888888'),
('901234567890', 'Phan Văn Quân', '0999999999');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoang_cach`
--

CREATE TABLE `khoang_cach` (
  `KT_MAKT` char(5) NOT NULL,
  `TRUONG_MA` char(5) NOT NULL,
  `KC_DODAI` float NOT NULL,
  `KC_DONVIDO` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khoang_cach`
--

INSERT INTO `khoang_cach` (`KT_MAKT`, `TRUONG_MA`, `KC_DODAI`, `KC_DONVIDO`) VALUES
('KT001', 'TR001', 49.2912, 'km'),
('KT002', 'TR001', 49.9577, 'km'),
('KT003', 'TR001', 51.0289, 'km'),
('KT004', 'TR001', 49.8281, 'km'),
('KT005', 'TR001', 50.0407, 'km'),
('KT006', 'TR001', 49.7324, 'km'),
('KT007', 'TR001', 36.3715, 'km'),
('KT008', 'TR001', 46.9869, 'km'),
('KT009', 'TR001', 40.9492, 'km'),
('KT010', 'TR001', 43.0126, 'km'),
('KT011', 'TR001', 42.4302, 'km'),
('KT012', 'TR001', 91.9396, 'km'),
('KT013', 'TR001', 41.901, 'km'),
('KT014', 'TR001', 42.5216, 'km'),
('KT015', 'TR001', 43.8116, 'km'),
('KT016', 'TR001', 48.1225, 'km');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khu_tro`
--

CREATE TABLE `khu_tro` (
  `KT_MAKT` char(5) NOT NULL,
  `DUONG_MA` char(5) NOT NULL,
  `CKT_SODT` char(10) NOT NULL,
  `KT_SONHA` varchar(255) NOT NULL,
  `KT_TENKHUTRO` varchar(255) NOT NULL,
  `KT_LONGTITUDE` float DEFAULT NULL,
  `KT_LATITUDE` float DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khu_tro`
--

INSERT INTO `khu_tro` (`KT_MAKT`, `DUONG_MA`, `CKT_SODT`, `KT_SONHA`, `KT_TENKHUTRO`, `KT_LONGTITUDE`, `KT_LATITUDE`, `is_delete`) VALUES
('KT001', 'D001', '0900000001', '12A', 'Khu trọ Bình An', 105.78, 10.045, 0),
('KT002', 'D002', '0900000002', '34B', 'Khu trọ An Phú', 105.778, 10.0479, 0),
('KT003', 'D003', '0900000003', '56C', 'Khu trọ Hòa Bình', 105.762, 10.0369, 0),
('KT004', 'D004', '0900000004', '78D', 'Khu trọ Tân Bình', 105.782, 10.0466, 0),
('KT005', 'D005', '0900000005', '90E', 'Khu trọ Phú Nhuận', 105.789, 10.0468, 0),
('KT006', 'D006', '0900000006', '19F', 'Khu trọ An Lạc', 105.779, 10.0444, 0),
('KT007', 'D007', '0900000007', '43G', 'Khu trọ Thịnh Vượng', 105.805, 10.075, 0),
('KT008', 'D008', '0900000008', '65H', 'Khu trọ Phúc Lộc', 105.789, 10.0224, 0),
('KT009', 'D009', '0900000009', '87I', 'Khu trọ Hưng Thịnh', 105.815, 10.085, 0),
('KT010', 'D010', '0900000010', '109J', 'Khu trọ Đại An', 105.82, 10.09, 0),
('KT011', 'D016', '0900000011', '15A', 'Khu trọ Minh Quân', 105.825, 10.095, 0),
('KT012', 'D017', '0900000012', '27B', 'Khu trọ Hòa Thọ', 105.534, 9.75083, 0),
('KT013', 'D018', '0900000013', '39C', 'Khu trọ Tân Phú', 105.835, 10.105, 0),
('KT014', 'D019', '0900000014', '51D', 'Khu trọ An Khang', 105.84, 10.11, 0),
('KT015', 'D020', '0900000015', '63E', 'Khu trọ Phúc Thọ', 105.845, 10.115, 0),
('KT016', 'DU021', '0900000001', '123', 'Khu Hoàng Hà', 105.774, 10.0324, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su`
--

CREATE TABLE `lich_su` (
  `TTP_MA` char(2) NOT NULL,
  `PHONG_MAPHONG` char(5) NOT NULL,
  `LS_NGAYBATDAUTHUE` datetime NOT NULL,
  `LS_NGAYKETTHUC` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lich_su`
--

INSERT INTO `lich_su` (`TTP_MA`, `PHONG_MAPHONG`, `LS_NGAYBATDAUTHUE`, `LS_NGAYKETTHUC`) VALUES
('01', 'P006', '2025-03-15 00:00:00', NULL),
('01', 'P007', '2025-04-01 00:00:00', NULL),
('02', 'P001', '2025-01-01 00:00:00', '2025-06-01 00:00:00'),
('02', 'P002', '2024-01-15 00:00:00', '2024-07-15 00:00:00'),
('02', 'P003', '2024-02-01 00:00:00', '2024-08-01 00:00:00'),
('02', 'P004', '2024-02-15 00:00:00', '2024-08-15 00:00:00'),
('02', 'P005', '2024-03-01 00:00:00', '2024-09-01 00:00:00'),
('03', 'P008', '2024-04-15 00:00:00', '2024-10-15 00:00:00'),
('03', 'P009', '2024-05-01 00:00:00', '2024-11-01 00:00:00'),
('04', 'P010', '2024-05-15 00:00:00', '2024-12-15 00:00:00'),
('05', 'P011', '2025-04-01 00:00:00', '2025-09-01 00:00:00'),
('05', 'P013', '2025-04-10 00:00:00', '2025-09-10 00:00:00'),
('05', 'P015', '2025-04-20 00:00:00', '2025-09-20 00:00:00'),
('06', 'P012', '2025-04-05 00:00:00', '2025-09-05 00:00:00'),
('06', 'P014', '2025-04-15 00:00:00', '2025-09-15 00:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_phong`
--

CREATE TABLE `loai_phong` (
  `LP_MALOAIPHONG` char(5) NOT NULL,
  `LP_TENLOAIPHONG` varchar(255) NOT NULL,
  `LP_DIENTICH` float NOT NULL,
  `LP_SUCCHUA` int(11) NOT NULL,
  `LP_VATCHAT` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_phong`
--

INSERT INTO `loai_phong` (`LP_MALOAIPHONG`, `LP_TENLOAIPHONG`, `LP_DIENTICH`, `LP_SUCCHUA`, `LP_VATCHAT`) VALUES
('LP001', 'Phòng đơn', 20, 1, 'Gác xép'),
('LP002', 'Phòng đôi', 30, 2, 'Ban công'),
('LP003', 'Phòng gia đình', 40, 4, 'Điều hòa'),
('LP004', 'Phòng cao cấp', 50, 3, 'Nội thất hiện đại'),
('LP005', 'Phòng studio', 25, 1, 'Minh bạch'),
('LP006', 'Phòng mini', 18, 1, 'Gác xép cơ bản'),
('LP007', 'Phòng thương gia', 60, 4, 'Nội thất sang trọng'),
('LP008', 'Phòng duplex', 70, 3, 'Hai tầng riêng biệt');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieu_thue`
--

CREATE TABLE `phieu_thue` (
  `PT_MA` char(5) NOT NULL,
  `PHONG_MAPHONG` char(5) NOT NULL,
  `KH_CCCD` char(12) NOT NULL,
  `PT_NGAYLAP` datetime NOT NULL,
  `PT_NGAYBATDAU` datetime NOT NULL,
  `PT_NGAYKETTHUC` datetime DEFAULT NULL,
  `PT_Tinhtrang` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phieu_thue`
--

INSERT INTO `phieu_thue` (`PT_MA`, `PHONG_MAPHONG`, `KH_CCCD`, `PT_NGAYLAP`, `PT_NGAYBATDAU`, `PT_NGAYKETTHUC`, `PT_Tinhtrang`) VALUES
('PT001', 'P001', '123456789012', '2025-01-01 00:00:00', '2025-01-05 00:00:00', '2025-07-01 00:00:00', 1),
('PT002', 'P002', '234567890123', '2025-01-10 00:00:00', '2025-01-15 00:00:00', '2025-07-10 00:00:00', 0),
('PT003', 'P003', '345678901234', '2025-01-20 00:00:00', '2025-01-25 00:00:00', '2025-07-20 00:00:00', 0),
('PT004', 'P004', '456789012345', '2025-02-01 00:00:00', '2025-02-05 00:00:00', '2025-08-01 00:00:00', 1),
('PT005', 'P005', '567890123456', '2025-02-10 00:00:00', '2025-02-15 00:00:00', '2025-08-10 00:00:00', 0),
('PT006', 'P006', '678901234567', '2025-02-20 00:00:00', '2025-02-25 00:00:00', '2025-08-20 00:00:00', 0),
('PT007', 'P007', '789012345678', '2025-03-01 00:00:00', '2025-03-05 00:00:00', '2025-09-01 00:00:00', 1),
('PT008', 'P008', '890123456789', '2025-03-10 00:00:00', '2025-03-15 00:00:00', '2025-09-10 00:00:00', 1),
('PT009', 'P009', '901234567890', '2025-03-20 00:00:00', '2025-03-25 00:00:00', '2025-09-20 00:00:00', 0),
('PT010', 'P010', '012345678901', '2025-03-30 00:00:00', '2025-04-05 00:00:00', '2025-09-30 00:00:00', 0),
('PT011', 'P011', '112233445566', '2025-04-01 00:00:00', '2025-04-03 00:00:00', '2025-10-01 00:00:00', 1),
('PT012', 'P012', '223344556677', '2025-04-05 00:00:00', '2025-04-07 00:00:00', '2025-10-05 00:00:00', 0),
('PT013', 'P013', '334455667788', '2025-04-10 00:00:00', '2025-04-12 00:00:00', '2025-10-10 00:00:00', 0),
('PT014', 'P014', '445566778899', '2025-04-15 00:00:00', '2025-04-17 00:00:00', '2025-10-15 00:00:00', 0),
('PT015', 'P015', '556677889900', '2025-04-20 00:00:00', '2025-04-22 00:00:00', '2025-10-20 00:00:00', 0),
('PT016', 'P014', '556677889900', '2025-04-20 00:00:00', '2025-04-22 00:00:00', '2025-10-20 00:00:00', 0),
('PT017', 'P007', '012345678999', '2025-03-10 00:00:00', '2025-03-13 00:00:00', '2025-04-13 00:00:00', 1),
('PT018', 'P006', '012345678999', '2025-03-10 00:00:00', '2025-03-13 00:00:00', '2025-04-13 00:00:00', 0),
('PT019', 'P006', '123456789091', '2025-03-17 00:00:00', '2025-03-20 00:00:00', '2025-04-20 00:00:00', 0),
('PT020', 'P006', '123456789091', '2025-03-17 00:00:00', '2025-03-20 00:00:00', '2025-04-20 00:00:00', 0),
('PT021', 'P014', '556677889900', '2025-04-20 00:00:00', '2025-04-22 00:00:00', '2025-10-20 00:00:00', 1),
('PT022', 'P006', '123456789091', '2025-03-17 00:00:00', '2025-03-20 00:00:00', '2025-04-20 00:00:00', 0),
('PT023', 'P006', '123456778901', '2025-03-24 00:00:00', '2025-03-27 00:00:00', '2025-04-27 00:00:00', 0),
('PT024', 'P006', '123456778901', '2025-03-24 00:00:00', '2025-03-27 00:00:00', '2025-04-27 00:00:00', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phong`
--

CREATE TABLE `phong` (
  `PHONG_MAPHONG` char(5) NOT NULL,
  `LP_MALOAIPHONG` char(5) NOT NULL,
  `PHONG_MOTA` varchar(255) DEFAULT NULL,
  `PHONG_ANH` varchar(255) DEFAULT NULL,
  `KT_MAKT` char(5) NOT NULL,
  `PHONG_Stt` varchar(10) NOT NULL,
  `is_delete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phong`
--

INSERT INTO `phong` (`PHONG_MAPHONG`, `LP_MALOAIPHONG`, `PHONG_MOTA`, `PHONG_ANH`, `KT_MAKT`, `PHONG_Stt`, `is_delete`) VALUES
('P001', 'LP001', 'Phòng đơn giá rẻ', NULL, 'KT001', '01', 0),
('P002', 'LP002', 'Phòng đôi view sông', NULL, 'KT002', '01', 0),
('P003', 'LP003', 'Phòng gia đình rộng rãi', NULL, 'KT003', '01', 0),
('P004', 'LP004', 'Phòng cao cấp có ban công', NULL, 'KT004', '01', 0),
('P005', 'LP005', 'Phòng studio đẹp', NULL, 'KT005', '01', 0),
('P006', 'LP001', 'Phòng đơn với tiện nghi cơ bản', NULL, 'KT006', '01', 0),
('P007', 'LP002', 'Phòng đôi với nội thất đẹp', NULL, 'KT015', '04', 0),
('P008', 'LP003', 'Phòng gia đình thoáng mát', NULL, 'KT008', '01', 0),
('P009', 'LP004', 'Phòng cao cấp view thành phố', NULL, 'KT009', '01', 0),
('P010', 'LP005', 'Phòng studio tiết kiệm', NULL, 'KT010', '01', 0),
('P011', 'LP006', 'Phòng mini giá rẻ', NULL, 'KT011', '01', 0),
('P012', 'LP007', 'Phòng thương gia view đẹp', NULL, 'KT012', '02', 0),
('P013', 'LP008', 'Phòng duplex hiện đại', NULL, 'KT013', '02', 0),
('P014', 'LP007', 'Phòng thương gia rộng rãi', NULL, 'KT012', '03', 0),
('P015', 'LP006', 'Phòng mini tiện nghi', NULL, 'KT013', '03', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quan_huyen`
--

CREATE TABLE `quan_huyen` (
  `QH_MA` char(5) NOT NULL,
  `TTP_MATINH` char(5) NOT NULL,
  `QH_TEN` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quan_huyen`
--

INSERT INTO `quan_huyen` (`QH_MA`, `TTP_MATINH`, `QH_TEN`) VALUES
('QH001', 'TTP01', 'Ninh Kiều'),
('QH002', 'TTP01', 'Cái Răng'),
('QH003', 'TTP01', 'Bình Thủy'),
('QH004', 'TTP01', 'Ô Môn'),
('QH005', 'TTP01', 'Thốt Nốt'),
('QH006', 'TTP02', 'Vị Thanh'),
('QH007', 'TTP02', 'Ngã Bảy'),
('QH008', 'TTP02', 'Châu Thành'),
('QH009', 'TTP02', 'Vị Thủy'),
('QH010', 'TTP03', 'Vĩnh Long'),
('QH011', 'TTP03', 'Long Hồ'),
('QH012', 'TTP03', 'Mang Thít'),
('QH013', 'TTP04', 'Sóc Trăng'),
('QH014', 'TTP04', 'Vĩnh Châu'),
('QH015', 'TTP04', 'Châu Thành'),
('QH016', 'TTP04', 'Mỹ Tú'),
('QH018', 'TTP00', ''),
('QH019', 'TP001', 'Tân Lộc'),
('QH17', 'TTP05', 'Quận Ninh Kiều');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tinh_thanh_pho`
--

CREATE TABLE `tinh_thanh_pho` (
  `TTP_MATINH` char(5) NOT NULL,
  `TTP_TEN` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tinh_thanh_pho`
--

INSERT INTO `tinh_thanh_pho` (`TTP_MATINH`, `TTP_TEN`) VALUES
('TP001', 'Tỉnh Đồng Tháp'),
('TTP00', 'Kiên Giang'),
('TTP01', 'Cần Thơ'),
('TTP02', 'Hậu Giang'),
('TTP03', 'Vĩnh Long'),
('TTP04', 'Sóc Trăng'),
('TTP05', 'Thành phố Cần Thơ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tinh_trang_phong`
--

CREATE TABLE `tinh_trang_phong` (
  `TTP_MA` char(2) NOT NULL,
  `TTP_TEN` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tinh_trang_phong`
--

INSERT INTO `tinh_trang_phong` (`TTP_MA`, `TTP_TEN`) VALUES
('01', 'Trống'),
('02', 'Đã cho thuê'),
('03', 'Bảo trì'),
('04', 'Đang sửa chữa'),
('05', 'Chờ dọn dẹp'),
('06', 'Đang hoàn thiện');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `truong`
--

CREATE TABLE `truong` (
  `TRUONG_MA` char(5) NOT NULL,
  `DUONG_MA` char(5) NOT NULL,
  `TRUONG_TEN` varchar(255) NOT NULL,
  `TRUONG_SODIACHI` varchar(255) NOT NULL,
  `TRUONG_LONGTITUDE` float NOT NULL,
  `TRUONG_LATITUDE` float NOT NULL,
  `TRUONG_ICON` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `truong`
--

INSERT INTO `truong` (`TRUONG_MA`, `DUONG_MA`, `TRUONG_TEN`, `TRUONG_SODIACHI`, `TRUONG_LONGTITUDE`, `TRUONG_LATITUDE`, `TRUONG_ICON`) VALUES
('TR001', 'D023', 'dshfksf', 'Xa lộ Tân Thành, Xã Tân Thành, Tân Lộc, Tỉnh Đồng Tháp', 105.593, 10.2564, 'uploads/icons/1742807170_anh-hoa.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `xa_phuong`
--

CREATE TABLE `xa_phuong` (
  `XP_MA` char(5) NOT NULL,
  `QH_MA` char(5) NOT NULL,
  `XP_TEN` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `xa_phuong`
--

INSERT INTO `xa_phuong` (`XP_MA`, `QH_MA`, `XP_TEN`) VALUES
('XP001', 'QH001', 'Xuân An'),
('XP002', 'QH001', 'An Hòa'),
('XP003', 'QH002', 'Phú Hưng'),
('XP004', 'QH002', 'Hòa An'),
('XP005', 'QH003', 'Bình Thuận'),
('XP006', 'QH002', 'Hưng Phú'),
('XP007', 'QH004', 'Bình An'),
('XP008', 'QH004', 'Phong Mỹ'),
('XP009', 'QH004', 'Thới Hòa'),
('XP010', 'QH001', 'An Bình'),
('XP011', 'QH006', 'Trung Lương'),
('XP012', 'QH006', 'Long Hòa'),
('XP013', 'QH007', 'Phú Tân'),
('XP014', 'QH007', 'Tân Bình'),
('XP015', 'QH008', 'Châu Phú'),
('XP016', 'QH008', 'An Phú'),
('XP017', 'QH009', 'Vị Trung'),
('XP018', 'QH009', 'Thới An'),
('XP019', 'QH010', 'Vĩnh Tân'),
('XP020', 'QH010', 'Tân Long'),
('XP021', 'QH011', 'Long An'),
('XP022', 'QH011', 'Hòa Lộc'),
('XP023', 'QH012', 'Mang Hòa'),
('XP024', 'QH012', 'Thới Hòa'),
('XP025', 'QH013', 'Mỹ Xá'),
('XP026', 'QH001', 'Phường Cái Khế'),
('XP027', 'QH014', 'Vĩnh Hòa'),
('XP028', 'QH014', 'Hòa Xuân'),
('XP029', 'QH015', 'Châu Hòa'),
('XP030', 'QH015', 'Phú Nhuận'),
('XP031', 'QH016', 'Mỹ Phước'),
('XP032', 'QH016', 'Mỹ An'),
('XP033', 'QH001', 'Phường An Cư'),
('XP034', 'QH001', 'Phường Tân An'),
('XP035', 'QH17', 'Quận An Ninh'),
('XP036', 'QH018', ''),
('XP037', 'QH019', 'Xã Tân Thành');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`TaiKhoan`);

--
-- Chỉ mục cho bảng `chu_khu_tro`
--
ALTER TABLE `chu_khu_tro`
  ADD PRIMARY KEY (`CKT_SODT`);

--
-- Chỉ mục cho bảng `duong`
--
ALTER TABLE `duong`
  ADD PRIMARY KEY (`DUONG_MA`),
  ADD KEY `FK_THUOC_PHUONG` (`XP_MA`);

--
-- Chỉ mục cho bảng `gia_thue`
--
ALTER TABLE `gia_thue`
  ADD PRIMARY KEY (`LP_MALOAIPHONG`,`KT_MAKT`,`GT_NGAYAPDUNG`),
  ADD KEY `FK_CO_GIA_THUE` (`KT_MAKT`);

--
-- Chỉ mục cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`KH_CCCD`),
  ADD UNIQUE KEY `KH_SDT` (`KH_SDT`);

--
-- Chỉ mục cho bảng `khoang_cach`
--
ALTER TABLE `khoang_cach`
  ADD PRIMARY KEY (`KT_MAKT`,`TRUONG_MA`),
  ADD KEY `FK_CACH_TRUONG` (`TRUONG_MA`);

--
-- Chỉ mục cho bảng `khu_tro`
--
ALTER TABLE `khu_tro`
  ADD PRIMARY KEY (`KT_MAKT`),
  ADD KEY `FK_GOM` (`CKT_SODT`),
  ADD KEY `FK_NAM` (`DUONG_MA`);

--
-- Chỉ mục cho bảng `lich_su`
--
ALTER TABLE `lich_su`
  ADD PRIMARY KEY (`TTP_MA`,`PHONG_MAPHONG`),
  ADD KEY `FK_RELATIONSHIP_2` (`PHONG_MAPHONG`);

--
-- Chỉ mục cho bảng `loai_phong`
--
ALTER TABLE `loai_phong`
  ADD PRIMARY KEY (`LP_MALOAIPHONG`);

--
-- Chỉ mục cho bảng `phieu_thue`
--
ALTER TABLE `phieu_thue`
  ADD PRIMARY KEY (`PT_MA`),
  ADD KEY `FK_LAP` (`KH_CCCD`),
  ADD KEY `FK_THUE_PHONG` (`PHONG_MAPHONG`);

--
-- Chỉ mục cho bảng `phong`
--
ALTER TABLE `phong`
  ADD PRIMARY KEY (`PHONG_MAPHONG`),
  ADD KEY `FK_THUOC_LOAI` (`LP_MALOAIPHONG`),
  ADD KEY `KT_MAKT` (`KT_MAKT`);

--
-- Chỉ mục cho bảng `quan_huyen`
--
ALTER TABLE `quan_huyen`
  ADD PRIMARY KEY (`QH_MA`),
  ADD KEY `FK_THUOC_TINH` (`TTP_MATINH`);

--
-- Chỉ mục cho bảng `tinh_thanh_pho`
--
ALTER TABLE `tinh_thanh_pho`
  ADD PRIMARY KEY (`TTP_MATINH`);

--
-- Chỉ mục cho bảng `tinh_trang_phong`
--
ALTER TABLE `tinh_trang_phong`
  ADD PRIMARY KEY (`TTP_MA`);

--
-- Chỉ mục cho bảng `truong`
--
ALTER TABLE `truong`
  ADD PRIMARY KEY (`TRUONG_MA`),
  ADD KEY `FK_NAM_O` (`DUONG_MA`);

--
-- Chỉ mục cho bảng `xa_phuong`
--
ALTER TABLE `xa_phuong`
  ADD PRIMARY KEY (`XP_MA`),
  ADD KEY `FK_THUOC_QUAN` (`QH_MA`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `duong`
--
ALTER TABLE `duong`
  ADD CONSTRAINT `FK_THUOC_PHUONG` FOREIGN KEY (`XP_MA`) REFERENCES `xa_phuong` (`XP_MA`);

--
-- Các ràng buộc cho bảng `gia_thue`
--
ALTER TABLE `gia_thue`
  ADD CONSTRAINT `FK_CO_GIA` FOREIGN KEY (`LP_MALOAIPHONG`) REFERENCES `loai_phong` (`LP_MALOAIPHONG`),
  ADD CONSTRAINT `FK_CO_GIA_THUE` FOREIGN KEY (`KT_MAKT`) REFERENCES `khu_tro` (`KT_MAKT`);

--
-- Các ràng buộc cho bảng `khoang_cach`
--
ALTER TABLE `khoang_cach`
  ADD CONSTRAINT `FK_CACH_TRO` FOREIGN KEY (`KT_MAKT`) REFERENCES `khu_tro` (`KT_MAKT`),
  ADD CONSTRAINT `FK_CACH_TRUONG` FOREIGN KEY (`TRUONG_MA`) REFERENCES `truong` (`TRUONG_MA`);

--
-- Các ràng buộc cho bảng `khu_tro`
--
ALTER TABLE `khu_tro`
  ADD CONSTRAINT `FK_GOM` FOREIGN KEY (`CKT_SODT`) REFERENCES `chu_khu_tro` (`CKT_SODT`),
  ADD CONSTRAINT `FK_NAM` FOREIGN KEY (`DUONG_MA`) REFERENCES `duong` (`DUONG_MA`);

--
-- Các ràng buộc cho bảng `lich_su`
--
ALTER TABLE `lich_su`
  ADD CONSTRAINT `FK_RELATIONSHIP_1` FOREIGN KEY (`TTP_MA`) REFERENCES `tinh_trang_phong` (`TTP_MA`),
  ADD CONSTRAINT `FK_RELATIONSHIP_2` FOREIGN KEY (`PHONG_MAPHONG`) REFERENCES `phong` (`PHONG_MAPHONG`);

--
-- Các ràng buộc cho bảng `phieu_thue`
--
ALTER TABLE `phieu_thue`
  ADD CONSTRAINT `FK_LAP` FOREIGN KEY (`KH_CCCD`) REFERENCES `khach_hang` (`KH_CCCD`),
  ADD CONSTRAINT `FK_THUE_PHONG` FOREIGN KEY (`PHONG_MAPHONG`) REFERENCES `phong` (`PHONG_MAPHONG`);

--
-- Các ràng buộc cho bảng `phong`
--
ALTER TABLE `phong`
  ADD CONSTRAINT `FK_THUOC_LOAI` FOREIGN KEY (`LP_MALOAIPHONG`) REFERENCES `loai_phong` (`LP_MALOAIPHONG`),
  ADD CONSTRAINT `phong_ibfk_1` FOREIGN KEY (`KT_MAKT`) REFERENCES `khu_tro` (`KT_MAKT`);

--
-- Các ràng buộc cho bảng `quan_huyen`
--
ALTER TABLE `quan_huyen`
  ADD CONSTRAINT `FK_THUOC_TINH` FOREIGN KEY (`TTP_MATINH`) REFERENCES `tinh_thanh_pho` (`TTP_MATINH`);

--
-- Các ràng buộc cho bảng `truong`
--
ALTER TABLE `truong`
  ADD CONSTRAINT `FK_NAM_O` FOREIGN KEY (`DUONG_MA`) REFERENCES `duong` (`DUONG_MA`);

--
-- Các ràng buộc cho bảng `xa_phuong`
--
ALTER TABLE `xa_phuong`
  ADD CONSTRAINT `FK_THUOC_QUAN` FOREIGN KEY (`QH_MA`) REFERENCES `quan_huyen` (`QH_MA`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
