<?php
if (!defined('PHPWG_ROOT_PATH'))
{
    die('Hacking attempt!');
}

//include_once(PERMALINK_GENERATOR_PATH.'include/functions.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

class FamilyNotifierAdmin
{
    
    private static $albumList = array();
    
    private $receivers = array(
        array(
            'email' => 'hohmannb@gmx.de',
            'enable' => true
        ),
        array(
            'email' => 'sina.hohmann@gmx.de',
            'enable' => false
        ),
        array(
            'email' => 'noho123@gmx.de',
            'enable' => false
        ), 
        array(
            'email' => 'heiho123@gmx.de',
            'enable' => false
        ), 
        array(
            'email' => 'janina.wittke@gmx.de',
            'enable' => false
        ), 
        array(
            'email' => 'lea.wittke@gmx.de',
            'enable' => false
        ), 
        array(
            'email' => 'baerbel@gmx.de',
            'enable' => false
        ), 
        array(
            'email' => 'detlef@gmx.de',
            'enable' => false
        ), 
    );
    
    private function getTemplate()
    {
        global $template;
        return $template;
    }
    
    private function sendMail()
    {
        if (isset($_POST['send_notifier']) === false)
        {
            return;
        }
        $errors = array();

        if (array_key_exists('email', $_POST) === false 
            && (array_key_exists('email_custom_checked', $_POST) === true && $_POST['email_custom_checked'] != 1 
                || array_key_exists('email_custom', $_POST) === true && strlen($_POST['email_custom']) === 0)) {
            $errors[] = 'Es wurde kein Empfänger ausgewählt.';
        }
        
        if (array_key_exists('album', $_POST) === false) {
            $errors[] = 'Es wurde kein Album ausgewählt.';
        }
        
        if (count($errors) > 0) {
            $this->getTemplate()->assign('errors', $errors);
            return;
        }
        
        $args = array(
            'subject' => 'Benachrichtigung: Neue Fotos',
            'mail_title' => 'Fotos der Familie (ehemals) HKS',
            'mail_subtitle' => 'Die neuen Fotos sind da!'
        );
        
        $albumList = array();
        
        foreach ($_POST['album'] as $albumId) {
            $albumList[] = self::$albumList[$albumId];
        }
        
        $tpl = array(
            'filename' => 'notifier_mail',
            'dirname' => dirname(__FILE__).DIRECTORY_SEPARATOR.'mail',
            'assign' => array(
                'albums' => $albumList,
                'derivative_params' => trigger_change('get_index_album_derivative_params', ImageStdParams::get_by_type(IMG_THUMB)),
                'url' => 'https://fotos.xovatec.de/',
                'notes' => nl2br($_POST['notes'])
            )
        );
        
        // empfanger richtig setzen
        // protokollieren (Monitor)
        // config datei z.B. fuer Empfaengerliste
        
        if(pwg_mail(array('email' => 'bastianhohmann01@gmail.com'), $args, $tpl) === false) {
            $this->getTemplate()->assign('errer', array('Beim Versand ist ein Fehler aufgetreten.'));
        }
        
        $this->getTemplate()->assign('infos', array('E-Mail wurde erfolgreich versendet.'));
    }
    
    private function initTemplateView()
    {
        
        $this->getTemplate()->set_filenames(
            array(
                'plugin_admin_content' => dirname(__FILE__).'/admin.tpl',
            )
        );
        
        $this->getTemplate()->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
    }
    
    private function buildCategoryList()
    {

        $this->getTemplate()->assign(array(
            'albums' => $this->getCategoryList(),
            'receivers' => $this->receivers,
            'derivative_params' => trigger_change('get_index_album_derivative_params', ImageStdParams::get_by_type(IMG_THUMB))
        ));
    }
    
    private function getCategoryList()
    {
        if (count(self::$albumList) > 0) {
            return self::$albumList;
        }
        
        $albumSql = "SELECT main.id as m_id, main.name as m_name, main.permalink as m_permalink, sub.id as s_id, sub.name as s_name, sub.permalink as s_permalink FROM `cmmhf_categories` main left join `cmmhf_categories` sub on main.id = sub.id_uppercat where main.id_uppercat is null ORDER BY main.`rank`, sub.rank ASC;";
        $albums = query2array($albumSql);
        
        $albumList = array();
        
        foreach ($albums as $album) {
            
            $id = $album['m_id'];
            $name = $album['m_name'];
            $permalink = $album['m_permalink'];
            
            if ($album['s_id'] != null) {
                $id = $album['s_id'];
                $name = $album['s_name'];
                $permalink = $album['s_permalink'];
            }
            
            $imageSql = "SELECT img.* FROM `cmmhf_images` img join `cmmhf_image_category` on id = image_id where category_id = " . $id . " order by rank Limit 1";
            $image = query2array($imageSql);
            
            
            $albumList[$id] = array(
                'id' => $id,
                'name' => $name,
                'url' => make_index_url(
                    array(
                        'category' => array('id' => $id, 'name' => $name, 'permalink' => $permalink)
                    )
                )
            );
            
            if (count($image) == 1) {
                $albumList[$id]['src_image'] = new SrcImage($image[0]);
            }
        }
        
        $query = '
            SELECT
                category_id,
                MIN(date_creation) AS `from`,
                MAX(date_creation) AS `to`
            FROM '.IMAGE_CATEGORY_TABLE.'
            INNER JOIN '.IMAGES_TABLE.' ON image_id = id
            WHERE category_id IN ('.implode(',', array_keys($albumList)).')
            '.get_sql_condition_FandF
            (
                array
                (
                    'visible_categories' => 'category_id',
                    'visible_images' => 'id'
                ),
                'AND'
                ).'
              GROUP BY category_id
            ;';
        
        $dates_of_category = query2array($query, 'category_id');
    
        foreach ($albumList as &$album) {
            if (isset($dates_of_category[ $album['id'] ]))
            {
                $from = $dates_of_category[ $album['id'] ]['from'];
                $to   = $dates_of_category[ $album['id'] ]['to'];
                
                if (!empty($from))
                {
                    $album['date'] = format_fromto($from, $to);
                }
            }
        }
        
        self::$albumList = $albumList;
        
        return $albumList;
    }
    
    public function run()
    {
        
        $this->buildCategoryList();
        
        $this->sendMail();
        
        $this->initTemplateView();
        
    }
}

$app = new FamilyNotifierAdmin();
$app->run();