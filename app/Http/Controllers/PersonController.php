<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('type', 'all');

        $persons = Person::query()
            ->when($filter === 'customer', fn ($q) => $q->customers())
            ->when($filter === 'supplier', fn ($q) => $q->suppliers())
            ->when($filter === 'employee', fn ($q) => $q->employees())
            ->orderBy('name')
            ->get();

        return view('persons.index', compact('persons', 'filter'));
    }

    public function create()
    {
        return view('persons.form', ['person' => new Person]);
    }

    /** دفتر تلفن — every person with a phone or mobile number on file, old-app screen this app never had. */
    public function phonebook()
    {
        $persons = Person::query()
            ->where(function ($q) {
                $q->where(fn ($p) => $p->whereNotNull('phone')->where('phone', '!=', ''))
                    ->orWhere(fn ($m) => $m->whereNotNull('mobile')->where('mobile', '!=', ''));
            })
            ->orderBy('name')
            ->get();

        return view('persons.phonebook', compact('persons'));
    }

    public function store(Request $request)
    {
        Person::create($this->validated($request));

        return redirect()->route('persons.index')->with('success', 'شخص جدید ثبت شد.');
    }

    public function edit(Person $person)
    {
        return view('persons.form', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
        $person->update($this->validated($request));

        return redirect()->route('persons.index')->with('success', 'معلومات شخص بروزرسانی شد.');
    }

    public function destroy(Person $person)
    {
        if ($person->journalLines()->exists()) {
            return back()->with('error', 'این شخص دارای تراکنش است و قابل حذف نیست.');
        }

        $person->delete();

        return redirect()->route('persons.index')->with('success', 'حذف شد.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'is_customer' => 'boolean',
            'is_supplier' => 'boolean',
            'is_employee' => 'boolean',
            'notes' => 'nullable|string',
        ]);
    }
}
