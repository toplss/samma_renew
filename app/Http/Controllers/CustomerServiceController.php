<?php

namespace App\Http\Controllers;

use App\Models\ShopItemContactModel;
use App\Models\TbAllianceModel;
use App\Models\TbAsReceptionModel;
use App\Models\TbClaimModel;
use App\Models\TbReturnReceptionModel;
use App\Models\ShopItemQaModel;
use App\Models\TbBbsBody;
use Illuminate\Http\Request;
use Debugbar;
use App\Models\TbCompanyModel;
use App\Models\TbErpFile;
use App\Services\MallMainServices;
use App\Services\MallShopService;
use App\Services\ShopCartService;
use App\Services\SmsManagementService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Illuminate\Validation\ValidationException;


class CustomerServiceController extends Controller
{

    private $default_access_auth = 10;


    public function MyPageUserGuide()
    {
        $items = TbCompanyModel::select(['title', 'content', 'content2', 'content3', 'content4', 'content5'])
        ->where('id', 7)->first();

        return view('customer_service.user_guide', compact('items'));
    }


    public function MyPageQaList(Request $request)
    {
        $start_date = $request->input('start_date', '');
        $end_date = $request->input('end_date', '');
        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            throw new Exception('로그인 사용자가 아닙니다.');
        }


        $itemQa = DB::table('g5_shop_item_qa')
            ->join('g5_shop_item', 'g5_shop_item_qa.it_id', 'g5_shop_item.it_id')
            ->select([
                'g5_shop_item_qa.iq_id',
                'g5_shop_item_qa.it_id',
                'g5_shop_item_qa.it_mb_num',
                'g5_shop_item_qa.mb_id',
                'g5_shop_item_qa.iq_gubun',
                'g5_shop_item_qa.iq_subject',
                'g5_shop_item_qa.iq_question',
                'g5_shop_item_qa.iq_answer',
                'g5_shop_item_qa.iq_time',
                'g5_shop_item.it_img1',
                'g5_shop_item.it_name',
                DB::raw("'item_qa' as table_name")
            ])
            ->where('g5_shop_item_qa.it_mb_num', $mb_code)
            ->when($start_date && $end_date, function($query) use($start_date, $end_date) {
                $query->whereBetween(DB::raw('DATE(g5_shop_item_qa.iq_time)'), [$start_date, $end_date]);
            });

        $itemContact = DB::table('g5_shop_item_contact')
            ->join('g5_shop_item', 'g5_shop_item_contact.it_id', 'g5_shop_item.it_id')
            ->select([
                'g5_shop_item_contact.iq_id',
                'g5_shop_item_contact.it_id',
                'g5_shop_item_contact.it_mb_num',
                'g5_shop_item_contact.mb_id',
                'g5_shop_item_contact.iq_gubun',
                'g5_shop_item_contact.iq_subject',
                'g5_shop_item_contact.iq_question',
                'g5_shop_item_contact.iq_answer',
                'g5_shop_item_contact.iq_time',
                'g5_shop_item.it_img1',
                'g5_shop_item.it_name',
                DB::raw("'item_contact' as table_name")
            ])
            ->where('g5_shop_item_contact.it_mb_num', $mb_code)
            ->when($start_date && $end_date, function($query) use($start_date, $end_date) {
                $query->whereBetween(DB::raw('DATE(g5_shop_item_contact.iq_time)'), [$start_date, $end_date]);
            });;

        $union = $itemQa->unionAll($itemContact);

        $items = DB::query()
        ->fromSub($union, 'u')
        ->select([
            '*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY iq_id DESC) as row_num')
        ])
        ->paginate(10);

        return view('customer_service.my_qa_list', compact('items'));
    }


    
    public function MyPageQaView(Request $request)
    {
        $request->validate([
            'table' => 'required|in:item_qa,item_contact',
            'idx'   => 'required|numeric',
        ], [
            'table.required' => '테이블 정보가 존재하지 않습니다.',
            'table.in' => '허용되지 않은 테이블 정보입니다.',

            'idx.required' => '상담문의 번호가 존재하지 않습니다.',
            'idx.numeric' => '상담문의 번호가 옳바른 형식이 아닙니다.',
        ]);

        $mb_code = session('ss_mb_code');

        if ($request->input('table') == 'item_qa') {
            $info = ShopItemQaModel::where('iq_id', $request->input('idx'))
            ->where('it_mb_num', $mb_code)
            ->first();
        } else {
            $info = ShopItemContactModel::where('iq_id', $request->input('idx'))
            ->where('it_mb_num', $mb_code)
            ->first();
        }

        if (!$info) {
            return redirect('/customer_service/my_qa_list')->with('error', '상담문의가 존재하지 않습니다.');
        }


        $items = app(ShopCartService::class)->MyRecetOrderItems($request);


        return view('customer_service.my_qa_view', [
            'idx'   => $request->input('idx'),
            'table' => $request->input('table'),
            'info'  => $info,
            'items' => $items
        ]);
    }



    public function MyPageBoardList(Request $request)
    {
        $ss = $request->input('ss', '');
        $search_kw = $request->input('search_kw', '');

        $mall = app(MallMainServices::class);

        $items = DB::table('tb_bbs_body')
        ->where('bbs_code', 'notice')
        ->when($ss, function($query) use($ss, $search_kw) {
            if ($ss == '1') {
                $query->where('bd_subject', 'LIKE', '%'.$search_kw.'%')
                ->orWhere('bd_content', 'LIKE', '%'.$search_kw.'%')
                ->orWhere('bd_name', 'LIKE', '%'.$search_kw.'%');
            }
            if ($ss == '4') {
                $query->where('bd_subject', 'LIKE', '%'.$search_kw.'%');
            }
            if ($ss == '6') {
                $query->where(function($query) use ($search_kw) {
                    $query->where('bd_subject', 'LIKE', '%'.$search_kw.'%')
                    ->orWhere('bd_content', 'LIKE', '%'.$search_kw.'%');
                });
            }
            if ($ss == '3') {
                $query->where('bd_name', 'LIKE', '%'.$search_kw.'%');
            }
        })
        ->select(
            DB::raw('COUNT(*) OVER()
            - ROW_NUMBER() OVER(
                ORDER BY bd_notice = 0 ASC, bd_num DESC
            ) + 1 AS row_num'),
            'bd_num',
            'bd_notice',
            'bd_subject',
            'bd_name',
            'bd_ext1',
            'bd_view_count',
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(bd_write_date), '%Y-%m-%d') as write_date")
        )
        ->orderBy('bd_notice', 'desc')
        ->orderBy('bd_recommend', 'desc')
        ->orderBy('bd_next_num', 'desc')
        ->orderBy('bd_write_date', 'desc')
        // ->orderBy(DB::raw('bd_notice = 0'), 'asc')
        ->paginate(10);

        $access_level = $this->default_access_auth;
       
        if ($items && session('ss_mb_code')) {
            $access_level = DB::table('tb_employee')
            ->where('mb_code', session('ss_mb_code'))
            ->value('mb_access_grade') ?? $this->default_access_auth;
        }

        // 배너 정보
        $banner = $mall->getBanner('renew_item_page', 1);
        $other['banner'] = $mall->makeBannerDiv($banner, 'N');
        $items->other = $other;


        return view('common_board.list', [
            'items' => $items,
            'access_level' => $access_level,
        ]);
    }


    public function MyPageBoardView(Request $request)
    {
        $request->validate([
            'bd_num' => 'required|integer',
        ],[
            'bd_num.required' => '게시글 번호가 존재하지 않습니다.',
            'bd_num.integer' => '게시글 번호가 옳바른 형식이 아닙니다.',
        ]);

        $bd_num = $request->input('bd_num');

        DB::transaction(function() use($bd_num, &$item) {
            $item = TbBbsBody::select(
                'bd_num',
                'bd_notice',
                'bd_subject',
                'bd_name',
                'bd_ext1',
                'bd_view_count',
                'bd_content',
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(bd_write_date), '%Y-%m-%d %H:%i:%s') as write_date")
            )
            ->with('files')
            ->where('bd_num', $bd_num)
            // ->lockForUpdate()
            ->first();

            DB::table('tb_bbs_body')->where('bd_num', $bd_num)->increment('bd_view_count');

            $item->bd_view_count++;
        });

        if ($item) {

            $access_level = $this->default_access_auth;

            if (session('ss_mb_code')) {
                $access_level = DB::table('tb_employee')
                ->where('mb_code', session('ss_mb_code'))
                ->value('mb_access_grade') ?? $this->default_access_auth;
            }

            return view('common_board.view', ['item' => $item, 'access_level' => $access_level]);

        } else {

            return redirect()->route('notice')->with('error', '게시글이 존재하지 않습니다.');
        }
    }



    public function MyPageBoardWirte(Request $request)
    {
        if (!session('ss_mb_code')) {
            return redirect()->route('login')->with('error', '로그인 후 이용가능합니다.');
        }

        $access_level = $this->default_access_auth;
       
        if (session('ss_mb_code')) {
            $access_level = DB::table('tb_employee')
            ->where('mb_code', session('ss_mb_code'))
            ->value('mb_access_grade') ?? $this->default_access_auth;

            if ($access_level > 2) {
                return redirect()->route('notice')->with('error', '게시글 작성 권한이 없습니다.');
            }
        }

        $item = (object)[];

        if ($request->input('bd_num')) {
            $item = TbBbsBody::with('files')->find($request->input('bd_num'));

        }

        return view('common_board.write', compact('item')); 
    }



    public function MyPageBoardSave(Request $request)
    {
        if (!session('ss_mb_code')) {
            return redirect()->route('login')->with('error', '로그인 후 이용가능합니다.');
        }

        $bd_num = $request->input('bd_num');

        $add_data['bbs_code'] = 'notice';

        if ($request->has('files')) 
            $add_data['bd_files'] = 'Y;';

        if ($request->input('bd_notice') == '1') 
            $add_data['bd_notice'] = '1';
        else 
            $add_data['bd_notice'] = '0';


        $add_data['mb_num'] = session('ss_mb_code');
        $add_data['mb_id']  = session('ss_mb_id');
        $add_data['bd_name']= $request->input('bd_name');
        $add_data['bd_ext1']= $request->input('bd_ext1', '');
        $add_data['bd_subject']= $request->input('bd_subject');
        $add_data['bd_url']= $request->input('bd_url', '');
        $add_data['bd_content']= $request->input('bwcontent');
        $add_data['bd_write_date']= time();
        
        if ($bd_num) {
            $add_data['bd_modify_date'] = time();

            $bbsBody = TbBbsBody::where('bd_num', $bd_num)->update($add_data);
        } else {
            $bbsBody = TbBbsBody::create($add_data);
        }
        

        $fileInfos = [];
        if ($request->has('files')) {
            foreach ($request->file('files') as $key => $row) {
                $file = $row;
    
                if ($file) {
                    $folder   = 'board/files/';
                    $filename = time().'_'.$file->getClientOriginalName();
    
                    $res = Storage::disk('public')->put($folder.$filename, file_get_contents($file));
    
                    if ($res) {
                        $fileInfos[] = [
                            'table_name'    => 'tb_bbs_body',
                            'table_idx'     => ($bd_num) ? $bd_num : $bbsBody->bd_num,
                            'file_orgin_name' => $file->getClientOriginalName(),
                            'file_name'     => $filename,
                            'file_size'     => $file->getSize(),
                            'file_type'     => $file->getClientMimeType(),
                            'created_at'    => date('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
    
            if (!empty($fileInfos)) {
                DB::table('tb_erp_files')->insert($fileInfos);
            }
        }
        
        if ($bd_num) {
            return redirect()->route('notice')->with('success', '게시글이 수정되었습니다.');
        } else {
            return redirect()->route('notice')->with('success', '게시글이 등록되었습니다.');
        }
    }



    public function MyPageBoardDelete(Request $request)
    {
        $request->validate([
            'bd_num' => 'required|integer|exists:tb_bbs_body,bd_num',
        ], [
            'bd_num.required' => '게시글 번호가 존재하지 않습니다.',
            'bd_num.integer' => '게시글 번호가 옳바른 형식이 아닙니다.',
            'bd_num.exists' => '게시글이 존재하지 않습니다.',
        ]);

        $mb_code = session('ss_mb_code');

        $access_level = $this->default_access_auth;

        if ($mb_code) {
            $access_level = DB::table('tb_employee')
            ->where('mb_code', $mb_code)
            ->value('mb_access_grade') ?? $this->default_access_auth;

            if ($access_level > 2) {
                return redirect()->route('notice')->with('error', '게시글 작성 권한이 없습니다.');
            }
        }

        $deleted = DB::table('tb_bbs_body')->where('bd_num', $request->input('bd_num'))->delete();

        if ($deleted) {
            $files = DB::table('tb_erp_files')->where('table_name', 'tb_bbs_body')->where('table_idx', $request->input('bd_num'));

            if ($files->exists()) {
                foreach ($files->get() as $file) {
                    Storage::disk('public')->delete('board/files/'.$file->file_name);
                }

                $files->delete();
            }
        }

        return redirect()->route('notice')->with('success', '게시글이 삭제되었습니다.');
    }




    public function MyPageBoardFileDelete(Request $request)
    {
        $request->validate([
            'f_k' => 'required|integer|exists:tb_erp_files,idx'
        ], [
            'f_k.required' => '파일 정보가 존재하지 않습니다.',
            'f_k.integer' => '파일 정보가 옳바른 형식이 아닙니다.',
            'f_k.exists' => '파일 정보가 존재하지 않습니다.',
        ]);


        $files = TbErpFile::find($request->input('f_k'));

        if ($files->exists()) {
            foreach ($files->get() as $file) {
                Storage::disk('public')->delete('board/files/'.$file->file_name);
            }

            $files->delete();
        }

        return redirect()->back()->with('success', '파일이 삭제되었습니다.');
    }




    public function MyPageBoardFileDownload(Request $request)
    {
        $request->validate([
            'f_k' => 'required|integer|exists:tb_erp_files,idx'
        ], [
            'f_k.required' => '파일 정보가 존재하지 않습니다.',
            'f_k.integer' => '파일 정보가 옳바른 형식이 아닙니다.',
            'f_k.exists' => '파일 정보가 존재하지 않습니다.',
        ]);


        $files = TbErpFile::where('table_name', 'tb_bbs_body')->where('idx', $request->input('f_k'))->first();

        if ($files->exists()) {
            $path = 'board/files/'.$files->file_name;
            return Storage::disk('public')->download(
                $path,
                $files->file_orgin_name
            );
        
        } else {
            return redirect()->back()->with('error', '파일이 존재하지 않습니다.');
        }
        
    }




    public function MyPageAllianceSave(Request $request)
    {
        $request->validate([
            'gubun' => 'required|in:alliance,store',
            'company' => 'required',
            'name' => 'required',
            'phone1' => 'required',
            'phone2' => 'required',
            'phone3' => 'required',
            'email1' => 'required',
            'email2' => 'required',
            'title' => 'required',
            'contents' => 'required',
        ], [
            'gubun.required' => '문의 분류를 선택해주세요.',
            'gubun.in' => '잘못된 분류입니다.',

            'company.required' => '업체명을 입력해주세요.',
            'name.required' => '이름을 입력해주세요.',

            'phone1.required' => '전화번호를 입력해주세요.',
            'phone2.required' => '전화번호를 입력해주세요.',
            'phone3.required' => '전화번호를 입력해주세요.',

            'email1.required' => '이메일을 입력해주세요.',
            'email2.required' => '이메일을 입력해주세요.',

            'title.required' => '제목을 입력해주세요.',
            'contents.required' => '내용을 입력해주세요.',
        ]);

        $file = $request->file('userfile');

        if ($file) {
            $folder   = '/common_data/alliance/';
            $filename = time().'_'.$file->getClientOriginalName();

            Storage::disk('sftp_remote')
                ->put($folder.$filename, fopen($file->getRealPath(), 'r')
            );
        }

        $req_id = "A-".time();

        $save = [
            'req_id' => $req_id,
            'mb_num' => session('ss_mb_code') ?? '',
            'mb_id'  => session('ss_mb_id') ?? '',
            'gubun'  => $request->input('gubun'),
            'name'   => $request->input('name'),
            'company'   => $request->input('company'),
            'email'  => $request->input('email1').'@'.$request->input('email2'),
            'phone'  => $request->input('phone1').'-'.$request->input('phone2').'-'.$request->input('phone3'),
            'title'  => $request->input('title'),
            'contents'  => $request->input('contents'),
            'url'    => $request->input('url') ?? '',
            'file1'  => $filename ?? '',
            'state'  => '1',
            'reg_date' => date('Y-m-d H:i:s')
        ];

        if (TbAllianceModel::create($save)) {
            return redirect()->route('/')->with('success', '제휴문의 및 입점문의 상담신청이 등록되었습니다');
        } else {
            return redirect()->route('/customer_service/alliance?gubun='.$request->gubun)->with('error', '제휴문의 및 입점문의 상담신청이 실패하였습니다.');
        }
    }


    public function MyPageClaimSave(Request $request)
    {
        $validated = $request->validate([
            'gubun' => 'required|in:product_claim,delivery_claim,customer_claim,etc',
            'company' => 'required|string|max:100',
            'name' => 'required|string',
            'phone1' => 'required|digits_between:2,4',
            'phone2' => 'required|digits_between:3,4',
            'phone3' => 'required|digits:4',
            'addr' => 'required|string',
            'title' => 'required|string',
            'contents' => 'required|string',
        ], [
            'gubun.required' => '분류값을 선택해주세요.',
            'gubun.in' => '허용되지 않은 분류값 입니다.',
        
            'company.required' => '업체명을 입력하세요.',
            'name.required' => '이름을 입력하세요.',
        
            'phone1.required' => '전화번호 앞자리를 입력하세요.',
            'phone2.required' => '전화번호 중간자리를 입력하세요.',
            'phone3.required' => '전화번호 끝자리를 입력하세요.',
        
            'addr.required' => '주소를 입력하세요.',
            'title.required' => '제목을 입력하세요.',
            'contents.required' => '내용을 입력하세요.',
        ]);
        

        $req_id = "A-".time();

        $save = [
            'req_id' => $req_id,
            'mb_num' => session('ss_mb_code') ?? '',
            'mb_id'  => session('ss_mb_id') ?? '',
            'gubun'  => $request->input('gubun'),
            'name'   => $request->input('name'),
            'company'   => $request->input('company'),
            'email'  => '',
            'phone'  => $request->input('phone1').'-'.$request->input('phone2').'-'.$request->input('phone3'),
            'title'  => $request->input('title'),
            'contents'  => $request->input('contents'),
            'url'    => $request->input('url') ?? '',
            'addr'   => $request->input('addr'),
            'file1'  => '',
            'state'  => '1',
            'reg_date' => date('Y-m-d H:i:s')
        ];

        if (TbClaimModel::create($save)) {
            return redirect()->route('/')->with('success', '클레임접수가 등록되었습니다');
        } else {
            return redirect()->route('/customer_service/claim')->with('error', '클레임접수가 실패하였습니다.');
        }
    }



    public function MyPageEquipQASave(Request $request)
    {
        try {
            $validated = $request->validate([
                'it_id' => 'required|string|exists:g5_shop_item,it_id',
                'it_name' => 'required|string',
            ], [
                'it_id.required' => '상품 정보가 존재하지 않습니다.',
                'it_id.exists' => '상품 정보가 존재하지 않습니다.',
                'it_name.required' => '상품 정보가 존재하지 않습니다.',
            ]);


            $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
            if (!$member) {
                throw new  Exception("회원정보가 존재하지 않습니다.");
            }


            $state = DB::table('g5_shop_buy_equip_qa')->where('mb_num', $member['mb_code'])
            ->where('title_no', $request->input('it_id'))
            ->value('state');

            if ($state == '1') {
                throw new  Exception("등록이 완료된 상품입니다.");
            } else if ($state == '2') {
                throw new  Exception("문의가 종료된 상품입니다.");
            } else {
                $req_id = "A-".time();

                $save = [
                    'req_id' => $req_id,
                    'mb_num' => session('ss_mb_code') ?? '',
                    'mb_id'  => session('ss_mb_id') ?? '',
                    'gubun'  => 'equip_buy_qa',
                    'name'   => $member['mb_name'],
                    'company'   => $member['mb_company'],
                    'email'  => $member['mb_email'],
                    'phone'  => $member['mb_hp'],
                    'title_no' => $request->input('it_id'),
                    'title'  => $request->input('it_name'),
                    'contents'  => '장비문의',
                    'url'    => '',
                    'file1'  => '',
                    'state'  => '1',
                    'reg_date' => date('Y-m-d H:i:s')
                ];

                $res = DB::table('g5_shop_buy_equip_qa')->insert($save);

                if ($res) {
                    return response()->json([
                        'status'  => 'success',
                        'message' => '장비 문의가 정상적으로 접수되었습니다. 빠른 시일 내에 연락드리겠습니다.'
                    ]);
                } else {
                    throw new  Exception("장비 문의 접수에 실패했습니다. 잠시 후 다시 시도하시거나 관리자에게 문의해 주세요.");
                }
            }
            
        } catch (ValidationException $ve) {
            return response()->json([
                'status'  => 'fail',
                'message' => $ve->validator->errors()->first()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => $e->getMessage()
            ]);
        }
    }




    public function MyPageAsReception(Request $request)
    {
        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            throw new Exception('로그인 사용자가 아닙니다.');
        }

        $items = DB::table('tb_tmp_choice_equipment as a')
                ->selectRaw("
                    ROW_NUMBER() OVER(
                        ORDER BY 
                            a.t_it_id_type = '7' ASC,
                            a.t_specific_item DESC,
                            a.t_it_id_type DESC,
                            a.it_id ASC,
                            a.t_rank_order ASC,
                            a.idx DESC
                    ) as row_num
                ")
                ->addSelect([
                    'a.idx',
                    'a.it_id',
                    'a.t_p_code',
                    'a.t_it_id_type',
                    'b.it_name',
                    'b.it_img1',
                    'c.ca_name'
                ])
                ->join('g5_shop_item as b', 'a.it_id', '=', 'b.it_id')
                ->join('g5_shop_category as c', 'a.ca_id2', '=', 'c.ca_id')
                ->where('a.t_gubun', 'equipment')
                ->where('a.t_no', $mb_code)
                ->where('a.t_it_id_type', '!=', '7')
                ->where('a.t_state', '2')
                ->whereIn('a.t_chk', ['', '1'])
                ->orderByRaw("
                    a.t_it_id_type = '7' ASC,
                    a.t_specific_item DESC,
                    a.t_it_id_type DESC,
                    a.it_id ASC,
                    a.t_rank_order ASC,
                    a.idx DESC
                ")
                ->get();
        
        return view('customer_service.as_reception', compact('items'));
    }


    public function MyPageAsReceptionSave(Request $request)
    {
        $request->validate([
            'gubun' => 'required|in:as',
            'userfile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'company' => 'required',
            'contents' => 'required',
        ], [
            'gubun.required' => '분류값을 선택해주세요.',
            'gubun.in' => '허용되지 않은 분류값 입니다.',

            'userfile.required' => '파일을 첨부해주세요.',
            'userfile.mimes'    => 'jpg, png, pdf 파일만 업로드 가능합니다.',
            'userfile.max'      => '파일 용량은 2MB 이하만 가능합니다.',

            'company.required' => '업체명이 입력되지 않았습니다.',
            'contents.required' => '내용이 입력되지 않았습니다.',
        ]);

        $file = $request->file('userfile');

        if ($file) {
            $folder   = '/common_data/as_reception/';
            $filename = time().'_'.$file->getClientOriginalName();

            Storage::disk('sftp_remote')
                ->put($folder.$filename, file_get_contents($file));

        }

        $titleArr = explode('||', $request->input('title'));

        $req_id = "A-".time();

        $save = [
            'req_id' => $req_id,
            'mb_num' => session('ss_mb_code') ?? '',
            'mb_id'  => session('ss_mb_id') ?? '',
            'gubun'  => $request->input('gubun'),
            'name'   => $request->input('name'),
            'company'   => $request->input('company'),
            'email'  => '',
            'phone'  => $request->input('phone1').'-'.$request->input('phone2').'-'.$request->input('phone3'),
            'title_no' => $titleArr[0],
            'title'  => $titleArr[1],
            'contents'  => $request->input('contents', ''),
            'url'    => $request->input('url') ?? '',
            'file1'  => $filename ?? '',
            'state'  => '1',
            'reg_date' => date('Y-m-d H:i:s')
        ];

        if ($as = TbAsReceptionModel::create($save)) {
            
            app(SmsManagementService::class)->kakaoAlrimTok('samma_3', null, null, null, $as->idx);

            return redirect()->route('/')->with('success', 'A/S접수가 등록되었습니다');
        } else {
            return redirect()->route('/customer_service/as_reception')->with('error', 'A/S접수가 실패 하였습니다.');
        }
    }


    /**
     * Constructor
     * Description : 반품접수
     * Author : Kim Hairyong 
     * Created Date : 2026-01-30
     * Params : Params
     * History :
     *   - 2026-01-30 : Initial creation
     */        
    public function MyPageReturnReceptionView(Request $request)
    {
        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            throw new Exception('로그인 사용자가 아닙니다.');
        }

        $result = app(ShopCartService::class)->MyRecetOrderItems($request);

        
        return view('customer_service.return_reception', compact('result'));
    }


    /**
     * Constructor
     * Description : 반품접수 상품검색 List
     * Author : Kim Hairyong 
     * Created Date : 2026-01-30
     * Params : Params
     * History :
     *   - 2026-01-30 : Initial creation
     */            
    public function MyPageReturnReceptionList(Request $request)
    {
        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            throw new Exception('로그인 사용자가 아닙니다.');
        }        

        //전체 상품정보 (회원이 구매한 상품포함 - Left Join)
        $result = DB::table('g5_shop_item as si')
                ->select([
                    'si.idx',
                    'si.ca_id',
                    'si.it_id',
                    'si.it_name',
                    'si.it_img1',
                    'si.it_storage',
                    DB::raw("
                        CASE
                            WHEN si.it_storage = '1' THEN '상온'
                            WHEN si.it_storage = '2' THEN '냉동'
                            WHEN si.it_storage = '3' THEN '냉장'
                            ELSE '상온'
                        END AS it_storage_label
                    "),
                    'si.it_return',
                    DB::raw("
                        CASE
                            WHEN si.it_return = '1' THEN '반품가능'
                            WHEN si.it_return = '2' THEN '반품불가'
                        END AS it_return_label
                    "),
                    'si.it_buy_min_qty',
                    'si.it_buy_max_qty',
                    'si.it_basic',
                    'si.it_price',
                    'si.it_price_purchase',
                    'si.it_price_piece',
                    'si.it_price_piece_use',
                    'si.it_price_box_unit',
                    'si.it_price_pack_unit',
                    'si.it_price_piece_unit',
                    'si.it_price_unit',

                    'si.it_price1', 'si.it_price_rate1', 'si.it_price_unit1',
                    'si.it_price2', 'si.it_price_rate2', 'si.it_price_unit2',
                    'si.it_price3', 'si.it_price_rate3', 'si.it_price_unit3',
                    'si.it_price4', 'si.it_price_rate4', 'si.it_price_unit4',
                    'si.it_price5', 'si.it_price_rate5', 'si.it_price_unit5',
                    'si.it_price6', 'si.it_price_rate6', 'si.it_price_unit6',
                    'si.it_price7', 'si.it_price_rate7', 'si.it_price_unit7',
                    'si.it_price8', 'si.it_price_rate8', 'si.it_price_unit8',
                    'si.it_price9', 'si.it_price_rate9', 'si.it_price_unit9',
                    'si.it_price10', 'si.it_price_rate10', 'si.it_price_unit10',

                    'si.agency_it_price1', 'si.agency_it_price_rate1', 'si.agency_it_price_unit1',
                    'si.agency_it_price2', 'si.agency_it_price_rate2', 'si.agency_it_price_unit2',
                    'si.agency_it_price3', 'si.agency_it_price_rate3', 'si.agency_it_price_unit3',
                    'si.agency_it_price4', 'si.agency_it_price_rate4', 'si.agency_it_price_unit4',
                    'si.agency_it_price5', 'si.agency_it_price_rate5', 'si.agency_it_price_unit5',

                    'sc.ct_id',
                    'sc.mb_code',
                    'sc.od_id',
                    'sc.od_group_code',
                    'sc.od_date',
                    'sc.ct_qty',
                    'sc.ct_price',
                    'sc.ct_status',
                    'sc.ct_cate',
                ])
                ->join('g5_shop_category as scg', function ($query) {
                    $query->on('si.ca_id', '=', 'scg.ca_id')
                        ->where('scg.ca_display', 1);
                })
                ->leftJoin('g5_shop_cart as sc', function ($query) use ($mb_code) {
                    $query->on('si.it_id', '=', 'sc.it_id')
                        ->where('sc.mb_code', $mb_code)
                        ->where('sc.ct_cate', '납품')
                        ->where('sc.ct_status', '<>', '쇼핑')
                        ->whereRaw("
                            sc.od_date >= DATE_FORMAT(
                                DATE_SUB(CURDATE(), INTERVAL 3 MONTH),
                                '%Y%m%d'
                            )
                        ");
                })
                ->groupBy('si.it_id')
                ->get();


        return view('customer_service.return_reception', compact('result'));        
    }



    /**
     * Constructor
     * Description : 반품접수 등록
     * Author : Kim Hairyong 
     * Created Date : 2026-01-30
     * Params : Params
     * History :
     *   - 2026-01-30 : Initial creation
     */            
    public function MyPageReturnReceptionSave(Request $request)
    {
        $req_id = 'A-' . time();

        $items = $request->input('items', []);
        $qtys  = $request->input('request_qty', []);
        $comments = $request->input('contents', []);
        $files = $request->file('userfile', []);

        foreach ($items as $it_id => $item) {

            $qty = (int)($qtys[$it_id] ?? 0);
            if ($qty < 1) continue;

            //첨부파일 처리
            $filename = '';
            if (isset($files[$it_id]) && $files[$it_id]->isValid()) {
                $file = $files[$it_id];
                $folder = '/common_data/return_reception/';
                $filename = $req_id.'_'.$it_id.'_'.$file->getClientOriginalName();

                Storage::disk('sftp_remote')
                    ->put($folder.$filename, file_get_contents($file));
            }

            TbReturnReceptionModel::create([
                'req_id'   => $req_id,
                'mb_num'   => session('ss_mb_code') ?? 0,
                'mb_id'    => session('ss_mb_id') ?? '',
                'gubun'    => $request->gubun,
                'term'     => 'M',
                'name'     => $request->name,
                'company'  => $request->company,
                'email'    => '',
                'tel'      => '',
                'phone'    => $request->phone1.'-'.$request->phone2.'-'.$request->phone3,
                'title_no' => $it_id,
                'title'    => $item['title'],
                'cnt'      => $qty,
                'contents' => $comments[$it_id] ?? '',
                'file1'    => $filename,
                'addr'     => $request->addr,
                'url'      => '',
                'staff_code'         => '',
                'staff_name'         => '',
                'change_staff_code' => '',
                'change_staff_name' => '',
                'memo'               => '',
                'state'     => '1',
                'reg_date'  => now(),
                'modify_date' => now(),
            ]);
        }

        return redirect()->route('return_list')->with('success', '반품접수가 등록되었습니다');
    }


    public function MyPageQaWrite(Request $request) 
    {
        $items = app(ShopCartService::class)->MyRecetOrderItems($request);

        return view('customer_service.my_qa_write', compact('items'));
    }


    public function MyPageQaSave(Request $request)
    {
        $request->validate([
            'iq_gubun' => 'required|in:상품,배송,취소,반품/취소,교환,기타',
            'iq_subject' => 'required',
            'iq_question' => 'required',
        ], [
            'iq_gubun.required' => '분류값을 선택해주세요.',
            'iq_gubun.in' => '구분값 항목이 옳바르지 않습니다.',
            'iq_subject.required' => '제목을 입력하세요.',
            'iq_question.required' => '내용을 선택해주세요.',
        ]);

        $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));

        $idx = $request->input('idx', '');

        $save = [
            'it_id'  => $request->input('it_id') ?? '',
            'it_mb_num' => session('ss_mb_code') ?? '',
            'mb_id'  => session('ss_mb_id') ?? '',
            'iq_secret' => '0',
            'iq_gubun'  => $request->input('iq_gubun'),
            'iq_name'   => $member['mb_name'],
            'iq_email'  => $member['mb_email'],
            'iq_hp'     => $member['mb_hp'],
            'iq_password' => '',
            'iq_subject'  => $request->input('iq_subject'),
            'iq_question'  => $request->input('iq_question'),
            'iq_answer'    => '',
            'iq_time' => date('Y-m-d H:i:s'),
            'iq_ip' => $request->ip(),
        ];

        if (ShopItemQaModel::updateOrCreate(
            ['iq_id' => $idx], 
            $save)
        ) {
            return redirect('/customer_service/my_qa_list')->with('success', '상담문의가 등록되었습니다');
        } else {
            return redirect()->route('/customer_service/my_qa_write')->with('error', '상담문의 등록이 실패하였습니다.');
        }
    }


    public function MyPageContactWrite(Request $request)
    {
        $items = app(ShopCartService::class)->MyRecetOrderItems($request);


        return view('customer_service.my_contact_write', compact('items'));
    }


    public function MyPageContactSave(Request $request)
    {
        $request->validate([
            'iq_gubun' => 'required',
            'iq_hp1' => 'required|digits:3',
            'iq_hp2' => 'required|digits:4',
            'iq_hp3' => 'required|digits:4',
        ], [
            'iq_gubun.required' => '분류값을 선택해주세요.',
            'iq_hp1.required' => '번호를 입력해주세요.',
            'iq_hp1.digits' => '숫자 3자리를 입력해주세요.',

            'iq_hp2.required' => '번호를 입력해주세요.',
            'iq_hp2.digits' => '숫자 4자리를 입력해주세요.',

            'iq_hp3.required' => '번호를 입력해주세요.',
            'iq_hp3.digits' => '숫자 4자리를 입력해주세요.',
        ]);

        $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));

        $idx = $request->input('idx', '');

        $save = [
            'it_id'  => $request->input('it_id') ?? '',
            'it_mb_num' => session('ss_mb_code') ?? '',
            'mb_id'  => session('ss_mb_id') ?? '',
            'iq_secret' => '0',
            'iq_gubun'  => $request->input('iq_gubun'),
            'iq_name'   => $member['mb_name'],
            'iq_email'  => $request->iq_email ?? $member['mb_email'],
            'iq_hp'     => $request->iq_hp1.'-'.$request->iq_hp2.'-'.$request->iq_hp3 ?? $member['mb_hp'],
            'iq_password' => '',
            'iq_subject'  => $request->input('iq_subject'),
            'iq_question'  => $request->input('iq_question'),
            'iq_answer'    => '',
            'iq_time' => date('Y-m-d H:i:s'),
            'iq_ip' => $request->ip(),
        ];

        if (ShopItemContactModel::updateOrCreate(
            ['iq_id' => $idx], 
            $save)
        ) {
            return redirect('/customer_service/my_qa_list')->with('success', '1:1문의가 등록되었습니다');
        } else {
            return redirect()->route('/customer_service/my_contact_write')->with('error', '1:1문의가 등록이 실패하였습니다.');
        }
    }



    public function MyPageQaDel(Request $request)
    {
        $request->validate([
            'table' => 'required|in:item_qa,item_contact',
            'idx'   => 'required|numeric',
        ], [
            'table.required' => '테이블 정보가 존재하지 않습니다.',
            'table.in' => '허용되지 않은 테이블 정보입니다.',

            'idx.required' => '상담문의 번호가 존재하지 않습니다.',
            'idx.numeric' => '상담문의 번호가 옳바른 형식이 아닙니다.',
        ]);


        $mb_code = session('ss_mb_code');

        if ($request->input('table') == 'item_qa') {
            $item = ShopItemQaModel::where('iq_id', $request->input('idx'))
            ->where('it_mb_num', $mb_code)
            ->exists();
        } else {
            $item = ShopItemContactModel::where('iq_id', $request->input('idx'))
            ->where('it_mb_num', $mb_code)
            ->exists();
        }

        if (!$item) {
            return redirect('/customer_service/my_qa_list')->with('error', '삭제할 상담문의가 존재하지 않습니다.');
        }

        if ($request->input('table') == 'item_qa') {
            $item = ShopItemQaModel::where('iq_id', $request->input('idx'))
            ->delete();
        } else {
            $item = ShopItemContactModel::where('iq_id', $request->input('idx'))
            ->delete();
        }

        if ($item) {
            return redirect('/customer_service/my_qa_list')->with('success', '상담문의가 삭제되었습니다');
        } else {
            return redirect('/customer_service/my_qa_list')->with('error', '상담문의 삭제가 실패하였습니다.');
        }
    }



    public function MyPageModifyView(Request $request)
    {
        $request->validate([
            'table' => 'required|in:item_qa,item_contact',
            'idx'   => 'required|numeric',
        ], [
            'table.required' => '테이블 정보가 존재하지 않습니다.',
            'table.in' => '허용되지 않은 테이블 정보입니다.',

            'idx.required' => '상담문의 번호가 존재하지 않습니다.',
            'idx.numeric' => '상담문의 번호가 옳바른 형식이 아닙니다.',
        ]);

        $mb_code = session('ss_mb_code');

        if ($request->input('table') == 'item_qa') {
            $item = ShopItemQaModel::where('iq_id', $request->input('idx'))
            ->where('it_mb_num', $mb_code)
            ->exists();
        } else {
            $item = ShopItemContactModel::where('iq_id', $request->input('idx'))
            ->where('it_mb_num', $mb_code)
            ->exists();
        }


        if (!$item) {
            return redirect('/customer_service/my_qa_list')->with('error', '삭제할 상담문의가 존재하지 않습니다.');
        }

        $items = app(ShopCartService::class)->MyRecetOrderItems($request);


        $view_name = $request->input('table') == 'item_qa' 
            ? 'customer_service.my_qa_write'
            : 'customer_service.my_contact_write';

        $info = $request->input('table') == 'item_qa' 
            ? ShopItemQaModel::where('iq_id', $request->input('idx'))->first()
            : ShopItemContactModel::where('iq_id', $request->input('idx'))->first();



        return view($view_name, [
                'idx'   => $request->input('idx'),
                'info'  => $info,
                'items' => $items
            ]);
    }



    /**
     * Constructor
     * Description : 퀵 메뉴 문의하기 등록
     * Author : Kim Hairyong 
     * Created Date : 2026-02-10
     * Params : Params
     * History :
     *   - 2026-02-10 : Initial creation
     */        
    public function ContactUsSave(Request $request)
    {
        $request->validate([
            'contact_company'  => 'required',
            'contact_phone'    => 'required',
            'contact_contents' => 'required',
        ],[
            'contact_company.required'  => '업체명을 입력해주세요.',
            'contact_phone.required'    => '연락처를 입력해주세요.',
            'contact_contents.required' => '문의 내용을 입력해주세요.',
        ]);

        //허니팟 체크 (봇 자동 등록 방지)
        if ($request->filled('check_bot')) {
            return redirect()->route('/');
        }        

        $service = app(MallShopService::class);
        $member = $service->getMemberInfo(session('ss_mb_code'));

        $req_id = "A-".time();
        DB::table('tb_contact_us')->insert([
            'req_id'       => $req_id,
            'mb_num'       => $member['mb_num'] ?? '',
            'mb_id'        => $member['mb_id'] ?? '',
            'gubun'        => $request->input('gubun') ?? '',
            'term'         => 'M',
            'name'         => $member['mb_name'] ?? '',
            'company'      => $request->input('contact_company'),
            'email'        => $member['mb_email'] ?? '',
            'tel'          => $request->input('tel') ?? '',
            'phone'        => $request->input('contact_phone'),
            'title_no'     => $request->input('title_no') ?? '',
            'title'        => $request->input('title') ?? '',
            'cnt'          => $request->input('cnt') ?? '',
            'addr'         => $request->input('addr') ?? '',
            'contents'     => $request->input('contact_contents'),
            'url'          => $request->input('url') ?? '',
            'file1'        => $request->input('file1') ?? '',
            'file2'        => $request->input('file2') ?? '',
            'memo'         => $request->input('memo') ?? '',
            'state'        => '1',
            'reg_date'     => now(),
            'modify_date'  => now(),
        ]);

        return redirect()->back()->with('success', '문의내용이 등록되었습니다.');

    }



}
