<?php

class states extends AOREducationObject {

    public static function getRegionName($state)
    {
        $sql = <<<EOT
            SELECT u.name
            FROM us_regions u
            JOIN states s ON s.usRegionId = u.id
            WHERE abbr = :code
EOT;
      $params = [[':code', $state, PDO::PARAM_STR]];
        $db = new EduDB;
        return $db->queryItemSafe($sql, $params);
    }

}