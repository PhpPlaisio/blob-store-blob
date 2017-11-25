/*================================================================================*/
/* DDL SCRIPT                                                                     */
/*================================================================================*/
/*  Title    : ABC-Framework: BLOB Store using BLOBs                              */
/*  FileName : abc-blob-store-blob.ecm                                            */
/*  Platform : MySQL 5.6                                                          */
/*  Version  :                                                                    */
/*  Date     : zaterdag 25 november 2017                                          */
/*================================================================================*/
/*================================================================================*/
/* CREATE TABLES                                                                  */
/*================================================================================*/

CREATE TABLE `ABC_BLOB_DATA` (
  `bdt_id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
  `cmp_id` SMALLINT UNSIGNED NOT NULL,
  `bdt_md5` BINARY(16) NOT NULL,
  `bdt_data` LONGBLOB NOT NULL,
  CONSTRAINT `PRIMARY_KEY` PRIMARY KEY (`bdt_id`)
)
PARTITION BY RANGE (bdt_id)
(
  PARTITION `p00000` VALUES LESS THAN MAXVALUE
);

/*
COMMENT ON COLUMN `ABC_BLOB_DATA`.`bdt_md5`
The MD5 hash of the BLOB.
*/

/*
COMMENT ON COLUMN `ABC_BLOB_DATA`.`bdt_data`
The actual BLOB.
*/

CREATE TABLE `ABC_BLOB` (
  `blb_id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
  `cmp_id` SMALLINT UNSIGNED NOT NULL,
  `bdt_id` INT UNSIGNED NOT NULL,
  `blb_size` INT UNSIGNED NOT NULL,
  `blb_md5` BINARY(16) NOT NULL,
  `blb_filename` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `blb_mime_type` VARCHAR(48) NOT NULL,
  `blb_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT `PRIMARY_KEY` PRIMARY KEY (`blb_id`)
);

/*
COMMENT ON COLUMN `ABC_BLOB`.`blb_size`
The size in bytes of the BLOB.
*/

/*
COMMENT ON COLUMN `ABC_BLOB`.`blb_md5`
The MD5 hash of the BLOB.
*/

/*
COMMENT ON COLUMN `ABC_BLOB`.`blb_filename`
The name of the BLOB.
*/

/*
COMMENT ON COLUMN `ABC_BLOB`.`blb_mime_type`
The MIME type of the BLOB.
*/

/*
COMMENT ON COLUMN `ABC_BLOB`.`blb_timestamp`
The timestamp when the BLOB was inserted or when known the last modification time of the BLOB.
*/

/*================================================================================*/
/* CREATE INDEXES                                                                 */
/*================================================================================*/

CREATE INDEX `bdt_md5` ON `ABC_BLOB_DATA` (`bdt_md5`);

CREATE INDEX `IX_FK_ABC_BLOB_DATA` ON `ABC_BLOB_DATA` (`cmp_id`);

CREATE INDEX `IX_ABC_BLOB1` ON `ABC_BLOB` (`blb_md5`);

CREATE INDEX `IX_FK_ABC_BLOB` ON `ABC_BLOB` (`cmp_id`);

CREATE INDEX `IX_FK_ABC_BLOB1` ON `ABC_BLOB` (`bdt_id`);

/*================================================================================*/
/* CREATE FOREIGN KEYS                                                            */
/*================================================================================*/

ALTER TABLE `ABC_BLOB`
  ADD CONSTRAINT `FK_ABC_BLOB_AUT_COMPANY`
  FOREIGN KEY (`cmp_id`) REFERENCES `ABC_AUTH_COMPANY` (`cmp_id`);
