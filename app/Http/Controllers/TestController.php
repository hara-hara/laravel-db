<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class TestController extends Controller
{
  function postData(Request $request)
  {
    Excel::Import(new UsersImport, $request->updateFile);
    return view('next');
  }
}
?>


