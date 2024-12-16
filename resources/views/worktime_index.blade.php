@extends('app')
  
@section('content')
    <div class=" w-screen  h-full relative flex flex-col">
        <header class="h-20 bg-green-500 flex items-center justify-center">
            <div ><h1 class="text-white text-xl">勤怠管理システム</h1></div>
        </header>
        <main class="flex h-full">
            <aside class="w-1/5 bg-green-200">
                @include('layouts.sidebar')
            </aside>
            <section class="w-4/5 bg-green-100">
                <h2 class="mx-1 my-2 text-2xl"><b>勤務表</b></h2>
                <table class="mx-1 my-2">
                    <tr class="matrix-header">
                        <th class="px-2">従業員番号</th>
                        <th class="px-2">名前</th>
                        <th class="px-2">勤務タイプ</th>
                        <th class="px-2">年月</th>
                        <th class="px-2">申請日</th>
                        <th class="px-2">承認日</th>
                        <th class="px-2">承認者ID</th>
                    </tr>
                    <tr>
                        <td class="matrix-normal"></td>
                        @foreach ($users as $user)
                            @if($user->id==$cur_user_id)
                                <td class="matrix-normal">
                                    {{$user->name}}
                                </td>
                            @endif
                        @endforeach
                        <td class="matrix-normal"></td>
                        <td class="matrix-normal"></td>
                        <td class="matrix-normal"></td>
                        <td class="matrix-normal"></td>
                        <td class="matrix-normal"></td>
                    </tr>
                </table>

                <form id="change_koushin" action="{{ route('worktime.update',['cur_user_id'=>$cur_user_id]) }}" method="POST">
                    @method('PUT')
                    @csrf
                    <div class="my-4">
                        <button class="btn btn-orange">
                            <a href="#" data-id="koushin" onclick="changePost(this)" class="btn btn-primary w-10">更新</a> <!-- 更新ボタンクリックでメッセージを出す -->
                        </button>
                        <button class="btn btn-blue">
                            <a class="btn btn-success" href="{{ route('worktime.index') }}/?cur_user_id=1&cur_year={{$cur_year}}&cur_month={{$cur_month-1}}">＜　前月</a>
                        </button>
                        <button class="btn btn-blue">
                            <a class="btn btn-success" href="{{ route('worktime.index') }}/?cur_user_id=1&cur_year={{$cur_year}}&cur_month={{$cur_month+1}}">次月　＞</a>
                        </button>
                        <button class="btn btn-green">
                            <a class="btn btn-success" href="{{ route('pdf') }}">PDF出力</a>
                        </button>
                    </div>



                    <!-- 勤怠表メイン -->

                    <p class="mx-1 my-2 text-2xl"><b>{{$cur_year}}年{{$cur_month}}月</b></p>
                    <input type="hidden" name="cur_year" value="{{$cur_year}}">
                    <input type="hidden" name="cur_month" value="{{$cur_month}}">


                    <table class="mx-1 my-2">
                        <tr class="matrix-header">
                            <th class="px-2">出勤日数</th>
                            <th class="px-2">欠勤日数</th>
                            <th class="px-2">有休取得日数</th>
                            <th class="px-2">有休取得時間(H)</th>
                            <th class="px-2">必要総就業時間(H)</th>
                        </tr>
                        <tr>
                            <td class="text-center matrix-normal">{{ $workday_counts }}</td>
                            <td class="text-center matrix-normal">{{ $v_dayoff_counts }}</td>
                            <td class="text-center matrix-normal">{{ $use_dayoff_counts }}</td>
                            <td class="text-center matrix-normal">{{ $use_dayoff_hours }}</td>
                            <td class="text-center matrix-normal">{{ $need_total_worktimes_hours }}</td>
                        </tr>
                        <tr class="matrix-header">
                            <th class="px-2">総労働時間(H)</th>
                            <th class="px-2">総残業(H)</th>
                            <th class="px-2">総法定内残業(H)</th>
                            <th class="px-2">総法定外残業(H)</th>
                            <th class="px-2">総就業時間(H)</th>
                        </tr>
                        <tr>
                            <td class="text-center matrix-normal">{{ $total_work_hours }}</td>
                            <td class="text-center matrix-normal">{{ $total_overtime_hours }}</td>
                            <td class="text-center matrix-normal">{{ $total_law_time_hours }}</td>
                            <td class="text-center matrix-normal">{{ $total_law_time_outer_hours }}</td>
                            <td class="text-center matrix-normal">{{ $total_worktime_hours }}</td>
                        </tr>
                    </table>
                                        
                    <table class="mx-1 my-2 border-separate border border-slate-900">
                        <tr class="">
                            <th rowspan="2" class="matrix-normal">日</th>
                            <th rowspan="2" class="matrix-normal">曜日</th>
                            <th rowspan="2" class="matrix-normal">区分</th>
                            <th colspan="2" class="matrix-normal">打刻時刻</th>
                            <th colspan="2" class="matrix-normal">就業時刻</th>
                            <th colspan="4" class="matrix-normal">就業時間</th>
                        </tr>
                        <tr>
                            <th class="matrix-normal">出社</th>
                            <th class="matrix-normal">退社</th>
                            <th class="matrix-normal">開始打刻</th>
                            <th class="matrix-normal">終了打刻</th>
                            <th class="matrix-normal">労働時刻</th>
                            <th class="matrix-normal">法定内残業</th>
                            <th class="matrix-normal">法定外残業</th>
                            <th class="matrix-normal">残業</th>
                            <th class="matrix-normal">更新</th>
                            <th class="matrix-normal">承認</th>
                        </tr>
                        <?php $ii=0; ?>
                        @if($worktimes!=null)
                            @foreach ($worktimes as $worktime)
                                <?php $ii++; ?>
                                <tr>
                                    <td class="text-center matrix-normal">{{ date('j',strtotime($worktime->work_date)) }}</td>
                                    <td class="text-center matrix-normal">
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
                                    <td class="matrix-normal">
                                        <select class="py-1 block w-full rounded-md border-gray-300 shadow-sm text-xs text-gray-800 bg-white focus:ring-indigo-500 focus:border-indigo-500" name="work_type-{{ date('j',strtotime($worktime->work_date)) }}">
                                            <option value="">選択して下さい</option>
                                            @foreach ($worktypes as $worktype)
                                                <option value="{{ $worktype->id }}" @if($worktype->id==$worktime->work_type) selected @endif>{{ $worktype->str }}</otion>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="hidden-work_type-{{ date('j',strtotime($worktime->work_date)) }}" value="{{$worktime->work_type}}">

                                    </td>
                                    <td class="matrix-normal">
                                        <input class="w-20 h-6" type="text" name="result_work_start-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_start <> ''){{ date('G:i',strtotime($worktime->result_work_start)) }}@endif"  placeholder="">
                                        <input type="hidden" name="hidden-result_work_start-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_start <> '' ) {{ date('G:i',strtotime($worktime->result_work_start)) }}@endif" >
                                        @error('work_start')
                                        <span style="color:red;">名前を20文字以内で入力してください</span>
                                        @enderror
                                    </td>
                                    <td class="matrix-normal">
                                        <input class="w-20 h-6" type="text" name="result_work_end-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_end <> '') {{ date('G:i',strtotime($worktime->result_work_end)) }} @endif"  placeholder="">
                                        <input type="hidden" name="hidden_result_work_end-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_end <> '') {{ date('G:i',strtotime($worktime->result_work_end)) }} @endif" >
                                        @error('work_end')
                                            <span style="color:red;">名前を20文字以内で入力してください</span>
                                        @enderror
                                    </td>

                                    <td class="matrix-normal">@if($worktime->real_work_start<>"") {{ date('G:i',strtotime($worktime->real_work_start)) }} @endif</td>


                                    <td class="matrix-normal">@if($worktime->real_work_end<>"") {{ date('G:i',strtotime($worktime->real_work_end)) }} @endif</td>

                                    <td class="matrix-normal">
                                        @if($worktime->result_work_start<>"" && $worktime->result_work_end<>"")
                                            {{ $w_time_results[$ii]['roudou_time'] }} 
                                        @endif
                                    </td>
                                    <td class="matrix-normal">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['houteinai_time'] }} @endif</td>
                                    <td class="matrix-normal">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['houteigai_time'] }} @endif</td>
                                    <td class="matrix-normal">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['zangyou_time'] }} @endif</td>
                                    <td class="matrix-normal">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['zangyou_time'] }} @endif</td>
                                    <td class="matrix-normal">@if($worktime->result_work_start<>"" && $worktime->result_work_end<>"") {{ $w_time_results[$ii]['zangyou_time'] }} @endif</td>
                                </tr>
                            @endforeach
                        @else
                            @for($j=1;$j<=$month_lastday;$j++)
                                <?php $ii++; ?>
                                <tr>
                                    <td class="matrix-normal">{{$j}}</td>
                                    <td class="matrix-normal">
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
                                    <td class="matrix-normal">
                                        <select name="work_type-{{$j}}">
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

                                    
                                    <td class="matrix-normal">
                                        <input type="text" name="result_work_start-{{$j}}" value="" placeholder="">
                                        <input type="hidden" name="hidden-result_work_start-{{$j}}" value="" >
                                        @error('work_start')
                                        <span style="color:red;">名前を20文字以内で入力してください</span>
                                        @enderror
                                    </td>
                                    
                                    <td class="matrix-normal">
                                    <input type="text" name="result_work_end-{{$j}}" value="" placeholder="">
                                    <input type="hidden" name="hidden_result_work_end-{{$j}}" value="" >
                                        @error('work_end')
                                            <span style="color:red;">名前を20文字以内で入力してください</span>
                                        @enderror
                                    </td>
                                    
                                    <td class="matrix-normal"></td>
                                    <td class="matrix-normal"></td>
                                    <td class="matrix-normal"></td>
                                    <td class="matrix-normal"></td>
                                    <td class="matrix-normal"></td>
                                    <td class="matrix-normal"></td>
                                    <td class="matrix-normal"></td>
                                    <td class="matrix-normal"></td>
                                    <td class="matrix-normal"></td>
                                </tr>
                            @endfor
                        @endif
                    </table>
                </form>
            </section>
        </main>
    </div>
>


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