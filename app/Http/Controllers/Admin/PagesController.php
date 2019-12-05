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
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'en_title' => 'required|unique:pages,cat_en_title,' . $request->input('id') . ',id',
			'ar_title' => 'required|unique:pages,cat_ar_title,' . $request->input('id') . ',id',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withInput()->withErrors($validator->errors());
		}

		$id = $request->input('id');
		$page = Page::findOrFail($id);

		$update = $page->update([
			'en_title' => $request->input('en_title'),
			'ar_title' => $request->input('ar_title')
		]);

		if ($update) {
			$request->session()->flash('success', 'Page has been updated successfully');
			return redirect()->route('pages.index');
		} else {
			$errors = array('Failed to update the page');
			return redirect()->back()->withInput()->withErrors($errors);
		}
	}
}
