<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommonController extends Controller
{
    //
    public function fileDonwload(Request $request)
    {
        $request->validate([
            'path' => 'required:in:recipe',
            'file_name' => 'required'
        ], [
            'path.required' => '경로는 필수 입력입니다.',
            'path.in' => '존재하지 경로 입니다.',
            'file_name.required' => '파일명은 필수 입력입니다.',
        ]);


        try {
            $file_name = $request->input('file_name');
            $path = $request->input('path');

            $dir  = [
                'recipe' => 'images/recipe/chios/'
            ];
            
            if (isset($dir[$path])) {
                $real = $dir[$path].$file_name;
                if (public_path($real)) {
                    return response()->download($real);
                } else {
                    return redirect()->back()->with('error', '파일이 존재하지 않습니다.');
                }
            } else {
                return redirect()->back()->with('error', '파일이 존재하지 않습니다.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '파일이 존재하지 않습니다.');
        }
    }
}
