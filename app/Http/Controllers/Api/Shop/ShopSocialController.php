<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopSocialController extends Controller
{
    public function updateThisSocial(Request $request) {
        $validated = $request->validate([
            'id' => 'required|integer',
            'platform' => 'required|string',
            'profileURL' => 'required|string',
        ]);
    
        $shop = auth()->user();
        $contacts = json_decode($shop->contacts);
    
    
        // Get individual request data
        $requestedId = $request->id;
        $requestedPlatform = $request->platform;
        $requestedProfileURL = $request->profileURL;
    
        $found = false;
    
        foreach ($contacts as $key => $contact) {
            if ($contact->id === $requestedId) { // Ensure both IDs are integers
                $contacts[$key]->platform = $requestedPlatform;
                $contacts[$key]->profileURL = $requestedProfileURL;
                $found = true;
                break;
            }
        }
    
        if (!$found) {
            return response()->json(['message' => 'Contact not found'], 404);
        }
    
        // Encode the updated array back to JSON
        $shop->contacts = json_encode($contacts);
        $shop->save(); // Save the shop model
    
        return response()->json([
            'message' => 'Contact added successfully!',
            'shop_auth_info' => OS_getMyShop()  
        ]);
    }

    public function addSocials(Request $request)
    {
        $request->validate([
            'profileURL' => 'required',
            'platform' => 'required',
        ]);

        $shop = auth()->user();

        $contacts = json_decode($shop->contacts, true) ?? [];
        
        // Determine the next ID
        $nextId = 0;
        foreach ($contacts as $contact) {
            if ($contact['id'] > $nextId) {
                $nextId = $contact['id'];
            }
        }
        $nextId++;  // Increment to get the next ID

        $contacts[] = [
            'id' => $nextId,  // Use the next ID
            'platform' => $request->platform,
            'profileURL' => $request->profileURL,
        ];

        $shop->contacts = json_encode($contacts);
        $shop->save();

        return response()->json([
            'message' => 'Contact added successfully!',
            'shop_auth_info' => OS_getMyShop()  // Ensure this method returns the updated shop info
        ]);
    }

    public function deleteThisSocial(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'platform' => 'required|string',
            'profileURL' => 'required|string',
        ]);

        $shop = auth()->user();  // Get the current authenticated shop
        $contactId = $request->id;  // Get the contact ID to remove
        
        // Decode the existing contacts
        $contacts = json_decode($shop->contacts, true) ?? [];

        // Filter out the contact with the specified ID
        $filteredContacts = array_filter($contacts, function ($contact) use ($contactId) {
            return $contact['id'] !== $contactId;
        });

        // Re-encode and save the updated contacts list
        $shop->contacts = json_encode(array_values($filteredContacts));
        $shop->save();

        return response()->json([
            'message' => 'Contact removed successfully!',
            'shop_auth_info' => OS_getMyShop()  // Optionally return the updated shop info
        ]);
    }

}
