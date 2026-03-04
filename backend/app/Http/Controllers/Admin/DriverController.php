<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        $query = Driver::query()->with('user')->latest();

        if ($request->filled('approval')) {
            if ($request->string('approval')->toString() === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->string('approval')->toString() === 'approved') {
                $query->where('is_approved', true);
            }
        }

        if ($request->filled('q')) {
            $q = '%'.$request->string('q').'%';
            $query->whereHas('user', function ($qry) use ($q) {
                $qry->where('name', 'like', $q)
                    ->orWhere('email', 'like', $q);
            });
        }

        $drivers = $query->paginate(20)->withQueryString();

        return view('admin.drivers.index', compact('drivers'));
    }

    public function show(Driver $driver): View
    {
        $driver->load(['user', 'deliveries.order' => function ($query) {
            $query->latest()->limit(10);
        }]);

        $stats = [
            'total_deliveries' => $driver->total_deliveries,
            'completed_deliveries' => $driver->deliveries()->where('status', 'delivered')->count(),
            'pending_deliveries' => $driver->deliveries()->where('status', 'picked_up')->count(),
            'average_rating' => $driver->average_rating,
        ];

        return view('admin.drivers.show', compact('driver', 'stats'));
    }

    public function approve(Driver $driver): RedirectResponse
    {
        $driver->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('admin.drivers.show', $driver)
            ->with('success', 'Driver approved.');
    }

    public function reject(Driver $driver): RedirectResponse
    {
        $driver->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return redirect()
            ->route('admin.drivers.show', $driver)
            ->with('success', 'Driver approval removed.');
    }
}
