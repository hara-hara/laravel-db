@extends('app')
   
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2 style="font-size:1rem;">文房具詳細画面</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ url('/worktimes') }}?page={{ $page_id }}">戻る</a>
            </div>
        </div>
    </div>
    
    <div style="text-align:left;">
        <div class="row">
            <div class="col-12 mb-2 mt-2">
                <div class="form-group">
                    {{ $worktime->name }}                
                </div>
            </div>
            <div class="col-12 mb-2 mt-2">
                <div class="form-group">
                    {{ $worktime->kakaku }}                
                </div>
            </div>
            <div class="col-12 mb-2 mt-2">
                <div class="form-group">
                    @foreach ($worktypes as $worktype)
                        @if($worktype->id==$worktime->worktype) {{ $worktype->str }} @endif
                    @endforeach         
                </div>
            </div>
            <div class="col-12 mb-2 mt-2">
                <div class="form-group">
                {{ $worktime->shosai }}                
                </div>
            </div>
        </div>
    </div>
@endsection