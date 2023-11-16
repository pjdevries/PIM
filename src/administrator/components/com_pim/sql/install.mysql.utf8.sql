CREATE TABLE IF NOT EXISTS `#__pim_items`
(
    `id`               int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`            varchar(255)     NOT NULL,
    `alias`            varchar(400)     NOT NULL,
    `files`            TEXT,
    `asset_id`         INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `state`            TINYINT(1)       NULL     DEFAULT 1,
    `ordering`         INT(11)          NULL     DEFAULT 0,
    `checked_out`      INT(11) UNSIGNED,
    `checked_out_time` DATETIME         NULL     DEFAULT NULL,
    `created_by`       INT(11)          NULL     DEFAULT 0,
    `modified_by`      INT(11)          NULL     DEFAULT 0,
    PRIMARY KEY (`id`)
) DEFAULT COLLATE = utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`,
                                `content_history_options`)
SELECT *
FROM (SELECT 'Item',
             'com_pim.item',
             '{"special":{"dbtable":"#__pim_items","key":"id","type":"ItemTable","prefix":"Joomla\\\\Component\\\\Pi\\\\Administrator\\\\Table\\\\"}}',
             CASE
                 WHEN 'rules' is null THEN ''
                 ELSE ''
                 END as rules,
             CASE
                 WHEN 'field_mappings' is null THEN ''
                 ELSE ''
                 END as field_mappings,
             '{"formFile":"administrator\/components\/com_pim\/forms\/item.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_pim.item'))
LIMIT 1;
