<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbNewWin extends Model
{
    //
    protected $table = 'tb_new_win';
    
    protected $primaryKey = 'nw_id'; // PK 지정
    
    public $timestamps = false;  
    
    // protected $fillable = [
    //     'nw_begin_time', 
    //     'nw_end_time', 
    //     'nw_division', 
    //     'nw_device', 
    //     'state', 
    //     'rank_order'
    // ];
}
