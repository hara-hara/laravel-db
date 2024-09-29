@extends('app')
  
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="text-left">
                <h2 style="font-size:1rem;">勤務表</h2>
            </div>
            <div class="text-right">
                <a class="btn btn-success" href="{{ route('worktime.create') }}">新規登録</a>
                <a class="btn btn-success" href="{{ route('worktime.edit','1') }}">変更編集</a>

            </div>
        </div>
    </div>


    <table class="table table-bordered">
        <tr>
            <th>名前</th>
            <th>可能就業時間(開始)</th>
            <th>可能就業時間(終了)</th>
            <th>基本就業時間(開始)</th>
            <th>基本就業時間(終了)</th>
            <th>昼休憩時間</th>
            <th>有休重み</th>
            <th>AM半休重み</th>
            <th>PM半休重み</th>
        </tr>
        <tr>
            <td style="text-align:left">
                @foreach ($users as $user)
                    @if($user->id==$cur_user_id) {{$user->name}} @endif
                @endforeach
            </td>
            <td style="text-align:left">{{ date('G:i',strtotime($master_worktime_type->able_worktime_start)) }}</td>
            <td style="text-align:left">{{ date('G:i',strtotime($master_worktime_type->able_worktime_end)) }}</td>
            <td style="text-align:left">{{ date('G:i',strtotime($master_worktime_type->basic_worktime_start)) }}</td>
            <td style="text-align:left">{{ date('G:i',strtotime($master_worktime_type->basic_worktime_end)) }}</td>
            <td style="text-align:left">{{ $master_worktime_type->lunch_break_times }}</td>
            <td style="text-align:left">{{ $master_worktime_type->dayoff_times }}</td>
            <td style="text-align:left">{{ $master_worktime_type->morningoff_times }}</td>
            <td style="text-align:left">{{ $master_worktime_type->aftenoonoff_times }}</td>
        </tr>
    </table>
    <table class="table table-bordered">
        <tr>
            <th>従業員ID</th>
            <th>年月</th>
            <th>申請日</th>
            <th>承認日</th>
            <th>承認者ID</th>
            <th>勤務総時間</th>
            <th>勤務タイプ</th>
            <th>出勤日数</th>
            <th>欠席日数</th>
            <th>有休取得日数</th>
            <th>有休取得時間(H)</th>
            <th>必要総就業時間(H)</th>
            <th>総労働時間(H)</th>
            <th>総残業(H)</th>
            <th>総法定内残業(H)</th>
            <th>総法定外残業(H)</th>
            <th>総就業時間(H)</th>
        </tr>
        <tr>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        <td style="text-align:left"></td>
        </tr>
    </table>

    <form id="change_koushin" action="{{ route('worktime.update',['cur_user_id'=>$cur_user_id]) }}" method="POST">
        @method('PUT')
        @csrf
    <div class="col-12 mb-2 mt-2">
        <a href="#" data-id="koushin" onclick="changePost(this)" class="btn btn-primary w-10">更新</a> <!-- 更新ボタンクリックでメッセージを出す -->
        <a class="btn btn-success" href="{{ route('worktime.index') }}/?cur_user_id=1&cur_year={{$cur_year}}&cur_month={{$cur_month-1}}">前月</a>
        <a class="btn btn-success" href="{{ route('worktime.index') }}/?cur_user_id=1&cur_year={{$cur_year}}&cur_month={{$cur_month+1}}">次月</a>
        <a class="btn btn-success" href="{{ route('pdf') }}">PDF出力</a>
    </div>
    <p><b>{{$cur_year}}年{{$cur_month}}月</b></p>
    <input type="hidden" name="cur_year" value="{{$cur_year}}">
    <input type="hidden" name="cur_month" value="{{$cur_month}}">
    
    <table class="table table-striped">
        <tr>
            <th class=".w-10" style="text-align:center">日</th>
            <th class=".w-10" style="text-align:center">曜日</th>
            <th class=".w-10" style="text-align:center">区分</th>
            <th class=".w-15" style="text-align:center">開始時刻</th>
            <th class=".w-15" style="text-align:center">終了時刻</th>
            <th class=".w-15" style="text-align:center">開始打刻</th>
            <th class=".w-15" style="text-align:center">終了打刻</th>
            <th class=".w-10" style="text-align:center">労働時刻</th>
            <th class=".w-10" style="text-align:center">法定内残業</th>
            <th class=".w-10" style="text-align:center">法定外残業</th>
            <th class=".w-10" style="text-align:center">残業</th>
            <th class=".w-10" style="text-align:center">更新</th>
            <th class=".w-10" style="text-align:center">承認</th>
        </tr>
        <?php $ii=0; ?>
        @if($worktimes!=null)
             @foreach ($worktimes as $worktime)
                <?php $ii++; ?>
                <style>
                </style>
                <tr class="tr_hight">
                    <td style="text-align:center">{{ date('j',strtotime($worktime->work_date)) }}</td>
                    <td style="text-align:center">
                        @switch(date('w',strtotime($worktime->work_date) ))
                        @case(0) <font color="red">日</font> @break
                        @case(1) 月 @break
                        @case(2) 火 @break
                        @case(3) 水 @break
                        @case(4) 木 @break
                        @case(5) 金 @break
                        @case(6) <font color="blue">土</font> @break
                        @endswitch
                    </td>
                    <td style="text-align:center">
                        <select class="select_hight" style="text-align:center" name="work_type-{{ date('j',strtotime($worktime->work_date)) }}">
                            <option value="">選択して下さい</option>
                            @foreach ($worktypes as $worktype)
                                <option value="{{ $worktype->id }}" @if($worktype->id==$worktime->work_type) selected @endif>{{ $worktype->str }}</otion>
                            @endforeach
                        </select>
                        <input type="hidden" name="hidden-work_type-{{ date('j',strtotime($worktime->work_date)) }}" value="{{$worktime->work_type}}">

                    </td>
                    <td style="text-align:center">
                        <input class="input_hight" style="text-align:center" type="text" name="result_work_start-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_start <> ''){{ date('G:i',strtotime($worktime->result_work_start)) }}@endif" class="form-control" placeholder="">
                        <input type="hidden" name="hidden-result_work_start-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_start <> '' ) {{ date('G:i',strtotime($worktime->result_work_start)) }}@endif" >
                        @error('work_start')
                        <span style="color:red;">名前を20文字以内で入力してください</span>
                        @enderror
                    </td>
                    <td style="text-align:center">
                    <input class="input_hight" style="text-align:center" type="text" name="result_work_end-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_end <> '') {{ date('G:i',strtotime($worktime->result_work_end)) }} @endif" class="form-control" placeholder="">
                    <input type="hidden" name="hidden_result_work_end-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_end <> '') {{ date('G:i',strtotime($worktime->result_work_end)) }} @endif" >
                    @error('work_end')
                        <span style="color:red;">名前を20文字以内で入力してください</span>
                        @enderror
                    </td>
                    
                    <td style="text-align:center">@if($worktime->real_work_start<>"") {{ date('G:i',strtotime($worktime->real_work_start)) }} @endif</td>

                    <td style="text-align:center">@if($worktime->real_work_end<>"") {{ date('G:i',strtotime($worktime->real_work_end)) }} @endif</td>

                    <td style="text-align:center">
                        @if($worktime->result_work_start<>"" && $worktime->result_work_end<>"")
                            {{ $w_time_results[$ii]['roudou_time'] }} 
                        @endif</td>
                    <td style="text-align:center">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['houteinai_time'] }} @endif</td>
                    <td style="text-align:center">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['houteigai_time'] }} @endif</td>
                    <td style="text-align:center">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['zangyou_time'] }} @endif</td>
                    <td style="text-align:center">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['zangyou_time'] }} @endif</td>
                    <td style="text-align:center">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['zangyou_time'] }} @endif</td>
                </tr>
            @endforeach
        @else
            @for($j=1;$j<=$month_lastday;$j++)
                <?php $ii++; ?>
                <tr class="tr_hight">
                    <td style="text-align:center">{{$j}}</td>
                    <td style="text-align:center">
                        @switch(date('w',strtotime($cur_year.'-'.$cur_month.'-'.$j) ))
                        @case(0) <font color="red">日</font> @break
                        @case(1) 月 @break
                        @case(2) 火 @break
                        @case(3) 水 @break
                        @case(4) 木 @break
                        @case(5) 金 @break
                        @case(6) <font color="blue">土</font> @break
                        @endswitch
                    </td>
                    <input type="hidden" name="work_date-{{$j}}" value="{{ $cur_year.'-'.$cur_month.'-'.$j }}">
                    <td style="text-align:center">
                        <select class="select_hight" style="text-align:center" name="work_type-{{$j}}">
                        <option value="">選択して下さい</option>
                        @foreach ($worktypes as $worktype)
                                <option value="{{ $worktype->id }}">{{ $worktype->str }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="hidden-work_type-{{$j}}" value="">
                        @error('work_type')
                        <span style="color:red;">分類を選択してください</span>
                        @enderror
                    </td>

                    
                    <td style="text-align:center">
                        <input class="input_hight" style="text-align:center" type="text" name="result_work_start-{{$j}}" value="" class="form-control" placeholder="">
                        <input type="hidden" name="hidden-result_work_start-{{$j}}" value="" >
                        @error('work_start')
                        <span style="color:red;">名前を20文字以内で入力してください</span>
                        @enderror
                    </td>
                    
                    <td style="text-align:center">
                    <input class="input_hight" style="text-align:center" type="text" name="result_work_end-{{$j}}" value="" class="form-control" placeholder="">
                    <input type="hidden" name="hidden_result_work_end-{{$j}}" value="" >
                        @error('work_end')
                            <span style="color:red;">名前を20文字以内で入力してください</span>
                        @enderror
                    </td>
                    
                    <td style="text-align:center"></td>
                    <td style="text-align:center"></td>
                    <td style="text-align:center"></td>
                    <td style="text-align:center"></td>
                    <td style="text-align:center"></td>
                    <td style="text-align:center"></td>
                    <td style="text-align:center"></td>
                    <td style="text-align:center"></td>
                    <td style="text-align:center"></td>

                </tr>
            @endfor
        @endif
    </table>
    </form>
 
    <script>
        <!-- 更新ボタンクリックでメッセージを出す -->
        function changePost(e){
                'use strict'
                if(confirm('更新しますか？')){
                    document.getElementById('change_' + e.dataset.id).submit();
                }
                else{
                    document.location.reload();
                }
        }
    </script>
@endsection