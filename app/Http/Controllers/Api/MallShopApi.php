<?php
/**
 * Class Name : MallShopApi
 * Description : 쇼핑몰 통합 api
 * Author : Lee Sangseung
 * Created Date : 2026-01-14
 * Version : 1.0
 * 
 * History :
 *   - 2026-01-14 : Initial creation
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TbYoutube;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Models\ShopOrderDataModel;
use App\Services\InicisService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MallShopApi extends Controller
{
    /**
     * 유튜브 관리
     *
     * @param Request $request
     * @return array
     */
    public function youtube_proc(Request $request)
    {
        $request->validate([
            'mode' => 'required|string',
            'tb_page' => 'required|string',
        ]);

        if ($request->get('mode') == 'show') {

            $result = TbYoutube::where('tb_page', $request->get('tb_page'))
            ->where('use_yn', 'Y')
            ->select('tb_youtube_url', 'tb_youtube_link', 'apply_area')
            ->first();
        }

        return [
            'data' => $result ?? []
        ];
    }



    public function file_transfer(Request $request)
    {
        try {
            Log::info(__METHOD__. ' <<== 실행');
            
            // path 파라미터 체크
            if (!$request->has('path') || !$request->hasFile('file') || !$request->has('filename')) {
                throw new Exception('필수 파라미터가 누락되었습니다.', 400);
            }

            $img_path = [
                'recipe.chios' => '/home/samma_renew/www/public/images/recipe/chios/', // 레시피 chios 이미지
                'recipe' => '/home/samma_renew/www/public/images/recipe/', // 레시피 이미지
                'item'   => '/home/samma_renew/www/public/images/item/', // 상품 이미지
                'banner' => '/home/samma_renew/www/public/images/common_data/banner/', // 배너 이미지
            ];


            $pathKey = $request->path;

            if (!isset($img_path[$pathKey])) {
                throw new Exception('유효하지 않은 path 값입니다.', 400);
            }
    
            
            if (!$request->hasFile('file')) {
                throw new Exception('업로드할 파일이 없습니다.', 400);
            }

            $file = $request->file('file');

            
            // Log::info('RECV SIZE => '.$request->file('file')->getSize());



            if (!$file->isValid()) {
                throw new Exception('파일 업로드 중 오류가 발생했습니다.', 500);
            }

            $itDir    = $request->input('it_dir', '');
            $folder   = rtrim($img_path[$pathKey] . $itDir, '/') . '/';
            $filename = $request->input('filename');


            // 폴더 없으면 생성
            if (!File::exists($folder)) {
                if (!File::makeDirectory($folder, 0755, true)) {
                    throw new Exception('폴더 생성 실패: 권한 확인 필요', 500);
                }
            }
    

            // 파일 저장
            $file->move($folder, $filename);

            return response()->json([
                'status' => 'success',
                'path' => $folder . $filename,
            ]);

        } catch (\Exception $e) {
            Log::error('File Transfer Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }



    public function delete_cach(Request $request)
    {
        try {
            Log::info($request->all());
            Log::info(__METHOD__. ' <<== 실행');
            if (!$request->has('del_cach')) {
                throw new Exception('필수값이 존재하지 않습니다.');
            }

            $del_key = [
                'items'  => 'mall:items:',
                'member' => ':member',
                'menu_banner'  => 'menu_banner',
                'intro'  => 'intro',
            ];

            if (!isset($del_key[$request->input('del_cach')])) {
                throw new Exception('존재하지 않는 타입 입니다.');
            }

            // 회원정보 캐시 삭제
            $redis_key = $request->input('key_value').$del_key[$request->input('del_cach')];

            if ($request->input('del_cach') == 'member') {
                if (Redis::exists($redis_key)) {
                    Redis::del($redis_key);
                }
            }


            // 인트로 삭제
            if ($request->input('del_cach') == 'intro') {
                Redis::del('intro');
            }


            // 배너면 메인 전체삭제
            if ($request->input('del_cach') == 'menu_banner') {
                Redis::del('main_page');
                Redis::del('main_banner');
                Redis::del('menu_banner');

                $cursor = null;

                do {
                    $result = Redis::scan($cursor, [
                        'match' => 'mall:items:*',
                        'count' => 100
                    ]);
                
                    if ($result === false) {
                        break;
                    }
                
                    foreach ($result as $key) {
                        Redis::del($key);
                    }

                } while ($cursor != 0);
            }


            // 상품정보 삭제
            if ($request->input('del_cach') === 'items') {
                $cursor = null;

                do {
                    $result = Redis::scan($cursor, [
                        'match' => 'laravel_cache:shop:items:*',
                        'count' => 100
                    ]);
                
                    if ($result === false) {
                        break;
                    }
                
                    foreach ($result as $key) {
                        Redis::del($key);
                    }

                } while ($cursor != 0);
            }

            return response()->json([
                'status' => 'success',
            ]);

        } catch (\Exception $e) {
            Log::error('delete_cach Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }


    public function checkSoldout(Request $request)
    {

        $od_id = $request->oid;     

// dd($od_id);

        $resDt = app(PaymentService::class)->checkSystemStockAjaxDt($od_id);


// dd($resDt);

        return response()->json($resDt);

        // if ($resDt['status'] == 'reject') {
        //     throw new Exception($resDt['message'], 409);
        // }    

        // return response()->json([
        //     'status' => 'success'
        // ]);        

    }

    public function orderdata(Request $request)
    {
        try {
            if (!session('ss_mb_code')) {
                throw new Exception('잘못된 접근 입니다.');
            }

            $od_id = $request->oid;
            $data  = $request->form_data;

            $resDt = app(PaymentService::class)->checkSystemStockAjaxDt($od_id);

            if ($resDt['status'] == 'reject') {
                return response()->json($resDt);
            }


            DB::transaction(function () use ($od_id, $data) {
                $exists = DB::table('g5_shop_order_data')
                    ->where('od_id', $od_id)
                    ->where('mb_id', session('ss_mb_id'))
                    ->where('od_chk', '0')
                    ->exists();
            
                if ($exists) {
                    // throw new Exception('이미 결제가 진행 중입니다.');
                } else {
                    DB::table('g5_shop_order_data')->insert([
                        'od_id' => $od_id,
                        'mb_id' => session('ss_mb_id'),
                        'dt_pg' => 'inicis',
                        'dt_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                        'dt_time' => now(),
                        'od_chk' => '0'
                    ]);
                }
            });

            return response()->json([
                'status' => 'success'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            // UNIQUE 제약 위반 → 이미 결제 진행 중
            return response()->json([
                'status' => 'error',
                'message' => '이미 결제가 진행 중입니다.',
            ], 200, [], JSON_UNESCAPED_UNICODE);
        
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }


}
