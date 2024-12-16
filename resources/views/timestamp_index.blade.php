@extends('app')
  
@section('content')

<h1>タイムスタンプ</h1>

<form id="change_koushin" action="{{ route('timestamp.update') }}" method="POST">
    @method('PUT')
    @csrf

    <div class="col-12 mb-2 mt-2">
        <p id="realtime"></p>
        <input type="submit" class="btn btn-primary w-10" name="button1" value="出勤">
        <input type="submit" class="btn btn-primary w-10" name="button2" value="退勤">
    </div>


    <p id="realtime"></p>
</form>
<script>
    function showClock() {
        let nowTime = new Date();
        let nowHour = nowTime.getHours();
        let nowMin  = nowTime.getMinutes();
        let nowSec  = nowTime.getSeconds();
        let msg = "現在時刻：" + nowHour + ":" + nowMin + ":" + nowSec;
        document.getElementById("realtime").innerHTML = msg;
    }
    setInterval('showClock()',1000);
</script>


@endsection
