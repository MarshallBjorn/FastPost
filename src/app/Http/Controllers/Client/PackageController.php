<?php

namespace App\Http\Controllers\Client;

use App\Models\Package;
use App\Models\User;
use App\Models\Postmat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;
// use Illuminate\Support\Facades\Auth;
use App\Models\Actualization;
use Endroid\QrCode\QrCode;

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
        ]);

        $receiver = User::where('email', $request->email)->first();

        if (!$receiver) {
            // return back()->withErrors(['email' => "Couldn't find any user with provided e-mail"]);

            $receiver = User::create([
                'email' => $request->email,
                'name' => 'Unknown Recipient',
                'first_name' => 'Unknown',
                'last_name' => 'User',
                'phone' => $request->phone,
                'user_type' => 'normal',
                'password' => bcrypt('passwordpassword'), // or random string
            ]);

            session()->flash('warning', "No existing user found with that email. A new placeholder recipient was created.");
        }

        $package = new Package();


        // $postmat = Postmat::where('name', $request->destination_postmat)->first();
        // $start_postmat = Postmat::where('name', $request->start_postmat)->first();

        $postmats = Postmat::whereIn('name', [
            $request->destination_postmat, 
            $request->start_postmat
        ])->get();
        
        $postmat = $postmats->firstWhere('name', $request->destination_postmat);
        $start_postmat = $postmats->firstWhere('name', $request->start_postmat);


        // $package->sender_id = Auth::id();
        // For now just get static user
        $package->sender_id = 1;
        $package->receiver_id = $receiver->id;
        $package->receiver_phone = $request->phone;
        $package->receiver_email = $receiver->email;
        $package->destination_postmat_id = $postmat->id;
        $package->start_postmat_id = $start_postmat->id;
        // ['registered', 'in_transit', 'in_postmat', 'collected']
        $package->status = 'registered';
        $package->sent_at = now();
        $package->save();

        // Create Package history
        Actualization::create([
            'package_id' => $package->id,
            // message = ['sent', 'in_warehouse', 'in_delivery']
            'message' => 'sent',
            'created_at' => now(),
        ]);

        // Generate QrCode

        $qrCode = new QrCode("http://localhost:8000/packages/{$package->id}");
        $qrCode->setSize(200);
        $qrCodeImage = $qrCode->writeString();
        $qrCodeBase64 = base64_encode($qrCodeImage);


        return view('public.client.packages.package_summary', [
            'package' => $package,
            'qrCode' => $qrCodeBase64
        ]);
    }
}
