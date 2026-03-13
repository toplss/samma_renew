<?php
/**
 * Class Name : MallMainServices
 * Description : 관리자페이지 배너관리 서비스
 * Author : Lee Sangseung
 * Created Date : 2026-01-14
 * Version : 1.0
 * 
 * History :
 *   - 2026-01-14 : Initial creation
 */

namespace App\Services;

use App\Models\TbBannerRenew;
use Debugbar;
use App\Services\BannerFactory;

class MallMainServices 
{
    /**
     * 배너호출
     *
     * @param [type] $group_id
     * @param [type] $layout_attribute
     * @return array
     */
    public function getBanner($group_id, $layout_attribute)
    {
        if ($group_id) {
            $banner_info = TbBannerRenew::join('tb_banner_group as tbg', 'tb_banner_renew.group_id', '=', 'tbg.group_id')
            ->where('tb_banner_renew.group_id', $group_id)
            ->where('tbg.group_display', 'Y')
            ->where('tb_banner_renew.layout_active', 'Y')
            ->select('banner_info', 'layout_attribute')
            ->get()[0];

            $result = json_decode($banner_info['banner_info'], true);

            $classNames = [];
            $images     = [];
            $urlLink    = [];
            $target     = [];
            $style      = [];

            foreach ($result as $key => $row) {
                
                if (strpos($key, 'preview') !== false) {
                    $images[] = $row;
                }
                if (strpos($key, 'element-class') !== false) {
                    $classNames[] = $row;
                }
                if (strpos($key, 'element-style') !== false) {
                    $style[] = $row;
                }
                if (strpos($key, 'banner_url') !== false) {
                    $urlLink[] = $row;
                }
                if (strpos($key, 'target') !== false) {
                    $target[] = $row;
                }
            }

            return [
                'classes' => $classNames,
                'images' => $images,
                'urlLink' => $urlLink,
                'target'  => $target,
                'styles'  => $style,
                'layout_attribute' => $banner_info['layout_attribute']
            ];

        } else {
            return [];
        }
    }


    /**
     * Undocumented function
     *
     * @param array $bannerData
     * @param string $class_replace
     * @return mxied
     */
    function makeBannerDiv(array $bannerData, $class_replace = '')
    {
        $target = $classes = $images = $links = $div = $styles = '';
        foreach ($bannerData['classes'] as $key => $row) {
            if ($class_replace !== '') {
                if (strpos($class_replace, ',') !== false) {
                    $str = explode(',', $class_replace);
                    foreach ($str as $str_row) {
                        $row = trim(str_replace($str_row, '', $row));
                    }
                    $classes = trim($row);
                } else {
                    $classes = trim(str_replace($class_replace, '', $row));    
                }

            } else {
                $classes = $row;
            }
            
            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';
            
            if ($images != '')  $img = '<img src="https://samma-erp.com'.$images.'">';
            else $img = '';

            if ($styles == 'undefined') 
                $styles = '';

            if (!$links) $href = '';
            else $href = 'href="'.$links.'"';

            if ($images) {
                
                $div .= '<div class="'.$classes.'" style="'.$styles.'" ><a '.$href.' target="'.$target.'">'.$img.'</a></div>';
            }
        }

        return $div;
    }


    public function getBannerFactory($data, $class, $options) 
    {
        switch ($options) {
            case 'renew_menu_right' : 
                return BannerFactory::{$options}($data, $class);
                break;

            case 'renew_menu_event' :
                return BannerFactory::{$options}($data, $class);
                break;

            case 'renew_menu_new' :
                return BannerFactory::{$options}($data, $class);
                break;

            case 'renew_menu_best' :
                return BannerFactory::{$options}($data, $class);
                break;

            case 'renew_menu_low_temp' :
                return BannerFactory::{$options}($data, $class);
                break;

            case 'renew_menu_vivacook' :
                return BannerFactory::{$options}($data, $class);
                break;

            case 'renew_menu_mygrang' :
                return BannerFactory::{$options}($data, $class);
                break;
        }
    }
}