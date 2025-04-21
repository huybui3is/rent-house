-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 30, 2025 lúc 02:01 PM
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
  `CKT_MATKHAU` varchar(100) NOT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chu_khu_tro`
--

INSERT INTO `chu_khu_tro` (`CKT_SODT`, `CKT_HOTEN`, `CKT_GIOITINH`, `CKT_MATKHAU`, `is_delete`) VALUES
('0900000001', 'Nguyễn Văn An', 'Nữ', 'pass1', 0),
('0900000002', 'Trần Thị Bình', 'Nữ', 'pass2', 0),
('0900000003', 'Lê Văn Cường', 'Nam', 'pass3', 0),
('0900000004', 'Phạm Thị Dung', 'Nữ', 'pass4', 0),
('0900000005', 'Hoàng Văn Hùng', 'Nam', 'pass5', 0),
('0900000006', 'Đỗ Thị Lan', 'Nữ', 'pass6', 0),
('0900000007', 'Vũ Văn Minh', 'Nam', 'pass7', 0),
('0900000008', 'Bùi Thị Ngọc', 'Nữ', 'pass8', 0),
('0900000009', 'Phan Văn Phúc', 'Nam', 'pass9', 0),
('0900000010', 'Đinh Thị Quyên', 'Nữ', 'pass10', 0),
('0900000011', 'Phạm Văn Long', 'Nam', 'pass11', 0),
('0900000012', 'Trần Thị Mai', 'Nữ', 'pass12', 0),
('0900000013', 'Lê Thị Hạnh', 'Nữ', 'pass13', 0),
('0900000014', 'Nguyễn Văn Tuấn', 'Nam', 'pass14', 0),
('0900000015', 'Đinh Thị Nga', 'Nữ', 'pass15', 0),
('9999999999', 'Tran Hoang Thi', 'nam', '123456', 0);

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
('DU021', 'XP035', 'Hùng Vương'),
('DU022', 'XP036', 'Hùng Vương'),
('DU023', 'XP036', 'Nguyễn Văn Thảnh'),
('DU024', 'XP037', 'Nguyễn Văn Thảnh'),
('DU025', 'XP038', 'Nguyễn Văn Linh'),
('DU026', 'XP039', 'Quốc lộ 1'),
('DU027', 'XP040', 'Nguyễn Thị Minh Khai'),
('DU028', 'XP041', 'Đường 3 Tháng 2'),
('DU029', 'XP042', 'Trần Việt Châu'),
('DU030', 'XP041', 'Trần Văn Hoài'),
('DU031', 'XP041', 'Đường 30 Tháng 4'),
('DU032', 'XP041', 'Nguyễn Văn Trỗi'),
('DU033', 'XP041', 'Mạc Thiên Tích');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gia_thue`
--

CREATE TABLE `gia_thue` (
  `LP_MALOAIPHONG` char(5) NOT NULL,
  `KT_MAKT` char(5) NOT NULL,
  `GT_NGAYAPDUNG` datetime NOT NULL,
  `GT_GIA` float NOT NULL,
  `GT_NGAYKETTHUC` datetime NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `gia_thue`
--

INSERT INTO `gia_thue` (`LP_MALOAIPHONG`, `KT_MAKT`, `GT_NGAYAPDUNG`, `GT_GIA`, `GT_NGAYKETTHUC`, `is_delete`) VALUES
('L0001', 'KT001', '2025-03-27 18:18:45', 1500000, '2025-03-21 00:00:00', 1),
('L0002', 'KT001', '2025-03-27 18:24:17', 1500000, '2025-03-11 00:00:00', 1),
('L0003', 'KT001', '2025-03-27 18:37:48', 100000000, '2025-04-05 00:00:00', 1),
('L0004', 'KT016', '2025-03-27 20:08:54', 100000000, '2025-04-05 00:00:00', 1),
('L0005', 'KT021', '2025-03-28 13:04:50', 100000000, '2025-12-31 00:00:00', 1),
('L0006', 'KT021', '2025-03-28 13:05:43', 16400000, '2025-05-01 00:00:00', 1),
('L0007', 'KT001', '2025-03-28 14:31:39', 100000000, '2025-04-03 00:00:00', 0),
('L0008', 'KT001', '2025-03-29 09:22:37', 100000000, '2025-05-01 00:00:00', 0),
('L0009', 'KT028', '2025-03-29 10:45:32', 900000, '2025-04-30 00:00:00', 0),
('L0010', 'KT029', '2025-03-30 12:59:23', 1000000, '2025-04-03 00:00:00', 0),
('L0011', 'KT030', '2025-03-30 18:12:05', 1500000, '2026-08-30 00:00:00', 0),
('LP001', 'KT001', '2024-01-01 00:00:00', 1500000, '2024-06-30 00:00:00', 1),
('LP001', 'KT006', '2024-01-25 00:00:00', 1600000, '2024-06-30 00:00:00', 1),
('LP001', 'KT014', '2025-03-15 00:00:00', 1700000, '2025-08-31 00:00:00', 1),
('LP002', 'KT002', '2024-01-05 00:00:00', 2000000, '2024-07-31 00:00:00', 0),
('LP002', 'KT007', '2024-02-01 00:00:00', 2000000, '2024-07-31 00:00:00', 0),
('LP002', 'KT015', '2025-03-20 00:00:00', 2100000, '2025-09-30 00:00:00', 0),
('LP003', 'KT003', '2024-01-10 00:00:00', 2500000, '2024-08-31 00:00:00', 0),
('LP003', 'KT008', '2024-02-05 00:00:00', 2550000, '2024-08-31 00:00:00', 0),
('LP004', 'KT004', '2024-01-15 00:00:00', 3000000, '2024-09-30 00:00:00', 0),
('LP004', 'KT009', '2024-02-10 00:00:00', 3100000, '2024-09-30 00:00:00', 0),
('LP005', 'KT005', '2024-01-20 00:00:00', 3600000, '2024-10-31 00:00:00', 0),
('LP005', 'KT010', '2024-02-15 00:00:00', 3500000, '2024-10-31 00:00:00', 0),
('LP006', 'KT011', '2025-03-01 00:00:00', 1600000, '2025-08-31 00:00:00', 0),
('LP006', 'KT013', '2025-03-01 00:00:00', 1650000, '2025-08-31 00:00:00', 0),
('LP007', 'KT010', '2025-03-05 00:00:00', 2200000, '2025-09-30 00:00:00', 0),
('LP007', 'KT012', '2025-03-05 00:00:00', 2000000, '2025-09-30 00:00:00', 0),
('LP008', 'KT004', '2025-03-10 00:00:00', 2900000, '2025-10-31 00:00:00', 0),
('LP008', 'KT013', '2025-03-10 00:00:00', 2800000, '2025-10-31 00:00:00', 0);

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
('012394012394', 'tran hoang thi', '0987654234'),
('098756785431', 'tran thi a', '0987567535'),
('112233445566', 'Nguyễn Thị Lan', '0901234567'),
('123456789012', 'Trần Văn Đạt', '0911111111'),
('223344556677', 'Trần Văn Quang', '0912345678'),
('234567890123', 'Lê Thị Hoa', '0922222222'),
('334455667788', 'Lê Thị My', '0923456789'),
('345678901234', 'Nguyễn Văn Nam', '0933333333'),
('445566778899', 'Phạm Văn Hoàng', '0934567890'),
('456789012345', 'Phạm Thị Lan', '0944444444'),
('556677889900', 'Vũ Thị Hạnh', '0945678901'),
('567890123456', 'Hoàng Văn Minh', '0955555555'),
('678901234567', 'Vũ Thị Hương', '0966666666'),
('700000000000', 'iubuybiuybiy', '0999999990'),
('789012345678', 'Đặng Văn Huy', '0977777777'),
('890123456789', 'Bùi Thị Phương', '0988888888'),
('901234567890', 'Phan Văn Quân', '0999999999'),
('999999999999', 'Bùi Hiếu Huy', '0123456789');

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
('KT001', 'TR001', 1.6683, 'km'),
('KT001', 'TR008', 2.4523, 'km'),
('KT002', 'TR001', 2.5622, 'km'),
('KT002', 'TR008', 3.3462, 'km'),
('KT003', 'TR001', 3.5348, 'km'),
('KT003', 'TR008', 4.1978, 'km'),
('KT004', 'TR001', 2.4326, 'km'),
('KT004', 'TR008', 3.2166, 'km'),
('KT005', 'TR001', 3.2573, 'km'),
('KT005', 'TR008', 4.0413, 'km'),
('KT006', 'TR001', 2.3368, 'km'),
('KT006', 'TR008', 3.1208, 'km'),
('KT007', 'TR001', 16.0109, 'km'),
('KT007', 'TR008', 15.3125, 'km'),
('KT008', 'TR001', 4.1895, 'km'),
('KT008', 'TR008', 4.8086, 'km'),
('KT009', 'TR001', 17.9588, 'km'),
('KT009', 'TR008', 17.2605, 'km'),
('KT010', 'TR001', 20.0222, 'km'),
('KT010', 'TR008', 19.3239, 'km'),
('KT011', 'TR001', 19.4398, 'km'),
('KT011', 'TR008', 18.7415, 'km'),
('KT012', 'TR001', 47.9216, 'km'),
('KT012', 'TR008', 47.2233, 'km'),
('KT013', 'TR001', 18.9106, 'km'),
('KT013', 'TR008', 18.2123, 'km'),
('KT014', 'TR001', 19.5312, 'km'),
('KT014', 'TR008', 18.8329, 'km'),
('KT015', 'TR001', 20.8212, 'km'),
('KT015', 'TR008', 20.1229, 'km'),
('KT016', 'TR001', 14.9713, 'km'),
('KT016', 'TR008', 14.273, 'km'),
('KT017', 'TR001', 15.0307, 'km'),
('KT017', 'TR008', 14.3324, 'km'),
('KT018', 'TR001', 15.0307, 'km'),
('KT018', 'TR008', 14.3324, 'km'),
('KT019', 'TR001', 2.1282, 'km'),
('KT019', 'TR008', 1.4298, 'km'),
('KT020', 'TR001', 1.6683, 'km'),
('KT020', 'TR008', 2.4523, 'km'),
('KT021', 'TR001', 10.6203, 'km'),
('KT021', 'TR008', 9.922, 'km'),
('KT022', 'TR001', 12.7745, 'km'),
('KT022', 'TR008', 12.0762, 'km'),
('KT023', 'TR001', 12.7745, 'km'),
('KT023', 'TR008', 12.0762, 'km'),
('KT024', 'TR001', 12.7745, 'km'),
('KT024', 'TR008', 12.0762, 'km'),
('KT025', 'TR001', 12.7745, 'km'),
('KT025', 'TR008', 12.0762, 'km'),
('KT026', 'TR001', 12.7745, 'km'),
('KT026', 'TR008', 12.0762, 'km'),
('KT027', 'TR001', 12.7745, 'km'),
('KT027', 'TR002', 16.0473, 'km'),
('KT027', 'TR003', 4.611, 'km'),
('KT027', 'TR004', 2.5931, 'km'),
('KT027', 'TR005', 5.4938, 'km'),
('KT027', 'TR006', 7.2622, 'km'),
('KT027', 'TR007', 5.9579, 'km'),
('KT027', 'TR008', 12.0762, 'km'),
('KT028', 'TR001', 0.6239, 'km'),
('KT028', 'TR002', 5.7579, 'km'),
('KT028', 'TR003', 17.5449, 'km'),
('KT028', 'TR004', 15.5271, 'km'),
('KT028', 'TR005', 17.827, 'km'),
('KT028', 'TR006', 19.5955, 'km'),
('KT028', 'TR007', 18.2912, 'km'),
('KT028', 'TR008', 1.408, 'km'),
('KT029', 'TR001', 2.6928, 'km'),
('KT029', 'TR002', 3.2689, 'km'),
('KT029', 'TR003', 18.7471, 'km'),
('KT029', 'TR004', 16.7293, 'km'),
('KT029', 'TR005', 19.0292, 'km'),
('KT029', 'TR006', 20.7976, 'km'),
('KT029', 'TR007', 19.4933, 'km'),
('KT029', 'TR008', 3.5423, 'km'),
('KT030', 'TR001', 0.6393, 'km'),
('KT030', 'TR002', 5.7933, 'km'),
('KT030', 'TR003', 16.7984, 'km'),
('KT030', 'TR004', 14.7806, 'km'),
('KT030', 'TR005', 17.0805, 'km'),
('KT030', 'TR006', 18.849, 'km'),
('KT030', 'TR007', 17.5447, 'km'),
('KT030', 'TR008', 1.7727, 'km'),
('KT031', 'TR001', 1.0737, 'km'),
('KT031', 'TR002', 6.2277, 'km'),
('KT031', 'TR003', 16.5941, 'km'),
('KT031', 'TR004', 14.5763, 'km'),
('KT031', 'TR005', 16.8762, 'km'),
('KT031', 'TR006', 18.6447, 'km'),
('KT031', 'TR007', 17.3404, 'km'),
('KT031', 'TR008', 0.5924, 'km'),
('KT032', 'TR001', 1.4493, 'km'),
('KT032', 'TR002', 5.8437, 'km'),
('KT032', 'TR003', 16.2002, 'km'),
('KT032', 'TR004', 14.1824, 'km'),
('KT032', 'TR005', 16.4823, 'km'),
('KT032', 'TR006', 18.2508, 'km'),
('KT032', 'TR007', 16.9465, 'km'),
('KT032', 'TR008', 1.271, 'km'),
('KT033', 'TR001', 1.3332, 'km'),
('KT033', 'TR002', 4.8799, 'km'),
('KT033', 'TR003', 18.311, 'km'),
('KT033', 'TR004', 16.2931, 'km'),
('KT033', 'TR005', 18.5931, 'km'),
('KT033', 'TR006', 20.3615, 'km'),
('KT033', 'TR007', 19.0572, 'km'),
('KT033', 'TR008', 1.6705, 'km'),
('KT034', 'TR001', 1.0667, 'km'),
('KT034', 'TR002', 5.4611, 'km'),
('KT034', 'TR003', 17.1947, 'km'),
('KT034', 'TR004', 15.1769, 'km'),
('KT034', 'TR005', 17.4768, 'km'),
('KT034', 'TR006', 19.2453, 'km'),
('KT034', 'TR007', 17.941, 'km'),
('KT034', 'TR008', 1.404, 'km');

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
('KT001', 'DU021', '0900000001', '12A', 'Khu trọ Bình', 105.778, 10.0421, 0),
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
('KT016', 'DU022', '0900000001', '12A', 'Khu trọ Bình An', 105.813, 10.0671, 0),
('KT017', 'DU024', '0900000001', '12A', 'Minh', 105.811, 10.068, 0),
('KT018', 'DU024', '0900000001', '12A', 'Minh Tân', 105.811, 10.068, 0),
('KT019', 'DU025', '0900000001', '123', 'Minh Minh', 105.77, 10.0187, 1),
('KT020', 'DU021', '0900000001', '12A', 'Khu trọ Bình', 105.778, 10.0421, 1),
('KT021', 'DU026', '0900000001', '12A', 'Minh Taaaaaan minh', 105.825, 10.0411, 0),
('KT022', 'DU027', '0900000001', '12A', 'Khu trọ Bình An', 105.826, 10.0631, 0),
('KT023', 'DU027', '0900000001', '12A', 'Khu trọ Bình An', 105.826, 10.0631, 0),
('KT024', 'DU027', '0900000001', '12A', 'Khu trọ Bình An', 105.826, 10.0631, 0),
('KT025', 'DU027', '0900000001', '12A', 'Khu trọ Bình An', 105.826, 10.0631, 0),
('KT026', 'DU027', '0900000001', '12A', 'Khu trọ Bình An', 105.826, 10.0631, 0),
('KT027', 'DU027', '0900000001', '12A', 'Khu trọ Bình An', 105.826, 10.0631, 0),
('KT028', 'DU028', '0900000001', '99', 'Bình Minh', 105.774, 10.0327, 0),
('KT029', 'DU029', '0900000001', '31', 'hoang ha', 105.776, 10.047, 0),
('KT030', 'DU028', '9999999999', '8386', 'Khu trọ sinh viên', 105.77, 10.0272, 0),
('KT031', 'DU030', '9999999999', '15', 'Khu trọ Mai Hoa', 105.773, 10.0255, 0),
('KT032', 'DU031', '9999999999', '67', 'Khu Trọ Bình Yên', 105.776, 10.0259, 0),
('KT033', 'DU032', '9999999999', '156', 'Khu trọ Tiến Phúc', 105.77, 10.0365, 0),
('KT034', 'DU033', '9999999999', '13', 'Khu trọ Ngô Hà', 105.775, 10.0297, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su`
--

CREATE TABLE `lich_su` (
  `TTP_MA` char(2) NOT NULL,
  `PHONG_MAPHONG` char(5) NOT NULL,
  `LS_NGAYBATDAUTHUE` datetime NOT NULL,
  `LS_NGAYKETTHUC` datetime DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lich_su`
--

INSERT INTO `lich_su` (`TTP_MA`, `PHONG_MAPHONG`, `LS_NGAYBATDAUTHUE`, `LS_NGAYKETTHUC`, `is_delete`) VALUES
('01', 'P001', '2025-03-27 15:52:39', NULL, 0),
('01', 'P0019', '2025-03-27 15:44:49', NULL, 0),
('01', 'P0020', '2025-03-27 19:10:23', NULL, 0),
('01', 'P0021', '2025-03-27 19:10:34', NULL, 0),
('01', 'P0024', '2025-03-27 19:26:19', NULL, 0),
('01', 'P0027', '2025-03-27 19:44:35', NULL, 0),
('01', 'P0028', '2025-03-27 19:45:14', NULL, 0),
('01', 'P0033', '2024-02-15 00:00:00', '2024-08-15 00:00:00', 0),
('01', 'P0035', '2025-03-28 14:31:55', '2025-03-29 04:47:11', 0),
('01', 'P0036', '2025-03-29 10:46:33', '2025-03-30 13:19:08', 0),
('01', 'P0037', '2025-03-30 16:22:16', NULL, 0),
('01', 'P0038', '2025-03-30 16:27:40', NULL, 0),
('01', 'P0039', '2025-03-30 16:34:30', NULL, 0),
('01', 'P0040', '2025-03-30 16:42:00', NULL, 0),
('01', 'P0041', '2025-03-30 18:14:06', NULL, 0),
('01', 'P006', '2025-03-15 00:00:00', NULL, 0),
('01', 'P007', '2025-04-01 00:00:00', NULL, 0),
('02', 'P0022', '2025-03-28 14:22:04', NULL, 0),
('02', 'P0023', '2025-03-28 14:22:04', NULL, 0),
('02', 'P0025', '2025-03-28 14:22:04', NULL, 0),
('02', 'P0026', '2025-03-28 14:22:04', NULL, 0),
('02', 'P0030', '2025-03-28 14:25:39', NULL, 0),
('02', 'P0031', '2025-03-28 14:25:39', '2024-12-15 00:00:00', 0),
('02', 'P0035', '2025-03-29 04:47:11', NULL, 0),
('02', 'P0036', '2025-03-30 13:19:01', NULL, 0),
('05', 'P013', '2025-04-10 00:00:00', '2025-09-10 00:00:00', 0),
('05', 'P015', '2025-04-20 00:00:00', '2025-09-20 00:00:00', 0),
('06', 'P012', '2025-04-05 00:00:00', '2025-09-05 00:00:00', 0),
('06', 'P014', '2025-04-15 00:00:00', '2025-09-15 00:00:00', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_phong`
--

CREATE TABLE `loai_phong` (
  `LP_MALOAIPHONG` char(5) NOT NULL,
  `LP_TENLOAIPHONG` varchar(255) NOT NULL,
  `LP_DIENTICH` float NOT NULL,
  `LP_SUCCHUA` int(11) NOT NULL,
  `LP_VATCHAT` varchar(255) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_phong`
--

INSERT INTO `loai_phong` (`LP_MALOAIPHONG`, `LP_TENLOAIPHONG`, `LP_DIENTICH`, `LP_SUCCHUA`, `LP_VATCHAT`, `is_delete`) VALUES
('L0001', 'Phòng đơn', 15, 3, 'rrrrrr', 1),
('L0002', 'Phòng đơn', 15, 3, 'rrrrrr', 0),
('L0003', 'phòng đôi', 20, 4, 'rrrrrr', 0),
('L0004', 'phòng đôi', 20, 4, 'rrrrrr', 0),
('L0005', 'phòng đôi', 20, 4, 'rrrrrr', 0),
('L0006', 'Phòng đơn', 13, 5, 'fffff', 0),
('L0007', 'phòng đôi', 22, 222, '222', 0),
('L0008', 'phòng đôi', 22, 222, '222', 0),
('L0009', 'phòng giường đơn', 2000, 90, 'gì cũng có', 0),
('L0010', 'phòng 3 người', 1000, 3, 'tivi', 0),
('L0011', 'Phòng đơn', 20, 2, 'Tủ lạnh, WC riêng', 0),
('LP001', 'Phòng đơn', 20, 1, 'Gác xép', 0),
('LP002', 'Phòng đôi', 30, 2, 'Ban công', 0),
('LP003', 'Phòng gia đình', 40, 4, 'Điều hòa', 0),
('LP004', 'Phòng cao cấp', 50, 3, 'Nội thất hiện đại', 0),
('LP005', 'Phòng studio', 25, 1, 'Minh bạch', 0),
('LP006', 'Phòng mini', 18, 1, 'Gác xép cơ bản', 0),
('LP007', 'Phòng thương gia', 60, 4, 'Nội thất sang trọng', 0),
('LP008', 'Phòng duplex', 70, 3, 'Hai tầng riêng biệt', 0);

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
  `PT_TINHTRANG` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phieu_thue`
--

INSERT INTO `phieu_thue` (`PT_MA`, `PHONG_MAPHONG`, `KH_CCCD`, `PT_NGAYLAP`, `PT_NGAYBATDAU`, `PT_NGAYKETTHUC`, `PT_TINHTRANG`, `is_delete`) VALUES
('PT001', 'P001', '123456789012', '2025-01-01 00:00:00', '2025-01-05 00:00:00', '2025-07-01 00:00:00', 1, 0),
('PT002', 'P002', '234567890123', '2025-01-10 00:00:00', '2025-01-15 00:00:00', '2025-07-10 00:00:00', 0, 0),
('PT003', 'P003', '345678901234', '2025-01-20 00:00:00', '2025-01-25 00:00:00', '2025-07-20 00:00:00', 0, 0),
('PT004', 'P004', '456789012345', '2025-02-01 00:00:00', '2025-02-05 00:00:00', '2025-08-01 00:00:00', 0, 0),
('PT005', 'P005', '567890123456', '2025-02-10 00:00:00', '2025-02-15 00:00:00', '2025-08-10 00:00:00', 0, 0),
('PT006', 'P006', '678901234567', '2025-02-20 00:00:00', '2025-02-25 00:00:00', '2025-08-20 00:00:00', 0, 0),
('PT007', 'P007', '789012345678', '2025-03-01 00:00:00', '2025-03-05 00:00:00', '2025-09-01 00:00:00', 1, 0),
('PT008', 'P008', '890123456789', '2025-03-10 00:00:00', '2025-03-15 00:00:00', '2025-09-10 00:00:00', 0, 0),
('PT009', 'P009', '901234567890', '2025-03-20 00:00:00', '2025-03-25 00:00:00', '2025-09-20 00:00:00', 0, 0),
('PT010', 'P010', '012345678901', '2025-03-30 00:00:00', '2025-04-05 00:00:00', '2025-09-30 00:00:00', 0, 0),
('PT011', 'P0030', '112233445566', '2025-04-01 00:00:00', '2025-03-28 14:25:39', NULL, 1, 0),
('PT012', 'P0030', '223344556677', '2025-04-05 00:00:00', '2025-03-28 14:25:39', NULL, 1, 0),
('PT013', 'P0031', '334455667788', '2025-04-10 00:00:00', '2025-03-28 14:25:39', NULL, 1, 0),
('PT014', 'P0031', '445566778899', '2025-04-15 00:00:00', '2025-03-28 14:25:39', NULL, 1, 0),
('PT015', 'P0031', '556677889900', '2025-04-20 00:00:00', '2025-03-28 14:25:39', NULL, 1, 0),
('PT016', 'P0035', '700000000000', '2025-03-29 00:00:00', '2025-04-01 00:00:00', '2025-05-01 00:00:00', 1, 0),
('PT017', 'P0036', '012394012394', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-05-02 00:00:00', 0, 0),
('PT018', 'P0036', '012394012394', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-05-02 00:00:00', 0, 0),
('PT019', 'P0036', '012394012394', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-05-02 00:00:00', 0, 0),
('PT020', 'P0036', '012394012394', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-05-02 00:00:00', 0, 0),
('PT021', 'P0036', '012394012394', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-05-02 00:00:00', 1, 0),
('PT022', 'P0036', '012394012394', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-05-02 00:00:00', 0, 0),
('PT023', 'P0036', '098756785431', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-05-02 00:00:00', 0, 0),
('PT024', 'P0036', '999999999999', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-06-18 00:00:00', 0, 0),
('PT025', 'P0036', '999999999999', '2025-03-30 00:00:00', '2025-04-02 00:00:00', '2025-05-02 00:00:00', 0, 0);

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
('P001', 'LP001', 'Phòng đơn giá rẻ', NULL, 'KT001', '01', 1),
('P0016', 'LP005', 'Phòng đơn giá rẻ', 'uploads/1742735702_phong-khach-san-tt-studio.jpg', 'KT017', '01', 1),
('P0017', 'LP001', 'Phòng đơn giá rẻ', 'uploads/1742735758_phong-khach-san-tt-studio.jpg', 'KT017', '02', 1),
('P0018', 'LP001', 'Phòng đơn giá rẻ', NULL, 'KT001', '02', 1),
('P0019', 'L0003', 'Phòng đơn giá rẻ', 'uploads/1743065089_thiet-ke-noi-that-khach-san-phong-cach-dong-duong-3.jpg', 'KT001', '03', 1),
('P002', 'LP002', 'Phòng đôi view sông', NULL, 'KT002', '01', 0),
('P0020', 'L0002', 'Phòng đơn giá rẻ', '', 'KT001', '04', 1),
('P0021', 'L0002', 'Phòng đơn giá rẻ', '', 'KT001', '05', 1),
('P0022', 'L0001', 'Phòng đơn giá rẻ', '', 'KT001', '06', 1),
('P0023', 'L0001', 'Phòng đơn giá rẻ', 'uploads/1743078344_171123-sanh-khach-san-tan-co-dien.jpg', 'KT001', '07', 1),
('P0024', 'L0003', 'Phòng đơn giá rẻ', 'uploads/1743078379_khach-san-tinh-yeu-ha-noi-9.jpg', 'KT001', '08', 1),
('P0025', 'L0001', 'Phòng đơn giá rẻ', 'uploads/1743078998_thiet-ke-noi-that-khach-san-phong-cach-dong-duong-3.jpg', 'KT001', '09', 1),
('P0026', 'L0001', 'Phòng đơn giá rẻ', '', 'KT001', '10', 1),
('P0027', 'L0003', 'Phòng đơn giá rẻ', '[\"\\/xampp\\/htdoct\\/htqlnt\\/ChuTro\\/upload\\/P0027\\/1743079475_0_khach-san-tinh-yeu-ha-noi-9.jpg\",\"\\/xampp\\/htdoct\\/htqlnt\\/ChuTro\\/upload\\/P0027\\/1743079475_1_park-hyatt-5-sao-sai-gon.jpg\"]', 'KT001', '11', 1),
('P0028', 'L0003', 'Phòng đơn giá rẻ', '', 'KT001', '12', 1),
('P0029', 'L0003', 'Phòng đơn giá rẻ', '', 'KT001', '13', 1),
('P003', 'LP003', 'Phòng gia đình rộng rãi', NULL, 'KT003', '01', 0),
('P0030', 'L0001', 'Phòng đơn giá rẻ', '', 'KT001', '14', 1),
('P0031', 'L0001', 'Phòng đơn giá rẻ', '[\"\\/xampp\\/htdoct\\/htqlnt\\/ChuTro\\/upload\\/P0031\\/1743080467_0_khach-san-tinh-yeu-ha-noi-9.jpg\",\"\\/xampp\\/htdoct\\/htqlnt\\/ChuTro\\/upload\\/P0031\\/1743080467_1_park-hyatt-5-sao-sai-gon.jpg\"]', 'KT001', '15', 0),
('P0032', 'L0004', 'Phòng đơn giá rẻ', '[\"\\/xampp\\/htdoct\\/htqlnt\\/ChuTro\\/upload\\/P0032\\/1743080994_0_khach-san-tinh-yeu-ha-noi-9.jpg\"]', 'KT016', '01', 1),
('P0033', 'L0005', 'Phòng đơn giá rẻ', '[\"\\/xampp\\/htdoct\\/htqlnt\\/ChuTro\\/upload\\/P0033\\/1743141967_0_phong-khach-san-tt-studio.jpg\",\"\\/xampp\\/htdoct\\/htqlnt\\/ChuTro\\/upload\\/P0033\\/1743141967_1_thiet-ke-noi-that-khach-san-phong-cach-dong-duong-3.jpg\"]', 'KT021', '01', 1),
('P0034', 'L0006', 'Phòng đơn giá rẻ', '[\"\\/xampp\\/htdoct\\/htqlnt\\/ChuTro\\/upload\\/P0034\\/1743142558_0_park-hyatt-5-sao-sai-gon.jpg\"]', 'KT021', '02', 1),
('P0035', 'L0007', 'Phòng đơn giá rẻ', 'D:\\xampp\\htdocs\\HTQLNT\\ChuTro\\uploads\\icons', 'KT001', '16', 0),
('P0036', 'L0009', 'phòng đẹp, sang', 'D:\\xampp\\htdocs\\HTQLNT\\ChuTro\\uploads\\P0036', 'KT028', '01', 0),
('P0037', 'L0009', 'oki;a', '[\"D:\\/xampp\\/htdocs\\/HTQLNT\\/ChuTro\\/uploads\\/P0037\\/1743326536_0_Screenshot 2023-08-28 072000.png\",\"D:\\/xampp\\/htdocs\\/HTQLNT\\/ChuTro\\/uploads\\/P0037\\/1743326536_1_Screenshot 2023-10-08 115754.png\",\"D:\\/xampp\\/htdocs\\/HTQLNT\\/ChuTro\\/uploads\\/P0037\\/1743', 'KT028', '02', 0),
('P0038', 'L0009', 'hahahh', '[\"D:\\/xampp\\/htdocs\\/HTQLNT\\/ChuTro\\/uploads\\/P0038\\/1743326860_0_Screenshot 2023-08-28 071933.png\",\"D:\\/xampp\\/htdocs\\/HTQLNT\\/ChuTro\\/uploads\\/P0038\\/1743326860_1_Screenshot 2023-08-28 071951.png\",\"D:\\/xampp\\/htdocs\\/HTQLNT\\/ChuTro\\/uploads\\/P0038\\/1743', 'KT028', '03', 0),
('P0039', 'L0009', 'okioki', 'D:/xampp/htdocs/HTQLNT/ChuTro/uploads/P0039/', 'KT028', '04', 0),
('P004', 'LP004', 'Phòng cao cấp có ban công', NULL, 'KT004', '01', 0),
('P0040', 'L0009', 'hihi', 'D:/xampp/htdocs/HTQLNT/ChuTro/uploads/P0040/', 'KT028', '05', 0),
('P0041', 'L0011', 'Phòng tiện nghi, sạch sẽ', 'D:/xampp/htdocs/HTQLNT/ChuTro/uploads/P0041/', 'KT030', '01', 0),
('P005', 'LP005', 'Phòng studio đẹp', NULL, 'KT005', '01', 0),
('P006', 'LP001', 'Phòng đơn với tiện nghi cơ bản', NULL, 'KT006', '01', 0),
('P007', 'LP002', 'Phòng đôi với nội thất đẹp', NULL, 'KT015', '04', 0),
('P008', 'LP003', 'Phòng gia đình thoáng mát', NULL, 'KT008', '01', 0),
('P009', 'LP004', 'Phòng cao cấp view thành phố', NULL, 'KT009', '01', 0),
('P010', 'LP005', 'Phòng studio tiết kiệm', NULL, 'KT010', '01', 0),
('P011', 'LP006', 'Phòng mini giá rẻ', NULL, 'KT011', '01', 0),
('P012', 'LP007', 'Phòng thương gia view đẹp', NULL, 'KT012', '02', 0),
('P013', 'LP008', 'Phòng duplex hiện đại', NULL, 'KT013', '02', 0),
('P014', 'LP007', 'Phòng thương gia rộng rãi', NULL, 'KT004', '03', 0),
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
('QH017', 'TTP00', 'Quận Ninh Kiều'),
('QH18', 'TTP05', 'Phường Cái Vồn'),
('QH19', 'TTP05', 'Huyện Long Hồ'),
('QH20', 'TTP05', 'Huyện Tam Bình'),
('QH21', 'TTP05', 'Phường Đông Thuận');

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
('TTP00', 'Thành phố Cần Thơ'),
('TTP01', 'Cần Thơ'),
('TTP02', 'Hậu Giang'),
('TTP03', 'Vĩnh Long'),
('TTP04', 'Sóc Trăng'),
('TTP05', 'Tỉnh Vĩnh Long');

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
  `TRUONG_ICON` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `truong`
--

INSERT INTO `truong` (`TRUONG_MA`, `DUONG_MA`, `TRUONG_TEN`, `TRUONG_SODIACHI`, `TRUONG_LONGTITUDE`, `TRUONG_LATITUDE`, `TRUONG_ICON`) VALUES
('TR001', 'DU028', 'Đại học Cần Thơ', 'Đường 3 Tháng 2, Phường Xuân Khánh, Quận Ninh Kiều, Thành phố Cần Thơ', 105.771, 10.0288, 'uploads/icons/1743220632_thiet-ke-phong-tro-2-tang 2.png'),
('TR002', 'D012', 'Đại học Hậu Giang', '456 Trần Phú, Hậu Giang', 105.79, 10.0551, ''),
('TR003', 'D013', 'Đại học Vĩnh Long', '789 Lê Lợi, Vĩnh Long', 105.8, 10.0651, ''),
('TR004', 'D014', 'Đại học Sóc Trăng', '101 Lê Hồng Phong, Sóc Trăng', 105.81, 10.0751, ''),
('TR005', 'D015', 'Cao đẳng Công nghệ Sài Gòn', '202 Nguyễn Huệ, Cần Thơ', 105.82, 10.0851, ''),
('TR006', 'D016', 'Cao đẳng Du lịch Cần Thơ', '15 Nguyễn Trãi, Cần Thơ', 105.825, 10.0952, ''),
('TR007', 'D017', 'Đại học Hậu Giang 2', '27 Lê Thái Tổ, Hậu Giang', 105.83, 10.1002, ''),
('TR008', 'DU028', 'Trường Đại học Cần Thơ', 'Đường 3 Tháng 2, Phường Xuân Khánh, Quận Ninh Kiều, Thành phố Cần Thơ', 105.769, 10.0268, 'uploads/icons/1743334080_tải xuống.png');

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
('XP035', 'QH017', 'Quận Ninh Kiều'),
('XP036', 'QH18', 'Phường Cái Vồn'),
('XP037', 'QH19', 'Phường Cái Vồn'),
('XP038', 'QH017', 'An Hòa'),
('XP039', 'QH20', 'Xã Mỹ Hòa'),
('XP040', 'QH21', 'Xã Mỹ Hòa'),
('XP041', 'QH017', 'Phường Xuân Khánh'),
('XP042', 'QH017', 'Phường Cái Khế');

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
