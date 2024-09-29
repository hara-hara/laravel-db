<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.jp/docs/5.3/assets/css/docs.css" rel="stylesheet"> 

    <style type="text/css">
    body {
        font-family: "Helvetica Neue",
            Arial,
            "Hiragino Kaku Gothic ProN",
            "Hiragino Sans",
            Meiryo,
            sans-serif;
    }
    </style>
    <link rel="stylesheet" href="{{ asset('/css/style.css')  }}" >
    <title>入力システム</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   </head>
 
  <body class="p-3 m-0 border-0 bd-example m-0 border-0 bd-example-cssgrid">
  <div><x-message :message="session('message')" /></div>
    <div class="container">
    <h1 style="font-size:1.25rem;">勤怠入力システム</h1>
      <main class="py-4">
        <div class="container">
          <div class="row">
            <div class="col-12 col-md-4 col-lg-3">
            @include('layouts.sidebar')
            </div>
            <div class="col-12 col-md-8  col-lg-9">                 
            @yield('content')
            </div>
          </div>
        </div>
      </main>
    </div>

  </body>
</html>