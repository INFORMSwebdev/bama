USE analytic_education;

ALTER TABLE institutions
    ADD COLUMN `Country` VARCHAR(3) NULL DEFAULT 'USA' COMMENT 'ISO-alpha3 country code' AFTER `RegionId`;