<?php

namespace App\Http\Controllers\Admin;

/**
 *
 * @author Mohamed Salah <mohamedsalah7191@gmail.com>
 */

use Log;
use App\Http\Controllers\Controller;
use App\Page;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;

class PagesController extends Controller
{
	public function __construct()
	{

	}

	public function index()
	{
		$pages = Page::get();
		return view('cpanel.pages.index', compact('pages'));
	}

	public function edit($id)
	{
		$page = Page::findOrFail($id);
		return view('cpanel.pages.edit', compact('page'));
	}

	public function update(Request $request)
	{
		$id = $request->input('id');

		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'en_title' => 'required|unique:pages,en_title,' . $id . ',id',
			'ar_title' => 'required|unique:pages,ar_title,' . $id . ',id',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withInput()->withErrors($validator->errors());
		}

		$page = Page::findOrFail($id);

		$update = $page->update([
			'en_title' => $request->input('en_title'),
			'ar_title' => $request->input('ar_title')
		]);

		if ($update) {
			$request->session()->flash('success', 'تم تحديث البيانات بنجاح.');
			return redirect()->route('pages.index');
		} else {
			$errors = ['فشل التحديث, يرجى المحاولة لاحقاً !!'];
			return redirect()->back()->withInput()->withErrors($errors);
		}
	}
}
