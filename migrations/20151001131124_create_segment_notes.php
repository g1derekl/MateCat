<?php

use Phinx\Migration\AbstractMigration;

class CreateSegmentNotes extends AbstractMigration {

  public $sql_up = <<<EOF
CREATE TABLE `segment_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_segment` bigint(20) NOT NULL,
  `internal_id` varchar(100) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_segment` (`id_segment`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
EOF;

  public $sql_down = 'DROP TABLE `segment_notes`';

    public function up() {
      $this->execute($this->sql_up);
    }

    public function down() {
      $this->execute($this->sql_down);
    }
}
