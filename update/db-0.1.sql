CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pipe_id` int(11) NOT NULL,
  `pipe_rel_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pipe_id` (`pipe_id`),
  KEY `pipe_rel_id` (`pipe_rel_id`)
);

CREATE TABLE IF NOT EXISTS `organizationRelation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_owner_org_id` int(11) NOT NULL,
  `rel_linked_org_id` int(11) NOT NULL,
  `rel_type` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_owner_org_id` (`rel_owner_org_id`,`rel_linked_org_id`),
  KEY `rel_linked_org_id` (`rel_linked_org_id`)
);