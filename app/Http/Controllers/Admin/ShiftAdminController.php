<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;

class ShiftAdminController extends Controller
{
    public function index() {
        $shifts = Shift::orderBy('id')->get();
        return view('admin.shifts.index', compact('shifts'));
    }
    public function create() {
        return view('admin.shifts.create');
    }
    public function store(Request $r) {
        $r->validate([
            'name'  => 'required|string|max:100',
            'color' => ['nullable','regex:/^#([0-9a-fA-F]{6})$/']
        ]);
        Shift::create($r->only('name','color'));
        return redirect('/admin/shifts')->with('ok','Schicht angelegt.');
    }
    public function edit($id) {
        $shift = Shift::findOrFail($id);
        return view('admin.shifts.edit', compact('shift'));
    }
    public function update(Request $r, $id) {
        $r->validate([
            'name'  => 'required|string|max:100',
            'color' => ['nullable','regex:/^#([0-9a-fA-F]{6})$/']
        ]);
        $shift = Shift::findOrFail($id);
        $shift->update($r->only('name','color'));
        return redirect('/admin/shifts')->with('ok','Schicht aktualisiert.');
    }
    public function destroy($id) {
        Shift::findOrFail($id)->delete();
        return back()->with('ok','Schicht gelöscht.');
    }
}
