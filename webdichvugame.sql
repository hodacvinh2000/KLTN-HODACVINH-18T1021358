-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 16, 2022 lúc 04:01 AM
-- Phiên bản máy phục vụ: 10.4.22-MariaDB
-- Phiên bản PHP: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `webdichvugame`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `anh`
--

CREATE TABLE `anh` (
  `id` int(11) NOT NULL,
  `tkgame_id` int(11) NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `anh`
--

INSERT INTO `anh` (`id`, `tkgame_id`, `link`) VALUES
(8, 10, '/images/vinhabc-1-2022-03-09-02-51-43-0.jpg'),
(9, 10, '/images/vinhabc-1-2022-03-09-02-51-43-1.jpg'),
(13, 10, '/images/vinhabc-1-2022-03-09-03-55-55-0.jpg'),
(17, 9, '/images/vinh123-1-2022-03-21-16-01-19-3.jpg'),
(18, 9, '/images/vinh123-1-2022-03-21-16-01-19-4.jpg'),
(19, 9, '/images/vinh123-1-2022-03-21-16-06-49-0.jpg'),
(20, 9, '/images/vinh123-1-2022-03-21-16-06-49-1.gif'),
(21, 9, '/images/vinh123-1-2022-03-21-16-06-49-2.jpg'),
(22, 9, '/images/vinh123-1-2022-03-21-16-06-49-3.jpg'),
(23, 9, '/images/vinh123-1-2022-03-21-16-06-49-4.jpg'),
(24, 9, '/images/vinh123-1-2022-03-21-16-06-49-5.jpg'),
(25, 9, '/images/vinh123-1-2022-03-21-16-06-50-6.jpg'),
(26, 13, '/images/hodacvinh1991-2-2022-04-30-17-17-40-0.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binhluannhiemvu`
--

CREATE TABLE `binhluannhiemvu` (
  `id` int(11) NOT NULL,
  `binhluan` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nhiemvu_id` int(11) NOT NULL,
  `nguoibinhluan_id` int(11) NOT NULL,
  `phanhoi_id` int(11) DEFAULT NULL,
  `thoigian` datetime NOT NULL,
  `sophanhoi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `binhluannhiemvu`
--

INSERT INTO `binhluannhiemvu` (`id`, `binhluan`, `nhiemvu_id`, `nguoibinhluan_id`, `phanhoi_id`, `thoigian`, `sophanhoi`) VALUES
(13, 'kích gửi', 7, 3, NULL, '2022-01-21 11:26:54', 0),
(44, 'Chán quá lỡ xóa bình luận rồi', 7, 1, NULL, '2022-03-22 14:31:33', 0),
(45, 'Ây da mãi mới xong', 7, 1, NULL, '2022-03-22 14:36:51', 2),
(76, 'ádasd', 7, 1, 45, '2022-03-23 09:45:33', 2),
(83, 'ádasd', 7, 1, 76, '2022-03-23 10:00:40', 0),
(85, 'ádasdsad', 7, 1, NULL, '2022-03-23 04:03:14', 0),
(86, 'ádasdasd', 7, 1, 76, '2022-03-23 10:04:36', 0),
(87, 'abcsdasdkj', 7, 1, 45, '2022-03-25 09:45:01', 0),
(88, 'ádasd', 27, 1, NULL, '2022-04-03 03:17:29', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20220114085630', '2022-01-14 09:56:44', 391),
('DoctrineMigrations\\Version20220114085835', '2022-01-14 09:58:38', 766),
('DoctrineMigrations\\Version20220114090938', '2022-01-14 10:09:40', 59),
('DoctrineMigrations\\Version20220114091102', '2022-01-14 10:11:05', 88),
('DoctrineMigrations\\Version20220114091220', '2022-01-14 10:12:23', 48),
('DoctrineMigrations\\Version20220114092648', '2022-01-14 10:26:50', 188),
('DoctrineMigrations\\Version20220115135021', '2022-01-15 14:50:32', 230),
('DoctrineMigrations\\Version20220118011818', '2022-01-18 02:18:34', 415),
('DoctrineMigrations\\Version20220118012844', '2022-01-18 02:29:50', 52),
('DoctrineMigrations\\Version20220118043025', '2022-01-18 05:30:29', 147),
('DoctrineMigrations\\Version20220118092459', '2022-01-18 10:25:16', 143),
('DoctrineMigrations\\Version20220119014305', '2022-01-19 02:43:20', 269),
('DoctrineMigrations\\Version20220119020752', '2022-01-19 03:08:07', 54),
('DoctrineMigrations\\Version20220119021353', '2022-01-19 03:13:55', 558),
('DoctrineMigrations\\Version20220119022426', '2022-01-19 03:24:29', 282),
('DoctrineMigrations\\Version20220119022601', '2022-01-19 03:26:03', 168),
('DoctrineMigrations\\Version20220119023128', '2022-01-19 03:31:30', 52),
('DoctrineMigrations\\Version20220225025557', '2022-02-25 03:56:41', 92),
('DoctrineMigrations\\Version20220225035929', '2022-02-25 04:59:34', 96),
('DoctrineMigrations\\Version20220228015410', '2022-02-28 02:54:23', 324),
('DoctrineMigrations\\Version20220228072422', '2022-02-28 08:24:36', 102),
('DoctrineMigrations\\Version20220301033752', '2022-03-01 04:37:55', 119),
('DoctrineMigrations\\Version20220301044350', '2022-03-01 05:43:54', 64),
('DoctrineMigrations\\Version20220301082100', '2022-03-01 09:24:58', 282),
('DoctrineMigrations\\Version20220302034400', '2022-03-02 04:44:05', 515),
('DoctrineMigrations\\Version20220308065818', '2022-03-08 07:58:34', 144),
('DoctrineMigrations\\Version20220312095625', '2022-03-12 10:56:38', 108),
('DoctrineMigrations\\Version20220317055816', '2022-03-17 06:58:30', 76),
('DoctrineMigrations\\Version20220317061817', '2022-03-17 07:18:21', 65),
('DoctrineMigrations\\Version20220317062203', '2022-03-17 07:22:06', 65),
('DoctrineMigrations\\Version20220317090858', '2022-03-17 10:09:02', 70),
('DoctrineMigrations\\Version20220319085108', '2022-03-19 09:51:23', 90),
('DoctrineMigrations\\Version20220319085625', '2022-03-19 09:56:28', 230),
('DoctrineMigrations\\Version20220322070925', '2022-03-22 08:09:41', 192);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `game`
--

CREATE TABLE `game` (
  `id` int(11) NOT NULL,
  `tengame` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anh` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `game`
--

INSERT INTO `game` (`id`, `tengame`, `anh`) VALUES
(1, 'Đột kích', '/images/games/1.jpg'),
(2, 'Liên minh huyền thoại', '/images/games/2.jpg'),
(3, 'PUBG - PC', '/images/games/3.jpg'),
(5, 'Cửu âm chân kinh', '/images/games/5.jpg'),
(21, 'ádasd', ''),
(22, 'ádasdasdasd', ''),
(23, 'ád', ''),
(25, 'ádasd', ''),
(26, 'Black and Soul', ''),
(27, 'ádasd', ''),
(29, 'Apex Legends - version 2', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhiemvu`
--

CREATE TABLE `nhiemvu` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `noidung` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trangthai` int(11) NOT NULL,
  `ngaydang` datetime NOT NULL,
  `tieude` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhiemvu`
--

INSERT INTO `nhiemvu` (`id`, `user_id`, `game_id`, `noidung`, `trangthai`, `ngaydang`, `tieude`) VALUES
(7, 3, 3, 'pubg pc mở free trên thế giới', 1, '2022-01-18 08:36:12', 'THÔNG BÁO PUBC PC'),
(9, 5, 5, 'Bán vật phẩm hiếm cửu âm chân kinhsadhasdjh ajshdajk haasdh asdjhasdkjahdjahdjghdfjhgdjggpriemapkd;sdkjf skljdfkgjs;dk fjg,df;gj[qowiecwkam;sldkvoip', 1, '2022-01-17 17:08:46', 'BÁN VẬT PHẨM CỬU ÂM CHÂN KINH'),
(10, 5, 2, 'Lập team LOL', 1, '2022-01-17 08:00:44', 'LẬP TEAM LOL ĐÁNH GIẢI'),
(11, 4, 2, 'ádasdasdsad', 1, '2022-03-01 10:12:45', 'ABCXYZ...'),
(15, 1, 1, 'ádasdasdassdasdasd', 1, '2022-03-22 18:41:20', 'LẠI LÀ ĐỘT KÍCH'),
(21, 1, 5, 'ádasdasdasdasđiugfhgdkjfhgdjfghdfjkghdjkfghdffjkghoetuyuwrjdlksjdfkjfòiẹklsdjfksdjfskldfjsk skdfjskdlf jlsdkfjskdfj jer sddkfj ẹ skdfj wierrj sdkfj wejk jsdkfjj ekfj sdkf è jksldjjf ew kfskd fjewk fskd f', 1, '2022-03-16 03:18:52', 'Test'),
(22, 1, 1, 'ABC XYZ flonotiro đã sửa', 1, '2022-03-16 10:13:52', 'Test thêm nhiệm vụ'),
(23, 1, 1, 'ádasdasdasdasda', 1, '2022-03-22 18:41:10', 'Sửa lần 2'),
(24, 1, 1, '- Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum\r\n - Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum\r\n- Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', 1, '2022-03-16 16:55:18', 'ádasdasd'),
(25, 1, 1, 'ABCSXUASODASOIDUUJSOIDJASDSAODJS', -3, '2022-03-22 14:15:21', 'Cập nhật lần 1'),
(26, 1, 1, 'ádasdasdasdasd', 1, '2022-03-16 16:55:32', 'ádasdasdasd'),
(27, 1, 1, 'sdasdasdasd', 1, '2022-03-16 16:55:42', 'ádasda'),
(28, 1, 1, 'Test đăng nhiệm vụ', 0, '2022-05-06 10:02:34', 'test 06/05/2022');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoangame`
--

CREATE TABLE `taikhoangame` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL,
  `gia` int(11) NOT NULL,
  `ingame` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoangame`
--

INSERT INTO `taikhoangame` (`id`, `game_id`, `username`, `password`, `description`, `status`, `gia`, `ingame`) VALUES
(9, 1, 'vinh123', '123', 'vinh là nhất', 1, 500000, 'Bánh Pow'),
(10, 1, 'vinhabc', '123', 'ádasdasdasdasdasd', 0, 100000, 'VinhPro123'),
(13, 2, 'hodacvinh1991', 'kinhkong113', '- Account lâu năm của Bánh Pow từ khi chơi Liên minh tới bây giờ.\r\n- Account gần full skin zed.\r\n- Tổng số trang phục gần 250.', 1, 50000, 'Bánh Pow'),
(14, 2, 'animedaica123', '123', 'Tài khoản game liên minh của Bánh Cow', 1, 50000, 'Bánh Cow'),
(15, 1, 'handoivodoi123', 'handoivodoi123', 'handoivodoi123\r\n', 1, 50000, 'handoivodoi123'),
(16, 2, 'handoivodoi123', 'handoivodoi123', 'handoivodoi123', 1, 50000, 'handoivodoi123'),
(17, 3, 'handoivodoi12345', 'handoivodoi123', 'handoivodoi123', 1, 50000, 'handoivodoi123'),
(18, 5, 'handoivodoi123', 'handoivodoi123', 'handoivodoi123', 1, 50000, 'handoivodoi123'),
(19, 21, 'handoivodoi', 'handoivodoi123', 'handoivodoi123', 1, 50000, 'handoivodoi123'),
(20, 22, 'handoivodoi123', 'handoivodoi123', 'handoivodoi123', 1, 50000, 'handoivodoi123'),
(21, 26, 'handoivodoi123', 'handoivodoi123', 'handoivodoi123', 1, 50000, 'handoivodoi123'),
(22, 29, 'handoivodoi123321', 'handoivodoi123', 'handoivodoi123', 1, 50000, 'handoivodoi123'),
(23, 1, 'testgame', '123', 'Test thử xem', 1, 10000, 'testgame');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thegame`
--

CREATE TABLE `thegame` (
  `id` int(11) NOT NULL,
  `seri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cardnumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `gia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thegame`
--

INSERT INTO `thegame` (`id`, `seri`, `cardnumber`, `status`, `game_id`, `gia`) VALUES
(11, '325345435', '345345345', 1, 5, 10000),
(14, '345435435345435', '345345345345345', 1, 3, 50000),
(15, '546132654312', '6465432132165', 0, 1, 20000),
(16, '125311231231', '3152312523123', 0, 1, 10000),
(17, '643213215648945', '3213216548974213', 0, 1, 10000),
(18, '74392875298347', '74392875298347', 1, 3, 100000),
(19, '74392875298347', '74392875298347', 1, 5, 50000),
(20, '74392875298347', '74392875298347', 1, 2, 20000),
(21, '74392875298347', '74392875298347', 1, 3, 20000),
(22, '74392875298347', '74392875298347', 1, 1, 100000),
(23, '121212121212121212', '121212121212121212', 1, 3, 50000),
(25, '123321123321123', '1233211232321', 1, 1, 50000),
(26, '987987987987', '987987987987', 1, 1, 10000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `tendangnhap` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matkhau` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hoten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngaysinh` date NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sodt` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gioitinh` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sodu` int(11) NOT NULL,
  `quyen` int(11) NOT NULL,
  `vertical_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`id`, `tendangnhap`, `matkhau`, `hoten`, `ngaysinh`, `email`, `sodt`, `gioitinh`, `sodu`, `quyen`, `vertical_code`) VALUES
(1, 'hodacvinh2000', '81dc9bdb52d04dc20036dbd8313ed055', 'Hồ Đắc Vinh', '2000-07-10', '18t1021358@husc.edu.vn', '0321456321', 'Nam', 9270000, 1, '99d1831bb9cac4ce8f3b058d4304eb5a'),
(3, 'hodacvinh1', '15c6d98082895abf1c20c0358aff2a67', 'Vinhtest1111', '2015-12-30', 'vinh1@gmail.com', '0123131323', 'Nam', 1000, 0, 'a3cf2b67dcb0da88a8042f34f7a33e83'),
(4, 'hodacvinh2', '202cb962ac59075b964b07152d234b70', 'Vinh2', '2000-07-17', 'hodacvinh2@gmail.com', '0123654789', 'Nam', 0, -1, ''),
(5, 'hodacvinh3', '202cb962ac59075b964b07152d234b70', 'Vinh3', '2000-07-17', 'hodacvinh3@gmail.com', '01234567824', 'Nam', 1000000, 0, ''),
(18, 'vinh8', '202cb962ac59075b964b07152d234b70', 'vinh8', '2000-07-17', 'vinh8@gmail.com', '213123213', 'Nam', 10000, 0, ''),
(19, 'vinh9', '202cb962ac59075b964b07152d234b70', 'vinh9', '2001-08-18', 'vinh9@gmail.com', '123158103', 'Nữ', 23123, -1, ''),
(21, 'vinh11', '202cb962ac59075b964b07152d234b70', 'vinh11', '2000-03-12', 'vinh11@gmail.com', '1231012039', 'Nữ', 23, 0, ''),
(24, 'vinh15', '202cb962ac59075b964b07152d234b70', 'vinh15', '2000-03-21', 'vinh15@gmail.com', '2131232131', 'Nữ', 0, 1, ''),
(26, 'vinhabc123', '202cb962ac59075b964b07152d234b70', 'ádasds', '2000-07-17', 'vinhabc123@gmail.com', '012323232', 'Nam', 0, 0, '091905350d530678424e37ddcc300290'),
(27, 'vinhabc124', '202cb962ac59075b964b07152d234b70', 'vinh124', '2000-07-17', 'hodacvinh2000@gmail.com', '0124124124', 'Nam', 0, 1, '83e694bc40efc1c5931c08cd3eef596a'),
(29, 'vinh09', '202cb962ac59075b964b07152d234b70', 'vinh09', '2000-07-17', 'vinh09@gmail.com', '090909090', 'Nam', 0, 0, ''),
(30, 'vinhtest1', '202cb962ac59075b964b07152d234b70', 'Hồ Đắc Vinh', '2000-07-07', 'vinhtest@gmail.com', '12312312312', 'Nam', 0, 0, '');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `anh`
--
ALTER TABLE `anh`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E43D746BF243CE1` (`tkgame_id`);

--
-- Chỉ mục cho bảng `binhluannhiemvu`
--
ALTER TABLE `binhluannhiemvu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BF8D8D9BDA2CE2BC` (`nhiemvu_id`),
  ADD KEY `IDX_BF8D8D9B9D44672D` (`nguoibinhluan_id`),
  ADD KEY `IDX_BF8D8D9B155B90CF` (`phanhoi_id`);

--
-- Chỉ mục cho bảng `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Chỉ mục cho bảng `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nhiemvu`
--
ALTER TABLE `nhiemvu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_239D744CA76ED395` (`user_id`),
  ADD KEY `IDX_239D744CE48FD905` (`game_id`);

--
-- Chỉ mục cho bảng `taikhoangame`
--
ALTER TABLE `taikhoangame`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A732799EE48FD905` (`game_id`);

--
-- Chỉ mục cho bảng `thegame`
--
ALTER TABLE `thegame`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3251B0DCE48FD905` (`game_id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D6495741188A` (`tendangnhap`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_8D93D6494DBC47F5` (`sodt`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `anh`
--
ALTER TABLE `anh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `binhluannhiemvu`
--
ALTER TABLE `binhluannhiemvu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT cho bảng `game`
--
ALTER TABLE `game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `nhiemvu`
--
ALTER TABLE `nhiemvu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `taikhoangame`
--
ALTER TABLE `taikhoangame`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `thegame`
--
ALTER TABLE `thegame`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `anh`
--
ALTER TABLE `anh`
  ADD CONSTRAINT `FK_E43D746BF243CE1` FOREIGN KEY (`tkgame_id`) REFERENCES `taikhoangame` (`id`);

--
-- Các ràng buộc cho bảng `binhluannhiemvu`
--
ALTER TABLE `binhluannhiemvu`
  ADD CONSTRAINT `FK_BF8D8D9B155B90CF` FOREIGN KEY (`phanhoi_id`) REFERENCES `binhluannhiemvu` (`id`),
  ADD CONSTRAINT `FK_BF8D8D9B9D44672D` FOREIGN KEY (`nguoibinhluan_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_BF8D8D9BDA2CE2BC` FOREIGN KEY (`nhiemvu_id`) REFERENCES `nhiemvu` (`id`);

--
-- Các ràng buộc cho bảng `nhiemvu`
--
ALTER TABLE `nhiemvu`
  ADD CONSTRAINT `FK_239D744CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_239D744CE48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`);

--
-- Các ràng buộc cho bảng `taikhoangame`
--
ALTER TABLE `taikhoangame`
  ADD CONSTRAINT `FK_A732799EE48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`);

--
-- Các ràng buộc cho bảng `thegame`
--
ALTER TABLE `thegame`
  ADD CONSTRAINT `FK_3251B0DCE48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
