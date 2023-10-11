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
        </tr>
        <tr>
            <td style="text-align:right">1001</td>
        </tr>
    </table>

    <table class="table table-striped">
        <tr>
            <th class=".w-10" style="text-align:center">日</th>
            <th class=".w-10" style="text-align:center">曜日</th>
            <th class=".w-10" style="text-align:center">区分</th>
            <th class=".w-15" style="text-align:center">開始時刻</th>
            <th class=".w-15" style="text-align:center">終了時刻</th>
            <th class=".w-10" style="text-align:center">労働時刻</th>
            <th class=".w-10" style="text-align:center">法定内残業</th>
            <th class=".w-10" style="text-align:center">法定外残業</th>
            <th class=".w-10" style="text-align:center">残業</th>
        </tr>
        <?php $ii=0; ?>
        @foreach ($worktimes as $worktime)
        <?php $ii++; ?>
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
            <td style="text-align:center">{{ $worktime->work_type }}</td>
            <td style="text-align:center">{{ date('G:i',strtotime($worktime->work_start)) }}</td>
            <td style="text-align:center">{{ date('G:i',strtotime($worktime->work_end)) }}</td>
            <td style="text-align:center">{{ $w_time_results[$ii]['roudou_time'] }}</td>
            <td style="text-align:center">{{ $w_time_results[$ii]['houteiNai_time'] }}</td>
            <td style="text-align:center">{{ $w_time_results[$ii]['houteiGai_time'] }}</td>
            <td style="text-align:center">{{ $w_time_results[$ii]['zangyou_time'] }}</td>
        </tr>
        @endforeach
    </table>
 
    {!! $worktimes->links('pagination::bootstrap-5') !!}

@endsection