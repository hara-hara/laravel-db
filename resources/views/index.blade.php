@extends('app')
  
@section('content')
<section class="text-gray-600 body-font">
<header class="h-20 bg-green-500 flex items-center justify-center">
            <div ><h1 class="text-white text-xl">勤怠管理システム</h1></div>
        </header>
  <div class="container px-5 py-24 mx-auto">
    <div class="flex flex-col text-center w-full mb-20">
      <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">メンバー登録</h1>
      <p class="lg:w-2/3 mx-auto leading-relaxed text-base">ここはメンバーを登録及び編集するページです。</p>
    </div>
    <div class="lg:w-2/3 w-full mx-auto overflow-auto">
      <table class="table-auto w-full text-left whitespace-no-wrap">
        <thead>
          <tr>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">No</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">名称</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">価格</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">分類</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">登録者</th>
            <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
            <th class="text-center w-30 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
          </tr>
        </thead>
        <tbody>
          <?php $i=0; ?>
          @foreach ($bunbougus as $bunbougu)
            <?php $i++; ?>
              <tr>
                @if($i==1)
                    <td class="px-4 py-3">{{ $bunbougu->id }}</td>
                    <td class="bpx-4 py-3"><a class="" href="{{ route('bunbougu.show',$bunbougu->id) }}?page_id={{ $page_id }}">{{ $bunbougu->name }}</a></td>
                    <td class="px-4 py-3">{{ $bunbougu->kakaku }}円</td>
                    <td class="px-4 py-3">{{ $bunbougu->bunrui }}</td>
                    <td class="px-4 py-3 text-lg text-gray-900">{{ $bunbougu->user->name }}</td>
                    @auth
                    <td class="w-20 text-center"><a class="btn btn-blue" href="{{ route('bunbougu.edit',$bunbougu->id) }}">変更</a></td>
                    <td class="w-20 text-center">
                        <form action="{{ route('bunbougu.destroy',$bunbougu->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-green" onclick='return confirm("削除しますか？");'>削除</button>
                        </form>
                    </td>
                    @endauth

                  @else
                    <td class="border-t-2 border-gray-200 px-4 py-3">{{ $bunbougu->id }}</td>
                    <td class="border-t-2 border-gray-200 px-4 py-3"><a class="" href="{{ route('bunbougu.show',$bunbougu->id) }}?page_id={{ $page_id }}">{{ $bunbougu->name }}</a></td>
                    <td class="border-t-2 border-gray-200 px-4 py-3">{{ $bunbougu->kakaku }}円</td>
                    <td class="border-t-2 border-gray-200 px-4 py-3">{{ $bunbougu->bunrui }}</td>
                    <td class="border-t-2 border-gray-200 px-4 py-3 text-lg text-gray-900">{{ $bunbougu->user->name }}</td>
                    @auth
                    <td class="border-t-2 border-gray-200 w-20 text-center"><a class="btn btn-blue" href="{{ route('bunbougu.edit',$bunbougu->id) }}">変更</a></td>
                    <td class="border-t-2 border-gray-200 w-20 text-center">
                        <form action="{{ route('bunbougu.destroy',$bunbougu->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-green" onclick='return confirm("削除しますか？");'>削除</button>
                        </form>
                    </td>
                    @endauth
                  @endif
                  
              </tr>
          @endforeach

        </tbody>
      </table>
    </div>
    <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">
      <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Learn More
        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
          <path d="M5 12h14M12 5l7 7-7 7"></path>
        </svg>
      </a>
      <button class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"><a href="{{ route('bunbougu.create') }}">新規登録</a></button>
    </div>
  </div>
</section>



    
    <section class="text-gray-600 body-font">
  <div class="container px-5 py-24 mx-auto">
    <div class="flex flex-col text-center w-full mb-20">
      <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1>
      <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Banh mi cornhole echo park skateboard authentic crucifix neutra tilde lyft biodiesel artisan direct trade mumblecore 3 wolf moon twee</p>
    </div>
    <div class="lg:w-2/3 w-full mx-auto overflow-auto">
      <table class="table-auto w-full text-left whitespace-no-wrap">
        <thead>
          <tr>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">Plan</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Speed</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Storage</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Price</th>
            <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-4 py-3">Start</td>
            <td class="px-4 py-3">5 Mb/s</td>
            <td class="px-4 py-3">15 GB</td>
            <td class="px-4 py-3 text-lg text-gray-900">Free</td>
            <td class="w-10 text-center">
              <input name="plan" type="radio">
            </td>
          </tr>
          <tr>
            <td class="border-t-2 border-gray-200 px-4 py-3">Pro</td>
            <td class="border-t-2 border-gray-200 px-4 py-3">25 Mb/s</td>
            <td class="border-t-2 border-gray-200 px-4 py-3">25 GB</td>
            <td class="border-t-2 border-gray-200 px-4 py-3 text-lg text-gray-900">$24</td>
            <td class="border-t-2 border-gray-200 w-10 text-center">
              <input name="plan" type="radio">
            </td>
          </tr>
          <tr>
            <td class="border-t-2 border-gray-200 px-4 py-3">Business</td>
            <td class="border-t-2 border-gray-200 px-4 py-3">36 Mb/s</td>
            <td class="border-t-2 border-gray-200 px-4 py-3">40 GB</td>
            <td class="border-t-2 border-gray-200 px-4 py-3 text-lg text-gray-900">$50</td>
            <td class="border-t-2 border-gray-200 w-10 text-center">
              <input name="plan" type="radio">
            </td>
          </tr>
          <tr>
            <td class="border-t-2 border-b-2 border-gray-200 px-4 py-3">Exclusive</td>
            <td class="border-t-2 border-b-2 border-gray-200 px-4 py-3">48 Mb/s</td>
            <td class="border-t-2 border-b-2 border-gray-200 px-4 py-3">120 GB</td>
            <td class="border-t-2 border-b-2 border-gray-200 px-4 py-3 text-lg text-gray-900">$72</td>
            <td class="border-t-2 border-b-2 border-gray-200 w-10 text-center">
              <input name="plan" type="radio">
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">
      <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Learn More
        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
          <path d="M5 12h14M12 5l7 7-7 7"></path>
        </svg>
      </a>
      <button class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Button</button>
    </div>
  </div>
</section>

@endsection