/*
 Navicat Premium Data Transfer

 Source Server         : Local
 Source Server Type    : MySQL
 Source Server Version : 100424 (10.4.24-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : zumart_server

 Target Server Type    : MySQL
 Target Server Version : 100424 (10.4.24-MariaDB)
 File Encoding         : 65001

 Date: 27/05/2023 12:53:10
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ec_reviews
-- ----------------------------
DROP TABLE IF EXISTS `ec_reviews`;
CREATE TABLE `ec_reviews`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NULL DEFAULT NULL,
  `parent_id` bigint NULL DEFAULT NULL,
  `star` double(8, 2) NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `is_reply` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ec_reviews_product_id_customer_id_status_created_at_index`(`product_id` ASC, `customer_id` ASC, `status` ASC, `created_at` ASC) USING BTREE,
  INDEX `ec_reviews_product_id_customer_id_status_index`(`product_id` ASC, `customer_id` ASC, `status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ec_reviews
-- ----------------------------
INSERT INTO `ec_reviews` VALUES (1, 2, 24, NULL, 5.00, 'barang sesuai ori', 'published', '2023-05-23 07:28:19', '2023-05-23 07:28:19', NULL, 0);
INSERT INTO `ec_reviews` VALUES (2, 2, 73, NULL, 3.00, '100% ori', 'published', '2023-05-25 06:33:53', '2023-05-25 06:33:53', NULL, 0);
INSERT INTO `ec_reviews` VALUES (3, 7, 73, NULL, 4.00, 'Pelayanan Toppp', 'published', '2023-05-25 06:44:37', '2023-05-26 06:41:13', NULL, 1);
INSERT INTO `ec_reviews` VALUES (5, 3, NULL, 3, 4.00, 'Terima kasih telah berbelanja di BenQ Official Store.😊 Kami senang melayani Anda & semoga kedepannya bisa melayani Anda kembali. Follow Instagram @benqindonesia untuk info update terbaru ya kak 🙏😍', 'published', '2023-05-26 06:41:13', '2023-05-26 06:41:13', NULL, 0);

SET FOREIGN_KEY_CHECKS = 1;
