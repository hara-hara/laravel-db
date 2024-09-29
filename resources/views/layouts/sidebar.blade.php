<!-- 
registしたユーザをDBから確認して一覧を作る    
-->

<div class="list-group"> 
    @foreach ($users as $user)
        <a href="{{route('worktime.index')}}?cur_user_id={{$user -> id}}&cur_year={{$cur_year}}&cur_month={{$cur_month}}" class='list-group-item'>
            <i class="fas fa-home pr-2"></i><span>{{$user -> name}}</span>
        </a>
    @endforeach
</div>