<?php

namespace App\Models;

use App\Services\MallMainServices;
use App\Services\MallShopService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Debugbar;
use Illuminate\Support\Facades\DB;

class ShopItem extends Model
{
    //
    protected $table = 'g5_shop_item';

    public $timestamps = false;


    public static function getShopItems($request)
    {
        $ca_id = $request->input('ca_id');

        $it_price = 'it_price';
        $chain_ca_id2 = null;
        if (session()->has('ss_mb_code')) {
            $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
            $it_price = $member['field_it_price'];                
            $chain_ca_id2 = (empty($member['chain_ca_id2']) || $member['chain_ca_id2'] == '1001') ? null : $member['chain_ca_id2'];

            // 사원체크 
            if (isset($member['mb_level_type'])) {
                if (trim($member['mb_level_type']) == '2' && trim($member['mb_gubun_type']) == 'employee') {
                    $request->merge(['member_type' => 'emp']);
                }
            }
            
            // 구매등급 추가
            $request->merge(['mb_buy' => $member['mb_buy']]);
        }

        if ($request->has('category')) {
            $key   = "shop:items:{$ca_id}:category:".$request->input('category').':desc.'.$request->desc.':chain_ca_id2:'.$chain_ca_id2.':mb_buy:'.$request->input('mb_buy');
            $key  .= ":member_type:".$request->input('member_type');

            $lock  = "lock:shop:items:{$ca_id}:category:".$request->input('category').':desc.'.$request->desc.':chain_ca_id2:'.$chain_ca_id2.':mb_buy:'.$request->input('mb_buy');
            $lock  .= ":member_type:".$request->input('member_type');
        } else {
            $key   = "shop:items:{$ca_id}".':desc.'.$request->desc.':chain_ca_id2:'.$chain_ca_id2.':mb_buy:'.$request->input('mb_buy');
            $key  .= ":member_type:".$request->input('member_type');

            $lock  = "lock:shop:items:{$ca_id}".':desc.'.$request->desc.':chain_ca_id2:'.$chain_ca_id2.':mb_buy:'.$request->input('mb_buy');
            $lock  .= ":member_type:".$request->input('member_type');
        }

        $strlen = strlen($ca_id);

        # 캐시 조회
        if ($data = Cache::get($key)) {
            return $data;
        }

        return Cache::lock($lock, 5)->block(3, function () use ($key, $ca_id, $strlen, $request, $it_price, $chain_ca_id2) { // 5락 유지시간, 3락 대기시간

            # 캐시 조회
            if ($data = Cache::get($key)) {
                return $data;
            }

            $except = [
                'vivacook', 'mygrang', 'hit', 'recom', 'new', 'best', 'sale', 'free', 'auction', 'etc', 'event'
            ];

            # 비바쿡과 마이그랑 메뉴만 it_type_order asc / 나머지 메뉴는 it_order asc 정렬 적용을 위한 코드 추가
            $sort_column = in_array($ca_id, ['vivacook', 'mygrang']) ? 'it_type_order' : 'it_order';

            # DB 조회 (한 명만)
            $data = ShopItem::where(function ($query) use ($request) {
                    // 회원이 전용
                    if ($request->input('member_type') || $request->input('mb_buy')) {
                       
                        // 로그인 유저가 관리자 (직원) 인 경우 관리자 전용상품 조회 가능
                        if ($request->input('member_type') == 'emp') {
                            $query->where('it_use', '1')->orWhere('it_only_admin', '1');
                        } else {
                        // 로그인 유저가 관리자 (직원) 가 아닌 경우 관리자 전용상품 조회 불가
                            $query->where('it_use', '1')->where('it_only_admin', '!=', '1');
                        }
                    } 
                    // 비회원 전용
                    else {
                        $query->where('it_use', '1')->where('it_only_admin', '!=', '1');
                    }
                })
                ->when($request->input('mb_buy'), function($query) use ($request) {
                    // 구매등급 추가
                    $query->where('it_member_group', 'LIKE', '%'.$request->mb_buy.'%');
                })
                ->when($strlen == '2' && !in_array($ca_id, $except), function($query) use($ca_id, $chain_ca_id2){
                    $query->where(function($query) use($ca_id) {
                        $query->where('ca_id', $ca_id)
                              ->orWhere('it_multi_cate_code', 'LIKE', '%'.$ca_id.'%');
                    })
                    ->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                              ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                   ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                   ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })
                ->when($strlen == '4' && !in_array($ca_id, $except), function($query) use($ca_id, $chain_ca_id2){
                    $query->where(function($query) use($ca_id) {
                        $query->where('ca_id2', $ca_id)
                        ->orWhere('it_multi_cate_code', 'LIKE', '%'.$ca_id.'%');
                    })
                    ->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                              ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                   ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                   ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })
                ->when($ca_id == 'vivacook' ,function($query) use($request, $chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type8', '1');

                    if ($request->input('category') && strlen($request->input('category')) == 2) {
                        $query->where('ca_id', $request->input('category'));
                    }
                    if ($request->input('category') && strlen($request->input('category')) == 4) {
                        $query->where('ca_id2', $request->input('category'));
                    }

                    $query->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                              ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                   ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                   ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })
                ->when($ca_id == 'mygrang' ,function($query) use($request, $chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type9', '1');

                    if ($request->input('category') && strlen($request->input('category')) == 2) {
                        $query->where('ca_id', $request->input('category'));
                    }
                    if ($request->input('category') && strlen($request->input('category')) == 4) {
                        $query->where('ca_id2', $request->input('category'));
                    }

                    $query->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                              ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                   ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                   ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })
                ->when($ca_id == 'hit' ,function($query, $chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type1', '1')
                        ->where(function($q) use ($chain_ca_id2) {
                            if (!$chain_ca_id2) {
                                $q->where('it_gubun2', '1')
                                ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                            } else {
                                $q->where(function($qq) use ($chain_ca_id2) {
                                    $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', '');
                                });
                            }
                        });
                })
                ->when($ca_id == 'recom' ,function($query, $chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type2', '1')
                        ->where(function($q) use ($chain_ca_id2) {
                            if (!$chain_ca_id2) {
                                $q->where('it_gubun2', '1')
                                ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                            } else {
                                $q->where(function($qq) use ($chain_ca_id2) {
                                    $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', '');
                                });
                            }
                        });
                })
                ->when($ca_id == 'new' ,function($query, $chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type3', '1')
                        ->where(function($q) use ($chain_ca_id2) {
                            if (!$chain_ca_id2) {
                                $q->where('it_gubun2', '1')
                                ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                            } else {
                                $q->where(function($qq) use ($chain_ca_id2) {
                                    $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', '');
                                });
                            }
                        });
                })
                ->when($ca_id == 'best' ,function($query, $chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type4', '1')
                        ->where(function($q) use ($chain_ca_id2) {
                            if (!$chain_ca_id2) {
                                $q->where('it_gubun2', '1')
                                ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                            } else {
                                $q->where(function($qq) use ($chain_ca_id2) {
                                    $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', '');
                                });
                            }
                        });
                })
                ->when($ca_id == 'sale' ,function($query, $chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type5', '1')
                        ->where(function($q) use ($chain_ca_id2) {
                            if (!$chain_ca_id2) {
                                $q->where('it_gubun2', '1')
                                ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                            } else {
                                $q->where(function($qq) use ($chain_ca_id2) {
                                    $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', '');
                                });
                            }
                        });
                })
                ->when($ca_id == 'event' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')
                    ->where('it_type5', '1')
                    ->where('it_display_use', '1')
                    ->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                            ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })
                ->when($ca_id == 'free' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type6', '1')
                        ->where(function($q) use ($chain_ca_id2) {
                            if (!$chain_ca_id2) {
                                $q->where('it_gubun2', '1')
                                ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                            } else {
                                $q->where(function($qq) use ($chain_ca_id2) {
                                    $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', '');
                                });
                            }
                        });
                })
                ->when($ca_id == 'auction' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type7', '1')
                        ->where(function($q) use ($chain_ca_id2) {
                            if (!$chain_ca_id2) {
                                $q->where('it_gubun2', '1')
                                ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                            } else {
                                $q->where(function($qq) use ($chain_ca_id2) {
                                    $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', '');
                                });
                            }
                        });
                })
                ->when($ca_id == 'etc' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type10', '1')
                        ->where(function($q) use ($chain_ca_id2) {
                            if (!$chain_ca_id2) {
                                $q->where('it_gubun2', '1')
                                ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                            } else {
                                $q->where(function($qq) use ($chain_ca_id2) {
                                    $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                    ->orWhere('it_affiliate_code', '');
                                });
                            }
                        });
                })
                
                //메인 슬라이드 - 초특가상품
                ->when($ca_id == 'MainSlideSuperSale' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type1', '1')->where('it_display_use', '1')
                    ->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                            ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })

                //메인 슬라이드 - 이번주 추천상품(신상품)
                ->when($ca_id == 'MainSlideNew' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type3', '1')->where('it_display_use', '1')
                    ->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                            ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })

                //메인 슬라이드 - 지난주 베스트
                ->when($ca_id == 'MainSlideBest' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type4', '1')->where('it_display_use', '1')
                    ->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                            ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })

                //메인 슬라이드 - 비바쿡 할인상품
                ->when($ca_id == 'MainSlideVivacook' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type8', '1')->where('it_display_use', '1')
                    ->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                            ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })

                //메인 슬라이드 - 마이그랑 할인상품
                ->when($ca_id == 'MainSlideMygrang' ,function($query) use ($chain_ca_id2) {
                    $query->where('ca_id', '!=', '10')->where('it_type9', '1')->where('it_display_use', '1')
                    ->where(function($q) use ($chain_ca_id2) {
                        if (!$chain_ca_id2) {
                            $q->where('it_gubun2', '1')
                            ->orWhere('it_multi_other_chain_cate_code', 'LIKE', '%1001%');
                        } else {
                            $q->where(function($qq) use ($chain_ca_id2) {
                                $qq->where('it_multi_other_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', 'LIKE', '%'.$chain_ca_id2.'%')
                                ->orWhere('it_affiliate_code', '');
                            });
                        }
                    });
                })

                //메인 슬라이드 - 출시예정 상품
                ->when($ca_id == 'MainSlideComming' ,function($query) {
                    $query->where('ca_id', 'i0')
                    ->where('it_type10', '1')
                    ->where('it_display_use', '1');
                })
                
                ->when($request->filled('desc'), function($query) use($request, $it_price) {
                    if ($request->desc == '2' && $it_price) {
                        $query->orderByRaw("g5_shop_item.$it_price * g5_shop_item.it_buy_min_qty ASC");
                    }
                    if ($request->desc == '3' && $it_price) {
                        $query->orderByRaw("g5_shop_item.$it_price * g5_shop_item.it_buy_min_qty DESC");
                    }
                    if ($request->desc == '5') {
                        $query->orderBy('g5_shop_item.it_insert_time', 'DESC');
                    }
                }, function($query) use ($sort_column) {
                    $query->orderBy('it_multi_cate_code', 'asc')
                    ->orderByRaw("{$sort_column} = 0 ASC")
                    ->orderBy($sort_column, 'asc')
                    ->orderBy('it_time', 'desc')
                    ->orderBy('idx', 'desc');
                })
                
                ->pluck('idx')
                ->toArray();

            Cache::put($key, $data, 600); // 10분 마다 갱신

            return $data;
        });
    }

    

    /**
     * 장비관련 상품목록
     *
     * @param [type] $request
     * @return mixed
     */
    public static function getShopItemEquips($request)
    {
        if (session()->has('ss_mb_code')) {
            $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
            // 사원체크 
            if (isset($member['mb_level_type'])) {
                if (trim($member['mb_level_type']) == '2' && trim($member['mb_gubun_type']) == 'employee') {
                    $request->merge(['member_type' => 'emp']);
                }
            }
            
            // 구매등급 추가
            $request->merge(['mb_buy' => $member['mb_buy']]);
        }

        $ca_id = $request->input('ca_id');
        $key   = "shop:items:{$ca_id}:mb_buy:".$request->input('mb_buy').':member_type:'.$request->input('member_type');
        $lock  = "lock:shop:items:{$ca_id}:mb_buy:".$request->input('mb_buy').':member_type:'.$request->input('member_type');

        # 캐시 조회
        if ($data = Cache::get($key)) {
            return $data;
        }

        
        return Cache::lock($lock, 5)->block(3, function () use ($key, $ca_id, $request) { // 5락 유지시간, 3락 대기시간
            # 캐시 조회
            if ($data = Cache::get($key)) {
                return $data;
            }

            # DB 조회 (한 명만)
            $data = ShopItem::where(function ($query) use ($request) {
                    // 회원이 관리자 등급이면 상품진열이 진열 또는 전용상품일 경우 
                    if ($request->input('member_type')) {
                        $query->where('it_use', '1')
                            ->orWhere('it_only_admin', '1');
                    } 
                    // 회원타입이 없는 경우
                    else {
                        $query->where('it_use', '1')->where('it_only_admin',  '!=', '1');
                    }
                })
                ->when($request->input('mb_buy'), function($query) use ($request) {
                    // 구매등급 추가
                    $query->where('it_member_group', 'LIKE', '%'.$request->mb_buy.'%');
                })
                ->when($ca_id , function($query) use($ca_id){
                    $query->where('ca_id', $ca_id);
                })
                ->orderBy('it_order', 'asc')
                ->orderBy('it_time', 'desc')
                ->orderBy('idx', 'desc')
                ->pluck('idx')
                ->toArray();

            Cache::put($key, $data, 600); // 10분 마다 갱신

            return $data;
        });
    }



    // 묶음상품 재고량 합산
    public static function stockLeftJoinSub()
    {
        $shopGroup = ShopItem::selectRaw('it_qty_system_stock AS cnt, stock_it_id')
        ->groupBy('stock_it_id');

        return ShopItem::leftJoinSub($shopGroup, 'shop_group', function($join) {
            $join->on('g5_shop_item.stock_it_id', '=', 'shop_group.stock_it_id');
        })
        ->leftjoin('g5_shop_item_cust_log', function($join) {
            $join->on('g5_shop_item.it_id', '=', 'g5_shop_item_cust_log.it_id')
            ->where('g5_shop_item_cust_log.it_cust_rate_chk', '1');
        });
    }



    public static function getRelationItems($request)
    {
        $shopGroup = ShopItem::selectRaw('it_qty_system_stock AS cnt, stock_it_id')
        ->groupBy('stock_it_id');

        return ShopItem::leftJoinSub($shopGroup, 'shop_group', function($join) {
            $join->on('g5_shop_item.stock_it_id', '=', 'shop_group.stock_it_id');
        })
        ->leftjoin('g5_shop_item_cust_log', function($join) {
            $join->on('g5_shop_item.it_id', '=', 'g5_shop_item_cust_log.it_id')
            ->where('g5_shop_item_cust_log.it_cust_rate_chk', '1');
        })
        ->select(
            'g5_shop_item.idx',
            'g5_shop_item.it_id',
            'g5_shop_item.ca_id',
            'g5_shop_item.it_use',
            'g5_shop_item.it_name',
            'g5_shop_item.it_basic',
            'g5_shop_item.it_gubun',
            'g5_shop_item.it_qty_box', 
            'g5_shop_item.it_qty_pack', 
            'g5_shop_item.it_qty_pcs',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_gubun = 'box' THEN '박스'
                    WHEN g5_shop_item.it_gubun = 'pack' THEN '팩'
                    WHEN g5_shop_item.it_gubun = 'pcs' THEN '낱개'
                END AS it_gubun_label
            "),
            'g5_shop_item.it_return',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_return = '1' THEN '반품가능'
                    WHEN g5_shop_item.it_return = '2' THEN '반품불가'
                END AS it_return_label
            "),
            'g5_shop_item.it_soldout',
            'g5_shop_item.it_storage',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_storage = '1' THEN '상온'
                    WHEN g5_shop_item.it_storage = '2' THEN '냉동'
                    WHEN g5_shop_item.it_storage = '3' THEN '냉장'
                END AS it_storage_label
            "),
            'g5_shop_item.stock_it_id',
            'g5_shop_item.it_special_explan',
            'g5_shop_item.it_force_soldout',
            'g5_shop_item.it_buy_min_qty',
            'g5_shop_item.it_buy_max_qty',
            'g5_shop_item.it_price_piece_use',
            'g5_shop_item.it_increase_type',
            'g5_shop_item.it_increase_date',
            'g5_shop_item.it_type3',
            'g5_shop_item.it_type4',
            'g5_shop_item.it_type5',
            'g5_shop_item.it_cust_rate',
            'g5_shop_item.it_cust_type',

            'g5_shop_item.it_box_sale_pcs',
            'g5_shop_item.it_box_sale_pack',
            'g5_shop_item.it_box_sale_tot',

            'g5_shop_item.it_price',
            'g5_shop_item.it_price_purchase',
            'g5_shop_item.it_price_piece',
            'g5_shop_item.it_price_piece_use',
            'g5_shop_item.it_price_box_unit',
            'g5_shop_item.it_price_pack_unit',
            'g5_shop_item.it_price_piece_unit',
            'g5_shop_item.it_price_unit',
            'g5_shop_item.it_price1',
            'g5_shop_item.it_price_rate1',
            'g5_shop_item.it_price_unit1',
            'g5_shop_item.it_price2',
            'g5_shop_item.it_price_rate2',
            'g5_shop_item.it_price_unit2',
            'g5_shop_item.it_price3',
            'g5_shop_item.it_price_rate3',
            'g5_shop_item.it_price_unit3',
            'g5_shop_item.it_price4',
            'g5_shop_item.it_price_rate4',
            'g5_shop_item.it_price_unit4',
            'g5_shop_item.it_price5',
            'g5_shop_item.it_price_rate5',
            'g5_shop_item.it_price_unit5',
            'g5_shop_item.it_price6',
            'g5_shop_item.it_price_rate6',
            'g5_shop_item.it_price_unit6',
            'g5_shop_item.it_price7',
            'g5_shop_item.it_price_rate7',
            'g5_shop_item.it_price_unit7',
            'g5_shop_item.it_price8',
            'g5_shop_item.it_price_rate8',
            'g5_shop_item.it_price_unit8',
            'g5_shop_item.it_price9',
            'g5_shop_item.it_price_rate9',
            'g5_shop_item.it_price_unit9',
            'g5_shop_item.it_price10',
            'g5_shop_item.it_price_rate10',
            'g5_shop_item.it_price_unit10',
            'g5_shop_item.agency_it_price1',
            'g5_shop_item.agency_it_price_rate1',
            'g5_shop_item.agency_it_price_unit1',
            'g5_shop_item.agency_it_price2',
            'g5_shop_item.agency_it_price_rate2',
            'g5_shop_item.agency_it_price_unit2',
            'g5_shop_item.agency_it_price3',
            'g5_shop_item.agency_it_price_rate3',
            'g5_shop_item.agency_it_price_unit3',
            'g5_shop_item.agency_it_price4',
            'g5_shop_item.agency_it_price_rate4',
            'g5_shop_item.agency_it_price_unit4',
            'g5_shop_item.agency_it_price5',
            'g5_shop_item.agency_it_price_rate5',
            'g5_shop_item.agency_it_price_unit5',


            'g5_shop_item.it_cust_price',
            'g5_shop_item.it_cust_price_use',
            'g5_shop_item.agency_it_buy_min_qty',
            'g5_shop_item.agency_it_buy_max_qty',
            'g5_shop_item.it_qty_system_stock',
            'shop_group.cnt AS it_group_qty_system_stock',
            DB::raw('IFNULL(shop_group.cnt, 0) + IFNULL(g5_shop_item.it_qty_system_stock, 0) AS it_group_sum_system_stock'),
            'shop_group.stock_it_id AS it_group_stock_it_id',
            'g5_shop_item.it_img1',
            'g5_shop_item.it_img1_url',

            'g5_shop_item_cust_log.it_price1 as cust_price1',
            'g5_shop_item_cust_log.it_price2 as cust_price2',
            'g5_shop_item_cust_log.it_price3 as cust_price3',
            'g5_shop_item_cust_log.it_price4 as cust_price4',
            'g5_shop_item_cust_log.it_price5 as cust_price5',
            'g5_shop_item_cust_log.it_price7 as cust_price6',
            'g5_shop_item_cust_log.it_price7 as cust_price7',
            'g5_shop_item_cust_log.it_price8 as cust_price8',
            'g5_shop_item_cust_log.it_price9 as cust_price9',
            'g5_shop_item_cust_log.it_price10 as cust_price10'
        )
        ->when(($request->input('ca_id2')), function($query) use ($request) {
            $query->where('g5_shop_item.ca_id2', $request->input('ca_id2'));
        })
        ->when(($request->input('not_it_id')), function($query) use ($request) {
            $query->where('g5_shop_item.it_id', '!=', $request->input('not_it_id'));
        })
        ->where('g5_shop_item.it_use', '1')
        ->where('g5_shop_item.ca_id', '!=', '10')
        ->whereIn('g5_shop_item.it_auction', ['0', '2'])
        ->orderBy('g5_shop_item.it_hit', 'desc')
        ->orderBy('g5_shop_item.it_order', 'desc')
        ->orderBy('g5_shop_item.it_time', 'desc')
        ->orderBy('g5_shop_item.idx', 'desc')
        ->limit(10)
        ->get();
        
    }
}
