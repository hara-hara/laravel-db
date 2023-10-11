@extends('app')
   
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2 style="font-size:1rem;">勤務表変更画面</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ url('/worktimes') }}?page={{ $page_id }}">戻る</a>
            </div>
        </div>
    </div>
    
    <div style="text-align:right;">
        <form action="{{ route('worktime.update') }}" method="POST">
            @method('PUT')
            @csrf

            
            <table class="table table-bordered">
                <tr>
                    <th>日</th>
                    <th>区分</th>
                    <th>開始時刻</th>
                    <th>終了時刻</th>
                </tr>
                @foreach ($comm_worktimes as $worktime)
                <tr>
                    <td style="text-align:right">
                        <input type="text" name="work_date-{{ date('j',strtotime($worktime->work_date)) }}" value="{{ $worktime->work_date }}" class="form-control" placeholder="">
                        @error('work_date')
                        <span style="color:red;">名前を20文字以内で入力してください</span>
                        @enderror
                    </td>
                    <td style="text-align:right">
                        <div class="form-group">
                            <select name="work_type-{{ date('j',strtotime($worktime->work_date)) }}" class="form-select">
                                <option>分類を選択してください</otion>
                                @foreach ($worktypes as $worktype)
                                    <option value="{{ $worktype->id }}"@if($worktype->id==$worktime->work_type) selected @endif>{{ $worktype->str }}</otion>
                                @endforeach
                            </select>
                            @error('work_type')
                            <span style="color:red;">分類を選択してください</span>
                            @enderror
                        </div>
                    </td>

                    <td style="text-align:right">
                        <input type="text" name="work_start-{{ date('j',strtotime($worktime->work_date)) }}" value="{{ $worktime->work_start }}" class="form-control" placeholder="">
                        @error('work_start')
                        <span style="color:red;">名前を20文字以内で入力してください</span>
                        @enderror
                    </td>
                    <td style="text-align:right">
                        <input type="text" name="work_end-{{ date('j',strtotime($worktime->work_date)) }}" value="{{ $worktime->work_end }}" class="form-control" placeholder="">
                        @error('work_end')
                        <span style="color:red;">名前を20文字以内で入力してください</span>
                        @enderror
                    </td>
                </tr>
                @endforeach
            </table>

            {!! $comm_worktimes->links('pagination::bootstrap-5') !!}


            <div class="col-12 mb-2 mt-2">
                    <input type="hidden" name="page" value="{{ $page_id }}"> <!-- ★ -->
                    <button type="submit" class="btn btn-primary w-100">変更</button>
            </div>
        </form>
    </div>
@endsection