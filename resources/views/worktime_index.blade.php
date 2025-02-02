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
                        <th class="px-2">申請日</th>
                        <th class="px-2">承認日</th>
                        <th class="px-2">承認者者</th>

                        
                    </tr>
                    <tr>
                        @foreach ($users as $user)
                            @if($user->id==$cur_member_id)
                            <td class="text-center matrix-normal">{{$user->member_no}}</td>
                            <td class="text-center matrix-normal">{{$user->name}}</td>
                            @endif
                        @endforeach
                        <td class="text-center matrix-normal">{{ $master_worktime_type->id }}</td>
                        <td class="text-center matrix-normal"></td>
                        <td class="text-center matrix-normal"></td>
                        <td class="text-center matrix-normal"></td>


                    </tr>
                </table>
                <table>
                    <tr class="matrix-header">
                        <th class="px-2">可能就業時間帯</th>
                        <th class="px-2">基本就業時間帯</th>
                        <th class="px-2">基本就業時間(H)</th>
                        <th class="px-2">休憩時間(H)</th>
                        <th class="px-2">有休(H)</th>
                        <th class="px-2">AM半休(H)</th>
                        <th class="px-2">PM半休(H)</th>
                    </tr>
                    <tr>
                        <td class="text-center matrix-normal">{{ $able_worktime_start }}～{{ $able_worktime_end }}</td>
                        <td class="text-center matrix-normal">{{ $basic_worktime_start }}～{{ $basic_worktime_end }}</td>
                        <td class="text-center matrix-normal">{{ $basic_worktimes }}</td>
                        <td class="text-center matrix-normal">{{ $master_worktime_type->lunch_break_times }}</td>
                        <td class="text-center matrix-normal">{{ $master_worktime_type->dayoff_times }}</td>
                        <td class="text-center matrix-normal">{{ $master_worktime_type->morningoff_times }}</td>
                        <td class="text-center matrix-normal">{{ $master_worktime_type->afternoonoff_times }}</td>
                    </tr>
                </table>

                <form id="change_koushin" action="{{ route('worktime.update',['cur_member_id'=>$cur_member_id]) }}" method="POST">
                    @method('PUT')
                    @csrf
                    <div class="my-4">
                        <button class="btn btn-orange">
                            <a href="#" data-id="koushin" onclick="changePost(this)" class="btn btn-primary w-10">更新</a> <!-- 更新ボタンクリックでメッセージを出す -->
                        </button>
                        <button class="btn btn-blue">
                            <a class="btn btn-success" href="{{ route('worktime.index') }}/?cur_member_id={{$cur_member_id}}&cur_year={{$cur_year}}&cur_month={{$cur_month-1}}">＜　前月</a>
                        </button>
                        <button class="btn btn-blue">
                            <a class="btn btn-success" href="{{ route('worktime.index') }}/?cur_member_id={{$cur_member_id}}&cur_year={{$cur_year}}&cur_month={{$cur_month+1}}">次月　＞</a>
                        </button>
                        <button class="btn btn-green">
                            <a class="btn btn-success" href="{{ route('pdf') }}/?cur_member_id={{$cur_member_id}}&cur_year={{$cur_year}}&cur_month={{$cur_month}}">PDF出力</a>
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
                            <th colspan="2" class="matrix-normal">出退時刻</th>
                            <th colspan="2" class="matrix-normal">就業時刻</th>
                            <th colspan="4" class="matrix-normal">就業時間</th>
                        </tr>
                        <tr>
                            <th class="matrix-normal">出社</th>
                            <th class="matrix-normal">退社</th>
                            <th class="matrix-normal w-20">開始</th>
                            <th class="matrix-normal w-20">終了</th>
                            <th class="matrix-normal">労働時間</th>
                            <th class="matrix-normal">法定内残業</th>
                            <th class="matrix-normal">法定外残業</th>
                            <th class="matrix-normal">残業</th>
                            {{--
                            <th class="matrix-normal">更新</th>
                            <th class="matrix-normal">承認</th>
                            --}}
                        </tr>
                        @for($ii=1;$ii<=$month_lastday;$ii++)
                            <tr class="text-center">
                                <td class="text-center matrix-normal">{{$ii}}</td>
                                    <td class="text-center matrix-normal">
                                        @switch(date('w',strtotime($cur_year."-".$cur_month."-".$ii) ))
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
                                        <select class="py-1 block w-full rounded-md border-gray-300 shadow-sm text-xs text-gray-800 bg-white focus:ring-indigo-500 focus:border-indigo-500" name="work_type-{{ $ii }}">
                                            <option value="">選択して下さい</option>
                                            @foreach ($worktypes as $worktype)
                                                <option value="{{ $worktype->id }}" @if($worktype->id == $w_time_results[$ii]['work_type']) selected @endif >
                                                    {{ $worktype->str }} 
                                                </otion>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="hidden-work_type-{{ $ii }}" value="{{ $w_time_results[$ii]['work_type'] }}">

                                    </td>
                                    <td class="matrix-normal">
                                        <input class="w-20 h-6" type="text" name="result_work_start-{{ $ii }}" value="{{ $w_time_results[$ii]['result_work_start']  }}"  placeholder="">
                                        <input type="hidden" name="hidden-result_work_start-{{ $ii }}" value="{{ $w_time_results[$ii]['result_work_start']  }}">
                                        @error('temp_result_work_start')
                                            <span style="color:red;">時間の形式で入力して下さい</span>
                                        @enderror
                                    </td>
                                    <td class="matrix-normal">
                                        <input class="w-20 h-6" type="text" name="result_work_end-{{ $ii }}" value="{{ $w_time_results[$ii]['result_work_end']  }}"  placeholder="">
                                        <input type="hidden" name="hidden_result_work_end-{{ $ii }}" value="{{ $w_time_results[$ii]['result_work_end']  }}">
                                        @error('temp_result_work_end')
                                            <span style="color:red;">時間の形式で入力して下さい</span>
                                        @enderror
                                    </td>

                                    @if($w_time_results[$ii]['result_work_start']  <> "" && $w_time_results[$ii]['result_work_end']  <> "")
                                        <td class="matrix-normal">{{ $w_time_results[$ii]['real_work_start'] }}</td>
                                        <td class="matrix-normal">{{ $w_time_results[$ii]['real_work_end'] }}</td>
                                        <td class="matrix-normal">{{ $w_time_results[$ii]['roudou_time'] }}</td>
                                        <td class="matrix-normal">{{ $w_time_results[$ii]['houteinai_time'] }}</td>
                                        <td class="matrix-normal">{{ $w_time_results[$ii]['houteigai_time'] }}</td>
                                        <td class="matrix-normal">{{ $w_time_results[$ii]['zangyou_time'] }}</td>
                                        {{--
                                        <td class="matrix-normal"></td>
                                        <td class="matrix-normal"></td>
                                        --}}
                                    @else
                                        <td class="matrix-normal"></td>    
                                        <td class="matrix-normal"></td>    
                                        <td class="matrix-normal"></td>    
                                        <td class="matrix-normal"></td>    
                                        <td class="matrix-normal"></td>    
                                        <td class="matrix-normal"></td>
                                        {{--
                                        <td class="matrix-normal"></td>    
                                        <td class="matrix-normal"></td>
                                        --}}    
                                    @endif
                            </tr>
                        @endfor
                    </table>
                </form>
            </section>
        </main>
    </div>



    <script>
        <!-- 更新ボタンクリックでメッセージを出す --> 	
        {{-- ここのコメントはHTML上には表示されません --}}

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