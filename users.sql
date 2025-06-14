/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 100017
 Source Host           : localhost:3306
 Source Schema         : laravel_12_db

 Target Server Type    : MySQL
 Target Server Version : 100017
 File Encoding         : 65001

 Date: 03/06/2025 14:26:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp(0) NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `department_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email`) USING BTREE,
  INDEX `users_department_id_foreign`(`department_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'สุทธา ประสงค์ทรัพย์', 'noi.0014@gmail.com', NULL, '$2y$12$NDLJuPVV07PedllcXgvgtO3oIhD3/sOMMotvEsqhNjKSl2jiInvD6', 'zhPUqBjx7Ki3PuUD28JvShb8VOjeqKFZ9E7ptm8KWADRZRUQnRRU6L9StXEn', '2025-04-15 14:20:51', '2025-04-15 14:20:51', 'admin', 1);
INSERT INTO `users` VALUES (2, 'Admin User', 'admin@example.com', NULL, '$2y$12$DpvZY5UUXV6he5HAgnn6FO.Runw/bgDSHbsZDIN8WGj3xvcnYXkUe', NULL, '2025-04-25 09:04:39', '2025-04-25 09:04:39', 'admin', 1);
INSERT INTO `users` VALUES (3, 'ธรรมดา หนึ่ง', 'user1@example.com', NULL, '$2y$12$/s69i7EQtrqxtAjmZVkmWOI5n6vNrsf6lhuTwtqCNxtUPT3tyPzOq', NULL, '2025-04-25 09:04:40', '2025-04-25 09:04:40', 'admin', 2);

SET FOREIGN_KEY_CHECKS = 1;
