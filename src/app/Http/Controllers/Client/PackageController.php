<?php

namespace App\Http\Controllers\Client;

use App\Models\Package;
use App\Models\User;
use App\Models\Postmat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use Endroid\QrCode\QrCode;
use App\Models\Stash;
use App\Utils\DistanceUtils;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function showForm()
    {
        $postmats = Postmat::all();

        return view('public.client.packages.send_package', compact('postmats'));
    }

    public function send_package(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required',
            'destination_postmat' => 'required',
            'start_postmat' => 'required',
            'size' => 'required|in:S,M,L',
            'weight' => 'required|integer|min:1',
        ]);

        $receiver = User::where('email', $request->email)->first();

        if (!$receiver) {
            $receiver_id = 0;
        } else {
            $receiver_id = $receiver->id;
        }

        $package = new Package();

        $postmats = Postmat::whereIn('name', [
            $request->destination_postmat,
            $request->start_postmat
        ])->get();

        $postmat = $postmats->firstWhere('name', $request->destination_postmat);
        $start_postmat = $postmats->firstWhere('name', $request->start_postmat);

        $available_stash = Stash::where('postmat_id', $start_postmat->id)
            ->where('size', $request->size)
            ->whereNull('package_id')
            ->first();

        $original_start_postmat = $start_postmat;
        $stash_changed = false;

        // If no stash available at start postmat, find the closest one with available stash
        if (!$available_stash) {
            // Get all active postmats with available stashes of required size
            $postmats_with_space = Postmat::where('status', 'active')
                ->whereHas('stashes', function ($query) use ($request) {
                    $query->where('size', $request->size)
                        ->whereNull('package_id');
                })
                ->get();

            // Find the closest postmat to the original start postmat
            $closest_distance = PHP_FLOAT_MAX;
            $closest_postmat = null;

            foreach ($postmats_with_space as $postmat) {
                $distance = DistanceUtils::haversineDistance(
                    $start_postmat->latitude,
                    $start_postmat->longitude,
                    $postmat->latitude,
                    $postmat->longitude
                );

                if ($distance < $closest_distance) {
                    $closest_distance = $distance;
                    $closest_postmat = $postmat;
                }
            }

            if ($closest_postmat) {
                $start_postmat = $closest_postmat;
                $available_stash = Stash::where('postmat_id', $start_postmat->id)
                    ->where('size', $request->size)
                    ->whereNull('package_id')
                    ->first();

                $stash_changed = true;
            } else {
                return back()->withErrors(['start_postmat' => 'No available stashes of size ' . $request->size . ' in any postmat.']);
            }
        }

        $package->sender_id = Auth::id();
        // $package->receiver_id = $receiver_id;
        $package->receiver_phone = $request->phone;
        $package->receiver_email = $request->email;
        $package->destination_postmat_id = $postmat->id;
        $package->start_postmat_id = $start_postmat->id;
        // ['registered', 'in_transit', 'in_postmat', 'collected']
        $package->status = 'registered';
        $package->weight = $request->weight;
        $package->size = $request->size;
        $package->sent_at = now();
        $package->save();

        // Create Package history
        Actualization::create([
            'package_id' => $package->id,
            // message = ['sent', 'in_warehouse', 'in_delivery']
            'message' => 'sent',
            'created_at' => now(),
        ]);

        // Reserve Stash in Postmat
        $available_stash->reserveFor($package);

        // Generate QrCode
        $track_url = "http://localhost:8000/track?code={$package->id}";
        $qrCode = new QrCode($track_url);
        $qrCode->setSize(200);
        $qrCodeImage = $qrCode->writeString();
        $qrCodeBase64 = base64_encode($qrCodeImage);

        return view('public.client.packages.package_summary', [
            'package' => $package,
            'qrCode' => $qrCodeBase64,
            'stashChanged' => $stash_changed,
            'originalStartPostmat' => $original_start_postmat,
            'distance' => $distance ?? null,
            'trackUrl' => $track_url,
        ]);
    }

    public function show_collect_package(Request $request)
    {
        return view('public.client.packages.package_collect');
    }

    public function collect_package(Request $request)
    {
        $validated = $request->validate([
            'receiver_phone' => ['required', 'string'],
            'unlock_code'    => ['required', 'digits:6'],
        ]);

        // 1. Find the matching package
        $package = Package::with('stash')
            ->where('receiver_phone', $validated['receiver_phone'])
            ->where('unlock_code',   $validated['unlock_code'])
            ->where('status', '!=', 'collected')
            ->first();

        if (! $package) {
            return back()->withErrors(['unlock_code' => 'Invalid phone or unlock code.'])
                ->withInput();
        }

        // 2. Ensure the package is in its destination postmat stash
        $stash = $package->stash;                 // may be null
        $inDestination = $stash
            && $stash->postmat_id == $package->destination_postmat_id
            && $stash->is_package_in;

        if (! $inDestination) {
            return back()->withErrors([
                'unlock_code' => 'Package is not yet available at the destination postmat.',
            ])->withInput();
        }

        // 3. Mark package as collected and clear the stash
        $package->update([
            'collected_date' => now(),
            'status'         => 'collected',
        ]);

        $stash->clearReservation();

        return redirect()->route('client.package.collected');
    }

    # TODO if user is logged in, then show diffrent page with function to open the stash.
    public function track(Request $request)
    {
        $request->validate([
            'code' => 'required|integer',
        ]);

        $package = Package::with('actualizations', 'destinationPostmat')->find($request->code);

        if (!$package) {
            return view('public.client.packages.package_track', [
                'error' => 'Package not found.',
                'not_exist' => true,
                'actualizations' => collect(),
                'postmat' => null,
                'maskedEmail' => null,
                'maskedPhone' => null,
            ]);
        }

        // Fallback dummy if no actualizations yet
        $actualizations = $package->actualizations;
        $postmat = $package->destinationPostmat;

        if ($actualizations->isEmpty()) {
            $actualizations = collect([
                (object)[
                    'message' => 'sent',
                    'created_at' => now()->subMinutes(15),
                ]
            ]);
        }

        $maskedEmail = $this->maskEmail($package->receiver_email);
        $maskedPhone = $this->maskPhone($package->receiver_phone);

        return view('public.client.packages.package_track', [
            'actualizations' => $actualizations,
            'postmat' => $postmat,
            'not_exist' => false,
            'maskedEmail' => $maskedEmail,
            'maskedPhone' => $maskedPhone,
            'package' => $package,
        ]);
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $maskedName = substr($name, 0, 1) . str_repeat('*', max(0, strlen($name) - 1));

        $domainParts = explode('.', $domain);
        $domainName = $domainParts[0] ?? '';
        $tld = $domainParts[1] ?? 'com';

        $maskedDomain = substr($domainName, 0, 1) . str_repeat('*', max(0, strlen($domainName) - 1));

        return "{$maskedName}@{$maskedDomain}.{$tld}";
    }

    private function maskPhone(string $phone): string
    {
        return substr($phone, 0, 2) . str_repeat('*', max(0, strlen($phone) - 4)) . substr($phone, -2);
    }
}
