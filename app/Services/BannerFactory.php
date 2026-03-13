<?php
namespace App\Services;

class BannerFactory
{
    # 메뉴탑 오른쪽 배너
    static public function renew_menu_right(array $bannerData, $class_replace = '')
    {
        if ($bannerData['layout_attribute'] == '1') {
            return self::renew_menu_right_attribute_1($bannerData, $class_replace);
        }

        if ($bannerData['layout_attribute'] == '2') {
            return self::renew_menu_right_attribute_2($bannerData, $class_replace);
        }
        
        if ($bannerData['layout_attribute'] == '3') {
            return self::renew_menu_right_attribute_3($bannerData, $class_replace);
        }
    }

    static function renew_menu_right_attribute_1(array $bannerData, $class_replace = '')
    {
        $target = $classes = $images = $links = $div = $styles = '';

        $imgGroup = '<div class="top">';
        foreach ($bannerData['classes'] as $key => $row) {
            if ($class_replace !== '') {
                if (strpos($class_replace, ',') !== false) {
                    $str = explode(',', $class_replace);
                    foreach ($str as $str_row) {
                        $row = trim(str_replace($str_row, '', $row));
                    }
                    $classes = trim($row);
                    
                } else {
                    $classes 	= trim(str_replace($class_replace, '', $row));    
                }
                
            } else {
                $classes 	= $row;
            }
            
            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';

            if ($images != '') 
                $img = '<img src="'.asset('images'.$images).'">';

            if ($links !== '') $img .= '<a href="'.$links.'" target="'.$target.'">'.$img.'</a>';

            if ($styles == 'undefined') 
                $styles = '';

            if ($classes == 'top') {
                // $imgGroup .= '<a href="'.$links.'" target="'.$target.'">'.$img.'</a>';
                $imgGroup .= $img;
            } else {
                $div .= '<div class="'.$classes.'" style="'.$styles.'" >'.$img.'</div>';
            }
        }

        $imgGroup .= '</div>';

        $str = $imgGroup. $div;

        return $str;
    }


    static function renew_menu_right_attribute_2(array $bannerData, $class_replace = '')
    {
        $target = $images = $links = '';

        $div = '';

        foreach ($bannerData['classes'] as $key => $row) {

            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';


            if ($key == 0 || $key == 1) {
                if ($links != '') 
                    $div .= '<div class="grid-item one" ><a herf="'.$links.'" target="'.$target.'"><img src="'.asset('images'.$images).'"></a></div>';
                else 
                    $div .= '<div class="grid-item one" ><img src="'.asset('images'.$images).'"></div>';
            }

            if ($key == 2 || $key == 3) {
                if ($links != '') 
                    $div .= '<div class="grid-item one" ><a herf="'.$links.'" target="'.$target.'"><img src="'.asset('images'.$images).'"></a></div>';
                else 
                    $div .= '<div class="grid-item one" ><img src="'.asset('images'.$images).'"></div>';
            }
        }

        $str = $div;

        return $str;
    }


    static function renew_menu_right_attribute_3(array $bannerData, $class_replace = '')
    {
        $target = $images = $links = '';

        $div = '';

        foreach ($bannerData['classes'] as $key => $row) {

            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';


            if ($key == 0 || $key == 1) {
                if ($links != '') 
                    $div .= '<div class="grid-item two" ><a herf="'.$links.'" target="'.$target.'"><img src="'.asset('images'.$images).'"></a></div>';
                else 
                    $div .= '<div class="grid-item two" ><img src="'.asset('images'.$images).'"></div>';
            }

            if ($key == 2 || $key == 3) {
                if ($links != '') 
                    $div .= '<div class="grid-item two" ><a herf="'.$links.'" target="'.$target.'"><img src="'.asset('images'.$images).'"></a></div>';
                else 
                    $div .= '<div class="grid-item two" ><img src="'.asset('images'.$images).'"></div>';
            }
        }

        $str = $div;

        return $str;
    }


    public static function renew_menu_event(array $bannerData, $class_replace = '')
    {
        $target = $classes = $images = $links = $div = $styles = '';

        $imgGroup = '';
        foreach ($bannerData['classes'] as $key => $row) {
            if ($class_replace !== '') {
                if (strpos($class_replace, ',') !== false) {
                    $str = explode(',', $class_replace);
                    foreach ($str as $str_row) {
                        $row = trim(str_replace($str_row, '', $row));
                    }
                    $classes = trim($row);
                    
                } else {
                    $classes 	= trim(str_replace($class_replace, '', $row));    
                }
                
            } else {
                $classes 	= $row;
            }
            
            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';
            
            if ($images != '') 
                $img = '<img src="'.asset('images'.$images).'">';

            if ($styles == 'undefined') 
                $styles = '';

            if (!$links) {
                $links = '#';
                $href = '';
            } else {
                $href = 'href="'.$links.'"';
            }

            $imgGroup .= '<a '.$href.' target="'.$target.'">'.$img.'</a>';
        }


        $str = $imgGroup;

        return $str;
    }


    public static function renew_menu_new(array $bannerData, $class_replace = '')
    {
        $target = $classes = $images = $links = $div = $styles = '';

        $imgGroup = '';
        foreach ($bannerData['classes'] as $key => $row) {
            if ($class_replace !== '') {
                if (strpos($class_replace, ',') !== false) {
                    $str = explode(',', $class_replace);
                    foreach ($str as $str_row) {
                        $row = trim(str_replace($str_row, '', $row));
                    }
                    $classes = trim($row);
                    
                } else {
                    $classes 	= trim(str_replace($class_replace, '', $row));    
                }
                
            } else {
                $classes 	= $row;
            }
            
            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';
            
            if ($images != '') 
                $img = '<img src="'.asset('images'.$images).'">';

            if ($styles == 'undefined') 
                $styles = '';

            if (!$links) {
                $links = '#';
                $href = '';
            } else {
                $href = 'href="'.$links.'"';
            }

            $imgGroup .= '<a '.$href.' target="'.$target.'">'.$img.'</a>';
        }


        $str = $imgGroup;

        return $str;
    }


    public static function renew_menu_best(array $bannerData, $class_replace = '')
    {
        $target = $classes = $images = $links = $div = $styles = '';

        $imgGroup = '';
        foreach ($bannerData['classes'] as $key => $row) {
            if ($class_replace !== '') {
                if (strpos($class_replace, ',') !== false) {
                    $str = explode(',', $class_replace);
                    foreach ($str as $str_row) {
                        $row = trim(str_replace($str_row, '', $row));
                    }
                    $classes = trim($row);
                    
                } else {
                    $classes 	= trim(str_replace($class_replace, '', $row));    
                }
                
            } else {
                $classes 	= $row;
            }
            
            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';
            
            if ($images != '') 
                $img = '<img src="'.asset('images'.$images).'">';

            if ($styles == 'undefined') 
                $styles = '';

            if (!$links) {
                $links = '#';
                $href = '';
            } else {
                $href = 'href="'.$links.'"';
            }

            $imgGroup .= '<a '.$href.' target="'.$target.'">'.$img.'</a>';
        }


        $str = $imgGroup;

        return $str;
    }


    public static function renew_menu_low_temp(array $bannerData, $class_replace = '')
    {
        $target = $classes = $images = $links = $div = $styles = '';

        $imgGroup = '';
        foreach ($bannerData['classes'] as $key => $row) {
            if ($class_replace !== '') {
                if (strpos($class_replace, ',') !== false) {
                    $str = explode(',', $class_replace);
                    foreach ($str as $str_row) {
                        $row = trim(str_replace($str_row, '', $row));
                    }
                    $classes = trim($row);
                    
                } else {
                    $classes 	= trim(str_replace($class_replace, '', $row));    
                }
                
            } else {
                $classes 	= $row;
            }
            
            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';
            
            if ($images != '') 
                $img = '<img src="'.asset('images'.$images).'">';

            if ($styles == 'undefined') 
                $styles = '';

            if (!$links) {
                $links = '#';
                $href = '';
            } else {
                $href = 'href="'.$links.'"';
            }

            $imgGroup .= '<a '.$href.' target="'.$target.'">'.$img.'</a>';
        }


        $str = $imgGroup;

        return $str;
    }


    public static function renew_menu_vivacook(array $bannerData, $class_replace = '')
    {
        $target = $classes = $images = $links = $div = $styles = '';

        $imgGroup = '';
        foreach ($bannerData['classes'] as $key => $row) {
            if ($class_replace !== '') {
                if (strpos($class_replace, ',') !== false) {
                    $str = explode(',', $class_replace);
                    foreach ($str as $str_row) {
                        $row = trim(str_replace($str_row, '', $row));
                    }
                    $classes = trim($row);
                    
                } else {
                    $classes 	= trim(str_replace($class_replace, '', $row));    
                }
                
            } else {
                $classes 	= $row;
            }
            
            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';
            
            if ($images != '') 
                $img = '<img src="'.asset('images'.$images).'">';

            if ($styles == 'undefined') 
                $styles = '';

            if (!$links) {
                $links = '#';
                $href = '';
            } else {
                $href = 'href="'.$links.'"';
            }

            $imgGroup .= '<a '.$href.' target="'.$target.'">'.$img.'</a>';
        }


        $str = $imgGroup;

        return $str;
    }


    public static function renew_menu_mygrang(array $bannerData, $class_replace = '')
    {
        $target = $classes = $images = $links = $div = $styles = '';

        $imgGroup = '';
        foreach ($bannerData['classes'] as $key => $row) {
            if ($class_replace !== '') {
                if (strpos($class_replace, ',') !== false) {
                    $str = explode(',', $class_replace);
                    foreach ($str as $str_row) {
                        $row = trim(str_replace($str_row, '', $row));
                    }
                    $classes = trim($row);
                    
                } else {
                    $classes 	= trim(str_replace($class_replace, '', $row));    
                }
                
            } else {
                $classes 	= $row;
            }
            
            $target 	= (array_key_exists($key, $bannerData['target'])) ? $bannerData['target'][$key] : '';
            $images     = (array_key_exists($key, $bannerData['images'])) ? $bannerData['images'][$key] : '';
            $links     	= (array_key_exists($key, $bannerData['urlLink'])) ? $bannerData['urlLink'][$key] : '';
            $styles     = (array_key_exists($key, $bannerData['styles'])) ? $bannerData['styles'][$key] : '';
            
            if ($images != '') 
                $img = '<img src="'.asset('images'.$images).'">';

            if ($styles == 'undefined') 
                $styles = '';

            if (!$links) {
                $links = '#';
                $href = '';
            } else {
                $href = 'href="'.$links.'"';
            }

            $imgGroup .= '<a '.$href.' target="'.$target.'">'.$img.'</a>';
        }


        $str = $imgGroup;

        return $str;
    }

}