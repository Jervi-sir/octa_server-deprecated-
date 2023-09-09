public function publishItemDepricated(Request $request) {

return response()->json($request);

$imagesPath = [];
for($i = 1; $i <= 7; $i++) {
    if($request->hasFile('image'.$i)) {
        $path = $request->file('image'.$i)->store('image', 'public');
        array_push($imagesPath, $path);
    }
}

return [$imagesPath];
try {
    $validateUser = Validator::make($request->all(), [
        'shop_name' => 'required',
        'details' => 'required',
        'contacts' => 'required',
        'map_location' => 'required',
        'name' => 'required',
        'images' => 'required|image|mimes:jpg,png,jpeg',
        'size' => 'required',
        'stock' => 'required',
        'price' => 'required',
        'type' => 'required'
    ]);

    if($validateUser->fails()){
        return response()->json([
            'status' => false,
            'message' => 'validation error',
            'errors' => $validateUser->errors()
        ], 401);
    }

    $shop = auth()->user();
    $item = new Item();
    $item->shop_id = $shop->id;
    $item->shop_name = $request->shop_name;
    $item->shop_image = $shop->shop_image ? '' : '';
    $item->details = $request->details;
    $item->contacts = $request->contacts;
    $item->map_location = $request->map_location;
    $item->name = $request->name;
    $item->item_images = '';
    $item->size = $request->size;
    $item->stock = intval($request->stock);
    $item->price = $request->price;
    $item->type = $request->type;

    //$nb_image = sizeof($request->file('item_images'));
    /*
    $image_array = [];
    for($i = 0; $i < $nb_image; $i++) {
        array_push($image_array, $request->file($request->file('item_images['.[$i].']'))->store('image', 'public'));
    }
    return $image_array;
    json_encode($image_array);
    */

    $item->item_images = $request->file('images')->store('image', 'public');
    $item->save();

    return response()->json([
        'status' => true,
        'message' => 'Item Added',
    ], 200);

} catch (\Throwable $th) {
    return response()->json([
        'status' => false,
        'message' => $th->getMessage()
    ], 500);
}
}