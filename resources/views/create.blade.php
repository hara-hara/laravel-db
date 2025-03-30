@extends('app')

@section('content')
<section class="text-gray-600 body-font">
    <header class="h-20 bg-green-500 flex items-center justify-center">
        <div>
            <h1 class="text-white text-xl">勤怠管理システム</h1>
        </div>
    </header>
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
            <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">メンバー情報登録</h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">ここはメンバーを登録及び編集するページです。</p>
        </div>





        <!-- Card Section -->
        <div class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
            <!-- Card -->
            <div class="bg-white rounded-xl shadow-xs p-4 sm:p-7 dark:bg-neutral-900">
                <form action="{{ route('bunbougu.store') }}" method="POST">
                    @csrf
                    <div
                        class="grid sm:grid-cols-12 gap-2 sm:gap-4 py-8 first:pt-0 last:pb-0 ">


                        <div class="sm:col-span-3">
                                <label for="af-submit-application-current-company"
                                    class="inline-block text-sm font-medium text-gray-500 mt-2.5 dark:text-neutral-500">
                                    ■名前
                                </label>
                        </div>
                        <!-- End Col -->
                        <div class="sm:col-span-9">
                            <input id="af-submit-application-current-company" name="name" type="text"
                                class="py-1.5 sm:py-2 px-3 pe-11 block w-full border-gray-200 shadow-2xs rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            @error('name')
                            <span style="color:red;">名前を20文字以内で入力してください</span>
                            @enderror
                        </div>
                        <!-- End Col -->



                        <div class="sm:col-span-3">
                            <div class="inline-block">
                                <label for="af-submit-application-current-company"
                                    class="inline-block text-sm font-medium text-gray-500 mt-2.5 dark:text-neutral-500">
                                    ■価格
                                </label>
                            </div>
                        </div>
                        <!-- End Col -->
                        <div class="sm:col-span-9">
                            <input id="af-submit-application-current-company" name="kakaku" type="text"
                                class="py-1.5 sm:py-2 px-3 pe-11 block w-full border-gray-200 shadow-2xs rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            @error('kakaku')
                            <span style="color:red;">価格を数字で入力してください</span>
                            @enderror
                        </div>
                        <!-- End Col -->

                        <div class="sm:col-span-3">
                            <div class="inline-block">
                                <label for="af-submit-application-bio"
                                    class="inline-block text-sm font-medium text-gray-500 mt-2.5 dark:text-neutral-500">
                                    ■分類
                                </label>
                            </div>
                        </div>
                        <!-- End Col -->

                        <div class="col-span-9">
                            <div class="form-group">
                                <select name="bunrui" class="py-1.5 sm:py-2 px-3 pe-11 block w-full border-gray-200 shadow-2xs rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <option>分類を選択してください</option>
                                    @foreach ($bunruis as $bunrui)
                                    <option value="{{ $bunrui->id }}">{{ $bunrui->str }}</option>
                                    @endforeach
                                </select>
                                @error('bunrui')
                                <span style="color:red;">分類を選択してください</span>
                                @enderror
                            </div>
                        </div>



                        <div class="sm:col-span-3">
                            <div class="inline-block">
                                <label for="af-submit-application-bio"
                                    class="inline-block text-sm font-medium text-gray-500 mt-2.5 dark:text-neutral-500">
                                    ■詳細
                                </label>
                            </div>
                        </div>
                        <!-- End Col -->

                        <div class="sm:col-span-9">
                            <textarea id="af-submit-application-bio" name="shosai"
                                class="py-1.5 sm:py-2 px-3 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                rows="6" placeholder="Add a cover letter or anything else you want to share.">
                            </textarea>
                            @error('shosai')
                            <span style="color:red;">詳細を140文字以内で入力してください</span>
                            @enderror
                        </div>
                        <!-- End Col -->
                    </div>
            </div>







            <div class="mt-5 flex justify-end gap-x-2">
                <div
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                    <a class="btn btn-success" href="{{ url('/bunbougus') }}">戻る</a>
                </div>
                <button type="button"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                    キャンセル
                </button>
                <input type="hidden" name="user_id" value="2"> <!-- ★ -->
                <button type="submit"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                    登録
                </button>
            </div>

            </form>
        </div>




        <div class="relative">
            <select data-hs-select='{
      "placeholder": "Select option...",
      "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
      "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
      "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto dark:bg-neutral-900 dark:border-neutral-700",
      "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
      "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-3.5 text-blue-600 dark:text-blue-500 \" xmlns=\"http:.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>"
    }'>
                <option value="">Choose</option>
                <option>Name</option>
                <option>Email address</option>
                <option>Description</option>
                <option>User ID</option>
            </select>

            <div class="absolute top-1/2 end-2.5 -translate-y-1/2">
                <svg class="shrink-0 size-4 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m7 15 5 5 5-5"></path>
                    <path d="m7 9 5-5 5 5"></path>
                </svg>
            </div>
        </div>





        <div style="text-align:right;">
            <form action="{{ route('bunbougu.store') }}" method="POST">
                @csrf

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
                            <select name="bunrui" class="form-select">
                                <option>分類を選択してください</option>
                                @foreach ($bunruis as $bunrui)
                                <option value="{{ $bunrui->id }}">{{ $bunrui->str }}</option>
                                @endforeach
                            </select>
                            @error('bunrui')
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
                            <textarea class="form-control" style="height:100px" name="shosai"
                                placeholder="詳細"></textarea>
                            @error('shosai')
                            <span style="color:red;">詳細を140文字以内で入力してください</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 mb-2 mt-2">
                        <input type="hidden" name="user_id" value="2"> <!-- ★ -->

                        <button type="submit" class="btn btn-primary w-100">登録</button>
                    </div>
                </div>
            </form>
        </div>








        @endsection