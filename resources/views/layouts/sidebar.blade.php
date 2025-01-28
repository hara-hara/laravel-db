<!-- 
registしたユーザをDBから確認して一覧を作る    
-->

<div> 
    <div class="font-bold">従業員リスト</div>
    @foreach ($users as $user)
        <a href="{{route('worktime.index')}}?cur_member_id={{$user->id}}&cur_year={{$cur_year}}&cur_month={{$cur_month}}">
            <div class="p-2 m-2">{{$user -> name}}</div>
        </a>
    @endforeach
</div>