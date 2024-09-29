@extends('app')
   
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2 style="font-size:1rem;">勤怠登録画面</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ url('/worktimes') }}">戻る</a>
            </div>
        </div>
    </div>
    
    <div style="text-align:right;">
    <form action="{{ route('worktime.store') }}" method="POST">
        @csrf
        @for($i=1 ; $i<30 ; $i++)
            <tr>
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
                <style>
                    .td_colsize {
                        width: 100px;
                        table-layout: auto;
                    }
                </style>

                <td class="td_colsize" style="text-align:center">
                    <select  name="work_type-{{ date('j',strtotime($worktime->work_date)) }}" class="form-select">
                        <option>分類を選択してください</otion>
                        @foreach ($worktypes as $worktype)
                            <option value="{{ $worktype->id }}"@if($worktype->id==$worktime->work_type) selected @endif>{{ $worktype->str }}</otion>
                        @endforeach
                    </select>
                    @error('work_type')
                    <span style="color:red;">分類を選択してください</span>
                    @enderror
                </td>

                <td style="text-align:center"> {{ date('G:i',strtotime($worktime->real_work_start)) }} </td>

                <td class="td_colsize" style="text-align:right">
                    <input type="text"  size="1" name="result_work_start-{{ date('j',strtotime($worktime->reault_work_date)) }}" value="@if($worktime->result_work_start<>"") {{ date('G:i',strtotime($worktime->result_work_start)) }} @endif" class="form-control" placeholder="">
                    @error('work_start')
                    <span style="color:red;">名前を20文字以内で入力してください</span>
                    @enderror
                </td>

                <td class="td_colsize" style="text-align:right">
                    <input type="text"  size="1" name="result_work_end-{{ date('j',strtotime($worktime->work_date)) }}" value="@if($worktime->result_work_end<>"") {{ date('G:i',strtotime($worktime->result_work_end)) }} @endif" class="form-control" placeholder="">
                    @error('work_end')
                    <span style="color:red;">名前を20文字以内で入力してください</span>
                    @enderror
                </td>

                <td style="text-align:center"> {{ date('G:i',strtotime($worktime->real_work_end)) }} </td>

                <td style="text-align:center">@if($worktime->real_work_start<>"" && $worktime->real_work_end<>"") {{ $w_time_results[$ii]['roudou_time'] }} @endif</td>
                <td style="text-align:center">@if($worktime->real_work_start<>"" && $worktime->real_work_end<>"") {{ $w_time_results[$ii]['houteiNai_time'] }} @endif</td>
                <td style="text-align:center">@if($worktime->real_work_start<>"" && $worktime->real_work_end<>"") {{ $w_time_results[$ii]['houteiGai_time'] }} @endif</td>
                <td style="text-align:center">@if($worktime->real_work_start<>"" && $worktime->real_work_end<>"") {{ $w_time_results[$ii]['zangyou_time'] }} @endif</td>
            </tr>
        @endfor     
        <div class="row">
            <div class="col-12 mb-2 mt-2">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="名前">
                    @error('name')
                        <span style="color:red;">名前を20文字以内で入力してください</span>
                    @enderror
                </div>
            </div>
            
            <div class="col-12 mb-2 mt-2">
                <div class="form-group">
                    <select name="worktype" class="form-select">
                        <option>分類を選択してください</option>
                        @foreach ($worktypes as $worktype)
                            <option value="{{ $worktype->id }}">{{ $worktype->str }}</option>
                        @endforeach
                    </select>
                    @error('worktype')
                            <span style="color:red;">分類を選択してください</span>
                    @enderror
                </div>
            </div>

            <div class="col-12 mb-2 mt-2">
                <div class="form-group">
                    <input type="text" name="kakaku" class="form-control" placeholder="価格">
                    @error('kakaku')
                            <span style="color:red;">価格を数字で入力してください</span>
                    @enderror
                </div>
            </div>
            <div class="col-12 mb-2 mt-2">
                <div class="form-group">
                <textarea class="form-control" style="height:100px" name="shosai" placeholder="詳細"></textarea>
                @error('shosai')
                    <span style="color:red;">詳細を140文字以内で入力してください</span>
                @enderror
                </div>
            </div>
            <div class="col-12 mb-2 mt-2">
                    <button type="submit" class="btn btn-primary w-100">登録</button>
            </div>
        </div>      
    </form>
    </div>
@endsection