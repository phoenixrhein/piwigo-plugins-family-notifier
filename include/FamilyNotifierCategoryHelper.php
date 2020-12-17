<?php

// Chech whether we are indeed included by Piwigo.
if (defined('PHPWG_ROOT_PATH') === false) {
    die('Hacking attempt!');
}

/**
 * class FamilyNotifierCategoryHelper
 */
class FamilyNotifierCategoryHelper
{

    /**
     *
     * @var []
     */
    private static $albumList = array();

    /**
     * Get category list
     *
     * @return [][]
     */
    public function getCategoryList()
    {
        if (count(self::$albumList) === 0) {
            self::$albumList = $this->buildCategoryList();
        }

        return self::$albumList;
    }

    /**
     * Build category list
     *
     * @return [][]
     */
    private function buildCategoryList()
    {
        $albumSql = "SELECT 
                        main.id as m_id,
                        main.name as m_name, 
                        main.permalink as m_permalink,
                        sub.id as s_id,
                        sub.name as s_name,
                        sub.permalink as s_permalink 
                    FROM `cmmhf_categories` main 
                    LEFT JOIN `cmmhf_categories` sub 
                    ON main.id = sub.id_uppercat where main.id_uppercat is null 
                    ORDER BY main.`rank`, sub.rank ASC;";
        $albums = query2array($albumSql);

        $albumList = [];

        foreach ($albums as $album) {

            $id = $album['m_id'];
            $name = $album['m_name'];
            $permalink = $album['m_permalink'];

            // sub category
            if ($album['s_id'] != null) {
                $id = $album['s_id'];
                $name = $album['s_name'];
                $permalink = $album['s_permalink'];
            }

            $albumList[$id] = array(
                'id' => $id,
                'name' => $name,
                'url' => make_index_url([
                    'category' => [
                        'id' => $id,
                        'name' => $name,
                        'permalink' => $permalink
                    ]
                ])
            );

            $imageSql = "SELECT 
                            img.* 
                         FROM `cmmhf_images` img 
                         JOIN `cmmhf_image_category` 
                         ON id = image_id 
                         WHERE category_id = " . $id . " 
                         ORDER By rank 
                         LIMIT 1";

            $image = query2array($imageSql);

            // set thumbnail image
            if (count($image) == 1) {
                $albumList[$id]['src_image'] = new SrcImage($image[0]);
            }
        }

        return $this->setTimePeriod($albumList);
    }

    /**
     * Calculate time period
     *
     * @param [][]
     */
    private function setTimePeriod(array $albumList)
    {
        $condition = get_sql_condition_FandF([
            'visible_categories' => 'category_id',
            'visible_images' => 'id'
        ], 'AND');

        // calculate period
        $query = '
            SELECT
                category_id,
                MIN(date_creation) AS `from`,
                MAX(date_creation) AS `to`
            FROM ' . IMAGE_CATEGORY_TABLE . '
            INNER JOIN ' . IMAGES_TABLE . '
            ON image_id = id
            WHERE category_id IN (' . implode(',', array_keys($albumList)) . ')
            ' . $condition . '
              GROUP BY category_id;';

        $dates_of_category = query2array($query, 'category_id');

        foreach ($albumList as &$album) {
            if (isset($dates_of_category[$album['id']])) {
                $from = $dates_of_category[$album['id']]['from'];
                $to = $dates_of_category[$album['id']]['to'];

                if (! empty($from)) {
                    $album['date'] = format_fromto($from, $to);
                }
            }
        }
        
        return $albumList;
    }
}