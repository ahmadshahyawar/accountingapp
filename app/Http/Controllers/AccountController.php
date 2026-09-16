<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');

        $accounts = Account::with('parent')
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderBy('code')
            ->get();

        return view('accounts.index', compact('accounts', 'type'));
    }

    public function create(Request $request)
    {
        $parents = Account::where('is_group', true)->orderBy('code')->get();
        $account = new Account(['type' => $request->get('type')]);

        return view('accounts.form', ['account' => $account, 'parents' => $parents]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Account::create($data);

        return redirect()->route('accounts.index')->with('success', 'حساب جدید ثبت شد.');
    }

    public function edit(Account $account)
    {
        $parents = Account::where('is_group', true)->where('id', '!=', $account->id)->orderBy('code')->get();

        return view('accounts.form', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account)
    {
        $data = $this->validated($request, $account->id);
        $account->update($data);

        return redirect()->route('accounts.index')->with('success', 'حساب بروزرسانی شد.');
    }

    public function destroy(Account $account)
    {
        if ($account->journalLines()->exists() || $account->children()->exists()) {
            return back()->with('error', 'این حساب دارای تراکنش یا زیرحساب است و قابل حذف نیست.');
        }

        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'حساب حذف شد.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => 'required|string|max:50|unique:accounts,code'.($ignoreId ? ",{$ignoreId}" : ''),
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'required|in:debit,credit',
            'is_group' => 'boolean',
            'is_active' => 'boolean',
        ]);
    }
}
