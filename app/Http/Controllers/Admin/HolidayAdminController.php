<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;

class HolidayAdminController extends Controller
{
    public function index() {
        $holidays = Holiday::orderBy('start_date')->get();
        return view('admin.holidays.index', compact('holidays'));
    }

    public function create() { return view('admin.holidays.create'); }

    public function store(Request $r) {
        $r->validate(['name'=>'required','start_date'=>'required|date','end_date'=>'required|date|after_or_equal:start_date']);
        Holiday::create($r->only('name','start_date','end_date'));
        return redirect()->to('/admin/holidays')->with('ok','Ferien angelegt');
    }

    public function edit($id) {
        $holiday = Holiday::findOrFail($id);
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $r, $id) {
        $r->validate(['name'=>'required','start_date'=>'required|date','end_date'=>'required|date|after_or_equal:start_date']);
        $h = Holiday::findOrFail($id);
        $h->update($r->only('name','start_date','end_date'));
        return redirect()->to('/admin/holidays')->with('ok','Ferien aktualisiert');
    }

    public function destroy($id) {
        Holiday::findOrFail($id)->delete();
        return back()->with('ok','Ferien gelöscht');
    }
}
